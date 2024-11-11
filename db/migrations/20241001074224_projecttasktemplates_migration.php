<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class ProjecttasktemplatesMigration extends AbstractMigration
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
    $item = $this->table('projecttasktemplates');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_projecttasktemplates');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                  => $row['id'],
            'entity_id'           => ($row['entities_id'] + 1),
            'is_recursive'        => $row['is_recursive'],
            'name'                => $row['name'],
            'description'         => $row['description'],
            'project_id'          => $row['projects_id'],
            'projecttask_id'      => $row['projecttasks_id'],
            'plan_start_date'     => Toolbox::fixDate($row['plan_start_date']),
            'plan_end_date'       => Toolbox::fixDate($row['plan_end_date']),
            'real_start_date'     => Toolbox::fixDate($row['real_start_date']),
            'real_end_date'       => Toolbox::fixDate($row['real_end_date']),
            'planned_duration'    => $row['planned_duration'],
            'effective_duration'  => $row['effective_duration'],
            'projectstate_id'     => $row['projectstates_id'],
            'projecttasktype_id'  => $row['projecttasktypes_id'],
            'user_id'             => $row['users_id'],
            'percent_done'        => $row['percent_done'],
            'is_milestone'        => $row['is_milestone'],
            'comment'             => $row['comments'],
            'updated_at'          => Toolbox::fixDate($row['date_mod']),
            'created_at'          => Toolbox::fixDate($row['date_creation']),
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('projecttasktemplates_id_seq', (SELECT MAX(id) FROM projecttasktemplates)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
