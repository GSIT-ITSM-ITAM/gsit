<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Followup extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Followup';
  protected $titles = ['Followup', 'Followups'];
  protected $icon = 'hands helping';
  protected $hasEntityField = false;

  protected $appends = [
    'user',
    'id',
    'content',
  ];

  protected $visible = [
    'user',
    'id',
    'content',
  ];

  protected $with = [
    'user',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User');
  }
}
