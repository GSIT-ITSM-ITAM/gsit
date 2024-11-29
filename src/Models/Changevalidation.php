<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Changevalidation extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Changevalidation';
  protected $titles = ['Change validation', 'Change validations'];
  protected $icon = 'edit';

  protected $appends = [
    'entity',
    'usersrequester',
    'uservalidate',
  ];

  protected $visible = [
    'entity',
    'usersrequester',
    'uservalidate',
  ];

  protected $with = [
    'entity:id,name,completename',
    'usersrequester:id,name,firstname,lastname',
    'uservalidate:id,name,firstname,lastname',
  ];


  public function entity(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Entity');
  }

  public function usersrequester(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id');
  }

  public function uservalidate(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User', 'user_id_validate');
  }
}
