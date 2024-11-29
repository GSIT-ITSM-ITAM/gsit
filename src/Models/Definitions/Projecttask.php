<?php

namespace App\Models\Definitions;

class Projecttask
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
        'id'    => 2,
        'title' => $translator->translate('As child of'),
        'type'  => 'dropdown_remote',
        'name'  => 'parent',
        'dbname' => 'projecttask_id',
        'itemtype' => '\App\Models\Projecttask',
        'fillable' => true,
      ],
      [
        'id'    => 3,
        'title' => $translator->translate('Status'),
        'type'  => 'dropdown_remote',
        'name'  => 'state',
        'dbname' => 'projectstate_id',
        'itemtype' => '\App\Models\Projectstate',
        'fillable' => true,
      ],
      [
        'id'    => 4,
        'title' => $translator->translatePlural('Type', 'Types', 1),
        'type'  => 'dropdown_remote',
        'name'  => 'type',
        'dbname' => 'projecttasktype_id',
        'itemtype' => '\App\Models\Projecttasktype',
        'fillable' => true,
      ],
      [
        'id'    => 15,
        'title' => $translator->translate('Creation date'),
        'type'  => 'datetime',
        'name'  => 'date',
        'fillable' => true,
      ],
      [
        'id'    => 7,
        'title' => $translator->translate('Planned start date'),
        'type'  => 'datetime',
        'name'  => 'plan_start_date',
        'fillable' => true,
      ],
      [
        'id'    => 8,
        'title' => $translator->translate('Planned end date'),
        'type'  => 'datetime',
        'name'  => 'plan_end_date',
        'fillable' => true,
      ],
      [
        'id'    => 17,
        'title' => $translator->translate('Planned duration'),
        'type'  => 'input',
        'name'  => 'planned_duration',
        'readonly'  => 'readonly',
      ],
      [
        'id'    => 9,
        'title' => $translator->translate('Real start date'),
        'type'  => 'datetime',
        'name'  => 'real_start_date',
        'fillable' => true,
      ],
      [
        'id'    => 10,
        'title' => $translator->translate('Real end date'),
        'type'  => 'datetime',
        'name'  => 'real_end_date',
        'fillable' => true,
      ],
      [
        'id'    => 18,
        'title' => $translator->translate('Effective duration'),
        'type'  => 'input',
        'name'  => 'effective_duration',
        'readonly'  => 'readonly',
      ],
      [
        'id'    => 5,
        'title' => $translator->translate('Percent done'),
        'type'  => 'dropdown',
        'name'  => 'percent_done',
        'dbname'  => 'percent_done',
        'values' => self::getNumberArray(0, 100, 5, [], '%'),
        'fillable' => true,
      ],
      [
        'id'    => 16,
        'title' => $translator->translate('Comments'),
        'type'  => 'textarea',
        'name'  => 'comment',
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
    }
    elseif ($number == "-")
    {
      // used for not defines value (from Infocom::Amort, p.e.)
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
      }
      else
      {
        break;
      }
    }
    //TRANS: %1$s is a number maybe float or string and %2$s the unit
    return sprintf($translator->translate('%1$s %2$s'), round($size, 2), $val);
  }

  public static function getRelatedPages($rootUrl)
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Project task', 'Project tasks', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Project task', 'Project tasks', 2),
        'icon' => 'home',
        'link' => $rootUrl . '/projecttasks',
      ],
      [
        'title' => $translator->translatePlural('Project task team', 'Project task teams', 1),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/projecttaskteams',
      ],
      [
        'title' => $translator->translatePlural('Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => $translator->translatePlural('Ticket', 'Tickets', 2),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/tickets',
        'rightModel' => '\App\Models\Ticket',
      ],
      [
        'title' => $translator->translatePlural('Note', 'Notes', 2),
        'icon' => 'sticky note',
        'link' => $rootUrl . '/notes',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
