<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consumable extends Common
{
  protected $definition = '\App\Models\Definitions\Consumable';
  protected $titles = ['Consumable', 'Consumables'];
  protected $icon = 'box open';

  protected $appends = [
    'entity',
    'date_in',
    'date_out',
  ];

  protected $visible = [
    'entity',
    'date_in',
    'date_out',
  ];

  protected $with = [
    'entity:id,name,completename',
  ];


  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }
}
