<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

include('vendor/autoload.php');

global $translator;
global $databaseName;

$GLOBALS['user_id'] = 1;
$GLOBALS['username'] = 'Theodore Admin';
$GLOBALS['profile_id'] = 1;
$GLOBALS['entity_id'] = 1;
$GLOBALS['entity_treepath'] = '00001';
$GLOBALS['entity_recursive'] = true;

$lang = new \App\Translation();
$translator = $lang->loadLanguage('en_GB');

$dbConfig = include(__DIR__ . '/../phinx.php');
$myDatabase = $dbConfig['environments']['tests'];
$configdb = [
  'driver'    => $myDatabase['adapter'],
  'host'      => $myDatabase['host'],
  'database'  => $myDatabase['name'],
  'username'  => $myDatabase['user'],
  'password'  => $myDatabase['pass'],
  'charset'   => $myDatabase['charset'],
  'collation' => $myDatabase['collation'],
];
$databaseName = $myDatabase['name'];

$capsule = new Capsule();
$capsule->addConnection($configdb);
$capsule->setEventDispatcher(new Dispatcher(new Container()));
$capsule->setAsGlobal();
$capsule->bootEloquent();

session_start();
