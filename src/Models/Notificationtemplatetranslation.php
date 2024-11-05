<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notificationtemplatetranslation extends Common
{
  protected $definition = '\App\Models\Definitions\Notificationtemplatetranslation';
  protected $titles = ['Notification template translation', 'Notification template translations'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
