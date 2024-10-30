<?php

namespace Tasks;

use Illuminate\Database\Capsule\Manager as Capsule;

require __DIR__ . '/../vendor/autoload.php';

global $translator;
global $basePath;

// Load lang
$lang = new \App\Translation();
$translator = $lang->loadLanguage('en_GB');
$apiversion = 'v1';

$basePath = "";

class Myapp
{
  public static function loadCapsule()
  {
    $dbConfig = include(__DIR__ . '/../phinx.php');
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

    $capsule = new Capsule();
    $capsule->addConnection($configdb);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();
  }
}
