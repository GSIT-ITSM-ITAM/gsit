<?php

namespace App\Models\Definitions;

class Problem
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
        'title' => $translator->translate('Title'),
        'type'  => 'input',
        'name'  => 'name',
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
        'id'    => 12,
        'title' => $translator->translate('Status'),
        'type'  => 'dropdown',
        'name'  => 'status',
        'dbname'  => 'status',
        'values' => self::getStatusArray(),
        'fillable' => true,
      ],
      [
        'id'    => 10,
        'title' => $translator->translate('Urgency'),
        'type'  => 'dropdown',
        'name'  => 'urgency',
        'dbname'  => 'urgency',
        'values' => self::getUrgencyArray(),
        'fillable' => true,
      ],
      [
        'id'    => 11,
        'title' => $translator->translate('Impact'),
        'type'  => 'dropdown',
        'name'  => 'impact',
        'dbname'  => 'impact',
        'values' => self::getImpactArray(),
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
      [
        'id'    => 15,
        'title' => $translator->translate('Opening date'),
        'type'  => 'datetime',
        'name'  => 'date',
        'fillable' => true,
      ],
      [
        'id'    => 16,
        'title' => $translator->translate('Closing date'),
        'type'  => 'datetime',
        'name'  => 'closedate',
        'fillable' => true,
      ],
      [
        'id'    => 18,
        'title' => $translator->translate('Time to resolve'),
        'type'  => 'datetime',
        'name'  => 'time_to_resolve',
        'fillable' => true,
      ],
      // [
      //   'id'    => 82,
      //   'title' => $translator->translate('Time to resolve exceeded'),
      //   'type'  => 'boolean',
      //   'name'  => 'is_late',
      // ],
      [
        'id'    => 17,
        'title' => $translator->translate('Resolution date'),
        'type'  => 'datetime',
        'name'  => 'solvedate',
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
        'id'    => 7,
        'title' => $translator->translate('Category'),
        'type'  => 'dropdown_remote',
        'name'  => 'category',
        'dbname' => 'category_id',
        'itemtype' => '\App\Models\Category',
        'fillable' => true,
      ],
      [
        'id'    => 45,
        'title' => $translator->translate('Total duration'),
        'type'  => 'dropdown',
        'name'  => 'actiontime',
        'dbname'  => 'actiontime',
        'values' => self::getTimestampArray(
          [
            'addfirstminutes' => true
          ]
        ),
        'fillable' => true,
      ],
      [
        'id'    => 64,
        'title' => $translator->translate('Last edit by'),
        'type'  => 'dropdown_remote',
        'name'  => 'usersidlastupdater',
        'dbname' => 'user_id_lastupdater',
        'itemtype' => '\App\Models\User',
        'fillable' => true,
      ],
      [
        'id'    => 22,
        'title' => $translator->translate('Writer'),
        'type'  => 'dropdown_remote',
        'name'  => 'usersidrecipient',
        'dbname'  => 'user_id_recipient',
        'itemtype' => '\App\Models\User',
        'fillable' => true,
      ],
      // [
      //   'id'    => 80,
      //   'title' => $translator->translatePlural('Entity', 'Entities', 1),
      //   'type'  => 'dropdown_remote',
      //   'name'  => 'completename',
      //   'itemtype' => '\App\Models\Entity',
      // ],
      [
        'id'    => 60,
        'title' => $translator->translate('Impacts'),
        'type'  => 'textarea',
        'name'  => 'impactcontent',
        'fillable' => true,
      ],
      [
        'id'    => 61,
        'title' => $translator->translate('Causes'),
        'type'  => 'textarea',
        'name'  => 'causecontent',
        'fillable' => true,
      ],
      [
        'id'    => 62,
        'title' => $translator->translate('Symptoms'),
        'type'  => 'textarea',
        'name'  => 'symptomcontent',
        'fillable' => true,
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
        'id'                 => '151',
        'table'              => $this->getTable(),
        'field'              => 'time_to_resolve',
        'name'               => __('Time to resolve + Progress'),
        'massiveaction'      => false,
        'nosearch'           => true,
        'additionalfields'   => ['status']
      ];

      if (!Session::isCron() // no filter for cron
      && Session::getCurrentInterface() == 'helpdesk')
      {
      $newtab['condition']         = ['is_helpdeskvisible' => 1];
      }
      $tab[] = $newtab;


      // Filter search fields for helpdesk
      if (!Session::isCron() // no filter for cron
      && Session::getCurrentInterface() != 'central')
      {
      // last updater no search
      $newtab['nosearch'] = true;
      }
      $tab[] = $newtab;

      // add objectlock search options
      $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));

      // For ITIL template
      $tab[] = [
        'id'                 => '142',
        'table'              => 'glpi_documents',
        'field'              => 'name',
        'name'               => Document::getTypeName(Session::getPluralNumber()),
        'forcegroupby'       => true,
        'usehaving'          => true,
        'nosearch'           => true,
        'nodisplay'          => true,
        'datatype'           => 'dropdown',
        'massiveaction'      => false,
        'joinparams'         => [
        'jointype'           => 'items_id',
        'beforejoin'         => [
        'table'              => 'glpi_documents_items',
        'joinparams'         => [
        'jointype'           => 'itemtype_item'
        ]
        ]
        ]
      ];

      $tab[] = [
        'id'                 => '63',
        'table'              => 'glpi_items_problems',
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
        'id'                 => '13',
        'table'              => 'glpi_items_problems',
        'field'              => 'items_id',
        'name'               => _n('Associated element', 'Associated elements', Session::getPluralNumber()),
        'datatype'           => 'specific',
        'comments'           => true,
        'nosort'             => true,
        'nosearch'           => true,
        'additionalfields'   => ['itemtype'],
        'joinparams'         => [
        'jointype'           => 'child'
        ],
        'forcegroupby'       => true,
        'massiveaction'      => false
      ];

      $tab[] = [
        'id'                 => '131',
        'table'              => 'glpi_items_problems',
        'field'              => 'itemtype',
        'name'               => _n('Associated item type', 'Associated item types', Session::getPluralNumber()),
        'datatype'           => 'itemtypename',
        'itemtype_list'      => 'ticket_types',
        'nosort'             => true,
        'additionalfields'   => ['itemtype'],
        'joinparams'         => [
        'jointype'           => 'child'
        ],
        'forcegroupby'       => true,
        'massiveaction'      => false
      ];

      $tab = array_merge($tab, $this->getSearchOptionsActors());

      $tab[] = [
        'id'                 => 'analysis',
        'name'               => __('Analysis')
      ];

      ];

      $tab = array_merge($tab, Notepad::rawSearchOptionsToAdd());

      $tab = array_merge($tab, ITILFollowup::rawSearchOptionsToAdd());

      $tab = array_merge($tab, ProblemTask::rawSearchOptionsToAdd());

      $tab = array_merge($tab, $this->getSearchOptionsSolution());

      $tab = array_merge($tab, $this->getSearchOptionsStats());

      $tab = array_merge($tab, ProblemCost::rawSearchOptionsToAdd());

      $tab[] = [
        'id'                 => 'ticket',
        'name'               => Ticket::getTypeName(Session::getPluralNumber())
      ];

      $tab[] = [
        'id'                 => '141',
        'table'              => 'glpi_problems_tickets',
        'field'              => 'id',
        'name'               => _x('quantity', 'Number of tickets'),
        'forcegroupby'       => true,
        'usehaving'          => true,
        'datatype'           => 'count',
        'massiveaction'      => false,
        'joinparams'         => [
        'jointype'           => 'child'
        ]
      ];
      */
    ];
  }

  public static function getStatusArray()
  {
    global $translator;
    return [
      1 => [
        'title' => $translator->translate('New'),
        'displaystyle' => 'marked',
        'color' => 'olive',
        'icon'  => 'book open',
      ],
      2 => [
        'title' => $translator->translate('status' . "\004" . 'Processing (assigned)'),
        'displaystyle' => 'marked',
        'color' => 'blue',
        'icon'  => 'book reader',
      ],
      3 => [
        'title' => $translator->translate('status' . "\004" . 'Processing (planned)'),
        'displaystyle' => 'marked',
        'color' => 'blue',
        'icon'  => 'business time',
      ],
      4 => [
        'title' => $translator->translate('Pending'),
        'displaystyle' => 'marked',
        'color' => 'grey',
        'icon'  => 'pause',
      ],
      5 => [
        'title' => $translator->translate('Solved'),
        'displaystyle' => 'marked',
        'color' => 'purple',
        'icon'  => 'vote yea',
      ],
      6 => [
        'title' => $translator->translate('Closed'),
        'displaystyle' => 'marked',
        'color' => 'brown',
        'icon'  => 'archive',
      ],
    ];
  }

  public static function getUrgencyArray()
  {
    global $translator;
    return [
      5 => [
        'title' => $translator->translate('urgency' . "\004" . 'Very high'),
      ],
      4 => [
        'title' => $translator->translate('urgency' . "\004" . 'High'),
      ],
      3 => [
        'title' => $translator->translate('urgency' . "\004" . 'Medium'),
      ],
      2 => [
        'title' => $translator->translate('urgency' . "\004" . 'Low'),
      ],
      1 => [
        'title' => $translator->translate('urgency' . "\004" . 'Very low'),
      ],
    ];
  }

  public static function getImpactArray()
  {
    global $translator;
    return [
      5 => [
        'title' => $translator->translate('impact' . "\004" . 'Very high'),
      ],
      4 => [
        'title' => $translator->translate('impact' . "\004" . 'High'),
      ],
      3 => [
        'title' => $translator->translate('impact' . "\004" . 'Medium'),
      ],
      2 => [
        'title' => $translator->translate('impact' . "\004" . 'Low'),
      ],
      1 => [
        'title' => $translator->translate('impact' . "\004" . 'Very low'),
      ],
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

  public static function getDefinitionAnalysis()
  {
    global $translator;
    return [
      [
        'id'    => 1,
        'title' => $translator->translate('Impacts'),
        'type'  => 'textarea',
        'name'  => 'impactcontent',
      ],
      [
        'id'    => 2,
        'title' => $translator->translate('Causes'),
        'type'  => 'textarea',
        'name'  => 'causecontent',
      ],
      [
        'id'    => 3,
        'title' => $translator->translate('Symptoms'),
        'type'  => 'textarea',
        'name'  => 'symptomcontent',
      ],
    ];
  }

  public static function getRelatedPages($rootUrl)
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Problem', 'Problems', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translate('Processing problem'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Analysis'),
        'icon' => 'edit',
        'link' => $rootUrl . '/analysis',
      ],
      [
        'title' => $translator->translate('Statistics'),
        'icon' => 'chartline',
        'link' => $rootUrl . '/stats',
      ],
      [
        'title' => $translator->translatePlural('Ticket', 'Tickets', 2),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/tickets',
        'rightModel' => '\App\Models\Ticket',
      ],
      [
        'title' => $translator->translatePlural('Change', 'Changes', 2),
        'icon' => 'paint roller',
        'link' => $rootUrl . '/changes',
        'rightModel' => '\App\Models\Change',
      ],
      [
        'title' => $translator->translatePlural('Cost', 'Costs', 2),
        'icon' => 'money bill alternate',
        'link' => $rootUrl . '/costs',
      ],
      [
        'title' => $translator->translatePlural('Project', 'Projects', 2),
        'icon' => 'folder open',
        'link' => $rootUrl . '/projects',
        'rightModel' => '\App\Models\Project',
      ],
      [
        'title' => $translator->translatePlural('Item', 'Items', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/items',
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
