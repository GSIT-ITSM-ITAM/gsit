<?php

namespace App\Models\Definitions;

class Projecttasktemplate
{
  public static function getDefinition()
  {
    global $translator;

    $MINUTE_TIMESTAMP = 60;
    $HOUR_TIMESTAMP = 3600;
    $DAY_TIMESTAMP = 86400;
    $WEEK_TIMESTAMP = 604800;
    $MONTH_TIMESTAMP = 2592000;

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
        'title' => $translator->translate('Status'),
        'type'  => 'dropdown_remote',
        'name'  => 'state',
        'dbname' => 'projectstate_id',
        'itemtype' => '\App\Models\Projectstate',
        'fillable' => true,
      ],
      [
        'id'    => 5,
        'title' => $translator->translatePlural('Type', 'Types', 1),
        'type'  => 'dropdown_remote',
        'name'  => 'type',
        'dbname' => 'projecttasktype_id',
        'itemtype' => '\App\Models\Projecttasktype',
        'fillable' => true,
      ],
      [
        'id'    => 6,
        'title' => $translator->translate('As child of'),
        'type'  => 'dropdown_remote',
        'name'  => 'projecttasks',
        'dbname' => 'projecttask_id',
        'itemtype' => '\App\Models\Projecttask',
        'fillable' => true,
      ],
      [
        'id'    => 7,
        'title' => $translator->translate('Percent done'),
        'type'  => 'dropdown',
        'name'  => 'percent_done',
        'dbname'  => 'percent_done',
        'values' => self::getNumberArray(0, 100, 5, [], '%'),
        'fillable' => true,
      ],
      [
        'id'    => 8,
        'title' => $translator->translate('Milestone'),
        'type'  => 'boolean',
        'name'  => 'is_milestone',
        'fillable' => true,
      ],
      [
        'id'    => 9,
        'title' => $translator->translate('Planned start date'),
        'type'  => 'datetime',
        'name'  => 'plan_start_date',
        'fillable' => true,
      ],
      [
        'id'    => 10,
        'title' => $translator->translate('Real start date'),
        'type'  => 'datetime',
        'name'  => 'real_start_date',
        'fillable' => true,
      ],
      [
        'id'    => 11,
        'title' => $translator->translate('Planned end date'),
        'type'  => 'datetime',
        'name'  => 'plan_end_date',
        'fillable' => true,
      ],
      [
        'id'    => 12,
        'title' => $translator->translate('Real end date'),
        'type'  => 'datetime',
        'name'  => 'real_end_date',
        'fillable' => true,
      ],
      [
        'id'    => 13,
        'title' => $translator->translate('Planned duration'),
        'type'  => 'dropdown',
        'name'  => 'planned_duration',
        'dbname'  => 'planned_duration',
        'values' => self::getTimestampArray(
          [
            'min'             => 0,
            'max'             => 100 * $HOUR_TIMESTAMP,
            'step'            => $HOUR_TIMESTAMP,
            'addfirstminutes' => true,
            'inhours'         => true
          ]
        ),
        'fillable' => true,
      ],
      [
        'id'    => 14,
        'title' => $translator->translate('Effective duration'),
        'type'  => 'dropdown',
        'name'  => 'effective_duration',
        'dbname'  => 'effective_duration',
        'values' => self::getTimestampArray(
          [
            'min'             => 0,
            'max'             => 100 * $HOUR_TIMESTAMP,
            'step'            => $HOUR_TIMESTAMP,
            'addfirstminutes' => true,
            'inhours'         => true
          ]
        ),
        'fillable' => true,
      ],
      [
        'id'    => 15,
        'title' => $translator->translate('Description'),
        'type'  => 'textarea',
        'name'  => 'description',
        'fillable' => true,
      ],
      [
        'id'    => 216,
        'title' => $translator->translate('Comments'),
        'type'  => 'textarea',
        'name'  => 'comments',
        'fillable' => true,
      ],
      [
        'id'    => 16,
        'title' => $translator->translate('Comments'),
        'type'  => 'textarea',
        'name'  => 'comment',
        'fillable' => true,
      ],
      // [
      //    'id'    => 80,
      //    'title' => $translator->translatePlural('Entity', 'Entities', 1),
      //    'type'  => 'dropdown_remote',
      //    'name'  => 'completename',
      //    'itemtype' => '\App\Models\Entity',
      // ],
      [
        'id'    => 86,
        'title' => $translator->translate('Child entities'),
        'type'  => 'boolean',
        'name'  => 'is_recursive',
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

      /*

      $tab[] = [
      'id'   => 'common',
      'name' => __('Characteristics')
      ];

      $tab[] = [
      'id'                => '2',
      'table'             => $this->getTable(),
      'field'             => 'id',
      'name'              => __('ID'),
      'massiveaction'     => false,
      'datatype'          => 'number'
      ];

      if ($DB->fieldExists($this->getTable(), 'product_number'))
      {
      $tab[] = [
      'id'  => '3',
      'table'  => $this->getTable(),
      'field'  => 'product_number',
      'name'   => __('Product number'),
      'autocomplete' => true,
      ];
      }

      // add objectlock search options
      $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));
      */
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

  public static function getTimestampArray($options = [])
  {
    global $translator;

    $MINUTE_TIMESTAMP = 60;
    $HOUR_TIMESTAMP = 3600;
    $DAY_TIMESTAMP = 86400;
    $WEEK_TIMESTAMP = 604800;
    $MONTH_TIMESTAMP = 2592000;


    $params = [];
    $params['min']                 = 0;
    $params['max']                 = $DAY_TIMESTAMP;
    $params['step']                = 5 * $MINUTE_TIMESTAMP;
    $params['addfirstminutes']     = false;
    $params['toadd']               = [];
    $params['inhours']             = false;

    if (is_array($options) && count($options))
    {
      foreach ($options as $key => $val)
      {
        $params[$key] = $val;
      }
    }

    $params['min'] = floor($params['min'] / $params['step']) * $params['step'];

    if ($params['min'] == 0)
    {
      $params['min'] = $params['step'];
    }

    $values = [];

    if ($params['addfirstminutes'])
    {
      $max = max($params['min'], 10 * $MINUTE_TIMESTAMP);
      for ($i = $MINUTE_TIMESTAMP; $i < $max; $i += $MINUTE_TIMESTAMP)
      {
        $values[$i] = '';
      }
    }

    for ($i = $params['min']; $i <= $params['max']; $i += $params['step'])
    {
      $values[$i] = '';
    }

    if (count($params['toadd']))
    {
      foreach ($params['toadd'] as $key)
      {
        $values[$key] = '';
      }
      ksort($values);
    }

    foreach ($values as $i => $val)
    {
      if (empty($val))
      {
        if ($params['inhours'])
        {
          $day  = 0;
          $hour = floor($i / $HOUR_TIMESTAMP);
        }
        else
        {
          $day  = floor($i / $DAY_TIMESTAMP);
          $hour = floor(($i % $DAY_TIMESTAMP) / $HOUR_TIMESTAMP);
        }
        $minute     = floor(($i % $HOUR_TIMESTAMP) / $MINUTE_TIMESTAMP);
        if ($minute === '0')
        {
          $minute = '00';
        }
        $values[$i] = '';
        if ($day > 0)
        {
          if (($hour > 0) || ($minute > 0))
          {
            if ($minute < 10)
            {
              $minute = '0' . $minute;
            }

            //TRANS: %1$d is the number of days, %2$d the number of hours,
            //       %3$s the number of minutes : display 1 day 3h15
            $values[$i] = sprintf(
              $translator->translatePlural('%1$d day %2$dh%3$s', '%1$d days %2$dh%3$s', $day),
              $day,
              $hour,
              $minute
            );
          }
          else
          {
            $values[$i] = sprintf($translator->translatePlural('%d day', '%d days', $day), $day);
          }
        }
        elseif ($hour > 0 || $minute > 0)
        {
          if ($minute < 10)
          {
            $minute = '0' . $minute;
          }

          //TRANS: %1$d the number of hours, %2$s the number of minutes : display 3h15
          $values[$i] = sprintf($translator->translate('%1$dh%2$s'), $hour, $minute);
        }
      }
    }

    $tab = [];
    foreach (array_keys($values) as $key)
    {
      $tab[$key]['title'] = $values[$key];
    }
    return $tab;
  }

  public static function getRelatedPages($rootUrl)
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Project task template', 'Project task templates', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
