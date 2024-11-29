<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Entity extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Entity';
  protected $titles = ['Entity', 'Entities'];
  protected $icon = 'layer group';
  protected $hasEntityField = false;

  protected $appends = [
    'notes',
    'completename',
  ];

  protected $visible = [
    'notes',
    'knowbaseitems',
    'documents',
    'completename',
    'profilesusers',
  ];

  protected $with = [
    'entity:id,name,completename',
    'notes:id',
    'knowbaseitems:id,name',
    'documents:id,name',
    'profilesusers',
  ];

  protected static function booted(): void
  {
    parent::booted();
    static::created(function ($model)
    {
      // Manage tree
      $currItem = (new self())->find($model->id);
      $currItem->treepath = sprintf("%05d", $currItem->id);
      if ($currItem->entity_id > 0)
      {
        $parentItem = self::find($currItem->entity_id);
        $currItem->treepath = $parentItem->treepath . $currItem->treepath;
      }
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
      $items = \App\Models\Entity::whereIn('id', $itemsId)->orderBy('treepath');
      foreach ($items as $item)
      {
        $names[] = $item->name;
      }
    }
    $names[] = $this->name;
    return implode(' > ', $names);
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function notes(): MorphMany
  {
    return $this->morphMany(
      '\App\Models\Notepad',
      'item',
    );
  }

  public function knowbaseitems(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Knowbaseitem',
      'item',
      'knowbaseitem_item'
    )->withPivot(
      'knowbaseitem_id',
    );
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

  public function profilesusers(): HasMany
  {
    return $this->hasMany('\App\Models\ProfileUser', 'entity_id');
  }
}
