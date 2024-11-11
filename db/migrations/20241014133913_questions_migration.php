<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class QuestionsMigration extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('questions');
    $table->addColumn('name', 'string', ['null' => true])
          ->addColumn('comment', 'text', ['null' => true])
          ->addColumn('created_at', 'timestamp', ['null' => true])
          ->addColumn('updated_at', 'timestamp', ['null' => true])
          ->addColumn('deleted_at', 'timestamp', ['null' => true])
          ->addColumn('fieldtype', 'string', ['null' => false, 'default' => 'text'])
          ->addColumn('is_required', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('show_empty', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('default_values', 'text', ['null' => true, 'default' => null])
          ->addColumn('values', 'text', ['null' => true, 'default' => null])
          ->addColumn('row', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('col', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('width', 'integer', ['null' => false, 'default' => 0])
          ->addIndex(['name'])
          ->addIndex(['is_required'])
          ->addIndex(['show_empty'])
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
    $item = $this->table('questions');

    if ($this->isMigratingUp())
    {
      $nbRows = $pdo->query('SELECT count(*) FROM glpi_plugin_formcreator_questions')->fetchColumn();
      $nbLoops = ceil($nbRows / 5000);

      for ($i = 0; $i < $nbLoops; $i++)
      {
        $stmt = $pdo->query(
          'SELECT * FROM glpi_plugin_formcreator_questions ORDER BY id LIMIT 5000 OFFSET ' . ($i * 5000)
        );
        $rows = $stmt->fetchAll();
        $data = [];
        foreach ($rows as $row)
        {
          $data[] = [
            'id'                => $row['id'],
            'name'              => $row['name'],
            'fieldtype'         => $row['fieldtype'],
            'is_required'       => $row['required'],
            'show_empty'        => $row['show_empty'],
            'default_values'    => $row['default_values'],
            'values'            => $row['values'],
            'comment'           => $row['description'],
            'row'               => $row['row'],
            'col'               => $row['col'],
            'width'             => $row['width'],
          ];
        }
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('questions_id_seq', (SELECT MAX(id) FROM questions)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
