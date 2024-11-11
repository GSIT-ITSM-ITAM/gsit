<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class ItemsDevicesimcardsMigration extends AbstractMigration
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
    $item = $this->table('item_devicesimcard');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_items_devicesimcards');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                => $row['id'],
            'item_id'           => $row['items_id'],
            'item_type'         => 'App\\Models\\' . $row['itemtype'],
            'devicesimcard_id'  => $row['devicesimcards_id'],
            'is_dynamic'        => $row['is_dynamic'],
            'entity_id'         => ($row['entities_id'] + 1),
            'is_recursive'      => $row['is_recursive'],
            'serial'            => $row['serial'],
            'otherserial'       => $row['otherserial'],
            'state_id'          => $row['states_id'],
            'location_id'       => $row['locations_id'],
            'line_id'           => $row['lines_id'],
            'user_id'           => $row['users_id'],
            'group_id'          => $row['groups_id'],
            'pin'               => $row['pin'],
            'pin2'              => $row['pin2'],
            'puk'               => $row['puk'],
            'puk2'              => $row['puk2'],
            'msin'              => $row['msin'],
            'deleted_at'        => self::convertIsDeleted($row['is_deleted']),
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('item_devicesimcard_id_seq', (SELECT MAX(id) FROM item_devicesimcard)+1)");
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
