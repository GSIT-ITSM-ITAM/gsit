<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class IpaddressesMigration extends AbstractMigration
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
    $item = $this->table('ipaddresses');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_ipaddresses');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'            => $row['id'],
            'entity_id'     => ($row['entities_id'] + 1),
            'item_id'       => $row['items_id'],
            'item_type'     => 'App\\Models\\' . $row['itemtype'],
            'version'       => $row['version'],
            'name'          => $row['name'],
            'binary_0'      => $row['binary_0'],
            'binary_1'      => $row['binary_1'],
            'binary_2'      => $row['binary_2'],
            'binary_3'      => $row['binary_3'],
            'is_dynamic'    => $row['is_dynamic'],
            'mainitem_id'   => $row['mainitems_id'],
            'mainitem_type' => 'App\\Models\\' . $row['mainitemtype'],
            'deleted_at'    => self::convertIsDeleted($row['is_deleted']),
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('ipaddresses_id_seq', (SELECT MAX(id) FROM ipaddresses)+1)");
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
