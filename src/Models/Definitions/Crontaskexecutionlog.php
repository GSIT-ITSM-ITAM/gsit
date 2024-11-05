<?php

namespace App\Models\Definitions;

class Crontaskexecutionlog
{
  public static function getDefinition()
  {
    global $translator;
    return [];
  }

  public static function getRelatedPages($rootUrl)
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Execution log', 'Execution logs', 1),
        'icon' => 'list ul',
        'link' => $rootUrl,
      ],
    ];
  }
}
