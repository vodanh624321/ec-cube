<?php
/**
 * Created by PhpStorm.
 * User: lqdung1992@gmail.com
 * Date: 02/19/2018
 * Time: 12:35
 */

namespace DoctrineMigrations;


use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20180219123500
 * @package DoctrineMigrations
 */
class Version20180219123500 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("INSERT INTO dtb_csv (csv_type, entity_name, field_name, reference_field_name, disp_name, rank, enable_flg, creator_id, create_date, update_date) VALUES (1, 'Eccube\\\\Entity\\\\Product', 'unit', NULL, '販売単位', 31, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");

        if ($this->connection->getDatabasePlatform()->getName() == "postgresql") {
            $this->addSql("SELECT setval('dtb_csv_csv_id_seq', 200);");
        }
    }

    public function down(Schema $schema)
    {
        // TODO: Implement down() method.
    }
}