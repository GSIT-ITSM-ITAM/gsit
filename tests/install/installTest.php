<?php

declare(strict_types=1);

namespace Tests\install;

use PHPUnit\Framework\TestCase;
use Illuminate\Database\Capsule\Manager as DB;
use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @coversNothing
 */
final class InstallTest extends TestCase
{
  public static function setUpBeforeClass(): void
  {
    $schema = DB::schema();
    $schema->dropAllTables();
  }

  /**
   * @coversNothing
   */
  public function testDatabasecleaned(): void
  {
    $schema = DB::schema();
    $tables = $schema->getTables();

    $this->assertCount(0, $tables);
  }

  /**
   * @coversNothing
   */
  public function testInstall(): void
  {
    $phinx = new PhinxApplication();
    $command = $phinx->find('migrate');

    $arguments = [
      'command'         => 'migrate',
      '--environment'   => 'tests',
    ];

    $input = new ArrayInput($arguments);
    $output = new BufferedOutput();
    $returnCode = $command->run(new ArrayInput($arguments), $output);

    $this->assertEquals(0, $returnCode, $output->fetch());
  }
}
