<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class ItilCategoriesMigration extends AbstractMigration
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
    $item = $this->table('categories');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_itilcategories');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                          => $row['id'],
            'entity_id'                   => ($row['entities_id'] + 1),
            'is_recursive'                => $row['is_recursive'],
            'category_id'                 => $row['itilcategories_id'],
            'name'                        => $row['name'],
            'completename'                => $row['completename'],
            'comment'                     => $row['comment'],
            'level'                       => $row['level'],
            'knowbaseitemcategory_id'     => $row['knowbaseitemcategories_id'],
            'user_id'                     => $row['users_id'],
            'group_id'                    => $row['groups_id'],
            'code'                        => $row['code'],
            'ancestors_cache'             => $row['ancestors_cache'],
            'sons_cache'                  => $row['sons_cache'],
            'is_helpdeskvisible'          => $row['is_helpdeskvisible'],
            'tickettemplate_id_incident'  => $row['tickettemplates_id_incident'],
            'tickettemplate_id_demand'    => $row['tickettemplates_id_demand'],
            'changetemplate_id'           => $row['changetemplates_id'],
            'problemtemplate_id'          => $row['problemtemplates_id'],
            'is_incident'                 => $row['is_incident'],
            'is_request'                  => $row['is_request'],
            'is_problem'                  => $row['is_problem'],
            'is_change'                   => $row['is_change'],
            'updated_at'                  => Toolbox::fixDate($row['date_mod']),
            'created_at'                  => Toolbox::fixDate($row['date_creation']),
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
