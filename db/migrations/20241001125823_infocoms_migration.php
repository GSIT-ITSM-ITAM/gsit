<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class InfocomsMigration extends AbstractMigration
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
    $item = $this->table('infocoms');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_infocoms');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                    => $row['id'],
            'item_id'               => $row['items_id'],
            'item_type'             => 'App\\Models\\' . $row['itemtype'],
            'entity_id'             => ($row['entities_id'] + 1),
            'is_recursive'          => $row['is_recursive'],
            'buy_date'              => $row['buy_date'],
            'use_date'              => $row['use_date'],
            'warranty_duration'     => $row['warranty_duration'],
            'warranty_info'         => $row['warranty_info'],
            'supplier_id'           => $row['suppliers_id'],
            'order_number'          => $row['order_number'],
            'delivery_number'       => $row['delivery_number'],
            'immo_number'           => $row['immo_number'],
            'value'                 => $row['value'],
            'warranty_value'        => $row['warranty_value'],
            'sink_time'             => $row['sink_time'],
            'sink_type'             => $row['sink_type'],
            'sink_coeff'            => $row['sink_coeff'],
            'comment'               => $row['comment'],
            'bill'                  => $row['bill'],
            'budget_id'             => $row['budgets_id'],
            'alert'                 => $row['alert'],
            'order_date'            => $row['order_date'],
            'delivery_date'         => $row['delivery_date'],
            'inventory_date'        => $row['inventory_date'],
            'warranty_date'         => $row['warranty_date'],
            'updated_at'            => Toolbox::fixDate($row['date_mod']),
            'created_at'            => Toolbox::fixDate($row['date_creation']),
            'decommission_date'     => $row['decommission_date'],
            'businesscriticity_id'  => $row['businesscriticities_id'],
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
