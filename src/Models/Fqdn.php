<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fqdn extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Fqdn';
  protected $titles = ['Internet domain', 'Internet domains'];
  protected $icon = 'edit';

  protected $appends = [
    'entity',
    'alias',
  ];

  protected $visible = [
    'entity',
    'alias',
  ];

  protected $with = [
    'entity:id,name,completename',
    'alias',
  ];

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function alias(): HasMany
  {
    return $this->hasMany('\App\Models\Networkalias', 'fqdn_id');
  }
}
