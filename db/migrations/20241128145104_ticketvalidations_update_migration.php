<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;

final class TicketvalidationsUpdateMigration extends AbstractMigration
{
  public function up()
  {
    $table = $this->table('ticketvalidations');
    $table->addColumn('created_at', 'timestamp', ['null' => true, 'after' => 'id'])
          ->addColumn('updated_at', 'timestamp', ['null' => true, 'after' => 'created_at'])
          ->addColumn('deleted_at', 'timestamp', ['null' => true, 'after' => 'updated_at'])
          ->addColumn('is_recursive', 'boolean', ['null' => false, 'default' => false, 'after' => 'entity_id'])
          ->update();
  }

  public function down()
  {
    $table = $this->table('ticketvalidations');
    $table->removeColumn('created_at')
          ->removeColumn('updated_at')
          ->removeColumn('deleted_at')
          ->removeColumn('entity_id')
          ->update();
  }
}
