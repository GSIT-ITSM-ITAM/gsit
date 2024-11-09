<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class RequesttypesMigration extends AbstractMigration
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
    $item = $this->table('requesttypes');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_requesttypes');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                      => $row['id'],
            'name'                    => $row['name'],
            'is_helpdesk_default'     => $row['is_helpdesk_default'],
            'is_followup_default'     => $row['is_followup_default'],
            'is_mail_default'         => $row['is_mail_default'],
            'is_mailfollowup_default' => $row['is_mailfollowup_default'],
            'is_active'               => $row['is_active'],
            'is_ticketheader'         => $row['is_ticketheader'],
            'is_itilfollowup'         => $row['is_itilfollowup'],
            'comment'                 => $row['comment'],
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
