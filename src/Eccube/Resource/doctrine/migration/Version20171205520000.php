<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20171205520000 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if ($this->connection->getDatabasePlatform()->getName() == "mysql") {
            $this->addSql("SET FOREIGN_KEY_CHECKS=0;");
            $this->addSql("SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO';");
        }

        $this->addSql("DELETE FROM dtb_block WHERE block_id=14;");
        $this->addSql("DELETE FROM dtb_block_position WHERE block_id=14;");

        if ($this->connection->getDatabasePlatform()->getName() == "postgresql") {
            $this->addSql("SELECT setval('dtb_block_block_id_seq', 14);");
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
