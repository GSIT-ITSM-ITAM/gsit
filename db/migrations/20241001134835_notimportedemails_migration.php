<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class NotimportedemailsMigration extends AbstractMigration
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
    $item = $this->table('notimportedemails');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_notimportedemails');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                => $row['id'],
            'from'              => $row['from'],
            'to'                => $row['to'],
            'mailcollector_id'  => $row['mailcollectors_id'],
            'date'              => Toolbox::fixDate($row['date']),
            'subject'           => $row['subject'],
            'messageid'         => $row['messageid'],
            'reason'            => $row['reason'],
            'user_id'           => $row['users_id'],
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('notimportedemails_id_seq', (SELECT MAX(id) FROM notimportedemails)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
