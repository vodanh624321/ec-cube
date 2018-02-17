<?php
/**
 * Created by PhpStorm.
 * User: lqdung1992@gmail.com
 * Date: 02/17/2018
 * Time: 11:16 PM
 */

namespace DoctrineMigrations;


use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Class Version20180217231600
 * @package DoctrineMigrations
 */
class Version20180217231600 extends AbstractMigration
{
    /**
     * @param Schema $schema
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    public function up(Schema $schema)
    {
        $table = $schema->getTable('dtb_customer');
        if ($table->hasColumn('page')) {
            $table->dropColumn('page');
        }
    }

    public function down(Schema $schema)
    {
        // TODO: Implement down() method.
    }
}