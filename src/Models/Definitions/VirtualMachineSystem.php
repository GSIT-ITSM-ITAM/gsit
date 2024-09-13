<?php

namespace App\Models\Definitions;

class VirtualMachineSystem
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
      ],
      [
        'id'    => 16,
        'title' => $translator->translate('Comments'),
        'type'  => 'textarea',
        'name'  => 'comment',
      ],
      [
         'id'    => 19,
         'title' => $translator->translate('Last update'),
         'type'  => 'datetime',
         'name'  => 'date_mod',
         'readonly'  => 'readonly',
      ],
      [
         'id'    => 121,
         'title' => $translator->translate('Creation date'),
         'type'  => 'datetime',
         'name'  => 'date_creation',
         'readonly'  => 'readonly',
      ],
    ];
  }

  public static function getRelatedPages()
  {
    global $translator;
    return [
      [
        'title' => $translator->translatePlural('Virtualization model', 'Virtualization models', 1),
        'icon' => 'caret square down outline',
        'link' => '',
      ],
      [
        'title' => $translator->translate('Historical'),
        'icon' => 'history',
        'link' => '',
      ],
    ];
  }
}
