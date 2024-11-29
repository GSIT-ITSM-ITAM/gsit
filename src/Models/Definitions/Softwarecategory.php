<?php

namespace App\Models\Definitions;

class Softwarecategory
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
        'title' => $translator->translate('As child of'),
        'type'  => 'dropdown_remote',
        'name'  => 'category',
        'dbname' => 'softwarecategory_id',
        'itemtype' => '\App\Models\Softwarecategory',
        'fillable' => true,
      ],
      [
        'id'    => 16,
        'title' => $translator->translate('Comments'),
        'type'  => 'textarea',
        'name'  => 'comment',
        'fillable' => true,
      ],
    ];
  }

  public static function getRelatedPages($rootUrl)
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Software category', 'Software categories', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Software category', 'Software categories', 2),
        'icon' => 'edit',
        'link' => $rootUrl . '/softwarecategories',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
