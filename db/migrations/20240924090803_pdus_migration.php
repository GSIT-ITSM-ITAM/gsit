<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class PdusMigration extends AbstractMigration
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
    $item = $this->table('pdus');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_pdus');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                => $row['id'],
            'name'              => $row['name'],
            'entity_id'         => ($row['entities_id'] + 1),
            'is_recursive'      => $row['is_recursive'],
            'location_id'       => $row['locations_id'],
            'serial'            => $row['serial'],
            'otherserial'       => $row['otherserial'],
            'pdumodel_id'       => $row['pdumodels_id'],
            'user_id_tech'      => $row['users_id_tech'],
            'group_id_tech'     => $row['groups_id_tech'],
            'is_template'       => $row['is_template'],
            'template_name'     => $row['template_name'],
            'state_id'          => $row['states_id'],
            'comment'           => $row['comment'],
            'manufacturer_id'   => $row['manufacturers_id'],
            'pdutype_id'        => $row['pdutypes_id'],
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
        $this->execute("SELECT setval('pdus_id_seq', (SELECT MAX(id) FROM pdus)+1)");
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
