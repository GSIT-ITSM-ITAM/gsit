<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class EventsMigration extends AbstractMigration
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
    $item = $this->table('events');

    if ($this->isMigratingUp())
    {
      $nbRows = $pdo->query('SELECT count(*) FROM glpi_events')->fetchColumn();
      $nbLoops = ceil($nbRows / 9200);

      for ($i = 0; $i < $nbLoops; $i++)
      {
        $stmt = $pdo->query('SELECT * FROM glpi_events ORDER BY id LIMIT 9200 OFFSET ' . ($i * 9200));

        $rows = $stmt->fetchAll();
        $data = [];
        foreach ($rows as $row)
        {
          $data[] = [
            'id'      => $row['id'],
            'item_id' => $row['items_id'],
            'type'    => $row['type'],
            'date'    => Toolbox::fixDate($row['date']),
            'service' => $row['service'],
            'level'   => $row['level'],
            'message' => $row['message'],
          ];
        }
        $item->insert($data)
             ->saveData();
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
