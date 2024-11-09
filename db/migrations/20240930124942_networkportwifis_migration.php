<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class NetworkportwifisMigration extends AbstractMigration
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
    $item = $this->table('networkportwifis');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_networkportwifis');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                        => $row['id'],
            'networkport_id'            => $row['networkports_id'],
            'item_devicenetworkcard_id' => $row['items_devicenetworkcards_id'],
            'wifinetwork_id'            => $row['wifinetworks_id'],
            'networkportwifi_id'        => $row['networkportwifis_id'],
            'version'                   => $row['version'],
            'mode'                      => $row['mode'],
            'updated_at'                => Toolbox::fixDate($row['date_mod']),
            'created_at'                => Toolbox::fixDate($row['date_creation']),

          ]
        ];
        $item->insert($data)
             ->saveData();
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
