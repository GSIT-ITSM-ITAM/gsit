<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class ItemsSoftwareversionsMigration extends AbstractMigration
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
    $item = $this->table('item_softwareversion');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_items_softwareversions');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $deletedItem = false;
        $templateItem = false;
        if (isset($row['is_deleted_computer']))
        {
          $deletedItem = $row['is_deleted_computer'];
        }
        if (isset($row['is_deleted_item']))
        {
          $deletedItem = $row['is_deleted_item'];
        }
        if (isset($row['is_template_computer']))
        {
          $templateItem = $row['is_template_computer'];
        }
        if (isset($row['is_template_item']))
        {
          $templateItem = $row['is_template_item'];
        }

        $data = [
          [
            'id'                  => $row['id'],
            'item_id'             => $row['items_id'],
            'item_type'           => 'App\\Models\\' . $row['itemtype'],
            'softwareversion_id'  => $row['softwareversions_id'],
            'is_deleted_item'     => $deletedItem,
            'is_template_item'    => $templateItem,
            'entity_id'           => ($row['entities_id'] + 1),
            'is_dynamic'          => $row['is_dynamic'],
            'date_install'        => Toolbox::fixDate($row['date_install']),
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
