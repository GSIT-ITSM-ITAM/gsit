<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class SoftwarelicensetypesMigration extends AbstractMigration
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
    $item = $this->table('softwarelicensetypes');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_softwarelicensetypes');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                      => $row['id'],
            'name'                    => $row['name'],
            'comment'                 => $row['comment'],
            'updated_at'              => Toolbox::fixDate($row['date_mod']),
            'created_at'              => Toolbox::fixDate($row['date_creation']),
            'softwarelicensetype_id'  => $row['softwarelicensetypes_id'],
            'level'                   => $row['level'],
            'ancestors_cache'         => $row['ancestors_cache'],
            'sons_cache'              => $row['sons_cache'],
            'entity_id'               => ($row['entities_id'] + 1),
            'is_recursive'            => $row['is_recursive'],
            'completename'            => $row['completename'],
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
