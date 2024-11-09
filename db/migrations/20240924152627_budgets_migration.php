<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class BudgetsMigration extends AbstractMigration
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
    $item = $this->table('budgets');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_budgets');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'            => $row['id'],
            'name'          => $row['name'],
            'entity_id'     => ($row['entities_id'] + 1),
            'is_recursive'  => $row['is_recursive'],
            'comment'       => $row['comment'],
            'begin_date'    => Toolbox::fixDate($row['begin_date']),
            'end_date'      => Toolbox::fixDate($row['end_date']),
            'value'         => $row['value'],
            'is_template'   => $row['is_template'],
            'template_name' => $row['template_name'],
            'updated_at'    => Toolbox::fixDate($row['date_mod']),
            'created_at'    => Toolbox::fixDate($row['date_creation']),
            'location_id'   => $row['locations_id'],
            'budgettype_id' => $row['budgettypes_id'],
            'deleted_at'    => self::convertIsDeleted($row['is_deleted']),
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
