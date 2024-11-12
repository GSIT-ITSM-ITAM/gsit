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
    'statusname',
  ];

  protected $visible = [
    'user',
    'id',
    'statusname',
  ];

  protected $with = [
    'user',
  ];

  protected static function booted(): void
  {
    parent::booted();

    static::created(function ($model)
    {
      if ($model->item_type == 'App\Models\Ticket')
      {
        $ticket = \App\Models\Ticket::find($model->item_id);
        $ticket->status = 5;
        $ticket->save();
      }
    });
  }

  public function getStatusnameAttribute()
  {
    global $translator;
    switch ($this->status)
    {
      case 1:
          return $translator->translate('Not subject to approval');

      case 2:
          return $translator->translate('Waiting for approval');

      case 3:
          return $translator->translate('Granted');

      case 4:
          return $translator->translate('Refused');
    }
    return '';
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User');
  }
}
