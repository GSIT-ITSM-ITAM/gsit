<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProjecttaskTicket extends Common
{
  protected $definition = '\App\Models\Definitions\ProjecttaskTicket';
  protected $titles = ['Project task', 'Project tasks'];
  protected $icon = 'columns';
  protected $table = 'projecttask_ticket';
  protected $hasEntityField = false;
}
