<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;

final class QuestionsSectionsMigration extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('question_section');
    $table->addColumn('question_id', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('section_id', 'integer', ['null' => false, 'default' => 0])
          ->addIndex(['question_id', 'section_id'], ['unique' => true])
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
    $item = $this->table('question_section');

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
            'question_id'   => $row['id'],
            'section_id'    => $row['plugin_formcreator_sections_id'],
          ];
        }
        $item->insert($data)
             ->saveData();
      }
      if ($configArray['environments'][$configArray['environments']['default_environment']]['adapter'] == 'pgsql')
      {
        $this->execute("SELECT setval('question_section_id_seq', (SELECT MAX(id) FROM question_section)+1)");
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
