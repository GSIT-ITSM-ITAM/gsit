<?php

declare(strict_types=1);

namespace Tests\unit\v1\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass('\App\v1\Controllers\Ticket')]
#[CoversClass('\App\v1\Controllers\Common')]
#[CoversClass('\App\Models\Ticket')]
#[CoversClass('\App\Models\Common')]
#[UsesClass('\App\Models\Definitions\Category')]
#[UsesClass('\App\Models\Definitions\Certificate')]
#[UsesClass('\App\Models\Definitions\Change')]
#[UsesClass('\App\Models\Definitions\Document')]
#[UsesClass('\App\Models\Definitions\Entity')]
#[UsesClass('\App\Models\Definitions\Followup')]
#[UsesClass('\App\Models\Definitions\Group')]
#[UsesClass('\App\Models\Definitions\ItemTicket')]
#[UsesClass('\App\Models\Definitions\Knowbaseitem')]
#[UsesClass('\App\Models\Definitions\Location')]
#[UsesClass('\App\Models\Definitions\Notepad')]
#[UsesClass('\App\Models\Definitions\Notification')]
#[UsesClass('\App\Models\Definitions\Problem')]
#[UsesClass('\App\Models\Definitions\Profile')]
#[UsesClass('\App\Models\Definitions\ProfileUser')]
#[UsesClass('\App\Models\Definitions\ProjecttaskTicket')]
#[UsesClass('\App\Models\Definitions\Solution')]
#[UsesClass('\App\Models\Definitions\Ticket')]
#[UsesClass('\App\Models\Definitions\Ticketcost')]
#[UsesClass('\App\Models\Definitions\Ticketvalidation')]
#[UsesClass('\App\Models\Definitions\User')]
#[UsesClass('\App\Models\Definitions\Usercategory')]
#[UsesClass('\App\Models\Definitions\Usertitle')]
#[UsesClass('\App\Models\Entity')]
#[UsesClass('\App\Models\Followup')]
#[UsesClass('\App\Models\Location')]
#[UsesClass('\App\Models\Profile')]
#[UsesClass('\App\Models\Solution')]
#[UsesClass('\App\Models\User')]
#[UsesClass('\App\v1\Controllers\Notification')]
#[UsesClass('\App\v1\Controllers\Toolbox')]

final class TicketTest extends TestCase
{
  protected $user01Id;
  protected $user02Id;

  protected function setUp(): void
  {
    // create users if not exists
    $user01 = \App\Models\User::where('name', 'test user01')->first();
    if (is_null($user01))
    {
      $user01 = \App\Models\User::create([
        'name' => 'test user01',
      ]);
    }
    $this->user01Id = $user01->id;

    $user02 = \App\Models\User::where('name', 'test user02')->first();
    if (is_null($user02))
    {
      $user02 = \App\Models\User::create([
        'name' => 'test user02',
      ]);
    }
    $this->user02Id = $user02->id;
  }

  public function testCreateTicketTechs()
  {
    $data = (object) [
      'content'     => 'Test',
      'name'        => 'Test',
      'technician'  => $this->user01Id . ',' . $this->user02Id,
      // save=view
      // content=Test+
      // name=Test+
      // type=1
      // status=1
      // category=0
      // location=0
      // urgency=3
      // impact=3
      // priority=3
      // time_to_resolve=
      // requester=
      // requestergroup=
      // watcher=
      // watchergroup=
      // technician=4099%2C4096
      // techniciangroup=      
    ];
    $ctrl = new \App\v1\Controllers\Ticket();
    $ticketId = $ctrl->saveItem($data);

    $this->assertNotNull($ticketId);
    $this->assertNotEquals(0, $ticketId);

    // check
    $ticket = \App\Models\Ticket::find($ticketId);
    $this->assertEquals(2, count($ticket->technician), 'must have 2 technicians on this ticket');
    $users = [];
    foreach ($ticket->technician as $user)
    {
      $users[] = $user->name;
    }
    $this->assertEquals(['test user01', 'test user02'], $users, 'users id not right');
  }
}
