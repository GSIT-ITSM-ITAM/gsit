<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Changecost extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Changecost';
  protected $titles = ['Change cost', 'Change costs'];
  protected $icon = 'edit';

  protected $appends = [
    'entity',
    'budget',
  ];

  protected $visible = [
    'entity',
    'budget',
  ];

  protected $with = [
    'entity:id,name,completename',
    'budget:id,name',
  ];


  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function budget(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Budget');
  }
}
