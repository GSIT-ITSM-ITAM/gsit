<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Common
{
  protected $definition = '\App\Models\Definitions\Reservation';
  protected $titles = ['Reservation', 'Reservations'];
  protected $icon = 'edit';
  protected $hasEntityField = false;

  protected $appends = [
    'user',
  ];

  protected $visible = [
    'user',
  ];

  protected $with = [
    'user',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id');
  }
}
