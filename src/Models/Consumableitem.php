<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Consumableitem extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Consumableitem';
  protected $titles = ['Consumable', 'Consumables'];
  protected $icon = 'box open';

  protected $appends = [
    'type',
    'manufacturer',
    'groupstech',
    'userstech',
    'location',
    'entity',
    'infocom',
  ];

  protected $visible = [
    'type',
    'manufacturer',
    'groupstech',
    'userstech',
    'location',
    'entity',
    'notes',
    'documents',
    'consumables',
    'infocom',
  ];

  protected $with = [
    'type:id,name',
    'manufacturer:id,name',
    'groupstech:id,name,completename',
    'userstech:id,name,firstname,lastname',
    'location:id,name',
    'entity:id,name,completename',
    'notes:id',
    'documents:id,name',
    'consumables:id',
    'infocom',
  ];


  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Consumableitemtype', 'consumableitemtype_id');
  }

  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Manufacturer');
  }

  public function groupstech(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group', 'group_id_tech');
  }

  public function userstech(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id_tech');
  }

  public function location(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Location');
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

  public function consumables(): HasMany
  {
    return $this->hasMany('\App\Models\Consumable', 'consumableitem_id');
  }

  public function infocom(): MorphMany
  {
    return $this->morphMany(
      '\App\Models\Infocom',
      'item',
    );
  }
}
