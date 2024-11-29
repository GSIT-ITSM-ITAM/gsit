<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservationitem extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Reservationitem';
  protected $titles = ['Reservation item', 'Reservation items'];
  protected $icon = 'edit';
  protected $table = 'reservationitems';

  protected $appends = [
    'entity',
    'reservations',
  ];

  protected $visible = [
    'entity',
    'reservations',
  ];

  protected $with = [
    'entity:id,name,completename',
    'reservations',
  ];


  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function reservations(): HasMany
  {
    return $this->hasMany('\App\Models\Reservation');
  }
}
