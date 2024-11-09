<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class CrontasklogsMigration extends AbstractMigration
{
  public function change(): void
  {
    return;
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
    $followups = $this->table('crontasklogs');

    if ($this->isMigratingUp())
    {
      $nbRows = $pdo->query('SELECT count(*) FROM glpi_crontasklogs')->fetchColumn();
      $nbLoops = ceil($nbRows / 100000);

      for ($i = 0; $i < $nbLoops; $i++)
      {
        $stmt = $pdo->query('SELECT * FROM glpi_crontasklogs ORDER BY id LIMIT 100000 OFFSET ' . ($i * 100000));
        $rows = $stmt->fetchAll();
        foreach ($rows as $row)
        {
          $data = [
            [
              'id'              => $row['id'],
              'crontask_id'     => $row['crontasks_id'],
              'crontasklog_id'  => $row['crontasklogs_id'],
              'date'            => Toolbox::fixDate($row['date']),
              'state'           => $row['state'],
              'elapsed'         => $row['elapsed'],
              'volume'          => $row['volume'],
              'content'         => $row['content'],
            ]
          ];

          $followups->insert($data)
                    ->saveData();
        }
      }
    } else {
      // rollback
      $followups->truncate();
    }
  }
}
