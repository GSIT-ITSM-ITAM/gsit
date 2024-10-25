<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pdutype extends Common
{
  use SoftDeletes;

  protected $definition = '\App\Models\Definitions\Pdutype';
  protected $titles = ['PDU type', 'PDU types'];
  protected $icon = 'edit';
  protected $hasEntityField = false;
}
