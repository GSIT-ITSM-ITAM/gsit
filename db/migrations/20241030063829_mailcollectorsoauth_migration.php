<?php

declare(strict_types=1);

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Phinx\Migration\AbstractMigration;
use Phinx\Migration\Manager\Environment;
use Phinx\Config\Config;
use App\v1\Controllers\Toolbox;
use Phinx\Db\Adapter\MysqlAdapter;

final class MailcollectorsoauthMigration extends AbstractMigration
{
  public function change(): void
  {
    $table = $this->table('mailcollectors');
    $table->addColumn('is_oauth', 'boolean', ['null' => false, 'default' => false])
          ->addColumn('oauth_provider', 'string', ['null' => true])
          ->addColumn('oauth_applicationid', 'string', ['null' => true])
          ->addColumn('oauth_directoryid', 'string', ['null' => true])
          ->addColumn('oauth_applicationsecret', 'string', ['null' => true])
          ->addColumn('oauth_token', 'text', ['null' => true])
          ->addColumn('oauth_refresh_token', 'text', ['null' => true])
          ->update();


    // TODO get from glpi_plugin_oauthimap_applications ans glpi_plugin_oauthimap_authorizations
  }
}
