<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solution extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Solution';
  protected $titles = ['Solution', 'Solutions'];
  protected $icon = 'hands helping';
  protected $hasEntityField = false;

  protected $appends = [
    'user',
    'id',
  ];

  protected $visible = [
    'user',
    'id',
  ];

  protected $with = [
    'user',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User');
  }
}
