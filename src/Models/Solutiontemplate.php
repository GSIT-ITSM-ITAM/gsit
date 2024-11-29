<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solutiontemplate extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Solutiontemplate';
  protected $titles = ['Solution template', 'Solution templates'];
  protected $icon = 'edit';

  protected $appends = [
    'types',
    'entity',
  ];

  protected $visible = [
    'types',
    'entity',
  ];

  protected $with = [
    'types:id,name',
    'entity:id,name,completename',
  ];

  public function types(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Solutiontype', 'solutiontype_id');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }
}
