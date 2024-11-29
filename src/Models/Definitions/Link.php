<?php

namespace App\Models\Definitions;

class Link
{
  public static function getDefinition()
  {
    global $translator;
    return [
      [
        'id'    => 101,
        'title' => $translator->translate('Valid tags'),
        'type'  => 'description',
        'name'  => 'description',
        'values' => self::getValidTags(),
        'fillable' => true,
      ],
      [
        'id'    => 1,
        'title' => $translator->translate('Name'),
        'type'  => 'input',
        'name'  => 'name',
        'fillable' => true,
      ],
      [
        'id'    => 3,
        'title' => $translator->translate('Link or filename'),
        'type'  => 'input',
        'name'  => 'link',
        'fillable' => true,
      ],
      [
        'id'    => 103,
        'title' => $translator->translate('Open in a new window'),
        'type'  => 'boolean',
        'name'  => 'open_window',
        'fillable' => true,
      ],
      [
        'id'    => 104,
        'title' => $translator->translate('File content'),
        'type'  => 'textarea',
        'name'  => 'data',
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

  public static function getValidTags()
  {
    global $translator;

    $tags = ['[LOGIN]', '[ID]', '[NAME]', '[LOCATION]', '[LOCATIONID]', '[IP]',
      '[MAC]', '[NETWORK]', '[DOMAIN]', '[SERIAL]', '[OTHERSERIAL]',
      '[USER]', '[GROUP]', '[REALNAME]', '[FIRSTNAME]'
    ];

    $ret = '';

    $count = count($tags);
    $i = 0;

    foreach ($tags as $tag)
    {
      $ret = $ret . $tag;
      $ret = $ret . "&nbsp;";
      $i++;
      if (($i % 8 == 0) && ($count > 1))
      {
        $ret = $ret . "<br>";
      }
    }

    $ret = $ret . "<br>" . $translator->translate('or') . "<br>[FIELD:<i>" .
           $translator->translate('field name in DB') . "</i>] (" . $translator->translate('Example:') .
           " [FIELD:name], [FIELD:content], ...)";

    return $ret;
  }

  public static function getRelatedPages($rootUrl)
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('External link', 'External links', 1),
        'icon' => 'home',
        'link' => $rootUrl,
      ],
      [
        'title' => $translator->translatePlural('Associated item type', 'Associated item types', 2),
        'icon' => 'desktop',
        'link' => $rootUrl . '/associateditemtypes',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => $rootUrl . '/history',
      ],
    ];
  }
}
