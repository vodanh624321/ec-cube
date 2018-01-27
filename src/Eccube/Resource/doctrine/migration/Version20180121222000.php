<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Block;
use Eccube\Entity\Master\ProductListMax;
use Eccube\Entity\PageLayout;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180121222000 extends AbstractMigration
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
        // top index b page
        $pageB = $app['eccube.repository.page_layout']->findOneBy(array('file_name' => 'index_b'));
        if (!$pageB) {
            $pageB = new PageLayout();
            $pageB->setDeviceType($DeviceType)
                ->setName('TOPページ B')
                ->setUrl('homepage_b')
                ->setFileName('index_b')
                ->setEditFlg(2);
            $em->persist($pageB);
            $em->flush();
        }

        // top index c page
        $pageC = $app['eccube.repository.page_layout']->findOneBy(array('file_name' => 'index_c'));
        if (!$pageC) {
            $pageC = new PageLayout();
            $pageC->setDeviceType($DeviceType)
                ->setName('TOPページ C')
                ->setUrl('homepage_c')
                ->setFileName('index_c')
                ->setEditFlg(2);
            $em->persist($pageC);
            $em->flush();
        }

        // help recommend page
        $pageH = $app['eccube.repository.page_layout']->findOneBy(array('file_name' => 'Help/recommend'));
        if (!$pageH) {
            $pageH = new PageLayout();
            $pageH->setDeviceType($DeviceType)
                ->setName('初めての方へ')
                ->setUrl('help_recommend')
                ->setFileName('Help/recommend')
                ->setEditFlg(2);
            $em->persist($pageH);
            $em->flush();
        }

        // news page
        $pageN = $app['eccube.repository.page_layout']->findOneBy(array('file_name' => 'News/list'));
        if (!$pageN) {
            $pageN = new PageLayout();
            $pageN->setDeviceType($DeviceType)
                ->setName('News')
                ->setUrl('news_list')
                ->setFileName('News/list')
                ->setEditFlg(2);
            $em->persist($pageN);
            $em->flush();
        }

        // news detail page
        $pageND = $app['eccube.repository.page_layout']->findOneBy(array('file_name' => 'News/detail'));
        if (!$pageND) {
            $pageND = new PageLayout();
            $pageND->setDeviceType($DeviceType)
                ->setName('News Detail')
                ->setUrl('news_detail')
                ->setFileName('News/detail')
                ->setEditFlg(2);
            $em->persist($pageND);
            $em->flush();
        }

        $blockHeaderIn = $app['eccube.repository.block']->findOneBy(array('file_name' => 'header_in'));
        if (!$blockHeaderIn) {
            $block = new Block();
            $block->setDeviceType($DeviceType);
            $block->setFileName('header_in')
                ->setName('Header In')
                ->setLogicFlg(1)
                ->setDeletableFlg(1);
            $em->persist($block);
            $em->flush();
        }

        $blockToolBar = $app['eccube.repository.block']->findOneBy(array('file_name' => 'tool_bar'));
        if (!$blockToolBar) {
            $blockToolBar = new Block();
            $blockToolBar->setDeviceType($DeviceType)
                ->setFileName('tool_bar')
                ->setName('Tool Bar')
                ->setLogicFlg(1)
                ->setDeletableFlg(1);
            $em->persist($blockToolBar);
            $em->flush();
        }
//
//        $blockNavi = $app['eccube.repository.block']->findOneBy(array('file_name' => 'navi'));
//        if (!$blockNavi) {
//            $blockNavi = new Block();
//            $blockNavi->setDeviceType($DeviceType)
//                ->setFileName('navi')
//                ->setName('Navi Bar')
//                ->setLogicFlg(0)
//                ->setDeletableFlg(1);
//            $em->persist($blockNavi);
//            $em->flush();
//        }

        $blockMainimg = $app['eccube.repository.block']->findOneBy(array('file_name' => 'mainimg'));
        if (!$blockMainimg) {
            $blockMainimg = new Block();
            $blockMainimg->setDeviceType($DeviceType)
                ->setFileName('mainimg')
                ->setName('Main Image')
                ->setLogicFlg(0)
                ->setDeletableFlg(1);
            $em->persist($blockMainimg);
            $em->flush();
        }

        $blockPagePick = $app['eccube.repository.block']->findOneBy(array('file_name' => 'page_pick'));
        if (!$blockPagePick) {
            $blockPagePick = new Block();
            $blockPagePick->setDeviceType($DeviceType)
                ->setFileName('page_pick')
                ->setName('Page Pick')
                ->setLogicFlg(0)
                ->setDeletableFlg(1);
            $em->persist($blockPagePick);
            $em->flush();
        }

        $blockSliderMain = $app['eccube.repository.block']->findOneBy(array('file_name' => 'slider_main'));
        if (!$blockSliderMain) {
            $blockSliderMain = new Block();
            $blockSliderMain->setDeviceType($DeviceType)
                ->setFileName('slider_main')
                ->setName('Slider Main')
                ->setLogicFlg(0)
                ->setDeletableFlg(1);
            $em->persist($blockSliderMain);
            $em->flush();
        }

        $blockMainPanel = $app['eccube.repository.block']->findOneBy(array('file_name' => 'main_panel'));
        if (!$blockMainPanel) {
            $blockMainPanel = new Block();
            $blockMainPanel->setDeviceType($DeviceType)
                ->setFileName('main_panel')
                ->setName('Main panel')
                ->setLogicFlg(0)
                ->setDeletableFlg(1);
            $em->persist($blockMainPanel);
            $em->flush();
        }

        $blockTopSide = $app['eccube.repository.block']->findOneBy(array('file_name' => 'top_side'));
        if (!$blockTopSide) {
            $blockTopSide = new Block();
            $blockTopSide->setDeviceType($DeviceType)
                ->setFileName('top_side')
                ->setName('Top Side')
                ->setLogicFlg(0)
                ->setDeletableFlg(1);
            $em->persist($blockTopSide);
            $em->flush();
        }

        $blockTopLink = $app['eccube.repository.block']->findOneBy(array('file_name' => 'top_link'));
        if (!$blockTopLink) {
            $blockTopLink = new Block();
            $blockTopLink->setDeviceType($DeviceType)
                ->setFileName('top_link')
                ->setName('Top Link')
                ->setLogicFlg(0)
                ->setDeletableFlg(1);
            $em->persist($blockTopLink);
            $em->flush();
        }

        $breadcrumb = $app['eccube.repository.block']->findOneBy(array('file_name' => 'breadcrumb'));
        if (!$breadcrumb) {
            $breadcrumb = new Block();
            $breadcrumb->setDeviceType($DeviceType)
                ->setFileName('breadcrumb')
                ->setName('Breadcrumb')
                ->setLogicFlg(0)
                ->setDeletableFlg(1);
            $em->persist($breadcrumb);
            $em->flush();
        }

        $bottomMainB = $app['eccube.repository.block']->findOneBy(array('file_name' => 'bottom_main_type_b'));
        if (!$bottomMainB) {
            $bottomMainB = new Block();
            $bottomMainB->setDeviceType($DeviceType)
                ->setFileName('bottom_main_type_b')
                ->setName('Bottom Main Type B')
                ->setLogicFlg(0)
                ->setDeletableFlg(1);
            $em->persist($bottomMainB);
            $em->flush();
        }

        $topSaleC = $app['eccube.repository.block']->findOneBy(array('file_name' => 'top_sale_slider_c'));
        if (!$topSaleC) {
            $topSaleC = new Block();
            $topSaleC->setDeviceType($DeviceType)
                ->setFileName('top_sale_slider_c')
                ->setName('Top sale slider Type C')
                ->setLogicFlg(0)
                ->setDeletableFlg(1);
            $em->persist($topSaleC);
            $em->flush();
        }

        $mainC = $app['eccube.repository.block']->findOneBy(array('file_name' => 'main_c'));
        if (!$mainC) {
            $mainC = new Block();
            $mainC->setDeviceType($DeviceType)
                ->setFileName('main_c')
                ->setName('Main C')
                ->setLogicFlg(0)
                ->setDeletableFlg(1);
            $em->persist($mainC);
            $em->flush();
        }

        // Remove all block
        $this->addSql("DELETE FROM dtb_block_position");

        // Update display item
        /** @var ProductListMax $num */
        $num = $app['eccube.repository.master.product_list_max']->find(15);
        $num->setId(40)
            ->setName('40件');
        $em->persist($num);
        $em->flush();

        $num = $app['eccube.repository.master.product_list_max']->find(30);
        $num->setId(100)
            ->setName('100件');
        $em->persist($num);
        $em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
