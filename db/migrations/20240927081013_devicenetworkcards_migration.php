<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class DevicenetworkcardsMigration extends AbstractMigration
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
    $item = $this->table('devicenetworkcards');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_devicenetworkcards');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        if (is_null($row['manufacturers_id']))
        {
          $row['manufacturers_id'] = 0;
        }
        if (is_null($row['entities_id']))
        {
          $row['entities_id'] = 0;
        }
        if (is_null($row['is_recursive']))
        {
          $row['is_recursive'] = 0;
        }
        $data = [
          [
            'id'                        => $row['id'],
            'name'                      => $row['designation'],
            'bandwidth'                 => $row['bandwidth'],
            'comment'                   => $row['comment'],
            'manufacturer_id'           => $row['manufacturers_id'],
            'mac_default'               => $row['mac_default'],
            'entity_id'                 => ($row['entities_id'] + 1),
            'is_recursive'              => $row['is_recursive'],
            'devicenetworkcardmodel_id' => $row['devicenetworkcardmodels_id'],
            'updated_at'                => Toolbox::fixDate($row['date_mod']),
            'created_at'                => Toolbox::fixDate($row['date_creation']),
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('devicenetworkcards_id_seq', (SELECT MAX(id) FROM devicenetworkcards)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
