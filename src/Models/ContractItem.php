<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ContractItem extends Common
{
  protected $definition = '\App\Models\Definitions\ContractItem';
  protected $titles = ['Contract Item', 'Contract Items'];
  protected $icon = 'box open';
  protected $table = 'contract_item';
  protected $hasEntityField = false;
}
