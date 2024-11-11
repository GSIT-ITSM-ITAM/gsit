<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class CrontasksMigration extends AbstractMigration
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
    $item = $this->table('crontasks');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_crontasks');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'            => $row['id'],
            'item_type'     => 'App\\Models\\' . $row['itemtype'],
            'name'          => $row['name'],
            'frequency'     => $row['frequency'],
            'param'         => $row['param'],
            'state'         => $row['state'],
            'mode'          => $row['mode'],
            'allowmode'     => $row['allowmode'],
            'hourmin'       => $row['hourmin'],
            'hourmax'       => $row['hourmax'],
            'logs_lifetime' => $row['logs_lifetime'],
            'lastrun'       => $row['lastrun'],
            'lastcode'      => $row['lastcode'],
            'comment'       => $row['comment'],
            'updated_at'    => Toolbox::fixDate($row['date_mod']),
            'created_at'    => Toolbox::fixDate($row['date_creation']),
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('crontasks_id_seq', (SELECT MAX(id) FROM crontasks)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
