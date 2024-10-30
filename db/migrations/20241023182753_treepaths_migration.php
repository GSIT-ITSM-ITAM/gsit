<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;
use Phinx\Db\Adapter\MysqlAdapter;

final class TreepathsMigration extends AbstractMigration
{
  protected $treepaths = [];

  public function up()
  {
    // we set fields and recalculate treepath for items in tree
    $table = $this->table('entities');
    if (!$table->hasColumn('treepath'))
    {
      $table->addColumn('treepath', 'string', ['null' => true])
            ->update();
    }

    $table = $this->table('categories');
    if (!$table->hasColumn('treepath'))
    {
      $table->addColumn('treepath', 'string', ['null' => true])
            ->update();
    }

    $table = $this->table('groups');
    if (!$table->hasColumn('treepath'))
    {
      $table->addColumn('treepath', 'string', ['null' => true])
            ->update();
    }

    $table = $this->table('knowbaseitemcategories');
    if (!$table->hasColumn('treepath'))
    {
      $table->addColumn('treepath', 'string', ['null' => true])
            ->update();
    }

    $table = $this->table('locations');
    if (!$table->hasColumn('treepath'))
    {
      $table->addColumn('treepath', 'string', ['null' => true])
            ->update();
    }

    $table = $this->table('softwarelicensetypes');
    if (!$table->hasColumn('treepath'))
    {
      $table->addColumn('treepath', 'string', ['null' => true])
            ->update();
    }

    $table = $this->table('softwarecategories');
    if (!$table->hasColumn('treepath'))
    {
      $table->addColumn('treepath', 'string', ['null' => true])
            ->update();
    }

    $table = $this->table('states');
    if (!$table->hasColumn('treepath'))
    {
      $table->addColumn('treepath', 'string', ['null' => true])
            ->update();
    }

    $table = $this->table('businesscriticities');
    if (!$table->hasColumn('treepath'))
    {
      $table->addColumn('treepath', 'string', ['null' => true])
            ->update();
    }

    $table = $this->table('documentcategories');
    if (!$table->hasColumn('treepath'))
    {
      $table->addColumn('treepath', 'string', ['null' => true])
            ->update();
    }

    $table = $this->table('ipnetworks');
    if (!$table->hasColumn('treepath'))
    {
      $table->addColumn('treepath', 'string', ['null' => true])
            ->update();
    }

    $this->treepaths = [];
    $this->generateTreepath('0', 'entities', 'entity_id');

    $this->treepaths = [];
    $this->generateTreepath('0', 'categories', 'category_id');

    $this->treepaths = [];
    $this->generateTreepath('0', 'groups', 'group_id');

    $this->treepaths = [];
    $this->generateTreepath('0', 'knowbaseitemcategories', 'knowbaseitemcategory_id');

    $this->treepaths = [];
    $this->generateTreepath('0', 'locations', 'location_id');

    $this->treepaths = [];
    $this->generateTreepath('0', 'softwarelicensetypes', 'softwarelicensetype_id');

    $this->treepaths = [];
    $this->generateTreepath('0', 'softwarecategories', 'softwarecategory_id');

    $this->treepaths = [];
    $this->generateTreepath('0', 'states', 'state_id');

    $this->treepaths = [];
    $this->generateTreepath('0', 'businesscriticities', 'businesscriticity_id');

    $this->treepaths = [];
    $this->generateTreepath('0', 'documentcategories', 'documentcategory_id');

    $this->treepaths = [];
    $this->generateTreepath('0', 'ipnetworks', 'ipnetwork_id');

    $table = $this->table('entities');
    if ($table->hasColumn('level'))
    {
      $table->removeColumn('level')
            ->removeColumn('sons_cache')
            ->removeColumn('ancestors_cache')
            ->update();
    }

    $table = $this->table('groups');
    if ($table->hasColumn('level'))
    {
      $table->removeColumn('level')
            ->removeColumn('sons_cache')
            ->removeColumn('ancestors_cache')
            ->update();
    }

    $table = $this->table('knowbaseitemcategories');
    if ($table->hasColumn('level'))
    {
      $table->removeColumn('level')
            ->removeColumn('sons_cache')
            ->removeColumn('ancestors_cache')
            ->update();
    }

    $table = $this->table('locations');
    if ($table->hasColumn('level'))
    {
      $table->removeColumn('level')
            ->removeColumn('sons_cache')
            ->removeColumn('ancestors_cache')
            ->update();
    }

    $table = $this->table('softwarelicensetypes');
    if ($table->hasColumn('level'))
    {
      $table->removeColumn('level')
            ->removeColumn('sons_cache')
            ->removeColumn('ancestors_cache')
            ->update();
    }

    $table = $this->table('softwarecategories');
    if ($table->hasColumn('level'))
    {
      $table->removeColumn('level')
            ->removeColumn('sons_cache')
            ->removeColumn('ancestors_cache')
            ->update();
    }

    $table = $this->table('states');
    if ($table->hasColumn('level'))
    {
      $table->removeColumn('level')
            ->removeColumn('sons_cache')
            ->removeColumn('ancestors_cache')
            ->update();
    }

    $table = $this->table('businesscriticities');
    if ($table->hasColumn('level'))
    {
      $table->removeColumn('level')
            ->removeColumn('sons_cache')
            ->removeColumn('ancestors_cache')
            ->update();
    }

    $table = $this->table('documentcategories');
    if ($table->hasColumn('level'))
    {
      $table->removeColumn('level')
            ->removeColumn('sons_cache')
            ->removeColumn('ancestors_cache')
            ->update();
    }

    $table = $this->table('ipnetworks');
    if ($table->hasColumn('level'))
    {
      $table->removeColumn('level')
            ->removeColumn('sons_cache')
            ->removeColumn('ancestors_cache')
            ->update();
    }
  }

  public function down()
  {
    $table = $this->table('entities');
    if (!$table->hasColumn('level'))
    {
      $table->addColumn('level', 'integer', ['null' => false, 'default' => '0'])
            ->addColumn('sons_cache', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
            ->addColumn('ancestors_cache', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
            ->update();
    }

    $table = $this->table('groups');
    if (!$table->hasColumn('level'))
    {
      $table->addColumn('level', 'integer', ['null' => false, 'default' => '0'])
            ->addColumn('sons_cache', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
            ->addColumn('ancestors_cache', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
            ->update();
    }

    $table = $this->table('knowbaseitemcategories');
    if (!$table->hasColumn('level'))
    {
      $table->addColumn('level', 'integer', ['null' => false, 'default' => '0'])
            ->addColumn('sons_cache', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
            ->addColumn('ancestors_cache', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
            ->update();
    }

    $table = $this->table('locations');
    if (!$table->hasColumn('level'))
    {
      $table->addColumn('level', 'integer', ['null' => false, 'default' => '0'])
            ->addColumn('sons_cache', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
            ->addColumn('ancestors_cache', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
            ->update();
    }

    $table = $this->table('softwarelicensetypes');
    if (!$table->hasColumn('level'))
    {
      $table->addColumn('level', 'integer', ['null' => false, 'default' => '0'])
            ->addColumn('sons_cache', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
            ->addColumn('ancestors_cache', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
            ->update();
    }

    $table = $this->table('softwarecategories');
    if (!$table->hasColumn('level'))
    {
      $table->addColumn('level', 'integer', ['null' => false, 'default' => '0'])
            ->addColumn('sons_cache', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
            ->addColumn('ancestors_cache', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
            ->update();
    }

    $table = $this->table('states');
    if (!$table->hasColumn('level'))
    {
      $table->addColumn('level', 'integer', ['null' => false, 'default' => '0'])
            ->addColumn('sons_cache', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
            ->addColumn('ancestors_cache', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
            ->update();
    }

    $table = $this->table('businesscriticities');
    if (!$table->hasColumn('level'))
    {
      $table->addColumn('level', 'integer', ['null' => false, 'default' => '0'])
            ->addColumn('sons_cache', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
            ->addColumn('ancestors_cache', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
            ->update();
    }

    $table = $this->table('documentcategories');
    if (!$table->hasColumn('level'))
    {
      $table->addColumn('level', 'integer', ['null' => false, 'default' => '0'])
            ->addColumn('sons_cache', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
            ->addColumn('ancestors_cache', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
            ->update();
    }

    $table = $this->table('ipnetworks');
    if (!$table->hasColumn('level'))
    {
      $table->addColumn('level', 'integer', ['null' => false, 'default' => '0'])
            ->addColumn('sons_cache', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
            ->addColumn('ancestors_cache', 'text', ['null' => true, 'limit' => MysqlAdapter::TEXT_LONG])
            ->save();
    }
  }

  private function generateTreepath($id, $table, $foreignkey)
  {
    $builderSelect = $this->getQueryBuilder('select');
    $statement = $builderSelect->select('*')->from($table)->where($foreignkey . " = '" . $id . "'")->execute();
    $items = $statement->fetchAll('assoc');
    $itemList = [];
    foreach ($items as $item)
    {
      $itemList[] = $item['id'];
      // generate treepath
      $treepath = sprintf("%05d", $item['id']);
      if (isset($this->treepaths[$item[$foreignkey]]))
      {
        $treepath = $this->treepaths[$item[$foreignkey]] . $treepath;
      }
      $this->treepaths[$item['id']] = $treepath;
      $builderUpdate = $this->getQueryBuilder('update');
      $builderUpdate
        ->update($table)
        ->set('treepath', $treepath)
        ->where(['id' => $item['id']])
        ->execute();
    }
    foreach ($itemList as $itemId)
    {
      $this->generateTreepath($itemId, $table, $foreignkey);
    }
  }
}
