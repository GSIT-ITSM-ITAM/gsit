<?php

namespace App\Models\Forms;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Answerquestion extends \App\Models\Common
{
  protected $definition = '\App\Models\Definitions\Forms\Answerquestion';
  protected $titles = ['Answerquestion', 'Answerquestions'];
  protected $icon = 'cubes';
  protected $hasEntityField = false;
}
