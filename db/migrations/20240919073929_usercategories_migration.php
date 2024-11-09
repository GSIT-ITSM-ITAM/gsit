<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class UsercategoriesMigration extends AbstractMigration
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
    $followups = $this->table('usercategories');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_usercategories'); // returns PDOStatement
      $rows = $stmt->fetchAll(); // returns the result as an array
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'            => $row['id'],
            'name'          => $row['name'],
            'comment'       => $row['comment'],
            'updated_at'    => Toolbox::fixDate($row['date_mod']),
            'created_at'    => Toolbox::fixDate($row['date_creation']),
          ]
        ];

        $followups->insert($data)
                  ->saveData();
      }
    } else {
      // rollback
      $followups->truncate();
    }
  }
}
