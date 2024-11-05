<?php

namespace App\Models\Definitions;

class Category
{
  public static function getDefinition()
  {
    global $translator;
    return [
      [
        'id'    => 14,
        'title' => $translator->translate('Name'),
        'type'  => 'input',
        'name'  => 'name',
        'fillable' => true,
      ],
      [
        'id'    => 1,
        'title' => $translator->translate('Complete name'),
        'type'  => 'input',
        'name'  => 'completename',
        'fillable' => false,
      ],
      [
        'id'      => 2,
        'title'   => $translator->translate('ID'),
        'type'    => 'input',
        'name'    => 'id',
        'display' => false,
      ],
      [
        'id'    => 13,
        'title' => $translator->translate('As child of'),
        'type'  => 'dropdown_remote',
        'name'  => 'category',
        'dbname' => 'category_id',
        'itemtype' => '\App\Models\Category',
        'fillable' => true,
      ],
      [
        'id'    => 70,
        'title' => $translator->translate('Technician in charge of the hardware'),
        'type'  => 'dropdown_remote',
        'name'  => 'users',
        'dbname' => 'user_id',
        'itemtype' => '\App\Models\User',
        'fillable' => true,
      ],
      [
        'id'    => 71,
        'title' => $translator->translate('Group in charge of the hardware'),
        'type'  => 'dropdown_remote',
        'name'  => 'groups',
        'dbname' => 'group_id',
        'itemtype' => '\App\Models\Group',
        'fillable' => true,
      ],
      [
        'id'    => 79,
        'title' => $translator->translate('Knowledge base'),
        'type'  => 'dropdown_remote',
        'name'  => 'knowbaseitemcategories',
        'dbname' => 'knowbaseitemcategory_id',
        'itemtype' => '\App\Models\Knowbaseitemcategory',
        'fillable' => true,
      ],
      [
        'id'    => 99,
        'title' => $translator->translate('Code representing the ticket category'),
        'type'  => 'input',
        'name'  => 'code',
        'fillable' => true,
      ],
      [
        'id'    => 3,
        'title' => $translator->translate('Visible in the simplified interface'),
        'type'  => 'boolean',
        'name'  => 'is_helpdeskvisible',
        'fillable' => true,
      ],
      [
        'id'    => 74,
        'title' => $translator->translate('Visible for an incident'),
        'type'  => 'boolean',
        'name'  => 'is_incident',
        'fillable' => true,
      ],
      [
        'id'    => 75,
        'title' => $translator->translate('Visible for a request'),
        'type'  => 'boolean',
        'name'  => 'is_request',
        'fillable' => true,
      ],
      [
        'id'    => 76,
        'title' => $translator->translate('Visible for a problem'),
        'type'  => 'boolean',
        'name'  => 'is_problem',
        'fillable' => true,
      ],
      [
        'id'    => 85,
        'title' => $translator->translate('Visible for a change'),
        'type'  => 'boolean',
        'name'  => 'is_change',
        'fillable' => true,
      ],
      [
        'id'    => 72,
        'title' => $translator->translate('Template for a request'),
        'type'  => 'dropdown_remote',
        'name'  => 'tickettemplatesDemand',
        'dbname' => 'tickettemplate_id_demand',
        'itemtype' => '\App\Models\Tickettemplate',
        'fillable' => true,
      ],
      [
        'id'    => 73,
        'title' => $translator->translate('Template for an incident'),
        'type'  => 'dropdown_remote',
        'name'  => 'tickettemplates_incident',
        'dbname' => 'tickettemplate_id_incident',
        'itemtype' => '\App\Models\Tickettemplate',
        'fillable' => true,
      ],
      [
        'id'    => 100,
        'title' => $translator->translate('Template for a change'),
        'type'  => 'dropdown_remote',
        'name'  => 'changetemplates',
        'dbname' => 'changetemplate_id',
        'itemtype' => '\App\Models\Changetemplate',
        'fillable' => true,
      ],
      [
        'id'    => 101,
        'title' => $translator->translate('Template for a problem'),
        'type'  => 'dropdown_remote',
        'name'  => 'problemtemplates',
        'dbname' => 'problemtemplate_id',
        'itemtype' => '\App\Models\Problemtemplate',
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
        'id'    => 80,
        'title' => $translator->translatePlural('Entity', 'Entities', 1),
        'type'  => 'dropdown_remote',
        'name'  => 'completename',
        'itemtype' => '\App\Models\Entity',
      ],
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
        'id'                => '1',
        'table'              => $this->getTable(),
        'field'              => 'completename',
        'name'               => __('Complete name'),
        'datatype'           => 'itemlink',
        'massiveaction'      => false
      ];

      $tab[] = [
        'id'                => '2',
        'table'              => $this->getTable(),
        'field'              => 'id',
        'name'               => __('ID'),
        'massiveaction'      => false,
        'datatype'           => 'number'
      ];

      // add objectlock search options
      $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));

      $tab[] = [
        'id'                 => '77',
        'table'              => 'glpi_tickets',
        'field'              => 'id',
        'name'               => _x('quantity', 'Number of tickets'),
        'datatype'           => 'count',
        'forcegroupby'       => true,
        'massiveaction'      => false,
        'joinparams'         => [
        'jointype'           => 'child'
        ]
      ];

      $tab[] = [
        'id'                 => '78',
        'table'              => 'glpi_problems',
        'field'              => 'id',
        'name'               => _x('quantity', 'Number of problems'),
        'datatype'           => 'count',
        'forcegroupby'       => true,
        'massiveaction'      => false,
        'joinparams'         => [
        'jointype'           => 'child'
        ]
      ];

      $tab[] = [
        'id'                 => '98',
        'table'              => 'glpi_changes',
        'field'              => 'id',
        'name'               => _x('quantity', 'Number of changes'),
        'datatype'           => 'count',
        'forcegroupby'       => true,
        'massiveaction'      => false,
        'joinparams'         => [
        'jointype'           => 'child'
        ]
      ];
      */
    ];
  }

  public static function getRelatedPages($rootUrl)
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('ITIL category', 'ITIL categories', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('ITIL category', 'ITIL categories', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
