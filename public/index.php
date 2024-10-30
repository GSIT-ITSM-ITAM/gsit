<?php

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\ErrorHandler\ErrorHandler as SymfonyErrorHandler;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';
// Load lang
$lang = new \App\Translation();
$translator = $lang->loadLanguage();
$apiversion = 'v1';

$app = AppFactory::create();

$app->addRoutingMiddleware();
$basePath = "";
if (strstr($_SERVER['REQUEST_URI'], 'index.php'))
{
  $uri_spl = explode('index.php', $_SERVER['REQUEST_URI']);
  $basePath = $uri_spl[0] . "index.php";
}
if (strstr($_SERVER['REQUEST_URI'], '/'))
{
  $uri_spl = explode('/', $_SERVER['REQUEST_URI']);
  $paths = [];
  foreach ($uri_spl as $path)
  {
    if ($path == '')
    {
      continue;
    }
    if (in_array($path, ['ping', 'view', 'api']))
    {
      break;
    } else {
      $paths[] = $path;
    }
  }
  $basePath = '/' . implode('/', $paths);
}
$app->setBasePath($basePath);



// Create Twig
$twig = Twig::create('../src/v1/Views');

// Add Twig-View Middleware
// $app->add(TwigMiddleware::create($app, $twig));
$app->add(new TwigMiddleware($twig, $app->getRouteCollector()->getRouteParser(), '', 'view'));

// See https://github.com/tuupola/slim-jwt-auth
$container = $app->getContainer();

$container["jwt"] = function ($container)
{
  return new StdClass();
};

$secret = sodium_base642bin('TEST', SODIUM_BASE64_VARIANT_ORIGINAL);

$capsule = new Capsule();
$dbConfig = include('../phinx.php');
$myDatabase = $dbConfig['environments'][$dbConfig['environments']['default_environment']];
$configdb = [
  'driver'    => $myDatabase['adapter'],
  'host'      => $myDatabase['host'],
  'database'  => $myDatabase['name'],
  'username'  => $myDatabase['user'],
  'password'  => $myDatabase['pass'],
  'charset'   => $myDatabase['charset'],
  'collation' => $myDatabase['collation'],
];
$capsule->addConnection($configdb);
$capsule->setEventDispatcher(new Dispatcher(new Container()));
$capsule->setAsGlobal();
$capsule->bootEloquent();

$ignoreList = [
  $basePath . "/ping",
  $basePath . "/view/login",
  $basePath . "/view/sso",
  $basePath . "/view/sso/cb",
  $basePath . "/api/v1/fusioninventory",
];
$mailcollectors = \App\Models\Mailcollector::where('is_active', true)->where('is_oauth', true)->get();
foreach ($mailcollectors as $collector)
{
  $ignoreList[] = $basePath . "/view/mailcollectors/" . $collector->id . "/oauth/cb";
}

$app->add(new Tuupola\Middleware\JwtAuthentication([
  "ignore" => $ignoreList,
  "secure" => false,
  "secret" => $secret,
  "before" => function ($request, $arguments)
  {
    $myUser = \App\Models\User::find($arguments['decoded']['user_id']);
    // $jwtid = $myUser->getPropertyAttribute('userjwtid');
    // if (is_null($jwtid) || $jwtid != $arguments['decoded']['jti'])
    // {
    //   throw new Exception('jti changed, ask for a new token ' . $myUser['jwtid'] . ' != ' .
    //                       $arguments['decoded']['jti'], 401);
    // }
    $GLOBALS['user_id'] = $arguments['decoded']['user_id'];
    $GLOBALS['username'] = $myUser->completename;
    $GLOBALS['profile_id'] = $arguments['decoded']['profile_id'];
    $GLOBALS['entity_id'] = $arguments['decoded']['entity_id'];
    $GLOBALS['entity_treepath'] = $arguments['decoded']['entity_treepath'];
    $GLOBALS['entity_recursive'] = $arguments['decoded']['entity_recursive'];
    // Load permissions
    // $GLOBALS['permissions'] = \App\v1\Controllers\Config\Role::generatePermission(
    //   $arguments['decoded']['role_id']
    // );
  },
  "error" => function ($response, $arguments)
  {
    global $basePath;

    $GLOBALS['user_id'] = null;
    // for web, redirect to login page
    header('Location: ' . $basePath . '/view/login');
    exit();

    // for API
    throw new Exception($arguments["message"], 401);
  }
]));

// Init session
$app->add(
  new \Slim\Middleware\Session([
    'name' => 'gsit_session',
    'autorefresh' => true,
    'lifetime' => '1 hour',
  ])
);

// Define routes
\App\Route::setRoutes($app);

// Define Custom Error Handler
$customErrorHandler = function (
  Request $request,
  Throwable $exception,
  bool $displayErrorDetails,
  bool $logErrors,
  bool $logErrorDetails
) use ($app)
{
  // TODO write error page
};

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
// $errorMiddleware->setDefaultErrorHandler($customErrorHandler);

// get php errors (warning...)
SymfonyErrorHandler::register();

$app->run();
