<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Location extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Location';
  protected $titles = ['Location', 'Locations'];
  protected $icon = 'edit';
  protected $tree = true;

  protected $appends = [
    'location',
    'entity',
    'completename',
  ];

  protected $visible = [
    'location',
    'entity',
    'documents',
    'completename',
  ];

  protected $with = [
    'location:id,name',
    'entity:id,name,completename',
    'documents:id,name',
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
    $names = [];
    if ($this->treepath != null)
    {
      $itemsId = str_split($this->treepath, 5);
      array_pop($itemsId);
      foreach ($itemsId as $key => $value)
      {
        $itemsId[$key] = (int) $value;
      }
      $items = \App\Models\Location::whereIn('id', $itemsId)->orderBy('treepath');
      foreach ($items as $item)
      {
        $names[] = $item->name;
      }
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

  public function documents(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Document',
      'item',
      'document_item'
    )->withPivot(
      'document_id',
      'updated_at',
    );
  }
}
