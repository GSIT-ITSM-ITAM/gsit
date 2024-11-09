<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class ApiclientsMigration extends AbstractMigration
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
    $item = $this->table('apiclients');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_apiclients');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                => $row['id'],
            'entity_id'         => ($row['entities_id'] + 1),
            'is_recursive'      => $row['is_recursive'],
            'name'              => $row['name'],
            'updated_at'        => Toolbox::fixDate($row['date_mod']),
            'is_active'         => $row['is_active'],
            'ipv4_range_start'  => (int) $row['ipv4_range_start'],
            'ipv4_range_end'    => (int) $row['ipv4_range_end'],
            'ipv6'              => $row['ipv6'],
            'app_token'         => $row['app_token'],
            'app_token_date'    => Toolbox::fixDate($row['app_token_date']),
            'dolog_method'      => $row['dolog_method'],
            'comment'           => $row['comment'],
            'created_at'        => Toolbox::fixDate($row['date_mod']),
          ]
        ];
        print_r($data);
        $item->insert($data)
             ->saveData();
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
