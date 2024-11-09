<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class PlanningexternaleventsMigration extends AbstractMigration
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
    $item = $this->table('planningexternalevents');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_planningexternalevents');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                                => $row['id'],
            'uuid'                              => $row['uuid'],
            'planningexternaleventtemplate_id'  => $row['planningexternaleventtemplates_id'],
            'entity_id'                         => ($row['entities_id'] + 1),
            'is_recursive'                      => $row['is_recursive'],
            'date'                              => Toolbox::fixDate($row['date']),
            'user_id'                           => $row['users_id'],
            'user_id_guests'                    => $row['users_id_guests'],
            'group_id'                          => $row['groups_id'],
            'name'                              => $row['name'],
            'text'                              => $row['text'],
            'begin'                             => $row['begin'],
            'end'                               => $row['end'],
            'rrule'                             => $row['rrule'],
            'state'                             => $row['state'],
            'planningeventcategory_id'          => $row['planningeventcategories_id'],
            'background'                        => $row['background'],
            'updated_at'                        => Toolbox::fixDate($row['date_mod']),
            'created_at'                        => Toolbox::fixDate($row['date_creation']),
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
