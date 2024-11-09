<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class ProblemsMigration extends AbstractMigration
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
    $item = $this->table('problems');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_problems');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                  => $row['id'],
            'name'                => $row['name'],
            'entity_id'           => ($row['entities_id'] + 1),
            'is_recursive'        => $row['is_recursive'],
            'status'              => $row['status'],
            'content'             => Toolbox::convertHtmlToMarkdown($row['content']),
            'updated_at'          => Toolbox::fixDate($row['date_mod']),
            'date'                => Toolbox::fixDate($row['date']),
            'solvedate'           => Toolbox::fixDate($row['solvedate']),
            'closedate'           => Toolbox::fixDate($row['closedate']),
            'time_to_resolve'     => Toolbox::fixDate($row['time_to_resolve']),
            'user_id_recipient'   => $row['users_id_recipient'],
            'user_id_lastupdater' => $row['users_id_lastupdater'],
            'urgency'             => $row['urgency'],
            'impact'              => $row['impact'],
            'priority'            => $row['priority'],
            'category_id'         => $row['itilcategories_id'],
            'impactcontent'       => $row['impactcontent'],
            'causecontent'        => $row['causecontent'],
            'symptomcontent'      => $row['symptomcontent'],
            'actiontime'          => $row['actiontime'],
            'begin_waiting_date'  => Toolbox::fixDate($row['begin_waiting_date']),
            'waiting_duration'    => $row['waiting_duration'],
            'close_delay_stat'    => $row['close_delay_stat'],
            'solve_delay_stat'    => $row['solve_delay_stat'],
            'created_at'          => Toolbox::fixDate($row['date_creation']),
            'deleted_at'          => self::convertIsDeleted($row['is_deleted']),
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

  public function convertIsDeleted($is_deleted)
  {
    if ($is_deleted == 1)
    {
      return date('Y-m-d H:i:s', time());
    }

    return null;
  }
}
