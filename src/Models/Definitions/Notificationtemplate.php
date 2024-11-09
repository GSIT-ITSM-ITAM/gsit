<?php

namespace App\Models\Definitions;

class Notificationtemplate
{
  public static function getDefinition()
  {
    global $translator;
    return [
      [
        'id'    => 1,
        'title' => $translator->translate('Name'),
        'type'  => 'input',
        'name'  => 'name',
        'fillable' => true,
      ],
      [
        'id'    => 4,
        'title' => $translator->translatePlural('Type', 'Types', 1),
        'type'  => 'dropdown',
        'name'  => 'itemtype',
        'dbname'  => 'itemtype',
        'values' => self::getTypeArray(),
        'fillable' => true,
      ],
      [
        'id'    => 16,
        'title' => $translator->translate('Comments'),
        'type'  => 'textarea',
        'name'  => 'comment',
        'fillable' => true,
      ],
      [
        'id'    => 161,
        'title' => $translator->translate('CSS'),
        'type'  => 'textarea',
        'name'  => 'css',
        'fillable' => true,
      ],
      [
        'id'    => 19,
        'title' => $translator->translate('Last update'),
        'type'  => 'datetime',
        'name'  => 'updated_at',
        'readonly'  => 'readonly',
      ],
      [
        'id'    => 121,
        'title' => $translator->translate('Creation date'),
        'type'  => 'datetime',
        'name'  => 'created_at',
        'readonly'  => 'readonly',
      ],
    ];
  }

  public static function getTypeArray()
  {
    global $translator;

    $types = [];
    $types['CartridgeItem'] = $translator->translatePlural('Cartridge model', 'Cartridge models', 1);
    $types['Change'] = $translator->translatePlural('Change', 'Changes', 1);
    $types['ConsumableItem'] = $translator->translatePlural('Consumable model', 'Consumable models', 1);
    $types['Contract'] = $translator->translatePlural('Contract', 'Contracts', 1);
    $types['CronTask'] = $translator->translatePlural('Automatic action', 'Automatic actions', 1);
    $types['DBConnection'] = $translator->translatePlural('SQL replica', 'SQL replicas', 1);
    $types['FieldUnicity'] = $translator->translate('Fields unicity');
    $types['Infocom'] = $translator->translate('Financial and administrative information');
    $types['MailCollector'] = $translator->translatePlural('Receiver', 'Receivers', 1);
    $types['ObjectLock'] = $translator->translatePlural('Object Lock', 'Object Locks', 1);
    $types['PlanningRecall'] = $translator->translatePlural('Planning reminder', 'Planning reminders', 1);
    $types['Problem'] = $translator->translatePlural('Problem', 'Problems', 1);
    $types['Project'] = $translator->translatePlural('Project', 'Projects', 1);
    $types['ProjectTask'] = $translator->translatePlural('Project task', 'Project tasks', 1);
    $types['Reservation'] = $translator->translatePlural('Reservation', 'Reservations', 1);
    $types['SoftwareLicense'] = $translator->translatePlural('License', 'Licenses', 1);
    $types['Ticket'] = $translator->translatePlural('Ticket', 'Tickets', 1);
    $types['User'] = $translator->translatePlural('User', 'Users', 1);
    $types['SavedSearch_Alert'] = $translator->translatePlural('Saved search alert', 'Saved searches alerts', 1);
    $types['Certificate'] = $translator->translatePlural('Certificate', 'Certificates', 1);
    $types['Domain'] = $translator->translatePlural('Domain', 'Domains', 1);

    asort($types);

    $newTypes = [];
    foreach (array_keys($types) as $key)
    {
      $newTypes[$key]['title'] = $types[$key];
    }

    return $newTypes;
  }

  public static function getRelatedPages($rootUrl)
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Notification template', 'Notification templates', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Template translation', 'Template translations', 2),
        'icon' => 'language',
        'link' => $rootUrl . '/templatetranslation',
      ],
      [
        'title' => $translator->translatePlural('Notification', 'Notifications', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => '',
      ],
    ];
  }
}
