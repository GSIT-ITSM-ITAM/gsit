<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Location';
  protected $titles = ['Location', 'Locations'];
  protected $icon = 'edit';

  protected $appends = [
    'location',
    'entity',
    'completename',
  ];

  protected $visible = [
    'location',
    'entity',
    'completename',
  ];

  protected $with = [
    'location:id,name',
    'entity:id,name',
  ];

  protected static function booted(): void
  {
    parent::booted();

    static::created(function ($model)
    {
      // Manage tree
      $currItem = (new self())->find($model->id);
      $currItem->treepath = sprintf("%05d", $currItem->id);
      if ($currItem->location_id > 0)
      {
        $parentItem = (new self())->find($currItem->location_id);
        $currItem->treepath = $parentItem->treepath . $currItem->treepath;
      }
      $currItem->name = 'YOLO';
      $currItem->save();
    });
  }

  public function getCompletenameAttribute()
  {
    $itemsId = str_split($this->treepath, 5);
    array_pop($itemsId);
    foreach ($itemsId as $key => $value)
    {
      $itemsId[$key] = (int) $value;
    }
    $items = \App\Models\Location::whereIn('id', $itemsId)->orderBy('treepath');
    $names = [];
    foreach ($items as $item)
    {
      $names[] = $item->name;
    }
    $names[] = $this->name;
    return implode(' > ', $names);
  }

  public function location(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Location');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }
}
