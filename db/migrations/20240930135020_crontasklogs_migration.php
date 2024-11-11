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

      $chunkSize = 100000;
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $chunkSize = 8000;
      }
    } else {
      return;
    }
    $crontask = $this->table('crontasklogs');

    if ($this->isMigratingUp())
    {
      $nbRows = $pdo->query('SELECT count(*) FROM glpi_crontasklogs')->fetchColumn();
      $nbLoops = ceil($nbRows / $chunkSize);

      for ($i = 0; $i < $nbLoops; $i++)
      {
        $stmt = $pdo->query('SELECT * FROM glpi_crontasklogs ORDER BY id LIMIT ' . $chunkSize . ' OFFSET ' .
          ($i * $chunkSize));
        $rows = $stmt->fetchAll();
        $data = [];
        foreach ($rows as $row)
        {
          $data[] = [
            'id'              => $row['id'],
            'crontask_id'     => $row['crontasks_id'],
            'crontasklog_id'  => $row['crontasklogs_id'],
            'date'            => Toolbox::fixDate($row['date']),
            'state'           => $row['state'],
            'elapsed'         => $row['elapsed'],
            'volume'          => $row['volume'],
            'content'         => $row['content'],
          ];
        }
        $crontask->insert($data)
                  ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('crontasklogs_id_seq', (SELECT MAX(id) FROM crontasklogs)+1)");
      }
    } else {
      // rollback
      $crontask->truncate();
    }
  }
}
