<?php

namespace App\Models\Definitions;

class Networkname
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
        'id'    => 12,
        'title' => $translator->translatePlural('Internet domain', 'Internet domains', 1),
        'type'  => 'dropdown_remote',
        'name'  => 'fqdn',
        'dbname' => 'fqdn_id',
        'itemtype' => '\App\Models\Fqdn',
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
          'id'                 => '13',
          'table'              => 'glpi_ipaddresses',
          'field'              => 'name',
          'name'               => IPAddress::getTypeName(1),
          'joinparams'         => [
             'jointype'           => 'itemtype_item'
          ],
          'forcegroupby'       => true,
          'massiveaction'      => false,
          'datatype'           => 'dropdown'
       ];

       $tab[] = [
          'id'                 => '20',
          'table'              => $this->getTable(),
          'field'              => 'itemtype',
          'name'               => _n('Type', 'Types', 1),
          'datatype'           => 'itemtype',
          'massiveaction'      => false
       ];

       $tab[] = [
          'id'                 => '21',
          'table'              => $this->getTable(),
          'field'              => 'items_id',
          'name'               => __('ID'),
          'datatype'           => 'integer',
          'massiveaction'      => false
       ];
      */
    ];
  }

  public static function getRelatedPages($rootUrl)
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Network name', 'Network names', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Network alias', 'Network aliases', 1),
        'icon' => 'caret square down outline',
        'link' => $rootUrl . '/networkalias',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
