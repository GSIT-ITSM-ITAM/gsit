<?php

namespace App\Models\Definitions;

class Computerantivirus
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
        'id'    => 2,
        'title' => $translator->translatePlural('Manufacturer', 'Manufacturers', 1),
        'type'  => 'dropdown_remote',
        'name'  => 'manufacturer',
        'dbname' => 'manufacturer_id',
        'itemtype' => '\App\Models\Manufacturer',
        'fillable' => true,
      ],
      [
        'id'    => 3,
        'title' => $translator->translate('Antivirus version'),
        'type'  => 'input',
        'name'  => 'antivirus_version',
        'fillable' => true,
      ],
      [
        'id'    => 4,
        'title' => $translator->translate('Expiration date'),
        'type'  => 'date',
        'name'  => 'date_expiration',
        'fillable' => true,
      ],
      [
        'id'    => 5,
        'title' => $translator->translate('Signature database version'),
        'type'  => 'input',
        'name'  => 'signature_version',
        'fillable' => true,
      ],
      [
        'id'    => 6,
        'title' => $translator->translate('Active'),
        'type'  => 'boolean',
        'name'  => 'is_active',
        'fillable' => true,
      ],
      [
        'id'    => 7,
        'title' => $translator->translate('Up to date'),
        'type'  => 'boolean',
        'name'  => 'is_uptodate',
        'fillable' => true,
      ],
    ];
  }
  public static function getRelatedPages($rootUrl)
  {
    global $translator;
    return [];
  }
}
