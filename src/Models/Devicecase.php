<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Devicecase extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicecase';
  protected $titles = ['Case', 'Cases'];
  protected $icon = 'edit';

  protected $appends = [
    'manufacturer',
    'type',
    'model',
    'entity',
    'items',
  ];

  protected $visible = [
    'manufacturer',
    'type',
    'model',
    'entity',
    'documents',
    'items',
  ];

  protected $with = [
    'manufacturer:id,name',
    'type:id,name',
    'model:id,name',
    'entity:id,name,completename',
    'documents',
    'items',
  ];

  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Manufacturer');
  }

  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Devicecasetype', 'devicecasetype_id');
  }

  public function model(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Devicecasemodel', 'devicecasemodel_id');
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

  public function items(): HasMany
  {
    return $this->hasMany('\App\Models\ItemDevicecase', 'devicecase_id');
  }
}
