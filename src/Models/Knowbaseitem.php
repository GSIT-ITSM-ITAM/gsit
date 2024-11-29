<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Knowbaseitem extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Knowbaseitem';
  protected $titles = ['Knowledge base item', 'Knowledge base items'];
  protected $icon = 'edit';

  protected $appends = [
    'category',
    'user',
  ];

  protected $visible = [
    'category',
    'user',
  ];

  protected $with = [
    'category:id,name',
    'user:id,name,firstname,lastname',
  ];

  public function category(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Knowbaseitemcategory', 'knowbaseitemcategory_id');
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User');
  }
}
