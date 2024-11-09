<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class KnowbaseitemsMigration extends AbstractMigration
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
    $item = $this->table('knowbaseitems');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_knowbaseitems');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                      => $row['id'],
            'knowbaseitemcategory_id' => $row['knowbaseitemcategories_id'],
            'name'                    => $row['name'],
            'answer'                  => $row['answer'],
            'is_faq'                  => $row['is_faq'],
            'user_id'                 => $row['users_id'],
            'view'                    => $row['view'],
            'date'                    => Toolbox::fixDate($row['date']),
            'updated_at'              => Toolbox::fixDate($row['date_mod']),
            'begin_date'              => Toolbox::fixDate($row['begin_date']),
            'end_date'                => Toolbox::fixDate($row['end_date']),
            'created_at'              => Toolbox::fixDate($row['date_mod']),
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
