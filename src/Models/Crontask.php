<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Crontask extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Crontask';
  protected $titles = ['Automatic action', 'Automatic actions'];
  protected $icon = 'edit';
  protected $hasEntityField = false;

  public function crontaskexecutions(): HasMany
  {
    return $this->hasMany('App\Models\Crontaskexecution')->orderBy('created_at', 'desc');
  }
}
