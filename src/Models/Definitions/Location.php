<?php

namespace App\Models\Definitions;

class Location
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
        'name'  => 'location',
        'dbname' => 'location_id',
        'itemtype' => '\App\Models\Location',
        'fillable' => true,
      ],
      [
        'id'    => 15,
        'title' => $translator->translate('Address'),
        'type'  => 'input',
        'name'  => 'address',
        'fillable' => true,
      ],
      [
        'id'    => 17,
        'title' => $translator->translate('Postal code'),
        'type'  => 'input',
        'name'  => 'postcode',
        'fillable' => true,
      ],
      [
        'id'    => 18,
        'title' => $translator->translate('Town'),
        'type'  => 'input',
        'name'  => 'town',
        'fillable' => true,
      ],
      [
        'id'    => 104,
        'title' => $translator->translate('location' . "\004" . 'State'),
        'type'  => 'input',
        'name'  => 'state',
        'fillable' => true,
      ],
      [
        'id'    => 105,
        'title' => $translator->translate('Country'),
        'type'  => 'input',
        'name'  => 'country',
        'fillable' => true,
      ],
      [
        'id'    => 11,
        'title' => $translator->translate('Building number'),
        'type'  => 'input',
        'name'  => 'building',
        'fillable' => true,
      ],
      [
        'id'    => 12,
        'title' => $translator->translate('Room number'),
        'type'  => 'input',
        'name'  => 'room',
        'fillable' => true,
      ],
      [
        'id'    => 21,
        'title' => $translator->translate('Latitude'),
        'type'  => 'input',
        'name'  => 'latitude',
        'fillable' => true,
      ],
      [
        'id'    => 20,
        'title' => $translator->translate('Longitude'),
        'type'  => 'input',
        'name'  => 'longitude',
        'fillable' => true,
      ],
      [
        'id'    => 22,
        'title' => $translator->translate('Altitude'),
        'type'  => 'input',
        'name'  => 'altitude',
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
      //   'id'    => 80,
      //   'title' => $translator->translatePlural('Entity', 'Entities', 1),
      //   'type'  => 'dropdown_remote',
      //   'name'  => 'completename',
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
      $tab = [];

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
        'id'                 => '101',
        'table'              => 'glpi_locations',
        'field'              => 'address',
        'name'               => __('Address'),
        'datatype'           => 'string',
        'autocomplete'       => true,
      ];

      $tab[] = [
        'id'                 => '102',
        'table'              => 'glpi_locations',
        'field'              => 'postcode',
        'name'               => __('Postal code'),
        'datatype'           => 'string',
        'autocomplete'       => true,
      ];

      $tab[] = [
        'id'                 => '103',
        'table'              => 'glpi_locations',
        'field'              => 'town',
        'name'               => __('Town'),
        'datatype'           => 'string',
        'autocomplete'       => true,
      ];
      */
    ];
  }

  public static function getRelatedPages($rootUrl)
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Location', 'Locations', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Location', 'Locations', 2),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/locations',
      ],
      [
        'title' => $translator->translatePlural('Item', 'Items', 2),
        'icon' => 'desktop',
        'link' => '',
      ],
      [
        'title' => $translator->translatePlural('Network outlet', 'Network outlets', 2),
        'icon' => 'caret square down outline',
        'link' => '',
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
