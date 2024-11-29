<?php

namespace App\Models\Definitions;

class Ipnetwork
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
        'id'    => 11,
        'title' => $translator->translatePlural('IP address', 'IP addresses', 1),
        'type'  => 'input',
        'name'  => 'address',
        'fillable' => true,
      ],
      [
        'id'    => 12,
        'title' => $translator->translatePlural('Subnet mask', 'Subnet masks', 1),
        'type'  => 'input',
        'name'  => 'netmask',
        'fillable' => true,
      ],
      [
        'id'    => 17,
        'title' => $translator->translate('Gateway'),
        'type'  => 'input',
        'name'  => 'gateway',
        'fillable' => true,
      ],
      [
        'id'    => 18,
        'title' => $translator->translate('Addressable network'),
        'type'  => 'boolean',
        'name'  => 'addressable',
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
    ];
  }

  public static function getRelatedPages($rootUrl)
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('IP network', 'IP networks', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('VLAN', 'VLANs', 1),
        'icon' => 'edit',
        'link' => $rootUrl . '/vlans',
      ],
      [
        'title' => $translator->translatePlural('IP address', 'IP addresses', 2),
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
