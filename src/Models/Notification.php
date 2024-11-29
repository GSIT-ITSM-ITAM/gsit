<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Notification';
  protected $titles = ['Notification', 'Notifications'];
  protected $icon = 'edit';

  protected $appends = [
    'entity',
    'templates',
  ];

  protected $visible = [
    'entity',
    'templates',
  ];

  protected $with = [
    'entity:id,name,completename',
  ];

  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function templates(): BelongsToMany
  {
      return $this->belongsToMany('\App\Models\Notificationtemplate')->withPivot('mode');
  }
}
