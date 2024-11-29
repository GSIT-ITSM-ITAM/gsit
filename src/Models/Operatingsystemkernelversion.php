<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Operatingsystemkernelversion extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Operatingsystemkernelversion';
  protected $titles = ['Kernel version', 'Kernel versions'];
  protected $icon = 'edit';
  protected $hasEntityField = false;

  protected $appends = [
    'kernel',
  ];

  protected $visible = [
    'kernel',
  ];

  protected $with = [
    'kernel:id,name',
  ];


  public function kernel(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Operatingsystemkernel', 'operatingsystemkernel_id');
  }
}
