<?php

namespace App\Models\Definitions;

class User
{
  public static function getDefinition()
  {
    global $translator;
    return [
      [
        'id'    => 1,
        'title' => $translator->translate('Login'),
        'type'  => 'input',
        'name'  => 'name',
        'fillable' => true,
      ],
      [
        'id'      => 2,
        'title'   => $translator->translate('ID'),
        'type'    => 'input',
        'name'    => 'id',
        'display' => false,
      ],
      [
        'id'    => 34,
        'title' => $translator->translate('Last name'),
        'type'  => 'input',
        'name'  => 'lastname',
        'fillable' => true,
      ],
      [
        'id'    => 9,
        'title' => $translator->translate('First name'),
        'type'  => 'input',
        'name'  => 'firstname',
        'fillable' => true,
      ],
      [
        'id'    => 8,
        'title' => $translator->translate('Active'),
        'type'  => 'boolean',
        'name'  => 'is_active',
        'fillable' => true,
      ],
      [
        'id'    => 62,
        'title' => $translator->translate('Valid since'),
        'type'  => 'datetime',
        'name'  => 'begin_date',
        'fillable' => true,
      ],
      [
        'id'    => 63,
        'title' => $translator->translate('Valid until'),
        'type'  => 'datetime',
        'name'  => 'end_date',
        'fillable' => true,
      ],
      [
        'id'    => 6,
        'title' => $translator->translatePlural('Phone', 'Phones', 1),
        'type'  => 'input',
        'name'  => 'phone',
        'fillable' => true,
      ],
      [
        'id'    => 10,
        'title' => $translator->translate('Phone 2'),
        'type'  => 'input',
        'name'  => 'phone2',
        'fillable' => true,
      ],
      [
        'id'    => 11,
        'title' => $translator->translate('Mobile phone'),
        'type'  => 'input',
        'name'  => 'mobile',
        'fillable' => true,
      ],
      [
        'id'    => 22,
        'title' => $translator->translate('Administrative number'),
        'type'  => 'input',
        'name'  => 'registration_number',
        'fillable' => true,
      ],
      [
        'id'    => 82,
        'title' => $translator->translate('Category'),
        'type'  => 'dropdown_remote',
        'name'  => 'category',
        'dbname' => 'usercategory_id',
        'itemtype' => '\App\Models\Usercategory',
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
        'id'    => 81,
        'title' => $translator->translate('Title'),
        'type'  => 'dropdown_remote',
        'name'  => 'title',
        'dbname' => 'usertitle_id',
        'itemtype' => '\App\Models\Usertitle',
        'fillable' => true,
      ],
      [
        'id'    => 3,
        'title' => $translator->translatePlural('Location', 'Locations', 1),
        'type'  => 'dropdown_remote',
        'name'  => 'location',
        'dbname' => 'location_id',
        'itemtype' => '\App\Models\Location',
        'fillable' => true,
      ],
      [
        'id'    => 79,
        'title' => $translator->translate('Default profile'),
        'type'  => 'dropdown_remote',
        'name'  => 'profile',
        'dbname' => 'profile_id',
        'itemtype' => '\App\Models\Profile',
        'fillable' => true,
      ],
      [
        'id'    => 277,
        'title' => $translator->translate('Default group'),
        'type'  => 'dropdown_remote',
        'name'  => 'defaultgroup',
        'dbname' => 'group_id',
        'itemtype' => '\App\Models\Group',
        'fillable' => true,
      ],
      [
        'id'    => 77,
        'title' => $translator->translate('Default entity'),
        'type'  => 'dropdown_remote',
        'name'  => 'entity',
        'dbname' => 'entity_id',
        'itemtype' => '\App\Models\Entity',
        'fillable' => true,
      ],
      [
        'id'    => 99,
        'title' => $translator->translate('Responsible'),
        'type'  => 'dropdown_remote',
        'name'  => 'supervisor',
        'dbname' => 'user_id_supervisor',
        'itemtype' => '\App\Models\User',
        'fillable' => true,
      ],
      [
        'id'    => 21,
        'title' => $translator->translate('User DN'),
        'type'  => 'textarea',
        'name'  => 'user_dn',
        'fillable' => true,
      ],
      [
        'id'    => 24,
        'title' => $translator->translate('Deleted user in LDAP directory'),
        'type'  => 'boolean',
        'name'  => 'is_deleted_ldap',
        'fillable' => true,
      ],
      [
        'id'    => 224,
        'title' => $translator->translate('Personal token'),
        'type'  => 'input',
        'name'  => 'personal_token',
        'fillable' => true,
      ],
      [
        'id'    => 225,
        'title' => $translator->translate('API token'),
        'type'  => 'input',
        'name'  => 'api_token',
        'fillable' => true,
      ],
      [
        'id'    => 28,
        'title' => $translator->translate('Synchronization field'),
        'type'  => 'input',
        'name'  => 'sync_field',
        'fillable' => true,
      ],
      [
        'id'    => 23,
        'title' => $translator->translate('Last synchronization'),
        'type'  => 'datetime',
        'name'  => 'date_sync',
        'readonly'  => 'readonly',
      ],
      [
        'id'    => 14,
        'title' => $translator->translate('Last login'),
        'type'  => 'datetime',
        'name'  => 'last_login',
        'readonly'  => 'readonly',
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
      [
        'id'    => 400,
        'title' => $translator->translate('Complete name'),
        'type'  => 'input',
        'name'  => 'completename',
        'fillable' => false,
      ],
      // [
      //   'id'    => 150,
      //   'title' => $translator->translate('Picture'),
      //   'type'  => 'file',
      //   'name'  => 'picture',
      //   'fillable' => true,
      // ],


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
         'id'                 => '5',
         'table'              => 'glpi_useremails',
         'field'              => 'email',
         'name'               => _n('Email', 'Emails', Session::getPluralNumber()),
         'datatype'           => 'email',
         'joinparams'         => [
            'jointype'           => 'child'
         ],
         'forcegroupby'       => true,
         'massiveaction'      => false
      ];

      $tab[] = [
         'id'                 => '150',
         'table'              => $this->getTable(),
         'field'              => 'picture',
         'name'               => __('Picture'),
         'datatype'           => 'specific',
         'nosearch'           => true,
         'massiveaction'      => false
      ];




      $tab[] = [
         'id'                 => '13',
         'table'              => 'glpi_groups',
         'field'              => 'completename',
         'name'               => Group::getTypeName(Session::getPluralNumber()),
         'forcegroupby'       => true,
         'datatype'           => 'itemlink',
         'massiveaction'      => false,
         'joinparams'         => [
            'beforejoin'         => [
               'table'              => 'glpi_groups_users',
               'joinparams'         => [
                  'jointype'           => 'child'
               ]
            ]
         ]
      ];


      $tab[] = [
         'id'                 => '15',
         'table'              => $this->getTable(),
         'field'              => 'authtype',
         'name'               => __('Authentication'),
         'massiveaction'      => false,
         'datatype'           => 'specific',
         'searchtype'         => 'equals',
         'additionalfields'   => [
            '0'                  => 'auths_id'
         ]
      ];

      $tab[] = [
         'id'                 => '30',
         'table'              => 'glpi_authldaps',
         'field'              => 'name',
         'linkfield'          => 'auths_id',
         'name'               => __('LDAP directory for authentication'),
         'massiveaction'      => false,
         'joinparams'         => [
             'condition'          => 'AND REFTABLE.`authtype` = ' . Auth::LDAP
         ],
         'datatype'           => 'dropdown'
      ];

      $tab[] = [
         'id'                 => '31',
         'table'              => 'glpi_authmails',
         'field'              => 'name',
         'linkfield'          => 'auths_id',
         'name'               => __('Email server for authentication'),
         'massiveaction'      => false,
         'joinparams'         => [
            'condition'          => 'AND REFTABLE.`authtype` = ' . Auth::MAIL
         ],
         'datatype'           => 'dropdown'
      ];


      $tab[] = [
         'id'                 => '17',
         'table'              => $this->getTable(),
         'field'              => 'language',
         'name'               => __('Language'),
         'datatype'           => 'language',
         'display_emptychoice' => true,
         'emptylabel'         => 'Default value'
      ];


      $tab[] = [
         'id'                 => '20',
         'table'              => 'glpi_profiles',
         'field'              => 'name',
         'name'               => sprintf(__('%1$s (%2$s)'), Profile::getTypeName(Session::getPluralNumber()),
                                                 Entity::getTypeName(1)),
         'forcegroupby'       => true,
         'massiveaction'      => false,
         'datatype'           => 'dropdown',
         'joinparams'         => [
            'beforejoin'         => [
               'table'              => 'glpi_profiles_users',
               'joinparams'         => [
                  'jointype'           => 'child'
               ]
            ]
         ]
      ];


      $tab[] = [
         'id'                 => '80',
         'table'              => 'glpi_entities',
         'linkfield'          => 'entities_id',
         'field'              => 'completename',
         'name'               => sprintf(__('%1$s (%2$s)'), Entity::getTypeName(Session::getPluralNumber()),
                                                 Profile::getTypeName(1)),
         'forcegroupby'       => true,
         'datatype'           => 'dropdown',
         'massiveaction'      => false,
         'joinparams'         => [
            'beforejoin'         => [
               'table'              => 'glpi_profiles_users',
               'joinparams'         => [
                  'jointype'           => 'child'
               ]
            ]
         ]
      ];


      $tab[] = [
         'id'                 => '60',
         'table'              => 'glpi_tickets',
         'field'              => 'id',
         'name'               => __('Number of tickets as requester'),
         'forcegroupby'       => true,
         'usehaving'          => true,
         'datatype'           => 'count',
         'massiveaction'      => false,
         'joinparams'         => [
            'beforejoin'         => [
               'table'              => 'glpi_tickets_users',
               'joinparams'         => [
                  'jointype'           => 'child',
                  'condition'          => 'AND NEWTABLE.`type` = ' . CommonITILActor::REQUESTER
               ]
            ]
         ]
      ];

      $tab[] = [
         'id'                 => '61',
         'table'              => 'glpi_tickets',
         'field'              => 'id',
         'name'               => __('Number of written tickets'),
         'forcegroupby'       => true,
         'usehaving'          => true,
         'datatype'           => 'count',
         'massiveaction'      => false,
         'joinparams'         => [
            'jointype'           => 'child',
            'linkfield'          => 'users_id_recipient'
         ]
      ];

      $tab[] = [
         'id'                 => '64',
         'table'              => 'glpi_tickets',
         'field'              => 'id',
         'name'               => __('Number of assigned tickets'),
         'forcegroupby'       => true,
         'usehaving'          => true,
         'datatype'           => 'count',
         'massiveaction'      => false,
         'joinparams'         => [
            'beforejoin'         => [
               'table'              => 'glpi_tickets_users',
               'joinparams'         => [
                  'jointype'           => 'child',
                  'condition'          => 'AND NEWTABLE.`type` = '.CommonITILActor::ASSIGN
               ]
            ]
         ]
      ];


      // add objectlock search options
      $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));

      */
    ];
  }

  public static function getRelatedPages($rootUrl)
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('User', 'Users', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Authorization', 'Authorizations', 2),
        'icon' => 'user lock',
        'link' => $rootUrl . '/authorization',
      ],
      [
        'title' => $translator->translatePlural('Group', 'Groups', 2),
        'icon' => 'users',
        'link' => $rootUrl . '/groups',
      ],
      [
        'title' => $translator->translate('Settings'),
        'icon' => 'caret square down outline',
        'link' => '',
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
        'title' => $translator->translate('Created tickets'),
        'icon' => 'caret square down outline',
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
        'title' => $translator->translatePlural('Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => $translator->translatePlural('Reservation', 'Reservations', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/reservations',
      ],
      [
        'title' => $translator->translate('Synchronization'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translatePlural('External link', 'External links', 2),
        'icon' => 'linkify',
        'link' => $rootUrl . '/externallinks',
      ],
      [
        'title' => $translator->translatePlural('Certificate', 'Certificates', 2),
        'icon' => 'certificate',
        'link' => $rootUrl . '/certificates',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
