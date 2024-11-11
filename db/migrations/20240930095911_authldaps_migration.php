<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class AuthldapsMigration extends AbstractMigration
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
    $item = $this->table('authldaps');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_authldaps');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                        => $row['id'],
            'name'                      => $row['name'],
            'host'                      => $row['host'],
            'basedn'                    => $row['basedn'],
            'rootdn'                    => $row['rootdn'],
            'port'                      => $row['port'],
            'condition'                 => $row['condition'],
            'login_field'               => $row['login_field'],
            'sync_field'                => $row['sync_field'],
            'use_tls'                   => $row['use_tls'],
            'group_field'               => $row['group_field'],
            'group_condition'           => $row['group_condition'],
            'group_search_type'         => $row['group_search_type'],
            'group_member_field'        => $row['group_member_field'],
            'email1_field'              => $row['email1_field'],
            'realname_field'            => $row['realname_field'],
            'firstname_field'           => $row['firstname_field'],
            'phone_field'               => $row['phone_field'],
            'phone2_field'              => $row['phone2_field'],
            'mobile_field'              => $row['mobile_field'],
            'comment_field'             => $row['comment_field'],
            'use_dn'                    => $row['use_dn'],
            'time_offset'               => $row['time_offset'],
            'deref_option'              => $row['deref_option'],
            'title_field'               => $row['title_field'],
            'category_field'            => $row['category_field'],
            'language_field'            => $row['language_field'],
            'entity_field'              => $row['entity_field'],
            'entity_condition'          => $row['entity_condition'],
            'updated_at'                => Toolbox::fixDate($row['date_mod']),
            'comment'                   => $row['comment'],
            'is_default'                => $row['is_default'],
            'is_active'                 => $row['is_active'],
            'rootdn_passwd'             => $row['rootdn_passwd'],
            'registration_number_field' => $row['registration_number_field'],
            'email2_field'              => $row['email2_field'],
            'email3_field'              => $row['email3_field'],
            'email4_field'              => $row['email4_field'],
            'location_field'            => $row['location_field'],
            'responsible_field'         => $row['responsible_field'],
            'pagesize'                  => $row['pagesize'],
            'ldap_maxlimit'             => $row['ldap_maxlimit'],
            'can_support_pagesize'      => $row['can_support_pagesize'],
            'picture_field'             => $row['picture_field'],
            'created_at'                => Toolbox::fixDate($row['date_creation']),
            'inventory_domain'          => $row['inventory_domain'],
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('authldaps_id_seq', (SELECT MAX(id) FROM authldaps)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
