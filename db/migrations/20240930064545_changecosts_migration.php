<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class ChangecostsMigration extends AbstractMigration
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
    $item = $this->table('changecosts');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_changecosts');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'            => $row['id'],
            'change_id'     => $row['changes_id'],
            'name'          => $row['name'],
            'comment'       => $row['comment'],
            'begin_date'    => Toolbox::fixDate($row['begin_date']),
            'end_date'      => Toolbox::fixDate($row['end_date']),
            'actiontime'    => $row['actiontime'],
            'cost_time'     => $row['cost_time'],
            'cost_fixed'    => $row['cost_fixed'],
            'cost_material' => $row['cost_material'],
            'budget_id'     => $row['budgets_id'],
            'entity_id'     => ($row['entities_id'] + 1),
            'is_recursive'  => $row['is_recursive'],
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('changecosts_id_seq', (SELECT MAX(id) FROM changecosts)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
