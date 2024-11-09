<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class FollowupsMigration extends AbstractMigration
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
    $followups = $this->table('followups');

    if ($this->isMigratingUp())
    {
      $nbRows = $pdo->query('SELECT count(*) FROM glpi_itilfollowups')->fetchColumn();
      $nbLoops = ceil($nbRows / 4000);

      for ($i = 0; $i < $nbLoops; $i++)
      {
        $stmt = $pdo->query('SELECT * FROM glpi_itilfollowups ORDER BY id LIMIT 4000 OFFSET ' . ($i * 4000));

        $rows = $stmt->fetchAll();
        $data = [];
        foreach ($rows as $row)
        {
          $data[] = [
            'item_type'         => 'App\\Models\\' . $row['itemtype'],
            'item_id'           => $row['items_id'],
            'date'              => Toolbox::fixDate($row['date']),
            'user_id'           => $row['users_id'],
            'user_id_editor'    => $row['users_id_editor'],
            'content'           => Toolbox::convertHtmlToMarkdown($row['content']),
            'is_private'        => $row['is_private'],
            'requesttype_id'    => $row['requesttypes_id'],
            'updated_at'        => Toolbox::fixDate($row['date_mod']),
            'created_at'        => Toolbox::fixDate($row['date_creation']),
            'timeline_position' => $row['timeline_position'],
            'sourceitem_id'     => $row['sourceitems_id'],
            'sourceof_item_id'  => $row['sourceof_items_id'],
          ];
        }
        $followups->insert($data)
                  ->saveData();
      }

      // get ticket tasks and move them into followups
      $nbRows = $pdo->query('SELECT count(*) FROM glpi_tickettasks')->fetchColumn();
      $nbLoops = ceil($nbRows / 4000);

      for ($i = 0; $i < $nbLoops; $i++)
      {
        $stmt = $pdo->query('SELECT * FROM glpi_tickettasks ORDER BY id LIMIT 4000 OFFSET ' . ($i * 4000));

        $rows = $stmt->fetchAll();
        $data = [];
        foreach ($rows as $row)
        {
          $data[] = [
            'item_type'         => 'App\\Models\\Ticket',
            'item_id'           => $row['tickets_id'],
            'date'              => $row['date'],
            'user_id'           => $row['users_id'],
            'user_id_editor'    => $row['users_id_editor'],
            'content'           => Toolbox::convertHtmlToMarkdown($row['content']),
            'is_private'        => $row['is_private'],
            'requesttype_id'    => 0,
            'updated_at'        => $row['date_mod'],
            'created_at'        => $row['date_creation'],
            'timeline_position' => $row['timeline_position'],
            'sourceitem_id'     => 0,
            'sourceof_item_id'  => 0,
            'is_tech'           => true,
          ];

          // id: 6
          // uuid: c1a073b8-bbc2-41fe-b970-deeb985eb962
          // tickets_id: 51
          // taskcategories_id: 0
          // date: 2023-03-16 15:06:37
          // users_id: 2
          // users_id_editor: 0
          // content: &lt;p&gt;blabla&lt;/p&gt;
          // is_private: 0
          // actiontime: 0
          // begin: NULL
          // end: NULL
          // state: 1
          // users_id_tech: 4
          // groups_id_tech: 0
          // date_mod: 2023-03-16 15:06:37
          // date_creation: 2023-03-16 15:06:37
          // tasktemplates_id: 1
          // timeline_position: 1
          // sourceitems_id: 0
        }
        $followups->insert($data)
                  ->saveData();
      }

      // get change tasks and move them into followups
      $nbRows = $pdo->query('SELECT count(*) FROM glpi_changetasks')->fetchColumn();
      $nbLoops = ceil($nbRows / 4000);

      for ($i = 0; $i < $nbLoops; $i++)
      {
        $stmt = $pdo->query('SELECT * FROM glpi_changetasks ORDER BY id LIMIT 4000 OFFSET ' . ($i * 4000));

        $rows = $stmt->fetchAll();
        $data = [];
        foreach ($rows as $row)
        {
          $data[] = [
            'item_type'         => 'App\\Models\\Change',
            'item_id'           => $row['changes_id'],
            'date'              => $row['date'],
            'user_id'           => $row['users_id'],
            'user_id_editor'    => $row['users_id_editor'],
            'content'           => Toolbox::convertHtmlToMarkdown($row['content']),
            'is_private'        => $row['is_private'],
            'requesttype_id'    => 0,
            'updated_at'        => $row['date_mod'],
            'created_at'        => $row['date_creation'],
            'timeline_position' => $row['timeline_position'],
            'sourceitem_id'     => 0,
            'sourceof_item_id'  => 0,
          ];

          // id: 6
          // `uuid` varchar(255) DEFAULT NULL,
          // `changes_id` int(11) NOT NULL DEFAULT 0,
          // `taskcategories_id` int(11) NOT NULL DEFAULT 0,
          // `state` int(11) NOT NULL DEFAULT 0,
          // `date` timestamp NULL DEFAULT NULL,
          // `begin` timestamp NULL DEFAULT NULL,
          // `end` timestamp NULL DEFAULT NULL,
          // `users_id` int(11) NOT NULL DEFAULT 0,
          // `users_id_editor` int(11) NOT NULL DEFAULT 0,
          // `users_id_tech` int(11) NOT NULL DEFAULT 0,
          // `groups_id_tech` int(11) NOT NULL DEFAULT 0,
          // `content` longtext DEFAULT NULL,
          // `actiontime` int(11) NOT NULL DEFAULT 0,
          // `date_mod` timestamp NULL DEFAULT NULL,
          // `date_creation` timestamp NULL DEFAULT NULL,
          // `tasktemplates_id` int(11) NOT NULL DEFAULT 0,
          // `timeline_position` tinyint(1) NOT NULL DEFAULT 0,
          // `is_private` tinyint(1) NOT NULL DEFAULT 0,
        }
        $followups->insert($data)
                  ->saveData();
      }

      // get problem tasks and move them into followups
      $nbRows = $pdo->query('SELECT count(*) FROM glpi_problemtasks')->fetchColumn();
      $nbLoops = ceil($nbRows / 4000);

      for ($i = 0; $i < $nbLoops; $i++)
      {
        $stmt = $pdo->query('SELECT * FROM glpi_problemtasks ORDER BY id LIMIT 4000 OFFSET ' . ($i * 4000));

        $rows = $stmt->fetchAll();
        $data = [];
        foreach ($rows as $row)
        {
          $data[] = [
            'item_type'         => 'App\\Models\\Problem',
            'item_id'           => $row['problems_id'],
            'date'              => $row['date'],
            'user_id'           => $row['users_id'],
            'user_id_editor'    => $row['users_id_editor'],
            'content'           => Toolbox::convertHtmlToMarkdown($row['content']),
            'is_private'        => $row['is_private'],
            'requesttype_id'    => 0,
            'updated_at'        => $row['date_mod'],
            'created_at'        => $row['date_creation'],
            'timeline_position' => $row['timeline_position'],
            'sourceitem_id'     => 0,
            'sourceof_item_id'  => 0,
          ];

          // id: 6
          // `uuid` varchar(255) DEFAULT NULL,
          // `problems_id` int(11) NOT NULL DEFAULT 0,
          // `taskcategories_id` int(11) NOT NULL DEFAULT 0,
          // `date` timestamp NULL DEFAULT NULL,
          // `begin` timestamp NULL DEFAULT NULL,
          // `end` timestamp NULL DEFAULT NULL,
          // `users_id` int(11) NOT NULL DEFAULT 0,
          // `users_id_editor` int(11) NOT NULL DEFAULT 0,
          // `users_id_tech` int(11) NOT NULL DEFAULT 0,
          // `groups_id_tech` int(11) NOT NULL DEFAULT 0,
          // `content` longtext DEFAULT NULL,
          // `actiontime` int(11) NOT NULL DEFAULT 0,
          // `state` int(11) NOT NULL DEFAULT 0,
          // `date_mod` timestamp NULL DEFAULT NULL,
          // `date_creation` timestamp NULL DEFAULT NULL,
          // `tasktemplates_id` int(11) NOT NULL DEFAULT 0,
          // `timeline_position` tinyint(1) NOT NULL DEFAULT 0,
          // `is_private` tinyint(1) NOT NULL DEFAULT 0,
        }
        $followups->insert($data)
                  ->saveData();
      }
    } else {
      // rollback
      $followups->truncate();
    }
  }
}
