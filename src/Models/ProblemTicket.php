<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProblemTicket extends Common
{
  protected $definition = '\App\Models\Definitions\ProblemTicket';
  protected $titles = ['Problem Ticket', 'Problem Ticket'];
  protected $icon = 'edit';
  protected $table = 'problem_ticket';
  protected $hasEntityField = false;
}
