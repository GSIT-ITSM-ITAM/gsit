<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Netpoint extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Netpoint';
  protected $titles = ['Network outlet', 'Network outlets'];
  protected $icon = 'edit';

  protected $appends = [
    'location',
    'entity',
  ];

  protected $visible = [
    'location',
    'entity',
  ];

  protected $with = [
    'location:id,name',
    'entity:id,name,completename',
  ];

  public function location(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Location');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }
}
