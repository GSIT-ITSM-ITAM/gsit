<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class NotificationsMigration extends AbstractMigration
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
    $item = $this->table('notifications');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_notifications');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'              => $row['id'],
            'name'            => $row['name'],
            'entity_id'       => ($row['entities_id'] + 1),
            'item_type'       => 'App\\Models\\' . $row['itemtype'],
            'event'           => $row['event'],
            'comment'         => $row['comment'],
            'is_recursive'    => $row['is_recursive'],
            'is_active'       => $row['is_active'],
            'updated_at'      => $row['date_mod'],
            'created_at'      => Toolbox::fixDate($row['date_creation']),
            'allow_response'  => Toolbox::fixDate($row['allow_response']),
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
