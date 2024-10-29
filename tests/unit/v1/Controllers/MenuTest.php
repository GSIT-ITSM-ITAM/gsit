<?php

declare(strict_types=1);

namespace Tests\unit\v1\Controllers;

use PHPUnit\Framework\TestCase;

/**
 * @covers \App\v1\Controllers\Menu
 */
final class MenuTest extends TestCase
{
  use \Tests\Traits\HttpTestTrait;

  public function testMenuDataHasDisplayField(): void
  {
    $request = $this->createRequest('GET', '/view/computers');

    $menu = new \App\v1\Controllers\Menu();

    $reflection = new \ReflectionClass($menu);
    $method = $reflection->getMethod('menuData');
    $method->setAccessible(true);

    $menuItems = $method->invoke($menu, $request);

    foreach ($menuItems as $menuItem)
    {
      foreach ($menuItem['sub'] as $subItem)
      {
        $this->assertArrayHasKey('display', $subItem, 'The menu name `' . $subItem['name'] . '` not have display key');
      }
      if (isset($menuItem['dropdown']))
      {
        foreach ($menuItem['dropdown'] as $subItem)
        {
          $this->assertArrayHasKey('display', $subItem, 'The menu dropdown name `' . $subItem['name'] . '` not have display key');
        }
      }
      if (isset($menuItem['component']))
      {
        foreach ($menuItem['component'] as $subItem)
        {
          $this->assertArrayHasKey('display', $subItem, 'The menu component name `' . $subItem['name'] . '` not have display key');
        }
      }
    }
  }

  public function testCleanMenuByDisplayEmpty(): void
  {
    $request = $this->createRequest('GET', '/view/computers');

    $menu = new \App\v1\Controllers\Menu();

    $reflection = new \ReflectionClass($menu);
    $method = $reflection->getMethod('menuData');
    $method->setAccessible(true);

    $menuItems = $method->invoke($menu, $request);

    $method = $reflection->getMethod('cleanMenuByDisplay');
    $method->setAccessible(true);

    $menuCleaned = $method->invoke($menu, $menuItems);

    $this->assertEmpty($menuCleaned, 'The menu must be empty because no rights defined');
  }

  public function testCleanMenuByDisplayComputer(): void
  {
    $request = $this->createRequest('GET', '/view/computers');

    $menu = new \App\v1\Controllers\Menu();

    $reflection = new \ReflectionClass($menu);
    $menuData = $reflection->getMethod('menuData');
    $menuData->setAccessible(true);
    $setRightForModel = $reflection->getMethod('setRightForModel');
    $setRightForModel->setAccessible(true);
    $cleanMenuByDisplay = $reflection->getMethod('cleanMenuByDisplay');
    $cleanMenuByDisplay->setAccessible(true);

    $setRightForModel->invoke($menu, 'App\Models\Computer');
    $menuItems = $menuData->invoke($menu, $request);
    $menuCleaned = $cleanMenuByDisplay->invoke($menu, $menuItems);

    $this->assertcount(1, $menuCleaned, 'The menu must have an entry');
    $this->assertArrayHasKey('sub', $menuCleaned[0], 'The menu must have sub key');
    $this->assertcount(1, $menuCleaned[0]['sub'], 'The sub menu must have an entry');
    $this->assertStringEndsWith('/view/computers', $menuCleaned[0]['sub'][0]['link']);
  }
}
