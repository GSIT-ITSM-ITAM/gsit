<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Savedsearch extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Savedsearch';
  protected $titles = ['Saved search', 'Saved searches'];
  protected $icon = 'bookmark';

  protected $appends = [
    'user',
    'entity',
  ];

  protected $visible = [
    'user',
    'entity',
  ];

  protected $with = [
    'user:id,name,firstname,lastname',
    'entity:id,name,completename',
  ];


  public function user(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User');
  }

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }
}
