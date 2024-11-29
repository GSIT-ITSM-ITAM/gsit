<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slm extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Slm';
  protected $titles = ['Service level', 'Service levels'];
  protected $icon = 'edit';

  protected $appends = [
    'calendar',
    'entity',
    'slas',
    'olas',
  ];

  protected $visible = [
    'calendar',
    'entity',
    'slas',
    'olas',
  ];

  protected $with = [
    'calendar:id,name',
    'entity:id,name,completename',
    'slas',
    'olas',
  ];

  public function calendar(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Calendar');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function slas(): HasMany
  {
    return $this->hasMany('\App\Models\Sla', 'slm_id');
  }

  public function olas(): HasMany
  {
    return $this->hasMany('\App\Models\Ola', 'slm_id');
  }
}
