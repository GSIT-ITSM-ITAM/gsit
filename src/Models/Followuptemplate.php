<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Followuptemplate extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Followuptemplate';
  protected $titles = ['Followup template', 'Followup templates'];
  protected $icon = 'edit';

  protected $appends = [
    'source',
    'entity',
  ];

  protected $visible = [
    'source',
    'entity',
  ];

  protected $with = [
    'source:id,name',
    'entity:id,name,completename',
  ];

  public function source(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Requesttype', 'requesttype_id');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }
}
