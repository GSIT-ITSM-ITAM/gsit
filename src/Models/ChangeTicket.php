<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChangeTicket extends Common
{
  protected $definition = '\App\Models\Definitions\ChangeTicket';
  protected $titles = ['Change Ticket', 'Change Ticket'];
  protected $icon = 'edit';
  protected $table = 'change_ticket';
  protected $hasEntityField = false;
}
