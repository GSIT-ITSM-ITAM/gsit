<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class EntitiesMigration extends AbstractMigration
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
    $item = $this->table('entities');

    if ($this->isMigratingUp())
    {
      $stmt = $pdo->query('SELECT * FROM glpi_entities');
      $rows = $stmt->fetchAll();
      foreach ($rows as $row)
      {
        $data = [
          [
            'id'                                      => ($row['id'] + 1),
            'name'                                    => $row['name'],
            'entity_id'                               => ($row['entities_id'] + 1),
            'completename'                            => $row['completename'],
            'comment'                                 => $row['comment'],
            'level'                                   => $row['level'],
            'sons_cache'                              => $row['sons_cache'],
            'ancestors_cache'                         => $row['ancestors_cache'],
            'address'                                 => $row['address'],
            'postcode'                                => $row['postcode'],
            'town'                                    => $row['town'],
            'state'                                   => $row['state'],
            'country'                                 => $row['country'],
            'website'                                 => $row['website'],
            'phonenumber'                             => $row['phonenumber'],
            'fax'                                     => $row['fax'],
            'email'                                   => $row['email'],
            'admin_email'                             => $row['admin_email'],
            'admin_email_name'                        => $row['admin_email_name'],
            'admin_reply'                             => $row['admin_reply'],
            'admin_reply_name'                        => $row['admin_reply_name'],
            'notification_subject_tag'                => $row['notification_subject_tag'],
            'ldap_dn'                                 => $row['ldap_dn'],
            'tag'                                     => $row['tag'],
            'authldap_id'                             => $row['authldaps_id'],
            'mail_domain'                             => $row['mail_domain'],
            'entity_ldapfilter'                       => $row['entity_ldapfilter'],
            'mailing_signature'                       => $row['mailing_signature'],
            'cartridges_alert_repeat'                 => $row['cartridges_alert_repeat'],
            'consumables_alert_repeat'                => $row['consumables_alert_repeat'],
            'use_licenses_alert'                      => $row['use_licenses_alert'],
            'send_licenses_alert_before_delay'        => $row['send_licenses_alert_before_delay'],
            'use_certificates_alert'                  => $row['use_certificates_alert'],
            'send_certificates_alert_before_delay'    => $row['send_certificates_alert_before_delay'],
            'use_contracts_alert'                     => $row['use_contracts_alert'],
            'send_contracts_alert_before_delay'       => $row['send_contracts_alert_before_delay'],
            'use_infocoms_alert'                      => $row['use_infocoms_alert'],
            'send_infocoms_alert_before_delay'        => $row['send_infocoms_alert_before_delay'],
            'use_reservations_alert'                  => $row['use_reservations_alert'],
            'use_domains_alert'                       => $row['use_domains_alert'],
            'send_domains_alert_close_expiries_delay' => $row['send_domains_alert_close_expiries_delay'],
            'send_domains_alert_expired_delay'        => $row['send_domains_alert_expired_delay'],
            'autoclose_delay'                         => $row['autoclose_delay'],
            'autopurge_delay'                         => $row['autopurge_delay'],
            'notclosed_delay'                         => $row['notclosed_delay'],
            'calendar_id'                             => $row['calendars_id'],
            'auto_assign_mode'                        => $row['auto_assign_mode'],
            'tickettype'                              => $row['tickettype'],
            'max_closedate'                           => Toolbox::fixDate($row['max_closedate']),
            'inquest_config'                          => $row['inquest_config'],
            'inquest_rate'                            => $row['inquest_rate'],
            'inquest_delay'                           => $row['inquest_delay'],
            'inquest_URL'                             => $row['inquest_URL'],
            'autofill_warranty_date'                  => Toolbox::fixDate($row['autofill_warranty_date']),
            'autofill_use_date'                       => Toolbox::fixDate($row['autofill_use_date']),
            'autofill_buy_date'                       => Toolbox::fixDate($row['autofill_buy_date']),
            'autofill_delivery_date'                  => Toolbox::fixDate($row['autofill_delivery_date']),
            'autofill_order_date'                     => Toolbox::fixDate($row['autofill_order_date']),
            'tickettemplate_id'                       => $row['tickettemplates_id'],
            'changetemplate_id'                       => $row['changetemplates_id'],
            'problemtemplate_id'                      => $row['problemtemplates_id'],
            'entity_id_software'                      => $row['entities_id_software'],
            'default_contract_alert'                  => $row['default_contract_alert'],
            'default_infocom_alert'                   => $row['default_infocom_alert'],
            'default_cartridges_alarm_threshold'      => $row['default_cartridges_alarm_threshold'],
            'default_consumables_alarm_threshold'     => $row['default_consumables_alarm_threshold'],
            'delay_send_emails'                       => $row['delay_send_emails'],
            'is_notif_enable_default'                 => $row['is_notif_enable_default'],
            'inquest_duration'                        => $row['inquest_duration'],
            'updated_at'                              => Toolbox::fixDate($row['date_mod']),
            'created_at'                              => Toolbox::fixDate($row['date_creation']),
            'autofill_decommission_date'              => Toolbox::fixDate($row['autofill_decommission_date']),
            'suppliers_as_private'                    => $row['suppliers_as_private'],
            'anonymize_support_agents'                => $row['anonymize_support_agents'],
            'enable_custom_css'                       => $row['enable_custom_css'],
            'custom_css_code'                         => $row['custom_css_code'],
            'latitude'                                => $row['latitude'],
            'longitude'                               => $row['longitude'],
            'altitude'                                => $row['altitude'],
          ]
        ];
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('entities_id_seq', (SELECT MAX(id) FROM entities)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
