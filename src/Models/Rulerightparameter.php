<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rulerightparameter extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Rulerightparameter';
  protected $titles = ['LDAP criterion', 'LDAP criteria'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
