<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class LogsMigration extends AbstractMigration
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
    $item = $this->table('logs');

    if ($this->isMigratingUp())
    {
      $nbRows = $pdo->query('SELECT count(*) FROM glpi_logs')->fetchColumn();
      $nbLoops = ceil($nbRows / 5900);

      for ($i = 0; $i < $nbLoops; $i++)
      {
        $stmt = $pdo->query('SELECT * FROM glpi_logs ORDER BY id LIMIT 5900 OFFSET ' . ($i * 5900));
        $rows = $stmt->fetchAll();
        $data = [];
        foreach ($rows as $row)
        {
          $item_id = $row['items_id'];
          if ($row['itemtype'] == 'Entity')
          {
            $item_id += 1;
          }

          $data[] = [
            'id'                => $row['id'],
            'item_type'         => 'App\\v1\\Models\\' . $row['itemtype'],
            'item_id'           => $item_id,
            'itemtype_link'     => $row['itemtype_link'],
            'linked_action'     => $row['linked_action'],
            'user_name'         => $row['user_name'],
            'updated_at'        => Toolbox::fixDate($row['date_mod']),
            'id_search_option'  => $row['id_search_option'],
            'old_value'         => $row['old_value'],
            'new_value'         => $row['new_value'],
            'created_at'        => Toolbox::fixDate($row['date_mod']),
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
