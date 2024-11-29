<?php

namespace App\Models\Definitions;

class Rack
{
  public static function getDefinition()
  {
    global $translator;
    return [
      [
        'id'    => 2,
        'title' => $translator->translate('Name'),
        'type'  => 'input',
        'name'  => 'name',
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
        'title' => $translator->translatePlural('Type', 'Types', 1),
        'type'  => 'dropdown_remote',
        'name'  => 'type',
        'dbname' => 'racktype_id',
        'itemtype' => '\App\Models\Racktype',
        'fillable' => true,
      ],
      [
        'id'    => 40,
        'title' => $translator->translatePlural('Model', 'Models', 1),
        'type'  => 'dropdown_remote',
        'name'  => 'model',
        'dbname' => 'rackmodel_id',
        'itemtype' => '\App\Models\Rackmodel',
        'fillable' => true,
      ],
      [
        'id'    => 5,
        'title' => $translator->translate('Serial number'),
        'type'  => 'input',
        'name'  => 'serial',
        'autocomplete'  => true,
        'fillable' => true,
      ],
      [
        'id'    => 6,
        'title' => $translator->translate('Inventory number'),
        'type'  => 'input',
        'name'  => 'otherserial',
        'autocomplete'  => true,
        'fillable' => true,
      ],
      [
        'id'    => 23,
        'title' => $translator->translatePlural('Manufacturer', 'Manufacturers', 1),
        'type'  => 'dropdown_remote',
        'name'  => 'manufacturer',
        'dbname' => 'manufacturer_id',
        'itemtype' => '\App\Models\Manufacturer',
        'fillable' => true,
      ],
      [
        'id'    => 24,
        'title' => $translator->translate('Technician in charge of the hardware'),
        'type'  => 'dropdown_remote',
        'name'  => 'userstech',
        'dbname' => 'user_id_tech',
        'itemtype' => '\App\Models\User',
        'fillable' => true,
      ],
      [
        'id'    => 49,
        'title' => $translator->translate('Group in charge of the hardware'),
        'type'  => 'dropdown_remote',
        'name'  => 'groupstech',
        'dbname' => 'group_id_tech',
        'itemtype' => '\App\Models\Group',
        'fillable' => true,
      ],
      [
        'id'    => 8,
        'title' => $translator->translate('Number of units'),
        'type'  => 'dropdown',
        'name'  => 'number_units',
        'values' => self::getNumberArray(1, 100, 1, [], 'U'),
        'fillable' => true,
      ],
      // [
      //   'id'    => 61,
      //   'title' => $translator->translate('Template name'),
      //   'type'  => 'input',
      //   'name'  => 'template_name',
      // ],
      // [
      //   'id'    => 80,
      //   'title' => $translator->translatePlural('Entity', 'Entities', 1),
      //   'type'  => 'dropdown_remote',
      //   'name'  => 'completename',
      //   'itemtype' => '\App\Models\Entity',
      // ],
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
         'id'                 => '7',
         'table'              => DCRoom::getTable(),
         'field'              => 'name',
         'name'               => DCRoom::getTypeName(1),
         'datatype'           => 'dropdown'
      ];

      $tab = array_merge($tab, Notepad::rawSearchOptionsToAdd());

      $tab = array_merge($tab, Datacenter::rawSearchOptionsToAdd(get_class($this)));

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

  public static function getDefinitionInfocom()
  {
    global $translator;
    return [
      [
        'id'    => 1,
        'title' => $translator->translate('Order date'),
        'type'  => 'date',
        'name'  => 'order_date',
        'fillable' => true,
      ],
      [
        'id'    => 2,
        'title' => $translator->translate('Date of purchase'),
        'type'  => 'date',
        'name'  => 'buy_date',
        'fillable' => true,
      ],
      [
        'id'    => 3,
        'title' => $translator->translate('Delivery date'),
        'type'  => 'date',
        'name'  => 'delivery_date',
        'fillable' => true,
      ],
      [
        'id'    => 4,
        'title' => $translator->translate('Startup date'),
        'type'  => 'date',
        'name'  => 'use_date',
        'fillable' => true,
      ],
      [
        'id'    => 5,
        'title' => $translator->translate('Date of last physical inventory'),
        'type'  => 'date',
        'name'  => 'inventory_date',
        'fillable' => true,
      ],
      [
        'id'    => 6,
        'title' => $translator->translate('Decommission date'),
        'type'  => 'date',
        'name'  => 'decommission_date',
        'fillable' => true,
      ],
      [
        'id'    => 7,
        'title' => $translator->translatePlural('Supplier', 'Suppliers', 1),
        'type'  => 'dropdown_remote',
        'name'  => 'supplier',
        'dbname' => 'supplier_id',
        'itemtype' => '\App\Models\Supplier',
        'fillable' => true,
      ],
      [
        'id'    => 8,
        'title' => $translator->translatePlural('Budget', 'Budgets', 1),
        'type'  => 'dropdown_remote',
        'name'  => 'budget',
        'dbname' => 'budget_id',
        'itemtype' => '\App\Models\Budget',
        'fillable' => true,
      ],
      [
        'id'    => 9,
        'title' => $translator->translate('Order number'),
        'type'  => 'input',
        'name'  => 'order_number',
        'fillable' => true,
      ],
      [
        'id'    => 10,
        'title' => $translator->translate('Immobilization number'),
        'type'  => 'input',
        'name'  => 'immo_number',
        'fillable' => true,
      ],
      [
        'id'    => 11,
        'title' => $translator->translate('Invoice number'),
        'type'  => 'input',
        'name'  => 'bill',
        'fillable' => true,
      ],
      [
        'id'    => 12,
        'title' => $translator->translate('Delivery form'),
        'type'  => 'input',
        'name'  => 'delivery_number',
        'fillable' => true,
      ],
      [
        'id'    => 13,
        'title' => $translator->translate('Value'),
        'type'  => 'input',
        'name'  => 'value',
        'fillable' => true,
      ],
      [
        'id'    => 14,
        'title' => $translator->translate('Warranty extension value'),
        'type'  => 'input',
        'name'  => 'warranty_value',
        'fillable' => true,
      ],
      [
        'id'    => 15,
        'title' => $translator->translate('Amortization type'),
        'type'  => 'dropdown',
        'name'  => 'sink_type',
        'dbname'  => 'sink_type',
        'values' => self::getAmortType(),
        'fillable' => true,
      ],
      [
        'id'    => 16,
        'title' => $translator->translate('Amortization duration'),
        'type'  => 'dropdown',
        'name'  => 'sink_time',
        'dbname'  => 'sink_time',
        'values' => self::getNumberArray(0, 15, 1, [], 'year'),
        'fillable' => true,
      ],
      [
        'id'    => 17,
        'title' => $translator->translate('Amortization coefficient'),
        'type'  => 'input',
        'name'  => 'sink_coeff',
        'fillable' => true,
      ],
      [
        'id'    => 18,
        'title' => $translator->translatePlural('Business criticity', 'Business criticities', 1),
        'type'  => 'dropdown_remote',
        'name'  => 'businesscriticity',
        'dbname' => 'businesscriticity_id',
        'itemtype' => '\App\Models\Businesscriticity',
        'fillable' => true,
      ],
      [
        'id'    => 19,
        'title' => $translator->translate('Comments'),
        'type'  => 'textarea',
        'name'  => 'comment',
        'fillable' => true,
      ],
      [
        'id'    => 20,
        'title' => $translator->translate('Start date of warranty'),
        'type'  => 'date',
        'name'  => 'warranty_date',
        'fillable' => true,
      ],
      [
        'id'    => 21,
        'title' => $translator->translate('Warranty duration'),
        'type'  => 'dropdown',
        'name'  => 'warranty_duration',
        'dbname'  => 'warranty_duration',
        'values' => self::getNumberArray(0, 120, 1, ['-1' => $translator->translate('Lifelong')], 'month'),
        'fillable' => true,
      ],
      [
        'id'    => 22,
        'title' => $translator->translate('Warranty information'),
        'type'  => 'input',
        'name'  => 'warranty_info',
        'fillable' => true,
      ],
    ];
  }

  public static function getAmortType()
  {
    global $translator;
    return [
      0 => [
        'title' => '',
      ],
      1 => [
        'title' => $translator->translate('Decreasing'),
      ],
      2 => [
        'title' => $translator->translate('Linear'),
      ],
    ];
  }

  public static function getRelatedPages($rootUrl)
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Rack', 'Racks', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Item', 'Items', 2),
        'icon' => 'desktop',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Analysis impact'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Management'),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/infocom',
      ],
      [
        'title' => $translator->translatePlural('Contract', 'Contract', 2),
        'icon' => 'file signature',
        'link' => $rootUrl . '/contracts',
      ],
      [
        'title' => $translator->translatePlural('Document', 'Documents', 2),
        'icon' => 'file',
        'link' => $rootUrl . '/documents',
      ],
      [
        'title' => $translator->translate('ITIL'),
        'icon' => 'hands helping',
        'link' => $rootUrl . '/itil',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
