<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class PrintersMigration extends AbstractMigration
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
    $item = $this->table('printers');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_printers');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                  => $row['id'],
            'entity_id'           => ($row['entities_id'] + 1),
            'is_recursive'        => $row['is_recursive'],
            'name'                => $row['name'],
            'updated_at'          => Toolbox::fixDate($row['date_mod']),
            'contact'             => $row['contact'],
            'contact_num'         => $row['contact_num'],
            'user_id_tech'        => $row['users_id_tech'],
            'group_id_tech'       => $row['groups_id_tech'],
            'serial'              => $row['serial'],
            'otherserial'         => $row['otherserial'],
            'have_serial'         => $row['have_serial'],
            'have_parallel'       => $row['have_parallel'],
            'have_usb'            => $row['have_usb'],
            'have_wifi'           => $row['have_wifi'],
            'have_ethernet'       => $row['have_ethernet'],
            'comment'             => $row['comment'],
            'memory_size'         => $row['memory_size'],
            'location_id'         => $row['locations_id'],
            'network_id'          => $row['networks_id'],
            'printertype_id'      => $row['printertypes_id'],
            'printermodel_id'     => $row['printermodels_id'],
            'manufacturer_id'     => $row['manufacturers_id'],
            'is_global'           => $row['is_global'],
            'is_template'         => $row['is_template'],
            'template_name'       => $row['template_name'],
            'init_pages_counter'  => $row['init_pages_counter'],
            'last_pages_counter'  => $row['last_pages_counter'],
            'user_id'             => $row['users_id'],
            'group_id'            => $row['groups_id'],
            'state_id'            => $row['states_id'],
            'ticket_tco'          => $row['ticket_tco'],
            'is_dynamic'          => $row['is_dynamic'],
            'created_at'          => Toolbox::fixDate($row['date_creation']),
            'deleted_at'          => self::convertIsDeleted($row['is_deleted']),
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('printers_id_seq', (SELECT MAX(id) FROM printers)+1)");
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
