<?php

namespace App\Models\Definitions;

class Softwareversion
{
  public static function getDefinition()
  {
    global $translator;
    return [
      [
        'id'    => 10,
        'title' => $translator->translatePlural('Software', 'Softwares', 1),
        'type'  => 'dropdown_remote',
        'name'  => 'software',
        'dbname' => 'software_id',
        'itemtype' => '\App\Models\Software',
        'readonly'  => 'readonly',
      ],
      [
        'id'    => 1,
        'title' => $translator->translate('Name'),
        'type'  => 'input',
        'name'  => 'name',
        'fillable' => true,
      ],
      [
        'id'    => 11,
        'title' => $translator->translatePlural('Operating system', 'Operating systems', 1),
        'type'  => 'dropdown_remote',
        'name'  => 'operatingsystem',
        'dbname' => 'operatingsystem_id',
        'itemtype' => '\App\Models\Operatingsystem',
        'fillable' => true,
      ],
      [
        'id'    => 12,
        'title' => $translator->translate('Status'),
        'type'  => 'dropdown_remote',
        'name'  => 'state',
        'dbname' => 'state_id',
        'itemtype' => '\App\Models\State',
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
    ];
  }

  public static function getRelatedPages($rootUrl)
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('License', 'Licenses', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translate('Summary'),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translatePlural('Installation', 'Installations', 2),
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
