<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notepad extends Common
{
  protected $definition = '\App\Models\Definitions\Notepad';
  protected $titles = ['Notepad', 'Notepads'];
  protected $icon = 'virus slash';
  protected $hasEntityField = false;

  protected $appends = [
  ];

  protected $visible = [
    'user',
    'userlastupdater',
  ];

  protected $with = [
    'user:id,name,firstname,lastname',
    'userlastupdater:id,name,firstname,lastname',
  ];

  protected $fillable = [
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User');
  }

  public function userlastupdater(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id_lastupdater');
  }
}
