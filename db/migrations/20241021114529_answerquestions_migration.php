<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;
use Phinx\Db\Adapter\MysqlAdapter;

final class AnswerquestionsMigration extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('answerquestions');
    $table->addColumn('answer_id', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('question_id', 'integer', ['null' => false, 'default' => 0])
          ->addColumn('answer', 'text', ['null' => true, 'default' => null, 'limit' => MysqlAdapter::TEXT_LONG])
          ->addIndex(['answer_id', 'question_id'], ['unique' => true])
          ->save();

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
    $item = $this->table('answerquestions');

    if ($this->isMigratingUp())
    {
      $nbRows = $pdo->query('SELECT count(*) FROM glpi_plugin_formcreator_formanswers')->fetchColumn();
      $nbLoops = ceil($nbRows / 5000);

      for ($i = 0; $i < $nbLoops; $i++) {
        $stmt = $pdo->query('SELECT * FROM glpi_plugin_formcreator_answers ORDER BY id LIMIT 5000 OFFSET ' .
                ($i * 5000));
        $rows = $stmt->fetchAll();
        foreach ($rows as $row)
        {
          if ($row['plugin_formcreator_formanswers_id'] == 0)
          {
            continue;
          }
          $data = [
            [
              'answer_id'          => $row['plugin_formcreator_formanswers_id'],
              'question_id'       => $row['plugin_formcreator_questions_id'],
              'answer'            => $row['answer'],
            ]
          ];

          $item->insert($data)
               ->saveData();
        }
      }
    } else {
      // rollback
      $item->truncate();
    }
  }
}
