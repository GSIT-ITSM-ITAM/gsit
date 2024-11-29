<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Domainrecord extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Domainrecord';
  protected $titles = ['Record', 'Records'];
  protected $icon = 'edit';

  protected $appends = [
    'entity',
    'type',
  ];

  protected $visible = [
    'entity',
    'type',
  ];

  protected $with = [
    'entity:id,name,completename',
    'type:id,name',
  ];

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function type(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Domainrecordtype', 'domainrecordtype_id');
  }
}
