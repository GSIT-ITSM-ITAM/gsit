<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class ComputervirtualmachinesMigration extends AbstractMigration
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
    $item = $this->table('computervirtualmachines');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_computervirtualmachines');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                      => $row['id'],
            'entity_id'               => ($row['entities_id'] + 1),
            'computer_id'             => $row['computers_id'],
            'name'                    => $row['name'],
            'virtualmachinestate_id'  => $row['virtualmachinestates_id'],
            'virtualmachinesystem_id' => $row['virtualmachinesystems_id'],
            'virtualmachinetype_id'   => $row['virtualmachinetypes_id'],
            'uuid'                    => $row['uuid'],
            'vcpu'                    => $row['vcpu'],
            'ram'                     => $row['ram'],
            'is_dynamic'              => $row['is_dynamic'],
            'comment'                 => $row['comment'],
            'updated_at'              => Toolbox::fixDate($row['date_mod']),
            'created_at'              => Toolbox::fixDate($row['date_creation']),
            'deleted_at'              => self::convertIsDeleted($row['is_deleted']),
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
