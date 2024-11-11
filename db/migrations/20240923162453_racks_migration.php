<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class RacksMigration extends AbstractMigration
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
    $item = $this->table('racks');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_racks');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                => $row['id'],
            'name'              => $row['name'],
            'comment'           => $row['comment'],
            'entity_id'         => ($row['entities_id'] + 1),
            'is_recursive'      => $row['is_recursive'],
            'location_id'       => $row['locations_id'],
            'serial'            => $row['serial'],
            'otherserial'       => $row['otherserial'],
            'rackmodel_id'      => $row['rackmodels_id'],
            'manufacturer_id'   => $row['manufacturers_id'],
            'racktype_id'       => $row['racktypes_id'],
            'state_id'          => $row['states_id'],
            'user_id_tech'      => $row['users_id_tech'],
            'group_id_tech'     => $row['groups_id_tech'],
            'width'             => $row['width'],
            'height'            => $row['height'],
            'depth'             => $row['depth'],
            'number_units'      => $row['number_units'],
            'is_template'       => $row['is_template'],
            'template_name'     => $row['template_name'],
            'dcroom_id'         => $row['dcrooms_id'],
            'room_orientation'  => $row['room_orientation'],
            'position'          => $row['position'],
            'bgcolor'           => $row['bgcolor'],
            'max_power'         => $row['max_power'],
            'mesured_power'     => $row['mesured_power'],
            'max_weight'        => $row['max_weight'],
            'updated_at'        => Toolbox::fixDate($row['date_mod']),
            'created_at'        => Toolbox::fixDate($row['date_creation']),
            'deleted_at'        => self::convertIsDeleted($row['is_deleted']),
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('racks_id_seq', (SELECT MAX(id) FROM racks)+1)");
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
