<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class ItilsolutionsMigration extends AbstractMigration
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
    $item = $this->table('solutions');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_itilsolutions');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                  => $row['id'],
            'item_type'           => 'App\\Models\\' . $row['itemtype'],
            'item_id'             => $row['items_id'],
            'solutiontype_id'     => $row['solutiontypes_id'],
            'solutiontype_name'   => $row['solutiontype_name'],
            'content'             => $row['content'],
            'created_at'          => Toolbox::fixDate($row['date_creation']),
            'updated_at'          => Toolbox::fixDate($row['date_mod']),
            'date_approval'       => Toolbox::fixDate($row['date_approval']),
            'user_id'             => $row['users_id'],
            'user_name'           => $row['user_name'],
            'user_id_editor'      => $row['users_id_editor'],
            'user_id_approval'    => $row['users_id_approval'],
            'user_name_approval'  => $row['user_name_approval'],
            'status'              => $row['status'],
            'followup_id'         => $row['itilfollowups_id'],
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
