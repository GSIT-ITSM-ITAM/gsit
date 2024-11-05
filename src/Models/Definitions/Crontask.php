<?php

namespace App\Models\Definitions;

class Crontask
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
        'readonly'  => 'readonly',
      ],
      [
        'id'    => 6,
        'title' => $translator->translate('Run frequency'),
        'type'  => 'dropdown',
        'name'  => 'frequency',
        'dbname'  => 'frequency',
        'values' => self::getFrequencyArray(),
        'fillable' => true,
      ],
      [
        'id'    => 4,
        'title' => $translator->translate('Status'),
        'type'  => 'dropdown',
        'name'  => 'state',
        'dbname'  => 'state',
        'values' => self::getStateArray(),
        'fillable' => true,
      ],
      [
        'id'    => 5,
        'title' => $translator->translate('Run mode'),
        'type'  => 'dropdown',
        'name'  => 'mode',
        'dbname'  => 'mode',
        'values' => self::getModeArray(),
        'fillable' => true,
      ],
      [
        'id'    => 17,
        'title' => $translator->translate('Begin hour of run period'),
        'type'  => 'dropdown',
        'name'  => 'hourmin',
        'dbname'  => 'hourmin',
        'values' => self::getNumberArray(0, 24),
        'fillable' => true,
      ],
      [
        'id'    => 18,
        'title' => $translator->translate('End hour of run period'),
        'type'  => 'dropdown',
        'name'  => 'hourmax',
        'dbname'  => 'hourmax',
        'values' => self::getNumberArray(0, 24),
        'fillable' => true,
      ],
      [
        'id'    => 19,
        'title' => $translator->translate('Number of days this action logs are stored'),
        'type'  => 'dropdown',
        'name'  => 'logs_lifetime',
        'dbname'  => 'logs_lifetime',
        'values' => self::getNumberArray(10, 360, 10, [0 => $translator->translate('Infinite')]),
        'fillable' => true,
      ],
      [
        'id'    => 7,
        'title' => $translator->translate('Last run'),
        'type'  => 'datetime',
        'name'  => 'lastrun',
        'readonly'  => 'readonly',
      ],
      [
        'id'    => 16,
        'title' => $translator->translate('Comments'),
        'type'  => 'textarea',
        'name'  => 'comment',
        'fillable' => true,
      ],
      [
        'id'    => 20,
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

      /*
      $tab[] = [
        'id'                 => '3',
        'table'              => $this->getTable(),
        'field'              => 'description',
        'name'               => __('Description'),
        'nosearch'           => true,
        'nosort'             => true,
        'massiveaction'      => false,
        'datatype'           => 'text',
        'computation'        => $DB->quoteName('TABLE.id') // Virtual data
      ];

      $tab[] = [
        'id'                 => '8',
        'table'              => $this->getTable(),
        'field'              => 'itemtype',
        'name'               => __('Item type'),
        'massiveaction'      => false,
        'datatype'           => 'itemtypename',
        'types'              => self::getUsedItemtypes()
      ];
      */
    ];
  }

  public static function getFrequencyArray()
  {
    global $translator;

    $MINUTE_TIMESTAMP = 60;
    $HOUR_TIMESTAMP = 3600;
    $DAY_TIMESTAMP = 86400;
    $WEEK_TIMESTAMP = 604800;
    $MONTH_TIMESTAMP = 2592000;

    $tab = [];

    $tab[$MINUTE_TIMESTAMP] = sprintf($translator->translate('%d minute', '%d minutes', 1), 1);

    // Minutes
    for ($i = 5; $i < 60; $i += 5)
    {
      $tab[$i * $MINUTE_TIMESTAMP] = sprintf($translator->translatePlural('%d minute', '%d minutes', $i), $i);
    }

    // Heures
    for ($i = 1; $i < 24; $i++)
    {
      $tab[$i * $HOUR_TIMESTAMP] = sprintf($translator->translatePlural('%d hour', '%d hours', $i), $i);
    }

    // Jours
    $tab[$DAY_TIMESTAMP] = $translator->translate('Each day');
    for ($i = 2; $i < 7; $i++)
    {
      $tab[$i * $DAY_TIMESTAMP] = sprintf($translator->translatePlural('%d day', '%d days', $i), $i);
    }

    $tab[$WEEK_TIMESTAMP]  = $translator->translate('Each week');
    $tab[$MONTH_TIMESTAMP] = $translator->translate('Each month');

    $newTab = [];
    foreach (array_keys($tab) as $key)
    {
      $newTab[$key]['title'] = $tab[$key];
    }

    return $newTab;
  }

  public static function getStateArray()
  {
    global $translator;
    return [
      0 => [
        'title' => $translator->translate('Disabled'),
      ],
      1 => [
        'title' => $translator->translate('Scheduled'),
      ],
      2 => [
        'title' => $translator->translate('Running'),
      ],
    ];
  }

  public static function getModeArray()
  {
    global $translator;
    return [
      1 => [
        'title' => $translator->translate('GLPI'),
      ],
      2 => [
        'title' => $translator->translate('CLI'),
      ],
    ];
  }

  public static function getNumberArray($min, $max, $step = 1, $toadd = [], $unit = '')
  {
    global $translator;

    $tab = [];
    foreach (array_keys($toadd) as $key)
    {
      $tab[$key]['title'] = $toadd[$key];
    }

    for ($i = $min; $i <= $max; $i = $i + $step)
    {
      $tab[$i]['title'] = self::getValueWithUnit($i, $unit, 0);
    }

    return $tab;
  }

  public static function getValueWithUnit($value, $unit, $decimals = 0)
  {
    global $translator;


    $formatted_number = is_numeric($value)
        ? self::formatNumber($value, false, $decimals)
        : $value;

    if (strlen($unit) == 0)
    {
      return $formatted_number;
    }

    switch ($unit)
    {
      case 'year':
        //TRANS: %s is a number of years
          return sprintf($translator->translatePlural('%s year', '%s years', $value), $formatted_number);

      case 'month':
        //TRANS: %s is a number of months
          return sprintf($translator->translatePlural('%s month', '%s months', $value), $formatted_number);

      case 'day':
        //TRANS: %s is a number of days
          return sprintf($translator->translatePlural('%s day', '%s days', $value), $formatted_number);

      case 'hour':
        //TRANS: %s is a number of hours
          return sprintf($translator->translatePlural('%s hour', '%s hours', $value), $formatted_number);

      case 'minute':
        //TRANS: %s is a number of minutes
          return sprintf($translator->translatePlural('%s minute', '%s minutes', $value), $formatted_number);

      case 'second':
        //TRANS: %s is a number of seconds
          return sprintf($translator->translatePlural('%s second', '%s seconds', $value), $formatted_number);

      case 'millisecond':
        //TRANS: %s is a number of milliseconds
          return sprintf($translator->translatePlural('%s millisecond', '%s milliseconds', $value), $formatted_number);

      case 'auto':
          return self::getSize($value * 1024 * 1024);

      case '%':
          return sprintf($translator->translate('%s%%'), $formatted_number);

      default:
          return sprintf($translator->translate('%1$s %2$s'), $formatted_number, $unit);
    }
  }

  public static function formatNumber($number, $edit = false, $forcedecimal = -1)
  {
    if (!(isset($_SESSION['glpinumber_format'])))
    {
      $_SESSION['glpinumber_format'] = '';
    }

    // Php 5.3 : number_format() expects parameter 1 to be double,
    if ($number == "")
    {
      $number = 0;
    } elseif ($number == "-")
    { // used for not defines value (from Infocom::Amort, p.e.)
      return "-";
    }

    $number  = doubleval($number);
    $decimal = 2;
    if ($forcedecimal >= 0)
    {
      $decimal = $forcedecimal;
    }

    // Edit: clean display for mysql
    if ($edit)
    {
      return number_format($number, $decimal, '.', '');
    }

    // Display: clean display
    switch ($_SESSION['glpinumber_format'])
    {
      case 0: // French
          return str_replace(' ', '&nbsp;', number_format($number, $decimal, '.', ' '));

      case 2: // Other French
          return str_replace(' ', '&nbsp;', number_format($number, $decimal, ',', ' '));

      case 3: // No space with dot
          return number_format($number, $decimal, '.', '');

      case 4: // No space with comma
          return number_format($number, $decimal, ',', '');

      default: // English
          return number_format($number, $decimal, '.', ',');
    }
  }

  public static function getSize($size)
  {
    global $translator;

    //TRANS: list of unit (o for octet)
    $bytes = [
      $translator->translate('o'),
      $translator->translate('Kio'),
      $translator->translate('Mio'),
      $translator->translate('Gio'),
      $translator->translate('Tio')
    ];
    foreach ($bytes as $val)
    {
      if ($size > 1024)
      {
        $size = $size / 1024;
      } else {
        break;
      }
    }
    //TRANS: %1$s is a number maybe float or string and %2$s the unit
    return sprintf($translator->translate('%1$s %2$s'), round($size, 2), $val);
  }


  /*
    ['CartridgeItem']['description' => __('Send alarms on cartridges')]
    ['Certificate']['description' => __('Send alarms on expired certificate')];
    ['ConsumableItem']['description' => __('Send alarms on consumables')];
    ['Contract']['description' => __('Send alarms on contracts')];
    ['Infocom']['description' => __('Send alarms on financial and administrative information')];
    ['PurgeLogs']['description' => __("Purge history")];
    ['ReservationItem']['description' => __('Alerts on reservations')];
    ['SoftwareLicense'][['description' => __('Send alarms on expired licenses')];


    CronTask
    case 'checkupdate' :
    return ['description' => __('Check for new updates')];

    case 'logs' :
    return ['description' => __('Clean old logs'),
    'parameter'
    => __('System logs retention period (in days, 0 for infinite)')];

    case 'session' :
    return ['description' => __('Clean expired sessions')];

    case 'graph' :
    return ['description' => __('Clean generated graphics')];

    case 'temp' :
    return ['description' => __('Clean temporary files')];

    case 'watcher' :
    return ['description' => __('Monitoring of automatic actions')];

    case 'circularlogs' :
    return ['description' => __("Archives log files and deletes aging ones"),
    'parameter'   => __("Number of days to keep archived logs")];

    DBConnection
    return ['description' => __('Check the SQL replica'),
    'parameter'   => __('Max delay between master and slave (minutes)')];
    Document
    case 'cleanorphans' :
    return ['description' => __('Clean orphaned documents')];
    Domain
    case 'DomainsAlert':
    return ['description' => __('Expired or expiring domains')];
    case 'mailgate' :
    return ['description' => __('Retrieve email (Mails receivers)'),
    'parameter'   => __('Number of emails to retrieve')];
    MailCollector
    case 'mailgateerror' :
    return ['description' => __('Send alarms on receiver errors')];
    ObjectLock
    case 'unlockobject' :
    return ['description' => __('Unlock forgotten locked objects'),
    'parameter'   => __('Timeout to force unlock (hours)')];
    OlaLevel_Ticket
    case 'olaticket' :
    return ['description' => __('Automatic actions of OLA')];
    PlanningRecall
    case 'planningrecall' :
    return ['description' => __('Send planning recalls')];
    case 'queuednotification' :
    return ['description' => __('Send mails in queue'),
    'parameter'   => __('Maximum emails to send at once')];
    QueuedNotification
    case 'queuednotificationclean' :
    return ['description' => __('Clean notification queue'),
    'parameter'   => __('Days to keep sent emails')];
    SavedSearch_Alert
    case 'send' :
    return ['description' => __('Saved searches alerts')];
    SavedSearch
    case 'countAll' :
    return ['description' => __('Update all bookmarks execution time')];
    SlaLevel_Ticket
    case 'slaticket' :
    return ['description' => __('Automatic actions of SLA')];
    Telemetry
    case 'telemetry' :
    return ['description' => __('Send telemetry information')];
    Ticket
    case 'closeticket' :
    return ['description' => __('Automatic tickets closing')];
    case 'alertnotclosed' :
    return ['description' => __('Not solved tickets')];
    case 'createinquest' :
    return ['description' => __('Generation of satisfaction surveys')];
    case 'purgeticket':
    return ['description' => __('Automatic closed tickets purge')];
    Ticketrecurrent
    case 'ticketrecurrent' :
    return ['description' => self::getTypeName(Session::getPluralNumber())];
    User
    case 'passwordexpiration':
    $info = [
    'description' => __('Handle users passwords expiration policy'),
    'parameter'   => __('Maximum expiration notifications to send at once'),
    ];
  */

  public static function getRelatedPages($rootUrl)
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Automatic action', 'Automatic actions', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Execution', 'Executions', 2),
        'icon' => 'cogs',
        'link' => $rootUrl . '/executions',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
