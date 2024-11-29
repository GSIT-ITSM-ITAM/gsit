<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rssfeed extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Rssfeed';
  protected $titles = ['RSS feed', 'RSS feed'];
  protected $icon = 'rss';
  protected $hasEntityField = false;

  protected $appends = [
    'user',
  ];

  protected $visible = [
    'user',
  ];

  protected $with = [
    'user:id,name,firstname,lastname',
  ];


  public function user(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User');
  }
}
