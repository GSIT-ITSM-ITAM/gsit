<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devicenetworkcard extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Devicenetworkcard';
  protected $titles = ['Network card', 'Network cards'];
  protected $icon = 'edit';

  protected $appends = [
    'manufacturer',
    'model',
  ];

  protected $visible = [
    'manufacturer',
    'model',
  ];

  protected $with = [
    'manufacturer:id,name',
    'model:id,name',
  ];

  public function manufacturer(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Manufacturer');
  }

  public function model(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Devicenetworkcardmodel');
  }
}