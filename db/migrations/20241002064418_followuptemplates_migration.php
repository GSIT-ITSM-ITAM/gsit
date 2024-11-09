<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class FollowuptemplatesMigration extends AbstractMigration
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
    $item = $this->table('followuptemplates');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_itilfollowuptemplates');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'              => $row['id'],
            'created_at'      => Toolbox::fixDate($row['date_creation']),
            'updated_at'      => Toolbox::fixDate($row['date_mod']),
            'entity_id'       => ($row['entities_id'] + 1),
            'is_recursive'    => $row['is_recursive'],
            'name'            => $row['name'],
            'content'         => $row['content'],
            'requesttype_id'  => $row['requesttypes_id'],
            'is_private'      => $row['is_private'],
            'comment'         => $row['comment'],
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
