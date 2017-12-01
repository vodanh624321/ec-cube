<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Eccube\Entity\PageLayout;

class Version20171201120000 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
//        INSERT INTO `dtb_block` (`block_id`, `device_type_id`, `block_name`, `file_name`, `create_date`, `update_date`, `logic_flg`, `deletable_flg`) VALUES (11, 10, 'Search smart', 'search_smart', '2017-12-01 20:17:29', '2017-12-01 20:17:29', 0, 1);
//        INSERT INTO `dtb_block` (`block_id`, `device_type_id`, `block_name`, `file_name`, `create_date`, `update_date`, `logic_flg`, `deletable_flg`) VALUES (12, 10, 'breadcrumb', 'breadcrumb', '2017-12-01 20:22:07', '2017-12-01 20:22:07', 0, 1);
//        INSERT INTO `dtb_block_position` (`page_id`, `target_id`, `block_id`, `block_row`, `anywhere`) VALUES (1, 3, 12, 1, 1);
//        INSERT INTO `dtb_block_position` (`page_id`, `target_id`, `block_id`, `block_row`, `anywhere`) VALUES (1, 4, 11, 1, 1);
//        $app = \Eccube\Application::getInstance();
//        /** @var EntityManager $em */
//        $em = $app["orm.em"];
//
//        $DeviceType = $app['eccube.repository.master.device_type']->find(10);
//
//        $PageLayout = new PageLayout();
//        $PageLayout
//            ->setDeviceType($DeviceType)
//            ->setName('商品購入/確認')
//            ->setUrl('shopping_confirm')
//            ->setFileName('Shopping/confirm')
//            ->setEditFlg(2)
//            ->setMetaRobots('noindex');
//        $em->persist($PageLayout);
//
//        $em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
