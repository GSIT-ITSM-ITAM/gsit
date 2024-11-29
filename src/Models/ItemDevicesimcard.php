<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ItemDevicesimcard extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\ItemDevicesimcard';
  protected $titles = ['Simcard', 'Simcards'];
  protected $icon = 'sim card';

  protected $table = 'item_devicesimcard';

  protected $appends = [
    'state',
    'location',
    'user',
    'group',
    'entity',
    'infocom',
  ];

  protected $visible = [
    'state',
    'location',
    'user',
    'group',
    'entity',
    'documents',
    'contracts',
    'infocom',
  ];

  protected $with = [
    'state:id,name',
    'location:id,name',
    'user:id,name,firstname,lastname',
    'group:id,name,completename',
    'entity:id,name,completename',
    'documents',
    'contracts:id,name',
    'infocom',
  ];


  public function state(): BelongsTo
  {
    return $this->belongsTo('\App\Models\State');
  }

  public function location(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Location');
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User');
  }

  public function group(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group');
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

  public function contracts(): MorphToMany
  {
    return $this->morphToMany(
      '\App\Models\Contract',
      'item',
      'contract_item'
    )->withPivot(
      'contract_id',
    );
  }

  public function infocom(): MorphMany
  {
    return $this->morphMany(
      '\App\Models\Infocom',
      'item',
    );
  }
}
