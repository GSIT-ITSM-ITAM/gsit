<?php

namespace App\Models;

class Displaypreference extends Common
{
  protected $hasEntityField = false;

  /**
   * Get display preference for a user for an itemtype
   *
   * @param string  $itemtype  itemtype
   * @param integer $user_id   user ID
   *
   * @return array
  **/
  public static function getForTypeUser($itemtype, $user_id)
  {
    $items = \App\Models\Displaypreference::where('itemtype', $itemtype)
      ->where('user_id', $user_id)->get();
    if (count($items) == 0)
    {
      $items = \App\Models\Displaypreference::where('itemtype', $itemtype)
        ->where('user_id', 0)->get();
    }

    $default_prefs = [];
    foreach ($items as $myItem)
    {
      $default_prefs[] = $myItem->num;
    }
    return $default_prefs;
  }
}
