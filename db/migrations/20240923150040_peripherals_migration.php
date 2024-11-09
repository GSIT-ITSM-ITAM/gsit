<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class PeripheralsMigration extends AbstractMigration
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
    $item = $this->table('peripherals');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_peripherals');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                  => $row['id'],
            'entity_id'           => ($row['entities_id'] + 1),
            'name'                => $row['name'],
            'updated_at'          => Toolbox::fixDate($row['date_mod']),
            'contact'             => $row['contact'],
            'contact_num'         => $row['contact_num'],
            'user_id_tech'        => $row['users_id_tech'],
            'group_id_tech'       => $row['groups_id_tech'],
            'comment'             => $row['comment'],
            'serial'              => $row['serial'],
            'otherserial'         => $row['otherserial'],
            'location_id'         => $row['locations_id'],
            'peripheraltype_id'   => $row['peripheraltypes_id'],
            'peripheralmodel_id'  => $row['peripheralmodels_id'],
            'brand'               => $row['brand'],
            'manufacturer_id'     => $row['manufacturers_id'],
            'is_global'           => $row['is_global'],
            'is_template'         => $row['is_template'],
            'template_name'       => $row['template_name'],
            'user_id'             => $row['users_id'],
            'group_id'            => $row['groups_id'],
            'state_id'            => $row['states_id'],
            'ticket_tco'          => $row['ticket_tco'],
            'is_dynamic'          => $row['is_dynamic'],
            'created_at'          => Toolbox::fixDate($row['date_creation']),
            'is_recursive'        => $row['is_recursive'],
            'deleted_at'          => self::convertIsDeleted($row['is_deleted']),
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
