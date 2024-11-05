<?php

namespace App\Models\Definitions;

class Followup
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
        'id'      => 2,
        'title'   => $translator->translate('ID'),
        'type'    => 'input',
        'name'    => 'id',
        'display' => false,
      ],
      [
        'id'    => 4,
        'title' => $translator->translate('Content'),
        'type'  => 'textarea',
        'name'  => 'content',
        'fillable' => true,
      ],
      [
        'id'    => 5,
        'title' => $translator->translate('Source of followup'),
        'type'  => 'dropdown_remote',
        'name'  => 'source',
        'dbname' => 'requesttype_id',
        'itemtype' => '\App\Models\Requesttype',
        'fillable' => true,
      ],
      [
        'id'    => 6,
        'title' => $translator->translate('Private'),
        'type'  => 'boolean',
        'name'  => 'is_private',
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
      [
        'id'    => 5,
        'title' => $translator->translatePlural('User', 'Users', 1),
        'type'  => 'dropdown_remote',
        'name'  => 'user',
        'dbname' => 'user_id',
        'itemtype' => '\App\Models\User',
        'fillable' => true,
        'relationfields' => ['id', 'name', 'completename'],
      ],


      /*
      $tab[] = [
        'id'   => 'common',
        'name' => __('Characteristics')
      ];

      $tab[] = [
        'id'                => '2',
        'table'             => $this->getTable(),
        'field'             => 'id',
        'name'              => __('ID'),
        'massiveaction'     => false,
        'datatype'          => 'number'
      ];

      if ($DB->fieldExists($this->getTable(), 'product_number'))
      {
      $tab[] = [
        'id'  => '3',
        'table'  => $this->getTable(),
        'field'  => 'product_number',
        'name'   => __('Product number'),
        'autocomplete' => true,
      ];
      }
      // add objectlock search options
      $tab = array_merge($tab, ObjectLock::rawSearchOptionsToAdd(get_class($this)));
      */
    ];
  }
}
