<?php

namespace App\Models\Rules;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Ticket extends \App\Models\Common
{
  protected $table = 'rules';
  protected $definition = '\App\Models\Definitions\Rule';
  protected $titles = ['Business rules for tickets', 'Business rules for tickets'];
  protected $icon = 'magic';

  protected $appends = [
  ];

  protected $visible = [
  ];

  protected $with = [
  ];

  protected static function booted(): void
  {
    parent::booted();

    static::addGlobalScope('ticketfilter', function (Builder $builder)
    {
      $builder->where('sub_type', 'RuleTicket');
    });
  }
}
