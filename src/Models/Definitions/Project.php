<?php

namespace App\Models\Definitions;

class Project
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
        'title' => $translator->translate('Code'),
        'type'  => 'input',
        'name'  => 'code',
        'fillable' => true,
      ],
      [
        'id'    => 21,
        'title' => $translator->translate('Description'),
        'type'  => 'textarea',
        'name'  => 'content',
        'fillable' => true,
      ],
      [
        'id'    => 14,
        'title' => $translator->translatePlural('Type', 'Types', 1),
        'type'  => 'dropdown_remote',
        'name'  => 'type',
        'dbname' => 'projecttype_id',
        'itemtype' => '\App\Models\Projecttype',
        'fillable' => true,
      ],
      [
        'id'    => 12,
        'title' => $translator->translate('Status'),
        'type'  => 'dropdown_remote',
        'name'  => 'state',
        'dbname' => 'projectstate_id',
        'itemtype' => '\App\Models\Projectstate',
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
        'id'    => 6,
        'title' => $translator->translate('Show on global GANTT'),
        'type'  => 'boolean',
        'name'  => 'show_on_global_gantt',
        'fillable' => true,
      ],
      [
        'id'    => 24,
        'title' => $translator->translate('Manager'),
        'type'  => 'dropdown_remote',
        'name'  => 'user',
        'dbname' => 'user_id',
        'itemtype' => '\App\Models\User',
        'fillable' => true,
      ],
      [
        'id'    => 49,
        'title' => $translator->translate('Manager group'),
        'type'  => 'dropdown_remote',
        'name'  => 'group',
        'dbname' => 'group_id',
        'itemtype' => '\App\Models\Group',
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
        'name'  => '_virtual_planned_duration',
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
        'name'  => '_virtual_effective_duration',
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
        'id'    => 5,
        'title' => $translator->translate('Percent done'),
        'type'  => 'dropdown',
        'name'  => 'percent_done',
        'dbname'  => 'percent_done',
        'values' => self::getNumberArray(0, 100, 5, [], '%'),
        'fillable' => true,
      ],
      [
        'id'    => 3,
        'title' => $translator->translate('Priority'),
        'type'  => 'dropdown',
        'name'  => 'priority',
        'dbname'  => 'priority',
        'values' => self::getPriorityArray(),
        'fillable' => true,
      ],
      // [
      //    'id'    => 50,
      //    'title' => $translator->translate('Template name'),
      //    'type'  => 'input',
      //    'name'  => 'template_name',
      // ],
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
      'id'                 => 'common',
      'name'               => __('Characteristics')
      ];

      $tab[] = [
      'id'                 => '2',
      'table'              => $this->getTable(),
      'field'              => 'id',
      'name'               => __('ID'),
      'massiveaction'      => false,
      'datatype'           => 'number'
      ];


      $tab[] = [
      'id'                 => '13',
      'table'              => $this->getTable(),
      'field'              => 'name',
      'name'               => __('Father'),
      'datatype'           => 'itemlink',
      'massiveaction'      => false,
      'joinparams'         => [
      'condition'          => 'AND 1=1'
      ]
      ];




      $tab[] = [
      'id'                 => '91',
      'table'              => ProjectCost::getTable(),
      'field'              => 'totalcost',
      'name'               => __('Total cost'),
      'datatype'           => 'decimal',
      'forcegroupby'       => true,
      'usehaving'          => true,
      'massiveaction'      => false,
      'joinparams'         => [
      'jointype'           => 'child',
      'specific_itemtype'  => 'ProjectCost',
      'condition'          => 'AND NEWTABLE.`projects_id` = REFTABLE.`id`',
      'beforejoin'         => [
      'table'        => $this->getTable(),
      'joinparams'   => [
      'jointype'  => 'child'
      ],
      ],
      ],
      'computation'        => '(SUM('.$DB->quoteName('TABLE.cost').'))',
      'nometa'             => true, // cannot GROUP_CONCAT a SUM
      ];

      $itil_count_types = [
      'Change'  => _x('quantity', 'Number of changes'),
      'Problem' => _x('quantity', 'Number of problems'),
      'Ticket'  => _x('quantity', 'Number of tickets'),
      ];
      $index = 92;
      foreach ($itil_count_types as $itil_type => $label)
      {
      $tab[] = [
      'id'                 => $index,
      'table'              => Itil_Project::getTable(),
      'field'              => 'id',
      'name'               => $label,
      'datatype'           => 'count',
      'forcegroupby'       => true,
      'usehaving'          => true,
      'massiveaction'      => false,
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND NEWTABLE.`itemtype` = '$itil_type'"
      ]
      ];
      $index++;
      }

      $tab[] = [
      'id'                 => 'project_team',
      'name'               => ProjectTeam::getTypeName(),
      ];

      $tab[] = [
      'id'                 => '87',
      'table'              => User::getTable(),
      'field'              => 'name',
      'name'               => User::getTypeName(2),
      'forcegroupby'       => true,
      'datatype'           => 'dropdown',
      'joinparams'         => [
      'jointype'          => 'itemtype_item_revert',
      'specific_itemtype' => 'User',
      'beforejoin'        => [
      'table'      => ProjectTeam::getTable(),
      'joinparams' => [
      'jointype' => 'child',
      ]
      ]
      ]
      ];

      $tab[] = [
      'id'                 => '88',
      'table'              => Group::getTable(),
      'field'              => 'completename',
      'name'               => Group::getTypeName(2),
      'forcegroupby'       => true,
      'datatype'           => 'dropdown',
      'joinparams'         => [
      'jointype'          => 'itemtype_item_revert',
      'specific_itemtype' => 'Group',
      'beforejoin'        => [
      'table'      => ProjectTeam::getTable(),
      'joinparams' => [
      'jointype' => 'child',
      ]
      ]
      ]
      ];

      $tab[] = [
      'id'                 => '89',
      'table'              => Supplier::getTable(),
      'field'              => 'name',
      'name'               => Supplier::getTypeName(2),
      'forcegroupby'       => true,
      'datatype'           => 'dropdown',
      'joinparams'         => [
      'jointype'          => 'itemtype_item_revert',
      'specific_itemtype' => 'Supplier',
      'beforejoin'        => [
      'table'      => ProjectTeam::getTable(),
      'joinparams' => [
      'jointype' => 'child',
      ]
      ]
      ]
      ];

      $tab[] = [
      'id'                 => '90',
      'table'              => Contact::getTable(),
      'field'              => 'name',
      'name'               => Contact::getTypeName(2),
      'forcegroupby'       => true,
      'datatype'           => 'dropdown',
      'joinparams'         => [
      'jointype'          => 'itemtype_item_revert',
      'specific_itemtype' => 'Contact',
      'beforejoin'        => [
      'table'      => ProjectTeam::getTable(),
      'joinparams' => [
      'jointype' => 'child',
      ]
      ]
      ]
      ];

      $tab[] = [
      'id'                 => 'project_task',
      'name'               => ProjectTask::getTypeName(),
      ];

      $tab[] = [
      'id'                 => '111',
      'table'              => ProjectTask::getTable(),
      'field'              => 'name',
      'name'               => __('Name'),
      'datatype'           => 'string',
      'massiveaction'      => false,
      'forcegroupby'       => true,
      'splititems'         => true,
      'joinparams'         => [
      'jointype'  => 'child'
      ]
      ];

      $tab[] = [
      'id'                 => '112',
      'table'              => ProjectTask::getTable(),
      'field'              => 'content',
      'name'               => __('Description'),
      'datatype'           => 'text',
      'massiveaction'      => false,
      'forcegroupby'       => true,
      'splititems'         => true,
      'joinparams'         => [
      'jointype'  => 'child'
      ]
      ];

      $tab[] = [
      'id'                 => '113',
      'table'              => ProjectState::getTable(),
      'field'              => 'name',
      'name'               => _x('item', 'State'),
      'datatype'           => 'dropdown',
      'massiveaction'      => false,
      'forcegroupby'       => true,
      'splititems'         => true,
      'joinparams'         => [
      'jointype'          => 'item_revert',
      'specific_itemtype' => 'ProjectState',
      'beforejoin'        => [
      'table'      => ProjectTask::getTable(),
      'joinparams' => [
      'jointype' => 'child',
      ]
      ]
      ]
      ];

      $tab[] = [
      'id'                 => '114',
      'table'              => ProjectTaskType::getTable(),
      'field'              => 'name',
      'name'               => _n('Type', 'Types', 1),
      'datatype'           => 'dropdown',
      'massiveaction'      => false,
      'forcegroupby'       => true,
      'splititems'         => true,
      'joinparams'         => [
      'jointype'          => 'item_revert',
      'specific_itemtype' => 'ProjectTaskType',
      'beforejoin'        => [
      'table'      => ProjectTask::getTable(),
      'joinparams' => [
      'jointype' => 'child',
      ]
      ]
      ]
      ];

      $tab[] = [
      'id'                 => '115',
      'table'              => ProjectTask::getTable(),
      'field'              => 'date',
      'name'               => __('Opening date'),
      'datatype'           => 'datetime',
      'massiveaction'      => false,
      'forcegroupby'       => true,
      'splititems'         => true,
      'joinparams'         => [
      'jointype'  => 'child'
      ]
      ];

      $tab[] = [
      'id'                 => '116',
      'table'              => ProjectTask::getTable(),
      'field'              => 'updated_at',
      'name'               => __('Last update'),
      'datatype'           => 'datetime',
      'massiveaction'      => false,
      'forcegroupby'       => true,
      'splititems'         => true,
      'joinparams'         => [
      'jointype'  => 'child'
      ]
      ];

      $tab[] = [
      'id'                 => '117',
      'table'              => ProjectTask::getTable(),
      'field'              => 'percent_done',
      'name'               => __('Percent done'),
      'datatype'           => 'number',
      'unit'               => '%',
      'min'                => 0,
      'max'                => 100,
      'step'               => 5,
      'massiveaction'      => false,
      'forcegroupby'       => true,
      'splititems'         => true,
      'joinparams'         => [
      'jointype'  => 'child'
      ]
      ];

      $tab[] = [
      'id'                 => '118',
      'table'              => ProjectTask::getTable(),
      'field'              => 'plan_start_date',
      'name'               => __('Planned start date'),
      'datatype'           => 'datetime',
      'massiveaction'      => false,
      'forcegroupby'       => true,
      'splititems'         => true,
      'joinparams'         => [
      'jointype'  => 'child'
      ]
      ];

      $tab[] = [
      'id'                 => '119',
      'table'              => ProjectTask::getTable(),
      'field'              => 'plan_end_date',
      'name'               => __('Planned end date'),
      'datatype'           => 'datetime',
      'massiveaction'      => false,
      'forcegroupby'       => true,
      'splititems'         => true,
      'joinparams'         => [
      'jointype'  => 'child'
      ]
      ];

      $tab[] = [
      'id'                 => '120',
      'table'              => ProjectTask::getTable(),
      'field'              => 'real_start_date',
      'name'               => __('Real start date'),
      'datatype'           => 'datetime',
      'massiveaction'      => false,
      'forcegroupby'       => true,
      'splititems'         => true,
      'joinparams'         => [
      'jointype'  => 'child'
      ]
      ];

      $tab[] = [
      'id'                 => '122',
      'table'              => ProjectTask::getTable(),
      'field'              => 'real_end_date',
      'name'               => __('Real end date'),
      'datatype'           => 'datetime',
      'massiveaction'      => false,
      'joinparams'         => [
      'jointype'  => 'child'
      ]
      ];

      $tab[] = [
      'id'                 => '123',
      'table'              => ProjectTask::getTable(),
      'field'              => 'planned_duration',
      'name'               => __('Planned Duration'),
      'datatype'           => 'timestamp',
      'min'                => 0,
      'max'                => 100*HOUR_TIMESTAMP,
      'step'               => HOUR_TIMESTAMP,
      'addfirstminutes'    => true,
      'inhours'            => true,
      'massiveaction'      => false,
      'forcegroupby'       => true,
      'splititems'         => true,
      'joinparams'         => [
      'jointype'  => 'child'
      ]
      ];

      $tab[] = [
      'id'                 => '124',
      'table'              => ProjectTask::getTable(),
      'field'              => 'effective_duration',
      'name'               => __('Effective duration'),
      'datatype'           => 'timestamp',
      'min'                => 0,
      'max'                => 100*HOUR_TIMESTAMP,
      'step'               => HOUR_TIMESTAMP,
      'addfirstminutes'    => true,
      'inhours'            => true,
      'massiveaction'      => false,
      'forcegroupby'       => true,
      'splititems'         => true,
      'joinparams'         => [
      'jointype'  => 'child'
      ]
      ];

      $tab[] = [
      'id'                 => '125',
      'table'              => ProjectTask::getTable(),
      'field'              => 'comment',
      'name'               => __('Comments'),
      'datatype'           => 'text',
      'massiveaction'      => false,
      'forcegroupby'       => true,
      'splititems'         => true,
      'joinparams'         => [
      'jointype'  => 'child'
      ]
      ];

      $tab[] = [
      'id'                 => '126',
      'table'              => ProjectTask::getTable(),
      'field'              => 'is_milestone',
      'name'               => __('Milestone'),
      'datatype'           => 'bool',
      'massiveaction'      => false,
      'forcegroupby'       => true,
      'splititems'         => true,
      'joinparams'         => [
      'jointype'  => 'child'
      ]
      ];

      // add objectlock search options
      $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));

      $tab = array_merge($tab, Notepad::rawSearchOptionsToAdd());

      */
    ];
  }

  public static function getPriorityArray()
  {
    global $translator;
    return [
      6 => [
        'title' => $translator->translate('priority' . "\004" . 'Major'),
        'color' => 'gsitmajor',
        'icon'  => 'fire extinguisher',
      ],
      5 => [
        'title' => $translator->translate('priority' . "\004" . 'Very high'),
        'color' => 'gsitveryhigh',
        'icon'  => 'fire alternate',
      ],
      4 => [
        'title' => $translator->translate('priority' . "\004" . 'High'),
        'color' => 'gsithigh',
        'icon'  => 'fire',
      ],
      3 => [
        'title' => $translator->translate('priority' . "\004" . 'Medium'),
        'color' => 'gsitmedium',
        'icon'  => 'volume up',
      ],
      2 => [
        'title' => $translator->translate('priority' . "\004" . 'Low'),
        'color' => 'gsitlow',
        'icon'  => 'volume down',
      ],
      1 => [
        'title' => $translator->translate('priority' . "\004" . 'Very low'),
        'color' => 'gsitverylow',
        'icon'  => 'volume off',
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

    // Edit : clean display for mysql
    if ($edit)
    {
      return number_format($number, $decimal, '.', '');
    }

    // Display : clean display
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
        'title' => $translator->translatePlural('Project', 'Projects', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Project task', 'Project tasks', 2),
        'icon' => 'columns',
        'link' => $rootUrl . '/projecttasks',
      ],
      [
        'title' => $translator->translatePlural('Project team', 'Project teams', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/projectteams',
      ],
      [
        'title' => $translator->translatePlural('Project', 'Projects', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/projects',
      ],
      [
        'title' => $translator->translate('GANTT'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Kanban'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translatePlural('Cost', 'Costs', 2),
        'icon' => 'money bill alternate',
        'link' => $rootUrl . '/costs',
      ],
      [
        'title' => $translator->translatePlural('Itil item', 'Itil items', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/itilitems',
      ],
      [
        'title' => $translator->translatePlural('Item', 'Items', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/items',
      ],
      [
        'title' => $translator->translatePlural('Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => $translator->translatePlural('Contract', 'Contract', 2),
        'icon' => 'file signature',
        'link' => $rootUrl . '/contracts',
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
