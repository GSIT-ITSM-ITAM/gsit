<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Contract extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Contract';
  protected $titles = ['Contract', 'Contracts'];
  protected $icon = 'file signature';

  protected $appends = [
    'type',
    'state',
    'entity',
    'notes',
  ];

  protected $visible = [
    'type',
    'state',
    'entity',
    'notes',
    'knowbaseitems',
    'documents',
    'suppliers',
    'costs',
  ];

  protected $with = [
    'type:id,name',
    'state:id,name',
    'entity:id,name,completename',
    'notes:id',
    'knowbaseitems:id,name',
    'documents:id,name',
    'suppliers:id,name',
    'costs:id,name',
  ];

  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Contracttype', 'contracttype_id');
  }

  public function state(): BelongsTo
  {
    return $this->belongsTo('\App\Models\State');
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

  public function suppliers(): BelongsToMany
  {
    return $this->belongsToMany('\App\Models\Supplier');
  }

  public function costs(): HasMany
  {
    return $this->hasMany('App\Models\Contractcost');
  }
}
