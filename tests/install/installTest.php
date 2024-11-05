<?php

declare(strict_types=1);

namespace Tests\install;

use GuzzleHttp\Psr7\BufferStream;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Capsule\Manager as DB;
use Phinx\Console\PhinxApplication;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * @covers \Migration\migrations
 */
final class InstallTest extends TestCase
{
  public static function setUpBeforeClass(): void
  {
    global $databaseName;

    $schema = DB::schema();
    $schema->dropAllTables();
  }

  public function testDatabasecleaned(): void
  {
    $schema = DB::schema();
    $tables = $schema->getTables();

    $this->assertCount(0, $tables);
  }

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
