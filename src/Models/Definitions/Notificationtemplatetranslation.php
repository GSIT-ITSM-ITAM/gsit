<?php

namespace App\Models\Definitions;

class Notificationtemplatetranslation
{
  public static function getDefinition()
  {
    global $translator;
    return [
      [
        'id'            => 1,
        'title'         => $translator->translate('Language'),
        'type'          => 'input',
        'name'          => 'language',
        'fillable' => true,
      ],
      [
        'id'            => 2,
        'title'         => $translator->translate('Subject'),
        'type'          => 'input',
        'name'          => 'subject',
        'fillable' => true,
      ],
      [
        'id'            => 3,
        'title'         => $translator->translate('Text format'),
        'type'          => 'textarea',
        'name'          => 'content_text',
        'fillable' => true,
      ],
      [
        'id'            => 4,
        'title'         => $translator->translate('HTML format'),
        'type'          => 'textarea',
        'name'          => 'content_html',
        'fillable' => true,
      ],

    ];
  }


  public static function getRelatedPages($rootUrl)
  {
    global $translator;
    return [
    ];
  }
}
