<?php

declare(strict_types=1);

namespace Tests\unit\v1\Controllers;

use PHPUnit\Framework\TestCase;

/**
 * @covers \App\v1\Controllers\Notification
 */
final class NotificationTicketTest extends TestCase
{
  private static $user2Id;
  private static $fields = [
    'name',
    'content',
    'id',
    'entity.id',
    'entity.name',
    'entity.completename',
    'entity.address',
    'entity.country',
    'entity.email',
    'entity.fax',
    'entity.phonenumber',
    'entity.postcode',
    'entity.state',
    'entity.town',
    'entity.website',
    'status',
    'category.id',
    'category.name',
    'category.completename',
    'location.id',
    'location.name',
    'location.completename',
    'actiontime',
    'urgency',
    'impact',
    'priority',
    'date',
    'closedate',
    'time_to_resolve',
    'solvedate',
    'updated_at',
    'usersidlastupdater.id',
    'usersidlastupdater.completename',
    'requester.id',
    'requester.completename',
    'requestergroup.id',
    'requestergroup.name',
    'requestergroup.completename',
    'usersidrecipient.id',
    'usersidrecipient.completename',
    'watcher.id',
    'watcher.completename',
    'watchergroup.id',
    'watchergroup.name',
    'watchergroup.completename',
    'technician.id',
    'technician.completename',
    'techniciangroup.id',
    'techniciangroup.name',
    'techniciangroup.completename',
    'followups.id',
    'followups.content',
    'followups.user.completename',
  ];
  private static $langFields = [
    'lang.name',
    'lang.content',
    'lang.id',
    'lang.entity.id',
    'lang.entity.name',
    'lang.entity.completename',
    'lang.entity.address',
    'lang.entity.country',
    'lang.entity.email',
    'lang.entity.fax',
    'lang.entity.phonenumber',
    'lang.entity.postcode',
    'lang.entity.state',
    'lang.entity.town',
    'lang.entity.website',
    'lang.status',
    'lang.category.id',
    'lang.category.name',
    'lang.category.completename',
    'lang.location.id',
    'lang.location.name',
    'lang.location.completename',
    'lang.actiontime',
    'lang.urgency',
    'lang.impact',
    'lang.priority',
    'lang.date',
    'lang.closedate',
    'lang.time_to_resolve',
    'lang.solvedate',
    'lang.updated_at',
    'lang.usersidlastupdater.id',
    'lang.usersidlastupdater.completename',
    'lang.requester.id',
    'lang.requester.completename',
    'lang.requestergroup.id',
    'lang.requestergroup.name',
    'lang.requestergroup.completename',
    'lang.usersidrecipient.id',
    'lang.usersidrecipient.completename',
    'lang.watcher.id',
    'lang.watcher.completename',
    'lang.watchergroup.id',
    'lang.watchergroup.name',
    'lang.watchergroup.completename',
    'lang.technician.id',
    'lang.technician.completename',
    'lang.techniciangroup.id',
    'lang.techniciangroup.name',
    'lang.techniciangroup.completename',
    'lang.followups.id',
    'lang.followups.content',
    'lang.followups.user.completename',
  ];
  private static $text = "
  name::{{ lang.name }}::||{{ name }}||
  content::{{ lang.content }}::||{{ content }}||
  id::{{ lang.id }}::||{{ id }}||
  entity.id::{{ lang.entity.id }}::||{{ entity.id }}||
  entity.name::{{ lang.entity.name }}::||{{ entity.name }}||
  entity.completename::{{ lang.entity.completename }}::||{{ entity.completename }}||
  entity.address::{{ lang.entity.address }}::||{{ entity.address }}||
  entity.country::{{ lang.entity.country }}::||{{ entity.country }}||
  entity.email::{{ lang.entity.email }}::||{{ entity.email }}||
  entity.fax::{{ lang.entity.fax }}::||{{ entity.fax }}||
  entity.phonenumber::{{ lang.entity.phonenumber }}::||{{ entity.phonenumber }}||
  entity.postcode::{{ lang.entity.postcode }}::||{{ entity.postcode }}||
  entity.state::{{ lang.entity.state }}::||{{ entity.state }}||
  entity.town::{{ lang.entity.town }}::||{{ entity.town }}||
  entity.website::{{ lang.entity.website }}::||{{ entity.website }}||
  status::{{ lang.status }}::||{{ status }}||
  category.id::{{ lang.category.id }}::||{{ category.id }}||
  category.name::{{ lang.category.name }}::||{{ category.name }}||
  category.completename::{{ lang.category.completename }}::||{{ category.completename }}||
  location.id::{{ lang.location.id }}::||{{ location.id }}||
  location.name::{{ lang.location.name }}::||{{ location.name }}||
  location.completename::{{ lang.location.completename }}::||{{ location.completename }}||
  actiontime::{{ lang.actiontime }}::||{{ actiontime }}||
  urgency::{{ lang.urgency }}::||{{ urgency }}||
  impact::{{ lang.impact }}::||{{ impact }}||
  priority::{{ lang.priority }}::||{{ priority }}||
  date::{{ lang.date }}::||{{ date }}||
  closedate::{{ lang.closedate }}::||{{ closedate }}||
  time_to_resolve::{{ lang.time_to_resolve }}::||{{ time_to_resolve }}||
  solvedate::{{ lang.solvedate }}::||{{ solvedate }}||
  updated_at::{{ lang.updated_at }}::||{{ updated_at }}||
  usersidlastupdater.id::{{ lang.usersidlastupdater.id }}::||{{ usersidlastupdater.id }}||
  usersidlastupdater.completename::{{ lang.usersidlastupdater.completename }}::||{{ usersidlastupdater.completename }}||
  for requester
  {% for item in requester %}
    item.id::{{ lang.requester.id }}::||{{ item.id }}||
    item.completename::{{ lang.requester.completename }}::||{{ item.completename }}||
  {% endfor %}
  for requestergroup
  {% for item in requestergroup %}
    item.id::{{ lang.requestergroup.id }}::||{{ item.id }}||
    item.name::{{ lang.requestergroup.name }}::||{{ item.name }}||
    item.completename::{{ lang.requestergroup.completename }}::||{{ item.completename }}||
  {% endfor %}
  usersidrecipient.id::{{ lang.usersidrecipient.id }}::||{{ usersidrecipient.id }}||
  usersidrecipient.completename::{{ lang.usersidrecipient.completename }}::||{{ usersidrecipient.completename }}||
  for watcher
  {% for item in watcher %}
    item.id::{{ lang.watcher.id }}::||{{ item.id }}||
    item.completename::{{ lang.watcher.completename }}::||{{ item.completename }}||
  {% endfor %}
  for watchergroup
  {% for item in watchergroup %}
    item.id::{{ lang.watchergroup.id }}::||{{ item.id }}||
    item.name::{{ lang.watchergroup.name }}::||{{ item.name }}||
    item.completename::{{ lang.watchergroup.completename }}::||{{ item.completename }}||
  {% endfor %}
  for technician
  {% for item in technician %}
    item.id::{{ lang.technician.id }}::||{{ item.id }}||
    item.completename::{{ lang.technician.completename }}::||{{ item.completename }}||
  {% endfor %}
  for techniciangroup
  {% for item in techniciangroup %}
    item.id::{{ lang.techniciangroup.id }}::||{{ item.id }}||
    item.name::{{ lang.techniciangroup.name }}::||{{ item.name }}||
    item.completename::{{ lang.techniciangroup.completename }}::||{{ item.completename }}||
  {% endfor %}
  for followups
  {% for followup in followups %}
    followup.id::{{ lang.followups.id }}::||{{ followup.id }}||
    followup.content::{{ lang.followups.content }}::||{{ followup.content }}||
    followup.user.completename::{{ lang.followups.user.completename }}::||{{ followup.user.completename }}||
  {% endfor %}
  ";
  private static $templateNotification = '';
  private static $ticketId = 0;

  public static function setUpBeforeClass(): void
  {
    // Generate template
    foreach (self::$fields as $field)
    {
      self::$templateNotification .= $field . ":||{{ " . $field . " }}||\n";
    }
    // Create location
    $location = new \App\Models\Location();
    $location->name = 'my ticket location';
    $location->save();

    // Create couple users
    $user1 = new \App\Models\User();
    $user1->name = 'user1@foo.com';
    $user1->save();
    $GLOBALS['user_id'] = $user1->id;

    $user2 = new \App\Models\User();
    $user2->name = 'user2@foo.com';
    $user2->save();
    self::$user2Id = $user2->id;

    // create groups
    $group1 = new \App\Models\Group();
    $group1->name = 'my group 1';
    $group1->save();

    // create category
    $category = new \App\Models\Category();
    $category->name = "GSIT app";
    $category->save();

    // fill in entity
    $entity = \App\Models\Entity::find(1);
    $entity->address = '40, Peplum street';
    $entity->country = 'France';
    $entity->email = 'info@foo.com';
    $entity->fax = '+1-212-9876543';
    $entity->phonenumber = '10-56-00-432-567';
    $entity->postcode = '69790';
    $entity->state = 'Rhône';
    $entity->town = 'Propières';
    $entity->website = 'https://www.foo.com';
    $entity->save();

    // create ticket
    \App\Models\Ticket::booted();
    $ticket = new \App\Models\Ticket();
    $ticket->name = 'I\'ve got a problem with my computer, not start';
    $ticket->content = 'this is the description';
    $ticket->category_id = $category->id;
    $ticket->location_id = $location->id;
    $ticket->save();
    self::$ticketId = $ticket->id;

    // add requester
    $ticket->requester()->attach($user1->id, ['type' => 1]);

    // add requestergroup
    $ticket->requestergroup()->attach($group1->id, ['type' => 1]);

    // add technician
    $ticket->technician()->attach($user1->id, ['type' => 2]);

    // add technician group
    $ticket->techniciangroup()->attach($group1->id, ['type' => 2]);

    // add watcher
    $ticket->watcher()->attach($user2->id, ['type' => 3]);

    // add watcher group
    $ticket->watchergroup()->attach($group1->id, ['type' => 3]);

    // create followups
    $followup = new \App\Models\Followup();
    $followup->item_type = get_class($ticket);
    $followup->item_id = $ticket->id;
    $followup->content = 'first followup';
    $followup->user_id = $user1->id;
    $followup->save();

    $ticket->refresh();
    $ticket->date = '2024/11/04 10:05:50';
    $ticket->closedate = '2024/11/04 10:05:50';
    $ticket->time_to_resolve = '2024/11/04 10:05:50';
    $ticket->solvedate = '2024/11/04 10:05:50';
    $ticket->save();
  }

  public function testGenerateDataForNotification(): void
  {
    $ticket = \App\Models\Ticket::find(self::$ticketId);
    $this->assertNotNull($ticket, 'the ticket must not be null');

    $notification = new \App\v1\Controllers\Notification();

    $reflection = new \ReflectionClass($notification);
    $method = $reflection->getMethod('generateDataForNotification');
    $method->setAccessible(true);

    $genData = $method->invoke($notification, $ticket);

    $this->assertGreaterThan(10, count($genData), 'generated data are not filled');

    $flattenData = $this->generateFlattenData($genData);

    $fields = self::$fields;
    sort($fields);
    sort($flattenData);

    $this->assertEquals($fields, $flattenData);
  }

  public function testGenerateLangdataForNotification(): void
  {
    $ticket = \App\Models\Ticket::find(self::$ticketId);
    $this->assertNotNull($ticket, 'the ticket must not be null');

    $notification = new \App\v1\Controllers\Notification();

    $reflection = new \ReflectionClass($notification);
    $method = $reflection->getMethod('generateLangdataForNotification');
    $method->setAccessible(true);

    $genData = $method->invoke($notification, $ticket);

    $this->assertGreaterThan(10, count($genData), 'generated data are not filled');

    $flattenData = $this->generateFlattenlangData($genData, [], 'lang.');

    $langFields = self::$langFields;
    sort($langFields);
    sort($flattenData);

    $this->assertEquals($langFields, $flattenData);
  }

  public function testRenderNotification(): void
  {
    $ticket = \App\Models\Ticket::find(self::$ticketId);

    $notification = new \App\v1\Controllers\Notification();

    $reflection = new \ReflectionClass($notification);
    $method = $reflection->getMethod('render');
    $method->setAccessible(true);

    $render = $method->invoke($notification, self::$text, $ticket);

    $this->assertStringNotContainsString('||||', $render, 'some strings not replaced');
    $this->assertStringNotContainsString('::::', $render, 'some lang strings not replaced');
  }

  public function testRenderNotificationEmptyTicket(): void
  {
    $ticket = new \App\Models\Ticket();

    $notification = new \App\v1\Controllers\Notification();

    $reflection = new \ReflectionClass($notification);
    $method = $reflection->getMethod('render');
    $method->setAccessible(true);

    $render = $method->invoke($notification, self::$text, $ticket);

    $this->assertStringContainsString('||||', $render, 'render must not have exception');
  }


  public function testRenderNotificationTwoFollowups(): void
  {
    // create second followup
    $followup = new \App\Models\Followup();
    $followup->item_type = 'App\Models\Ticket';
    $followup->item_id = self::$ticketId;
    $followup->content = 'second followup';
    $followup->user_id = self::$user2Id;
    $followup->save();

    $ticket = \App\Models\Ticket::find(self::$ticketId);

    $notification = new \App\v1\Controllers\Notification();

    $reflection = new \ReflectionClass($notification);
    $method = $reflection->getMethod('render');
    $method->setAccessible(true);

    $render = $method->invoke($notification, self::$text, $ticket);

    // No more need this followup
    $followup->forceDelete();

    // Check first followup
    $this->assertStringContainsString(
      'followup.content::Content::||first followup||',
      $render,
      'first followup content not right'
    );
    $this->assertStringContainsString(
      'followup.user.completename::Complete name::||user1@foo.com||',
      $render,
      'first followup user not right'
    );

    // Check second followup
    $this->assertStringContainsString(
      'followup.content::Content::||second followup||',
      $render,
      'second followup content not right'
    );
    $this->assertStringContainsString(
      'followup.user.completename::Complete name::||user2@foo.com||',
      $render,
      'second followup user not right'
    );
  }


  private function generateFlattenData($data, $newData = [], $prefix = '')
  {
    foreach ($data as $key => $value)
    {
      if (is_array($value))
      {
        if (is_numeric($key))
        {
          $newData = $this->generateFlattenData($value, $newData, $prefix);
        } else {
          $newData = $this->generateFlattenData($value, $newData, $prefix . $key . '.');
        }
      } else {
        $newData[] = $prefix . $key;
      }
    }
    return $newData;
  }

  private function generateFlattenlangData($data, $newData = [], $prefix = '')
  {
    foreach ($data as $key => $value)
    {
      if (is_array($value))
      {
        $newData = $this->generateFlattenlangData($value, $newData, $prefix . $key . '.');
      } else {
        $newData[] = $prefix . $key;
      }
    }
    return $newData;
  }
}

// create notificationtemplate / translation
// create notification
// create ticket
// check in notification generated all fields filled
