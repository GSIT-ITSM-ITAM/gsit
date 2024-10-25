<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicecontrol extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicecontrol';
  protected $titles = ['Controller', 'Controllers'];
  protected $icon = 'edit';

  protected $appends = [
    'manufacturer',
    'model',
    'interface',
  ];

  protected $visible = [
    'manufacturer',
    'model',
    'interface',
  ];

  protected $with = [
    'manufacturer:id,name',
    'model:id,name',
    'interface:id,name',
  ];

  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Manufacturer');
  }

  public function model(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Devicecontrolmodel', 'devicecontrolmodel_id');
  }

  public function interface(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Interfacetype', 'interfacetype_id');
  }
}
