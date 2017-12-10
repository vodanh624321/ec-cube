<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Block;
use Eccube\Entity\BlockPosition;

class Version20171201120000 extends AbstractMigration
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
        $app = \Eccube\Application::getInstance();
        /** @var EntityManagerInterface $em */
        $em = $app["orm.em"];
        $DeviceType = $app['eccube.repository.master.device_type']->find(10);
        // top page
        $page = $app['eccube.repository.page_layout']->find(1);
        // detail page
        $pageDetail = $app['eccube.repository.page_layout']->find(3);

        // search smart
        $block = new Block();
        $block->setDeviceType($DeviceType);
        $block->setFileName('search_smart')
            ->setName('Search smart')
            ->setLogicFlg(0)
            ->setDeletableFlg(1);
        $em->persist($block);
        $em->flush();

        $blockPosition = new BlockPosition();
        $blockPosition->setPageLayout($page)->setPageId(1)
            ->setTargetId(4)
            ->setBlock($block)
            ->setBlockId($block->getId())
            ->setBlockRow(1)
            ->setAnywhere(1);
        $em->persist($blockPosition);
        $block->addBlockPosition($blockPosition);
        $em->persist($block);
        $em->flush();

        // breadcrumb
        $block = new Block();
        $block->setDeviceType($DeviceType);
        $block->setFileName('breadcrumb')
            ->setName('Breadcrumb')
            ->setLogicFlg(0)
            ->setDeletableFlg(1);
        $em->persist($block);
        $em->flush();

        $blockPosition = new BlockPosition();
        $blockPosition->setPageLayout($page)->setPageId(1)
            ->setTargetId(3)
            ->setBlock($block)
            ->setBlockId($block->getId())
            ->setBlockRow(1)
            ->setAnywhere(1);
        $em->persist($blockPosition);
        $block->addBlockPosition($blockPosition);
        $em->persist($block);
        $em->flush();

        // header_link
        $block = new Block();
        $block->setDeviceType($DeviceType);
        $block->setFileName('header_link')
            ->setName('Header link')
            ->setLogicFlg(0)
            ->setDeletableFlg(1);
        $em->persist($block);
        $em->flush();

        $blockPosition = new BlockPosition();
        $blockPosition->setPageLayout($page)->setPageId(1)
            ->setTargetId(2)
            ->setBlock($block)
            ->setBlockId($block->getId())
            ->setBlockRow(5)
            ->setAnywhere(1);
        $em->persist($blockPosition);
        $block->addBlockPosition($blockPosition);
        $em->persist($block);
        $em->flush();

//        $this->addSql("INSERT INTO dtb_block (block_id, device_type_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (11, 10, 'Search smart', 'search_smart', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1);");
//        $this->addSql("INSERT INTO dtb_block (block_id, device_type_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (12, 10, 'Breadcrumb', 'breadcrumb', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1);");
//        $this->addSql("INSERT INTO dtb_block (block_id, device_type_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (13, 10, 'Header Link', 'header_link', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 1);");
//        $this->addSql("INSERT INTO dtb_block (block_id, device_type_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (14, 10, 'Recommend', 'recommend', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 1, 1);");
        $this->addSql("UPDATE dtb_block SET block_name='Free Area' WHERE block_id=9;");
        $this->addSql("UPDATE dtb_block SET logic_flg=1 WHERE block_id=8;");

        $this->addSql("UPDATE dtb_block_position SET page_id=1, target_id=4, block_row=2, anywhere=1 WHERE  block_id=1 AND page_id=1;");
        $this->addSql("UPDATE dtb_block_position SET page_id=1, target_id=4, block_row=3, anywhere=1 WHERE  block_id=9 AND page_id=1;");
        $this->addSql("UPDATE dtb_block_position SET page_id=1, target_id=6, block_row=1, anywhere=0 WHERE  block_id=8 AND page_id=1;");
        $this->addSql("UPDATE dtb_block_position SET page_id=1, target_id=6, block_row=3, anywhere=0 WHERE  block_id=4 AND page_id=1;");

        $this->addSql("DELETE FROM dtb_block_position WHERE block_id=10;");

//        $this->addSql("INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 4, 11, 1, 1);");
//        $this->addSql("INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 3, 12, 1, 1);");
//        $this->addSql("INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 2, 13, 5, 1);");
//        $this->addSql("INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 6, 14, 2, 0);");
//        $this->addSql("INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (3, 6, 14, 1, 0);");
//        $this->addSql("INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (3, 6, 8, 2, 0);");
        // block new_product (search by category)
        /** @var Block $block */
        $block = $app['eccube.repository.block']->find(8);
        if ($block) {
            $pos = new BlockPosition();
            $pos->setBlock($block)
                ->setBlockId($block->getId())
                ->setTargetId(6)
                ->setPageLayout($pageDetail)
                ->setPageId($pageDetail->getId())
                ->setAnywhere(0)
                ->setBlockRow(2);
            $em->persist($pos);
            $block->addBlockPosition($pos);
            $em->persist($block);
            $em->flush();
        }
//        if ($this->connection->getDatabasePlatform()->getName() == "postgresql") {
//            $this->addSql("SELECT setval('dtb_block_block_id_seq', 15);");
//        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
