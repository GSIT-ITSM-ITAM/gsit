<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Calendar extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Calendar';
  protected $titles = ['Calendar', 'Calendars'];
  protected $icon = 'edit';


  protected $appends = [
    'entity',
    'timeranges',
    'holidays',
  ];

  protected $visible = [
    'entity',
    'timeranges',
    'holidays',
  ];

  protected $with = [
    'entity:id,name,completename',
    'timeranges',
    'holidays',
  ];

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function timeranges(): HasMany
  {
    return $this->hasMany('\App\Models\Calendarsegment', 'calendar_id');
  }

  public function holidays(): BelongsToMany
  {
    return $this->belongsToMany('\App\Models\Holiday', 'calendar_holiday', 'calendar_id', 'holiday_id');
  }
}
