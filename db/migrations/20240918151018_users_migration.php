<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class UsersMigration extends AbstractMigration
{
  public function change(): void
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
    $users = $this->table('users');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_users');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                              => $row['id'],
            'name'                            => $row['name'],
            'password'                        => $row['password'],
            'password_last_update'            => $row['password_last_update'],
            'phone'                           => $row['phone'],
            'phone2'                          => $row['phone2'],
            'mobile'                          => $row['mobile'],
            'lastname'                        => $row['realname'],
            'firstname'                       => $row['firstname'],
            'location_id'                     => $row['locations_id'],
            'language'                        => $row['language'],
            'use_mode'                        => $row['use_mode'],
            'list_limit'                      => $row['list_limit'],
            'is_active'                       => $row['is_active'],
            'comment'                         => $row['comment'],
            'auth_id'                         => $row['auths_id'],
            'authtype'                        => $row['authtype'],
            'last_login'                      => Toolbox::fixDate($row['last_login']),
            'updated_at'                      => Toolbox::fixDate($row['date_mod']),
            'synchronized_at'                 => Toolbox::fixDate($row['date_sync']),
            'profile_id'                      => $row['profiles_id'],
            'entity_id'                       => ($row['entities_id'] + 1),
            'usertitle_id'                    => $row['usertitles_id'],
            'usercategory_id'                 => $row['usercategories_id'],
            'date_format'                     => $row['date_format'],
            'number_format'                   => $row['number_format'],
            'names_format'                    => $row['names_format'],
            'csv_delimiter'                   => $row['csv_delimiter'],
            'is_ids_visible'                  => $row['is_ids_visible'],
            'use_flat_dropdowntree'           => $row['use_flat_dropdowntree'],
            'show_jobs_at_login'              => $row['show_jobs_at_login'],
            'priority_1'                      => $row['priority_1'],
            'priority_2'                      => $row['priority_2'],
            'priority_3'                      => $row['priority_3'],
            'priority_4'                      => $row['priority_4'],
            'priority_5'                      => $row['priority_5'],
            'priority_6'                      => $row['priority_6'],
            'followup_private'                => $row['followup_private'],
            'task_private'                    => $row['task_private'],
            'default_requesttype_id'          => $row['default_requesttypes_id'],
            'password_forget_token'           => $row['password_forget_token'],
            'password_forget_token_date'      => Toolbox::fixDate($row['password_forget_token_date']),
            'user_dn'                         => $row['user_dn'],
            'registration_number'             => $row['registration_number'],
            'show_count_on_tabs'              => $row['show_count_on_tabs'],
            'refresh_views'                   => $row['refresh_views'],
            'set_default_tech'                => $row['set_default_tech'],
            'personal_token'                  => $row['personal_token'],
            'personal_token_date'             => Toolbox::fixDate($row['personal_token_date']),
            'api_token'                       => $row['api_token'],
            'api_token_date'                  => Toolbox::fixDate($row['api_token_date']),
            'cookie_token'                    => $row['cookie_token'],
            'cookie_token_date'               => Toolbox::fixDate($row['cookie_token_date']),
            'display_count_on_home'           => $row['display_count_on_home'],
            'notification_to_myself'          => $row['notification_to_myself'],
            'duedateok_color'                 => $row['duedateok_color'],
            'duedatewarning_color'            => $row['duedatewarning_color'],
            'duedatecritical_color'           => $row['duedatecritical_color'],
            'duedatewarning_less'             => $row['duedatewarning_less'],
            'duedatecritical_less'            => $row['duedatecritical_less'],
            'duedatewarning_unit'             => $row['duedatewarning_unit'],
            'duedatecritical_unit'            => $row['duedatecritical_unit'],
            'display_options'                 => $row['display_options'],
            'is_deleted_ldap'                 => $row['is_deleted_ldap'],
            'pdffont'                         => $row['pdffont'],
            'picture'                         => $row['picture'],
            'begin_date'                      => Toolbox::fixDate($row['begin_date']),
            'end_date'                        => Toolbox::fixDate($row['end_date']),
            'keep_devices_when_purging_item'  => $row['keep_devices_when_purging_item'],
            'privatebookmarkorder'            => $row['privatebookmarkorder'],
            'backcreated'                     => $row['backcreated'],
            'task_state'                      => $row['task_state'],
            'layout'                          => $row['layout'],
            'palette'                         => $row['palette'],
            'set_default_requester'           => $row['set_default_requester'],
            'lock_autolock_mode'              => $row['lock_autolock_mode'],
            'lock_directunlock_notification'  => $row['lock_directunlock_notification'],
            'created_at'                      => $row['date_creation'],
            'highcontrast_css'                => $row['highcontrast_css'],
            'plannings'                       => $row['plannings'],
            'sync_field'                      => $row['sync_field'],
            'group_id'                        => $row['groups_id'],
            'user_id_supervisor'              => $row['users_id_supervisor'],
            'timezone'                        => $row['timezone'],
            'default_dashboard_central'       => $row['default_dashboard_central'] ?? null,
            'default_dashboard_assets'        => $row['default_dashboard_assets'] ?? null,
            'default_dashboard_helpdesk'      => $row['default_dashboard_helpdesk'] ?? null,
            'default_dashboard_mini_ticket'   => $row['default_dashboard_mini_ticket'] ?? null,
            'deleted_at'                      => self::convertIsDeleted($row['is_deleted']),
          ]
        ];

        $users->insert($data)
              ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('users_id_seq', (SELECT MAX(id) FROM users)+1)");
      }
    } else {
      // rollback
      $users->truncate();
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
