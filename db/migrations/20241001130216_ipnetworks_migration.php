<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class IpnetworksMigration extends AbstractMigration
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
    $item = $this->table('ipnetworks');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_ipnetworks');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'              => $row['id'],
            'entity_id'       => ($row['entities_id'] + 1),
            'is_recursive'    => $row['is_recursive'],
            'ipnetwork_id'    => $row['ipnetworks_id'],
            'completename'    => $row['completename'],
            'level'           => $row['level'],
            'ancestors_cache' => $row['ancestors_cache'],
            'sons_cache'      => $row['sons_cache'],
            'addressable'     => $row['addressable'],
            'version'         => $row['version'],
            'name'            => $row['name'],
            'address'         => $row['address'],
            'address_0'       => $row['address_0'],
            'address_1'       => $row['address_1'],
            'address_2'       => $row['address_2'],
            'address_3'       => $row['address_3'],
            'netmask'         => $row['netmask'],
            'netmask_0'       => $row['netmask_0'],
            'netmask_1'       => $row['netmask_1'],
            'netmask_2'       => $row['netmask_2'],
            'netmask_3'       => $row['netmask_3'],
            'gateway'         => $row['gateway'],
            'gateway_0'       => $row['gateway_0'],
            'gateway_1'       => $row['gateway_1'],
            'gateway_2'       => $row['gateway_2'],
            'gateway_3'       => $row['gateway_3'],
            'comment'         => $row['comment'],
            'updated_at'      => Toolbox::fixDate($row['date_mod']),
            'created_at'      => Toolbox::fixDate($row['date_creation']),
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('ipnetworks_id_seq', (SELECT MAX(id) FROM ipnetworks)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
