<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Link extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Link';
  protected $titles = ['External link', 'External links'];
  protected $icon = 'edit';

  protected $appends = [
    'entity',
  ];

  protected $visible = [
    'entity',
  ];

  protected $with = [
    'entity:id,name,completename',
  ];

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }
}
