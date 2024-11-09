<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class KnowbaseitemcategoriesMigration extends AbstractMigration
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
    $item = $this->table('knowbaseitemcategories');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_knowbaseitemcategories');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                      => $row['id'],
            'entity_id'               => ($row['entities_id'] + 1),
            'is_recursive'            => $row['is_recursive'],
            'knowbaseitemcategory_id' => $row['knowbaseitemcategories_id'],
            'name'                    => $row['name'],
            'completename'            => $row['completename'],
            'comment'                 => $row['comment'],
            'level'                   => $row['level'],
            'sons_cache'              => $row['sons_cache'],
            'ancestors_cache'         => $row['ancestors_cache'],
            'updated_at'              => Toolbox::fixDate($row['date_mod']),
            'created_at'              => Toolbox::fixDate($row['date_creation']),
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
