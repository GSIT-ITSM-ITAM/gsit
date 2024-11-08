<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Devicefirmware extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicefirmware';
  protected $titles = ['Firmware', 'Firmware'];
  protected $icon = 'edit';

  protected $table = "devicefirmwares";

  protected $appends = [
    'manufacturer',
    'type',
    'model',
    'entity',
  ];

  protected $visible = [
    'manufacturer',
    'type',
    'model',
    'entity',
    'documents',
  ];

  protected $with = [
    'manufacturer:id,name',
    'type:id,name',
    'model:id,name',
    'entity:id,name',
    'documents:id,name',
  ];

  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Manufacturer');
  }

  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Devicefirmwaretype', 'devicefirmwaretype_id');
  }

  public function model(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Devicefirmwaremodel', 'devicefirmwaremodel_id');
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
