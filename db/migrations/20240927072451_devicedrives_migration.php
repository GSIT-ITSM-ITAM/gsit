<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class DevicedrivesMigration extends AbstractMigration
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
    $item = $this->table('devicedrives');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_devicedrives');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                  => $row['id'],
            'name'                => $row['designation'],
            'is_writer'           => $row['is_writer'],
            'speed'               => $row['speed'],
            'comment'             => $row['comment'],
            'manufacturer_id'     => $row['manufacturers_id'],
            'interfacetype_id'    => $row['interfacetypes_id'],
            'entity_id'           => ($row['entities_id'] + 1),
            'is_recursive'        => $row['is_recursive'],
            'devicedrivemodel_id' => $row['devicedrivemodels_id'],
            'updated_at'          => Toolbox::fixDate($row['date_mod']),
            'created_at'          => Toolbox::fixDate($row['date_creation']),
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
