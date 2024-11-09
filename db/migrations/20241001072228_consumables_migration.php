<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class ConsumablesMigration extends AbstractMigration
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
    $item = $this->table('consumables');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_consumables');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                => $row['id'],
            'entity_id'         => ($row['entities_id'] + 1),
            'consumableitem_id' => $row['consumableitems_id'],
            'date_in'           => $row['date_in'],
            'date_out'          => $row['date_out'],
            'item_type'         => 'App\\Models\\' . $row['itemtype'],
            'item_id'           => $row['items_id'],
            'updated_at'        => Toolbox::fixDate($row['date_mod']),
            'created_at'        => Toolbox::fixDate($row['date_creation']),
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
