<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tasktemplate extends Common
{
  protected $definition = '\App\Models\Definitions\Tasktemplate';
  protected $titles = ['Task template', 'Task templates'];
  protected $icon = 'edit';
  protected $hasEntityField = false;

  protected $appends = [
    'category',
    'users',
    'groups',
  ];

  protected $visible = [
    'category',
    'users',
    'groups',
  ];

  protected $with = [
    'category:id,name',
    'users:id,name,firstname,lastname',
    'groups:id,name,completename',
  ];

  public function category(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Taskcategory');
  }

  public function users(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id_tech');
  }

  public function groups(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Group', 'group_id_tech');
  }
}
