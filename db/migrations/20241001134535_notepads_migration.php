<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class NotepadsMigration extends AbstractMigration
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
    $item = $this->table('notepads');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_notepads');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        if ($row['itemtype'] == 'Entity')
        {
          $row['items_id'] = $row['items_id'] + 1;
        }

        $data = [
          [
            'id'                  => $row['id'],
            'item_type'           => self::convertItemtype($row['itemtype']),
            'item_id'             => $row['items_id'],
            'created_at'          => Toolbox::fixDate($row['date']),
            'updated_at'          => Toolbox::fixDate($row['date_mod']),
            'user_id'             => $row['users_id'],
            'user_id_lastupdater' => $row['users_id_lastupdater'],
            'content'             => $row['content'],
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('notepads_id_seq', (SELECT MAX(id) FROM notepads)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }

  public function convertItemtype($itemtype)
  {
    $new_itemtype = '';

    if ($itemtype != null) {
      $new_itemtype = $itemtype;
      $new_itemtype = ucfirst(strtolower($new_itemtype));
      if ($new_itemtype == 'Item_devicesimcard') {
        $new_itemtype = 'ItemDevicesimcard';
      }
      $new_itemtype = 'App\\Models\\' . $new_itemtype;
    }

    return $new_itemtype;
  }
}
