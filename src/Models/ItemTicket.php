<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ItemTicket extends Common
{
  protected $definition = '\App\Models\Definitions\ItemTicket';
  protected $titles = ['Ticket Item', 'Ticket Items'];
  protected $icon = 'edit';
  protected $table = 'item_ticket';
  protected $hasEntityField = false;
}
