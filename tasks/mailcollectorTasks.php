<?php

// tasks/backupTasks.php

use Crunz\Schedule;

$schedule = new Schedule();

$task = $schedule->run(function ()
{
  \Tasks\Myapp::loadCapsule();

  $mailcollector = new \App\v1\Controllers\Mailcollector();
  $createdTickets = $mailcollector->collect();
  echo 'Tickets created: ' . $createdTickets;
  echo "\n";
});

$task
  ->everyMinute()
  ->description('Run actions in the queue');

return $schedule;
