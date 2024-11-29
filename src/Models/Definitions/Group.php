<?php

namespace App\Models\Definitions;

class Group
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
        'name'  => 'child',
        'dbname' => 'group_id',
        'itemtype' => '\App\Models\Group',
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
        'id'    => 11,
        'title' => $translator->translatePlural('Requester', 'Requesters', 1),
        'type'  => 'boolean',
        'name'  => 'is_requester',
        'fillable' => true,
      ],
      [
        'id'    => 212,
        'title' => $translator->translatePlural('Watcher', 'Watchers', 1),
        'type'  => 'boolean',
        'name'  => 'is_watcher',
        'fillable' => true,
      ],
      [
        'id'    => 12,
        'title' => $translator->translate('Assigned to'),
        'type'  => 'boolean',
        'name'  => 'is_assign',
        'fillable' => true,
      ],
      [
        'id'    => 72,
        'title' => $translator->translatePlural('Task', 'Tasks', 1),
        'type'  => 'boolean',
        'name'  => 'is_task',
        'fillable' => true,
      ],
      [
        'id'    => 20,
        'title' => $translator->translate('Can be notified'),
        'type'  => 'boolean',
        'name'  => 'is_notify',
        'fillable' => true,
      ],
      [
        'id'    => 18,
        'title' => $translator->translate('Can be manager'),
        'type'  => 'boolean',
        'name'  => 'is_manager',
        'fillable' => true,
      ],
      [
        'id'    => 17,
        'title' => sprintf(
          $translator->translate('%1$s %2$s'),
          $translator->translate('Can contain'),
          $translator->translatePlural('Item', 'Items', 2)
        ),
        'type'  => 'boolean',
        'name'  => 'is_itemgroup',
        'fillable' => true,
      ],
      [
        'id'    => 15,
        'title' => sprintf(
          $translator->translate('%1$s %2$s'),
          $translator->translate('Can contain'),
          $translator->translatePlural('User', 'Users', 2)
        ),
        'type'  => 'boolean',
        'name'  => 'is_usergroup',
        'fillable' => true,
      ],
      // [
      //   'id'    => 80,
      //   'title' => $translator->translatePlural('Entity', 'Entities', 1),
      //   'type'  => 'dropdown_remote',
      //   'name'  => 'entity',
      //   'dbname' => 'entities_id',
      //   'itemtype' => '\App\Models\Entity',
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

      if (AuthLDAP::useAuthLdap())
      {
      $tab[] = [
        'id'                 => '3',
        'table'              => $this->getTable(),
        'field'              => 'ldap_field',
        'name'               => __('Attribute of the user containing its groups'),
        'datatype'           => 'string',
        'autocomplete'       => true,
      ];

      $tab[] = [
        'id'                 => '4',
        'table'              => $this->getTable(),
        'field'              => 'ldap_value',
        'name'               => __('Attribute value'),
        'datatype'           => 'text',
        'autocomplete'       => true,
      ];

      $tab[] = [
        'id'                 => '5',
        'table'              => $this->getTable(),
        'field'              => 'ldap_group_dn',
        'name'               => __('Group DN'),
        'datatype'           => 'text',
        'autocomplete'       => true,
      ];
      }

      $tab[] = [
        'id'                 => '70',
        'table'              => 'glpi_users',
        'field'              => 'name',
        'name'               => __('Manager'),
        'datatype'           => 'dropdown',
        'right'              => 'all',
        'forcegroupby'       => true,
        'massiveaction'      => false,
        'joinparams'         => [
        'beforejoin'         => [
        'table'              => 'glpi_groups_users',
        'joinparams'         => [
        'jointype'           => 'child',
        'condition'          => 'AND NEWTABLE.`is_manager` = 1'
        ]
        ]
        ]
      ];

      $tab[] = [
        'id'                 => '71',
        'table'              => 'glpi_users',
        'field'              => 'name',
        'name'               => __('Delegatee'),
        'datatype'           => 'dropdown',
        'right'              => 'all',
        'forcegroupby'       => true,
        'massiveaction'      => false,
        'joinparams'         => [
        'beforejoin'         => [
        'table'              => 'glpi_groups_users',
        'joinparams'         => [
        'jointype'           => 'child',
        'condition'          => 'AND NEWTABLE.`is_userdelegate` = 1'
        ]
        ]
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
        'title' => $translator->translatePlural('Group', 'Groups', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translate('Child groups'),
        'icon' => 'users',
        'link' => $rootUrl . '/groups',
      ],
      [
        'title' => $translator->translate('Used items'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Managed items'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translatePlural('User', 'Users', 2),
        'icon' => 'user',
        'link' => $rootUrl . '/users',
      ],
      [
        'title' => $translator->translatePlural('Notification', 'Notifications', 2),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Created tickets'),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/tickets',
      ],
      [
        'title' => $translator->translatePlural('Problem', 'Problems', 2),
        'icon' => 'drafting compass',
        'link' => $rootUrl . '/problems',
        'rightModel' => '\App\Models\Problem',
      ],
      [
        'title' => $translator->translatePlural('Change', 'Changes', 2),
        'icon' => 'paint roller',
        'link' => $rootUrl . '/changes',
        'rightModel' => '\App\Models\Change',
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
