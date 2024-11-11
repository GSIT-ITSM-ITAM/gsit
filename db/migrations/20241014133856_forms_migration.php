<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class FormsMigration extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('forms');
    $table->addColumn('name', 'string', ['null' => true])
          ->addColumn('comment', 'text', ['null' => true])
          ->addColumn('created_at', 'timestamp', ['null' => true])
          ->addColumn('updated_at', 'timestamp', ['null' => true])
          ->addColumn('deleted_at', 'timestamp', ['null' => true])
          ->addColumn('entity_id', 'integer', ['null' => false, 'default' => 1])
          ->addColumn('is_recursive', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('icon', 'string', ['null' => false])
          ->addColumn('icon_color', 'string', ['null' => false])
          ->addColumn('content', 'text', ['null' => true])
          ->addColumn('category_id', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('is_active', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('is_homepage', 'integer', ['null' => false, 'default' => 0])
          ->addIndex(['name'])
          ->addIndex(['entity_id'])
          ->addIndex(['is_active'])
          ->addIndex(['category_id'])
          ->addIndex(['created_at'])
          ->addIndex(['updated_at'])
          ->addIndex(['deleted_at'])
          ->create();

    $configArray = require('phinx.php');
    $environments = array_keys($configArray['environments']);
    if (in_array('old', $environments))
    {
      // Migration of database

      $config = Config::fromPhp('phinx.php');
      $environment = new Environment('old', $config->getEnvironment('old'));
      $pdo = $environment->getAdapter()->getConnection();
    } else {
      return;
    }
    $item = $this->table('forms');

    if ($this->isMigratingUp())
    {
      $nbRows = $pdo->query('SELECT count(*) FROM glpi_plugin_formcreator_forms')->fetchColumn();
      $nbLoops = ceil($nbRows / 5000);

      for ($i = 0; $i < $nbLoops; $i++)
      {
        $stmt = $pdo->query('SELECT * FROM glpi_plugin_formcreator_forms ORDER BY id LIMIT 5000 OFFSET ' . ($i * 5000));
        $rows = $stmt->fetchAll();
        $data = [];
        foreach ($rows as $row)
        {
          $data[] = [
            'id'                => $row['id'],
            'name'              => $row['name'],
            'entity_id'         => ($row['entities_id'] + 1),
            'is_recursive'      => $row['is_recursive'],
            'icon'              => $row['icon'],
            'icon_color'        => $row['icon_color'],
            'comment'           => $row['description'],
            'content'           => $row['content'],
            'category_id'       => $row['plugin_formcreator_categories_id'],
            'is_active'         => $row['is_active'],
            'is_homepage'       => $row['helpdesk_home'],
            'deleted_at'        => self::convertIsDeleted($row['is_deleted']),
          ];
        }
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('forms_id_seq', (SELECT MAX(id) FROM forms)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }

  public function convertIsDeleted($is_deleted)
  {
    if ($is_deleted == 1)
    {
      return date('Y-m-d H:i:s', time());
    }
    return null;
  }
}
