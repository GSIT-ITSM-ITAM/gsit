<?php

namespace App\Models\Definitions;

class Profile
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
        'title' => $translator->translate('Default profile'),
        'type'  => 'boolean',
        'name'  => 'is_default',
        'fillable' => true,
      ],
      [
        'id'    => 5,
        'title' => $translator->translate("Profile's interface"),
        'type'  => 'dropdown',
        'name'  => 'interface',
        'dbname'  => 'interface',
        'values' => self::getInterfaceArray(),
        'fillable' => true,
      ],
      [
        'id'    => 118,
        'title' => $translator->translate('Ticket creation form on login'),
        'type'  => 'boolean',
        'name'  => 'create_ticket_on_login',
        'fillable' => true,
      ],
      [
        'id'    => 220,
        'title' => $translator->translate('Update password'),
        'type'  => 'boolean',
        'name'  => 'password_update',
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

      // add objectlock search options
      $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));

      $tab[] = [
      'id'                 => 'inventory',
      'name'               => __('Assets')
      ];

      $tab[] = [
      'id'                 => '20',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => _n('Computer', 'Computers', Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Computer',
      'rightname'          => 'computer',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'computer'"
      ]
      ];

      $tab[] = [
      'id'                 => '21',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => _n('Monitor', 'Monitors', Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Monitor',
      'rightname'          => 'monitor',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'monitor'"
      ]
      ];

      $tab[] = [
      'id'                 => '22',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => _n('Software', 'Software', Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Software',
      'rightname'          => 'software',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'software'"
      ]
      ];

      $tab[] = [
      'id'                 => '23',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => _n('Network', 'Networks', Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Network',
      'rightname'          => 'networking',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'networking'"
      ]
      ];

      $tab[] = [
      'id'                 => '24',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => _n('Printer', 'Printers', Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Printer',
      'rightname'          => 'printer',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'printer'"
      ]
      ];

      $tab[] = [
      'id'                 => '25',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => Peripheral::getTypeName(Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Peripheral',
      'rightname'          => 'peripheral',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'peripheral'"
      ]
      ];

      $tab[] = [
      'id'                 => '26',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => _n('Cartridge', 'Cartridges', Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Cartridge',
      'rightname'          => 'cartridge',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'cartridge'"
      ]
      ];

      $tab[] = [
      'id'                 => '27',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => _n('Consumable', 'Consumables', Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Consumable',
      'rightname'          => 'consumable',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'consumable'"
      ]
      ];

      $tab[] = [
      'id'                 => '28',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => Phone::getTypeName(Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Phone',
      'rightname'          => 'phone',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'phone'"
      ]
      ];

      $tab[] = [
      'id'                 => '129',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => __('Internet'),
      'datatype'           => 'right',
      'rightclass'         => 'NetworkName',
      'rightname'          => 'internet',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'internet'"
      ]
      ];

      $tab[] = [
      'id'                 => 'management',
      'name'               => __('Management')
      ];

      $tab[] = [
      'id'                 => '30',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => Contact::getTypeName(1)." / ".Supplier::getTypeName(1),
      'datatype'           => 'right',
      'rightclass'         => 'Contact',
      'rightname'          => 'contact_entreprise',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'contact_enterprise'"
      ]
      ];

      $tab[] = [
      'id'                 => '31',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => Document::getTypeName(Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Document',
      'rightname'          => 'document',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'document'"
      ]
      ];

      $tab[] = [
      'id'                 => '32',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => _n('Contract', 'Contracts', Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Contract',
      'rightname'          => 'contract',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'contract'"
      ]
      ];

      $tab[] = [
      'id'                 => '33',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => __('Financial and administratives information'),
      'datatype'           => 'right',
      'rightclass'         => 'Infocom',
      'rightname'          => 'infocom',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'infocom'"
      ]
      ];

      $tab[] = [
      'id'                 => '101',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => Budget::getTypeName(1),
      'datatype'           => 'right',
      'rightclass'         => 'Budget',
      'rightname'          => 'budget',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'budget'"
      ]
      ];

      $tab[] = [
      'id'                 => 'tools',
      'name'               => __('Tools')
      ];

      $tab[] = [
      'id'                 => '34',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => __('Knowledge base'),
      'datatype'           => 'right',
      'rightclass'         => 'KnowbaseItem',
      'rightname'          => 'knowbase',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'knowbase'"
      ]
      ];

      $tab[] = [
      'id'                 => '36',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => _n('Reservation', 'Reservations', Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'ReservationItem',
      'rightname'          => 'reservation',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'reservation'"
      ]
      ];

      $tab[] = [
      'id'                 => '38',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => _n('Report', 'Reports', Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Report',
      'rightname'          => 'reports',
      'nowrite'            => true,
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'reports'"
      ]
      ];

      $tab[] = [
      'id'                 => 'config',
      'name'               => __('Setup')
      ];

      $tab[] = [
      'id'                 => '42',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => _n('Dropdown', 'Dropdowns', Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'DropdownTranslation',
      'rightname'          => 'dropdown',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'dropdown'"
      ]
      ];

      $tab[] = [
      'id'                 => '44',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => _n('Component', 'Components', Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Item_Devices',
      'rightname'          => 'device',
      'noread'             => true,
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'device'"
      ]
      ];

      $tab[] = [
      'id'                 => '106',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => _n('Notification', 'Notifications', Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Notification',
      'rightname'          => 'notification',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'notification'"
      ]
      ];

      $tab[] = [
      'id'                 => '45',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => DocumentType::getTypeName(1),
      'datatype'           => 'right',
      'rightclass'         => 'DocumentType',
      'rightname'          => 'typedoc',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'typedoc'"
      ]
      ];

      $tab[] = [
      'id'                 => '46',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => _n('External link', 'External links', Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Link',
      'rightname'          => 'link',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'link'"
      ]
      ];

      $tab[] = [
      'id'                 => '47',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => __('General setup'),
      'datatype'           => 'right',
      'rightclass'         => 'Config',
      'rightname'          => 'config',
      'noread'             => true,
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'config'"
      ]
      ];

      $tab[] = [
      'id'                 => '109',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => __('Personalization'),
      'datatype'           => 'right',
      'rightclass'         => 'Config',
      'rightname'          => 'personalization',
      'noread'             => true,
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'personalization'"
      ]
      ];

      $tab[] = [
      'id'                 => '52',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => __('Search result user display'),
      'datatype'           => 'right',
      'rightclass'         => 'DisplayPreference',
      'rightname'          => 'search_config',
      'noread'             => true,
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'search_config'"
      ]
      ];

      $tab[] = [
      'id'                 => '107',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => _n('Calendar', 'Calendars', Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Calendar',
      'rightname'          => 'calendar',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'calendar'"
      ]
      ];

      $tab[] = [
      'id'                 => 'admin',
      'name'               => __('Administration')
      ];

      $tab[] = [
      'id'                 => '48',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => __('Business rules for tickets'),
      'datatype'           => 'right',
      'rightclass'         => 'RuleTicket',
      'rightname'          => 'rule_ticket',
      'nowrite'            => true,
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'rule_ticket'"
      ]
      ];

      $tab[] = [
      'id'                 => '105',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => __('Rules for assigning a ticket created through a mails receiver'),
      'datatype'           => 'right',
      'rightclass'         => 'RuleMailCollector',
      'rightname'          => 'rule_mailcollector',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'rule_mailcollector'"
      ]
      ];

      $tab[] = [
      'id'                 => '49',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => __('Rules for assigning a computer to an entity'),
      'datatype'           => 'right',
      'rightclass'         => 'RuleImportComputer',
      'rightname'          => 'rule_import',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'rule_import'"
      ]
      ];

      $tab[] = [
      'id'                 => '50',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => __('Authorizations assignment rules'),
      'datatype'           => 'right',
      'rightclass'         => 'Rule',
      'rightname'          => 'rule_ldap',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'rule_ldap'"
      ]
      ];

      $tab[] = [
      'id'                 => '51',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => __('Rules for assigning a category to a software'),
      'datatype'           => 'right',
      'rightclass'         => 'Rulesoftwarecategory',
      'rightname'          => 'rule_softwarecategories',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'rule_softwarecategories'"
      ]
      ];

      $tab[] = [
      'id'                 => '90',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => __('Software dictionary'),
      'datatype'           => 'right',
      'rightclass'         => 'RuleDictionnarySoftware',
      'rightname'          => 'rule_dictionnary_software',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'rule_dictionnary_software'"
      ]
      ];

      $tab[] = [
      'id'                 => '91',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => __('Dropdowns dictionary'),
      'datatype'           => 'right',
      'rightclass'         => 'RuleDictionnaryDropdown',
      'rightname'          => 'rule_dictionnary_dropdown',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'rule_dictionnary_dropdown'"
      ]
      ];

      $tab[] = [
      'id'                 => '55',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => self::getTypeName(Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Profile',
      'rightname'          => 'profile',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'profile'"
      ]
      ];

      $tab[] = [
      'id'                 => '56',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => User::getTypeName(Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'User',
      'rightname'          => 'user',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'user'"
      ]
      ];

      $tab[] = [
      'id'                 => '58',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => Group::getTypeName(Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Group',
      'rightname'          => 'group',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'group'"
      ]
      ];

      $tab[] = [
      'id'                 => '59',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => Entity::getTypeName(Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Entity',
      'rightname'          => 'entity',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'entity'"
      ]
      ];

      $tab[] = [
      'id'                 => '60',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => __('Transfer'),
      'datatype'           => 'right',
      'rightclass'         => 'Transfer',
      'rightname'          => 'transfer',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'transfer'"
      ]
      ];

      $tab[] = [
      'id'                 => '61',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => _n('Log', 'Logs', Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Log',
      'rightname'          => 'logs',
      'nowrite'            => true,
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'logs'"
      ]
      ];

      $tab[] = [
      'id'                 => 'ticket',
      'name'               => __('Assistance')
      ];

      $tab[] = [
      'id'                 => '102',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => __('Create a ticket'),
      'datatype'           => 'right',
      'rightclass'         => 'Ticket',
      'rightname'          => 'ticket',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'ticket'"
      ]
      ];

      $newtab = [
      'id'                 => '108',
      'table'              => 'glpi_tickettemplates',
      'field'              => 'name',
      'name'               => __('Default ticket template'),
      'datatype'           => 'dropdown',
      ];
      if (Session::isMultiEntitiesMode())
      {
      $newtab['condition']     = ['entities_id' => 0, 'is_recursive' => 1];
      }
      else
      {
      $newtab['condition']     = ['entities_id' => 0];
      }
      $tab[] = $newtab;

      $tab[] = [
      'id'                 => '103',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => _n('Ticket template', 'Ticket templates', Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'TicketTemplate',
      'rightname'          => 'tickettemplate',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'tickettemplate'"
      ]
      ];

      $tab[] = [
      'id'                 => '79',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => __('Planning'),
      'datatype'           => 'right',
      'rightclass'         => 'Planning',
      'rightname'          => 'planning',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'planning'"
      ]
      ];

      $tab[] = [
      'id'                 => '85',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => __('Statistics'),
      'datatype'           => 'right',
      'rightclass'         => 'Stat',
      'rightname'          => 'statistic',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'statistic'"
      ]
      ];

      $tab[] = [
      'id'                 => '119',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => _n('Ticket cost', 'Ticket costs', Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'TicketCost',
      'rightname'          => 'ticketcost',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'ticketcost'"
      ]
      ];

      $tab[] = [
      'id'                 => '86',
      'table'              => $this->getTable(),
      'field'              => 'helpdesk_hardware',
      'name'               => __('Link with items for the creation of tickets'),
      'massiveaction'      => false,
      'datatype'           => 'specific'
      ];

      $tab[] = [
      'id'                 => '87',
      'table'              => $this->getTable(),
      'field'              => 'helpdesk_item_type',
      'name'               => __('Associable items to a ticket'),
      'massiveaction'      => false,
      'datatype'           => 'specific'
      ];

      $tab[] = [
      'id'                 => '88',
      'table'              => $this->getTable(),
      'field'              => 'managed_domainrecordtypes',
      'name'               => __('Managed domain records types'),
      'massiveaction'      => false,
      'datatype'           => 'specific'
      ];

      $tab[] = [
      'id'                 => '89',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => __('See hardware of my groups'),
      'datatype'           => 'bool',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'show_group_hardware'"
      ]
      ];

      $tab[] = [
      'id'                 => '100',
      'table'              => $this->getTable(),
      'field'              => 'ticket_status',
      'name'               => __('Life cycle of tickets'),
      'nosearch'           => true,
      'datatype'           => 'text',
      'massiveaction'      => false
      ];

      $tab[] = [
      'id'                 => '110',
      'table'              => $this->getTable(),
      'field'              => 'problem_status',
      'name'               => __('Life cycle of problems'),
      'nosearch'           => true,
      'datatype'           => 'text',
      'massiveaction'      => false
      ];

      $tab[] = [
      'id'                 => '112',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => Problem::getTypeName(Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Problem',
      'rightname'          => 'problem',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'problem'"
      ]
      ];

      $tab[] = [
      'id'                 => '111',
      'table'              => $this->getTable(),
      'field'              => 'change_status',
      'name'               => __('Life cycle of changes'),
      'nosearch'           => true,
      'datatype'           => 'text',
      'massiveaction'      => false
      ];

      $tab[] = [
      'id'                 => '115',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => _n('Change', 'Changes', Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Change',
      'rightname'          => 'change',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'change'"
      ]
      ];

      $tab[] = [
      'id'                 => 'other',
      'name'               => __('Other')
      ];

      $tab[] = [
      'id'                 => '4',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => __('Update password'),
      'datatype'           => 'bool',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'password_update'"
      ]
      ];

      $tab[] = [
      'id'                 => '63',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => _n('Public reminder', 'Public reminders', Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'Reminder',
      'rightname'          => 'reminder_public',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'reminder_public'"
      ]
      ];

      $tab[] = [
      'id'                 => '64',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => _n('Public saved search', 'Public saved searches', Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'SavedSearch',
      'rightname'          => 'bookmark_public',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'bookmark_public'"
      ]
      ];

      $tab[] = [
      'id'                 => '120',
      'table'              => 'glpi_profilerights',
      'field'              => 'rights',
      'name'               => _n('Public RSS feed', 'Public RSS feeds', Session::getPluralNumber()),
      'datatype'           => 'right',
      'rightclass'         => 'RSSFeed',
      'rightname'          => 'rssfeed_public',
      'joinparams'         => [
      'jointype'           => 'child',
      'condition'          => "AND `NEWTABLE`.`name`= 'rssfeed_public'"
      ]
      ];
      */
    ];
  }

  public static function getInterfaceArray()
  {
    global $translator;
    return [
      'central' => [
        'title' => $translator->translate('Standard interface'),
      ],
      'helpdesk' => [
        'title' => $translator->translate('Simplified interface'),
      ]
    ];
  }

  public static function getRelatedPages($rootUrl)
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Profile', 'Profiles', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translate('Assets'),
        'icon' => 'laptop house',
        'link' => $rootUrl . '/assets',
      ],
      [
        'title' => $translator->translate('Assistance'),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/assistance',
      ],
      [
        'title' => $translator->translatePlural('Form', 'Forms', 2),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/forms',
      ],
      [
        'title' => $translator->translate('Management'),
        'icon' => 'block layout',
        'link' => $rootUrl . '/management',
      ],
      [
        'title' => $translator->translate('Tools'),
        'icon' => 'toolbox',
        'link' => $rootUrl . '/tools',
      ],
      [
        'title' => $translator->translate('Administration'),
        'icon' => 'screwdriver',
        'link' => $rootUrl . '/administration',
      ],
      [
        'title' => $translator->translate('Setup'),
        'icon' => 'tools',
        'link' => $rootUrl . '/setup',
      ],
      [
        'title' => $translator->translatePlural('User', 'Users', 2),
        'icon' => 'users',
        'link' => $rootUrl . '/users',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
