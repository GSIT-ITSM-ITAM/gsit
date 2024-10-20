<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class DevicegraphiccardsMigration extends AbstractMigration
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
    $item = $this->table('devicegraphiccards');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_devicegraphiccards');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                        => $row['id'],
            'name'                      => $row['designation'],
            'interfacetype_id'          => $row['interfacetypes_id'],
            'comment'                   => $row['comment'],
            'manufacturer_id'           => $row['manufacturers_id'],
            'memory_default'            => $row['memory_default'],
            'entity_id'                 => $row['entities_id'],
            'is_recursive'              => $row['is_recursive'],
            'devicegraphiccardmodel_id' => $row['devicegraphiccardmodels_id'],
            'chipset'                   => $row['chipset'],
            'updated_at'                => $row['date_mod'],
            'created_at'                => $row['date_creation'],
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