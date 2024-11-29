<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Infocom extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Infocom';
  protected $titles = ['Infocom', 'Infocoms'];
  protected $icon = 'edit';

  protected $appends = [
    'entity',
    'supplier',
    'budget',
    'businesscriticity',
  ];

  protected $visible = [
    'entity',
    'supplier',
    'budget',
    'businesscriticity',
  ];

  protected $with = [
    'entity:id,name,completename',
    'supplier:id,name',
    'budget:id,name',
    'businesscriticity:id,name',
  ];

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function supplier(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Supplier', 'supplier_id');
  }

  public function budget(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Budget', 'budget_id');
  }

  public function businesscriticity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Businesscriticity', 'businesscriticity_id');
  }
}
