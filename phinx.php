<?php

return
[
  'paths' => [
    'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
    'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds'
  ],
  'environments' => [
    'default_migration_table' => 'phinxlog',
    'default_environment' => 'production',
    'production' => [
      'adapter' => 'mysql',
      'host' => 'localhost',
      'name' => 'gsit',
      'user' => 'gsit',
      'pass' => 'mypass',
      'port' => '3306',
      'charset' => 'utf8mb4',
      'collation' => 'utf8mb4_general_ci',
    ],
    'productionpgsql' => [
      'adapter' => 'pgsql',
      'host' => 'localhost',
      'name' => 'gsit',
      'user' => 'gsit',
      'pass' => 'mypass',
      'port' => '5432',
      'charset' => 'utf8mb4',
      'collation' => 'utf8mb4_general_ci',
    ],
    // 'old' => [
    //   'adapter' => 'mysql',
    //   'host' => 'localhost',
    //   'name' => 'glpi',
    //   'user' => 'glpi',
    //   'pass' => 'mypass',
    //   'port' => '3306',
    //   'charset' => 'utf8mb4',
    //   'collation' => 'utf8_unicode_ci',
    // ],
    'tests' => [
      'adapter' => 'mysql',
      'host' => 'localhost',
      'name' => 'gsittests',
      'user' => 'gsittests',
      'pass' => 'gsittests',
      'port' => '3306',
      'charset' => 'utf8mb4',
      'collation' => 'utf8mb4_general_ci',
    ],
  ],
  'version_order' => 'creation'
];
