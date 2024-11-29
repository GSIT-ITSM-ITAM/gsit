<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Devicebattery extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicebattery';
  protected $titles = ['Battery', 'Batteries'];
  protected $icon = 'edit';

  protected $appends = [
    'manufacturer',
    'type',
    'entity',
    'items',
  ];

  protected $visible = [
    'manufacturer',
    'type',
    'entity',
    'documents',
    'items',
  ];

  protected $with = [
    'manufacturer:id,name',
    'type:id,name',
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
    return $this->belongsTo('\App\Models\Devicebatterytype', 'devicebatterytype_id');
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
    return $this->hasMany('\App\Models\ItemDevicebattery', 'devicebattery_id');
  }
}
