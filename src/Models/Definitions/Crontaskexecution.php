<?php

namespace App\Models\Definitions;

class Crontaskexecution
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
        'title' => $translator->translatePlural('Execution', 'Executions', 1),
        'icon' => 'cogs',
        'link' => $rootUrl,
      ],
    ];
  }
}
