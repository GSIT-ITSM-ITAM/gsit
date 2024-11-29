<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class News extends Common
{
  protected $table = 'glpi_plugin_news_alerts';
  protected $definition = '\App\Models\Definitions\News';
  protected $titles = ['Alert', 'Alerts'];
  protected $icon = 'bell';

  protected $appends = [
    // 'user',
  ];

  protected $visible = [
    // 'user',
  ];

  protected $with = [
    // 'user:id,name,firstname,lastname',
  ];

  // public function user(): BelongsTo
  // {
  //   return $this->belongsTo('\App\Models\User', 'users_id');
  // }
}
