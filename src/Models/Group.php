<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Group extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Group';
  protected $titles = ['Group', 'Groups'];
  protected $icon = 'users';

  protected $appends = [
    'entity',
    'notes',
    'completename',
  ];

  protected $visible = [
    'entity',
    'notes',
    'completename',
    'parents',
  ];

  protected $with = [
    'entity:id,name,completename',
    'notes:id',
    'parents:id,name,group_id,entity_id',
  ];

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
      $items = \App\Models\Group::whereIn('id', $itemsId)->orderBy('treepath');
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

  public function parents(): HasMany
  {
    return $this->hasMany('\App\Models\Group', 'group_id');
  }

  public function child(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group', 'group_id');
  }
}
