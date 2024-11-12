<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;
use Phinx\Db\Adapter\MysqlAdapter;

final class ItilrightsMigration extends AbstractMigration
{
  public function up()
  {
    $table = $this->table('profilerights');
    $table->addColumn('readmyitems', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('readmygroupitems', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('readprivateitems', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('canassign', 'boolean', ['null' => false, 'default' => false])
          ->update();

    $configArray = require('phinx.php');
    $environments = array_keys($configArray['environments']);
    if (in_array('old', $environments))
    {
      // recompute ticket rights from old DB
      $oldRights = [
        'readmyitems'       => 1,
        'read'              => 1024,
        'readmygroupitems'  => 2048,
        'canassign'         => 8192,
      ];

      $builderSelect = $this->getQueryBuilder('select');
      $statement = $builderSelect
        ->select('*')
        ->from('profilerights')
        ->where(['model' => 'App\Models\Ticket'])
        ->execute();
      $items = $statement->fetchAll('assoc');
      foreach ($items as $item)
      {
        if (intval($item['rights']) & $oldRights['readmyitems'])
        {
          $builderUpdate = $this->getQueryBuilder('update');
          $builderUpdate
            ->update('profilerights')
            ->set('readmyitems', true, 'boolean')
            ->where(['id' => $item['id']])
            ->execute();
        }
        if (intval($item['rights']) & $oldRights['read'])
        {
          $builderUpdate = $this->getQueryBuilder('update');
          $builderUpdate
            ->update('profilerights')
            ->set('read', true, 'boolean')
            ->where(['id' => $item['id']])
            ->execute();
        } else {
          $builderUpdate = $this->getQueryBuilder('update');
          $builderUpdate
            ->update('profilerights')
            ->set('read', false, 'boolean')
            ->where(['id' => $item['id']])
            ->execute();
        }
        if (intval($item['rights']) & $oldRights['readmygroupitems'])
        {
          $builderUpdate = $this->getQueryBuilder('update');
          $builderUpdate
            ->update('profilerights')
            ->set('readmygroupitems', true, 'boolean')
            ->where(['id' => $item['id']])
            ->execute();
        }
        if (intval($item['rights']) & $oldRights['canassign'])
        {
          $builderUpdate = $this->getQueryBuilder('update');
          $builderUpdate
            ->update('profilerights')
            ->set('canassign', true, 'boolean')
            ->where(['id' => $item['id']])
            ->execute();
        }
      }
    }
  }

  public function down()
  {
    $table = $this->table('profilerights');
    $table->removeColumn('readmyitems')
          ->removeColumn('readmygroupitems')
          ->removeColumn('readprivateitems')
          ->removeColumn('canassign')
          ->update();
  }
}
