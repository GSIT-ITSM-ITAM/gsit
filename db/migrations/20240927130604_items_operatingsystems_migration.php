<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class ItemsOperatingsystemsMigration extends AbstractMigration
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
    $item = $this->table('item_operatingsystem');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_items_operatingsystems');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                              => $row['id'],
            'item_id'                         => $row['items_id'],
            'item_type'                       => 'App\\Models\\' . $row['itemtype'],
            'operatingsystem_id'              => $row['operatingsystems_id'],
            'operatingsystemversion_id'       => $row['operatingsystemversions_id'],
            'operatingsystemservicepack_id'   => $row['operatingsystemservicepacks_id'],
            'operatingsystemarchitecture_id'  => $row['operatingsystemarchitectures_id'],
            'operatingsystemkernelversion_id' => $row['operatingsystemkernelversions_id'],
            'license_number'                  => $row['license_number'],
            'licenseid'                       => $row['licenseid'],
            'operatingsystemedition_id'       => $row['operatingsystemeditions_id'],
            'updated_at'                      => Toolbox::fixDate($row['date_mod']),
            'created_at'                      => Toolbox::fixDate($row['date_creation']),
            'is_dynamic'                      => $row['is_dynamic'],
            'entity_id'                       => ($row['entities_id'] + 1),
            'is_recursive'                    => $row['is_recursive'],
            'deleted_at'                      => self::convertIsDeleted($row['is_deleted']),
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

  public function convertIsDeleted($is_deleted)
  {
    if ($is_deleted == 1)
    {
      return date('Y-m-d H:i:s', time());
    }

    return null;
  }
}
