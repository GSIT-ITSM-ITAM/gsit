<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Softwarelicensetype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Softwarelicensetype';
  protected $titles = ['License type', 'License types'];
  protected $icon = 'edit';

  protected $appends = [
    'softwarelicensetype',
    'entity',
  ];

  protected $visible = [
    'softwarelicensetype',
    'entity',
  ];

  protected $with = [
    'softwarelicensetype:id,name',
    'entity:id,name,completename',
  ];

  public function softwarelicensetype(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Softwarelicensetype');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }
}
