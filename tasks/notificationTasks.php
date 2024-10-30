<?php

namespace Tasks;

use Crunz\Schedule;

$schedule = new Schedule();
$task = $schedule->run(function ()
{
  \Tasks\Myapp::loadCapsule();

  print_r(\App\v1\Controllers\Ticket::computePriority(2, 5));
  // $ticket = \App\Models\Ticket::find(570179);

  echo "notifications\n";
  // Do some cool stuff in here
});

$task
  ->everyMinute()
  ->description('Run actions in the queue');

return $schedule;
