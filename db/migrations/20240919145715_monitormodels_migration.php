<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class MonitormodelsMigration extends AbstractMigration
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
    $item = $this->table('monitormodels');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_monitormodels');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                => $row['id'],
            'name'              => $row['name'],
            'comment'           => $row['comment'],
            'product_number'    => $row['product_number'],
            'weight'            => $row['weight'],
            'required_units'    => $row['required_units'],
            'depth'             => $row['depth'],
            'power_connections' => $row['power_connections'],
            'power_consumption' => $row['power_consumption'],
            'is_half_rack'      => $row['is_half_rack'],
            'picture_front'     => $row['picture_front'],
            'picture_rear'      => $row['picture_rear'],
            'updated_at'        => Toolbox::fixDate($row['date_mod']),
            'created_at'        => Toolbox::fixDate($row['date_creation']),
          ]
        ];

        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('monitormodels_id_seq', (SELECT MAX(id) FROM monitormodels)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
