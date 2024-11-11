<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class DisplaypreferencesMigration extends AbstractMigration
{
  public function change()
  {
    $configArray = require('phinx.php');
    $environments = array_keys($configArray['environments']);
    if (in_array('old', $environments))
    {
      // Migration of database

      $config = Config::fromPhp('phinx.php');
      $environment = new Environment('old', $config->getEnvironment('old'));
      $pdo = $environment->getAdapter()->getConnection();
    } else {
      return;
    }
    $item = $this->table('displaypreferences');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_displaypreferences');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        // rename ItemDeviceXXX -> DeviceXXX
        $row['itemtype'] = str_replace('ItemDevice', 'Device', $row['itemtype']);

        $data = [
          [
            'id'        => $row['id'],
            'itemtype'  => 'App\\Models\\' . $row['itemtype'],
            'num'       => $row['num'],
            'rank'      => $row['rank'],
            'user_id'   => $row['users_id'],
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('displaypreferences_id_seq', (SELECT MAX(id) FROM displaypreferences)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
