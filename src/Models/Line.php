<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Line extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Line';
  protected $titles = ['Line', 'Lines'];
  protected $icon = 'phone';

  protected $appends = [
    'location',
    'type',
    'operator',
    'state',
    'user',
    'group',
    'entity',
    'notes',
    'infocom',
  ];

  protected $visible = [
    'location',
    'type',
    'operator',
    'state',
    'user',
    'group',
    'entity',
    'notes',
    'documents',
    'contracts',
    'infocom',
  ];

  protected $with = [
    'location:id,name',
    'type:id,name',
    'operator:id,name',
    'state:id,name',
    'user:id,name,firstname,lastname',
    'group:id,name,completename',
    'entity:id,name,completename',
    'notes:id',
    'documents:id,name',
    'contracts:id,name',
    'infocom',
  ];

  public function location(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Location');
  }

  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Linetype', 'linetype_id');
  }

  public function operator(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Lineoperator', 'lineoperator_id');
  }

  public function state(): BelongsTo
  {
    return $this->belongsTo('\App\Models\State');
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
