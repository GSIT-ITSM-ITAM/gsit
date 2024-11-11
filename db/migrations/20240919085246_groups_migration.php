<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class GroupsMigration extends AbstractMigration
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
    $item = $this->table('groups');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_groups');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'              => $row['id'],
            'entity_id'       => ($row['entities_id'] + 1),
            'is_recursive'    => $row['is_recursive'],
            'name'            => $row['name'],
            'comment'         => $row['comment'],
            'ldap_field'      => $row['ldap_field'],
            'ldap_value'      => $row['ldap_value'],
            'ldap_group_dn'   => $row['ldap_group_dn'],
            'updated_at'      => Toolbox::fixDate($row['date_mod']),
            'group_id'        => $row['groups_id'],
            'completename'    => $row['completename'],
            'level'           => $row['level'],
            'ancestors_cache' => $row['ancestors_cache'],
            'sons_cache'      => $row['sons_cache'],
            'is_requester'    => $row['is_requester'],
            'is_watcher'      => $row['is_watcher'],
            'is_assign'       => $row['is_assign'],
            'is_task'         => $row['is_task'],
            'is_notify'       => $row['is_notify'],
            'is_itemgroup'    => $row['is_itemgroup'],
            'is_usergroup'    => $row['is_usergroup'],
            'is_manager'      => $row['is_manager'],
            'created_at'      => Toolbox::fixDate($row['date_creation']),
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('groups_id_seq', (SELECT MAX(id) FROM groups)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
