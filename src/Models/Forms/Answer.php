<?php

namespace App\Models\Forms;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Answer extends \App\Models\Common
{
  protected $definition = '\App\Models\Definitions\Forms\Answer';
  protected $titles = ['Answer', 'Answers'];
  protected $icon = 'cubes';
  protected $hasEntityField = false;

  public function form(): BelongsTo
  {
    return $this->belongsTo('\App\Models\Forms\Form');
  }

  public function answerquestions(): HasMany
  {
    return $this->hasMany('\App\Models\Forms\Answerquestion');
  }

  public function user(): BelongsTo
  {
    return $this->belongsTo('\App\Models\User');
  }
}
