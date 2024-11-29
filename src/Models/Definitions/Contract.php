<?php

namespace App\Models\Definitions;

class Contract
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
        'id'    => 3,
        'title' => $translator->translate('phone' . "\004" . 'Number'),
        'type'  => 'input',
        'name'  => 'num',
        'fillable' => true,
      ],
      [
        'id'    => 31,
        'title' => $translator->translate('Status'),
        'type'  => 'dropdown_remote',
        'name'  => 'state',
        'dbname' => 'state_id',
        'itemtype' => '\App\Models\State',
        'fillable' => true,
      ],
      [
        'id'    => 4,
        'title' => $translator->translatePlural('Contract type', 'Contract types', 1),
        'type'  => 'dropdown_remote',
        'name'  => 'type',
        'dbname' => 'contracttype_id',
        'itemtype' => '\App\Models\Contracttype',
        'fillable' => true,
      ],
      [
        'id'    => 5,
        'title' => $translator->translate('Start date'),
        'type'  => 'date',
        'name'  => 'begin_date',
        'fillable' => true,
      ],
      [
        'id'    => 10,
        'title' => $translator->translate('Account number'),
        'type'  => 'input',
        'name'  => 'accounting_number',
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
        'id'    => 23,
        'title' => $translator->translate('Renewal'),
        'type'  => 'dropdown',
        'name'  => 'renewal',
        'dbname'  => 'renewal',
        'values' => self::getContractRenewalArray(),
        'fillable' => true,
      ],
      [
        'id'    => 6,
        'title' => $translator->translate('Initial contract period'),
        'type'  => 'dropdown',
        'name'  => 'duration',
        'dbname'  => 'duration',
        'values' => self::getNumberArray(1, 120, 1, [0 => '-----'], 'month'),
        'fillable' => true,
      ],
      [
        'id'    => 7,
        'title' => $translator->translate('Notice'),
        'type'  => 'dropdown',
        'name'  => 'notice',
        'dbname'  => 'notice',
        'values' => self::getNumberArray(0, 120, 1, [], 'month'),
        'fillable' => true,
      ],
      [
        'id'    => 21,
        'title' => $translator->translate('Contract renewal period'),
        'type'  => 'dropdown',
        'name'  => 'periodicity',
        'dbname'  => 'periodicity',
        'values' => self::getNumberArray(
          12,
          60,
          12,
          [
            0 => '-----',
            1 => sprintf($translator->translatePlural('%d month', '%d months', 1), 1),
            2 => sprintf($translator->translatePlural('%d month', '%d months', 2), 2),
            3 => sprintf($translator->translatePlural('%d month', '%d months', 3), 3),
            6 => sprintf($translator->translatePlural('%d month', '%d months', 6), 6)
          ],
          'month'
        ),
        'fillable' => true,
      ],
      [
        'id'    => 22,
        'title' => $translator->translate('Invoice period'),
        'type'  => 'dropdown',
        'name'  => 'billing',
        'dbname'  => 'billing',
        'values' => self::getNumberArray(
          12,
          60,
          12,
          [
            0 => '-----',
            1 => sprintf($translator->translatePlural('%d month', '%d months', 1), 1),
            2 => sprintf($translator->translatePlural('%d month', '%d months', 2), 2),
            3 => sprintf($translator->translatePlural('%d month', '%d months', 3), 3),
            6 => sprintf($translator->translatePlural('%d month', '%d months', 6), 6)
          ],
          'month'
        ),
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
        'id'                 => 'common',
        'name'               => __('Characteristics')
      ];

      $tab[] = [
        'id'                 => '20',
        'table'              => $this->getTable(),
        'field'              => 'end_date',
        'name'               => __('End date'),
        'datatype'           => 'date_delay',
        'datafields'         => [
        '1'                  => 'begin_date',
        '2'                  => 'duration'
        ],
        'searchunit'         => 'MONTH',
        'delayunit'          => 'MONTH',
        'maybefuture'        => true,
        'massiveaction'      => false
      ];

      $tab[] = [
        'id'                 => '12',
        'table'              => $this->getTable(),
        'field'              => 'expire',
        'name'               => __('Expiration'),
        'datatype'           => 'date_delay',
        'datafields'         => [
        '1'                  => 'begin_date',
        '2'                  => 'duration'
        ],
        'searchunit'         => 'DAY',
        'delayunit'          => 'MONTH',
        'maybefuture'        => true,
        'massiveaction'      => false
      ];

      $tab[] = [
        'id'                 => '13',
        'table'              => $this->getTable(),
        'field'              => 'expire_notice',
        'name'               => __('Expiration date + notice'),
        'datatype'           => 'date_delay',
        'datafields'         => [
        '1'                  => 'begin_date',
        '2'                  => 'duration',
        '3'                  => 'notice'
        ],
        'searchunit'         => 'DAY',
        'delayunit'          => 'MONTH',
        'maybefuture'        => true,
        'massiveaction'      => false
      ];

      $tab[] = [
        'id'                 => '59',
        'table'              => $this->getTable(),
        'field'              => 'alert',
        'name'               => __('Email alarms'),
        'datatype'           => 'specific',
        'searchtype'         => ['equals', 'notequals']
      ];

      $tab[] = [
        'id'                 => '72',
        'table'              => 'glpi_contracts_items',
        'field'              => 'id',
        'name'               => _x('quantity', 'Number of items'),
        'forcegroupby'       => true,
        'usehaving'          => true,
        'datatype'           => 'count',
        'massiveaction'      => false,
        'joinparams'         => [
        'jointype'           => 'child'
        ]
      ];

      $tab[] = [
        'id'                 => '29',
        'table'              => 'glpi_suppliers',
        'field'              => 'name',
        'name'               => _n('Associated supplier', 'Associated suppliers',
        Session::getPluralNumber()),
        'forcegroupby'       => true,
        'datatype'           => 'itemlink',
        'massiveaction'      => false,
        'joinparams'         => [
        'beforejoin'         => [
        'table'              => 'glpi_contracts_suppliers',
        'joinparams'         => [
        'jointype'           => 'child'
        ]
        ]
        ]
      ];

      $tab[] = [
        'id'                 => '50',
        'table'              => $this->getTable(),
        'field'              => 'template_name',
        'name'               => __('Template name'),
        'datatype'           => 'text',
        'massiveaction'      => false,
        'nosearch'           => true,
        'nodisplay'          => true,
        'autocomplete'       => true,
      ];

      // add objectlock search options
      $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));

      $tab = array_merge($tab, Notepad::rawSearchOptionsToAdd());

      $tab[] = [
        'id'                 => 'cost',
        'name'               => _n('Cost', 'Costs', 1)
      ];

      $tab[] = [
        'id'                 => '11',
        'table'              => 'glpi_contractcosts',
        'field'              => 'totalcost',
        'name'               => __('Total cost'),
        'datatype'           => 'decimal',
        'forcegroupby'       => true,
        'usehaving'          => true,
        'massiveaction'      => false,
        'joinparams'         => [
        'jointype'           => 'child'
        ],
        'computation'        =>
        '(SUM(' . $DB->quoteName('TABLE.cost') . ') / COUNT(' .
        $DB->quoteName('TABLE.id') . ')) * COUNT(DISTINCT ' .
        $DB->quoteName('TABLE.id') . ')',
        'nometa'             => true, // cannot GROUP_CONCAT a SUM
      ];

      $tab[] = [
        'id'                 => '41',
        'table'              => 'glpi_contractcosts',
        'field'              => 'cost',
        'name'               => _n('Cost', 'Costs', Session::getPluralNumber()),
        'datatype'           => 'decimal',
        'forcegroupby'       => true,
        'massiveaction'      => false,
        'joinparams'         => [
        'jointype'           => 'child'
        ]
      ];

      $tab[] = [
        'id'                 => '42',
        'table'              => 'glpi_contractcosts',
        'field'              => 'begin_date',
        'name'               => sprintf(__('%1$s - %2$s'), _n('Cost', 'Costs', 1), __('Begin date')),
        'datatype'           => 'date',
        'forcegroupby'       => true,
        'massiveaction'      => false,
        'joinparams'         => [
        'jointype'           => 'child'
        ]
      ];

      $tab[] = [
        'id'                 => '43',
        'table'              => 'glpi_contractcosts',
        'field'              => 'end_date',
        'name'               => sprintf(__('%1$s - %2$s'), _n('Cost', 'Costs', 1), __('End date')),
        'datatype'           => 'date',
        'forcegroupby'       => true,
        'massiveaction'      => false,
        'joinparams'         => [
        'jointype'           => 'child'
        ]
      ];

      $tab[] = [
        'id'                 => '44',
        'table'              => 'glpi_contractcosts',
        'field'              => 'name',
        'name'               => sprintf(__('%1$s - %2$s'), _n('Cost', 'Costs', 1), __('Name')),
        'forcegroupby'       => true,
        'massiveaction'      => false,
        'joinparams'         => [
        'jointype'           => 'child'
        ],
        'datatype'           => 'dropdown'
      ];

      $tab[] = [
        'id'                 => '45',
        'table'              => 'glpi_budgets',
        'field'              => 'name',
        'name'               => sprintf(__('%1$s - %2$s'), _n('Cost', 'Costs', 1), Budget::getTypeName(1)),
        'datatype'           => 'dropdown',
        'forcegroupby'       => true,
        'massiveaction'      => false,
        'joinparams'         => [
        'beforejoin'         => [
        'table'              => 'glpi_contractcosts',
        'joinparams'         => [
        'jointype'           => 'child'
        ]
        ]
        ]
      ];
      */
    ];
  }

  public static function getContractRenewalArray()
  {
    global $translator;
    return [
      0 => [
        'title' => $translator->translate('Never'),
      ],
      1 => [
        'title' => $translator->translate('Tacit'),
      ],
      2 => [
        'title' => $translator->translate('Express'),
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
        'title' => $translator->translatePlural('Contract', 'Contract', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Cost', 'Costs', 2),
        'icon' => 'money bill alternate',
        'link' => $rootUrl . '/costs',
      ],
      [
        'title' => $translator->translatePlural('Supplier', 'Suppliers', 2),
        'icon' => 'dolly',
        'link' => $rootUrl . '/suppliers',
      ],
      [
        'title' => $translator->translatePlural('Item', 'Items', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/attacheditems',
      ],
      [
        'title' => $translator->translatePlural('Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => $translator->translatePlural('External link', 'External links', 2),
        'icon' => 'linkify',
        'link' => $rootUrl . '/externallinks',
      ],
      [
        'title' => $translator->translatePlural('Note', 'Notes', 2),
        'icon' => 'sticky note',
        'link' => $rootUrl . '/notes',
      ],
      [
        'title' => $translator->translate('Knowledge base'),
        'icon' => 'book',
        'link' => $rootUrl . '/knowbaseitems',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
