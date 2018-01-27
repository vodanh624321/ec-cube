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
use Eccube\Entity\BlockPosition;
use Eccube\Entity\Master\Tag;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180127222000 extends AbstractMigration
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
        // top page
        $page = $app['eccube.repository.page_layout']->find(1);
        // Target 2
        $block = $app['eccube.repository.block']->find(1);
        $blockPosition = new BlockPosition();
        $blockPosition->setPageLayout($page)->setPageId($page->getId())
            ->setTargetId(2)
            ->setBlock($block)->setBlockId($block->getId())
            ->setBlockRow(3)
            ->setAnywhere(1);
        $em->persist($blockPosition);
        $block->addBlockPosition($blockPosition);
        $em->persist($block);

        $block = $app['eccube.repository.block']->findOneBy(array('file_name' => 'header_in'));
        $blockPosition = new BlockPosition();
        $blockPosition->setPageLayout($page)->setPageId($page->getId())
            ->setTargetId(2)
            ->setBlock($block)->setBlockId($block->getId())
            ->setBlockRow(1)
            ->setAnywhere(1);
        $em->persist($blockPosition);
        $block->addBlockPosition($blockPosition);
        $em->persist($block);

        $block = $app['eccube.repository.block']->findOneBy(array('file_name' => 'tool_bar'));
        $blockPosition = new BlockPosition();
        $blockPosition->setPageLayout($page)->setPageId($page->getId())
            ->setTargetId(2)
            ->setBlock($block)->setBlockId($block->getId())
            ->setBlockRow(2)
            ->setAnywhere(1);
        $em->persist($blockPosition);
        $block->addBlockPosition($blockPosition);
        $em->persist($block);
        $em->flush();

        // Target 3
        $block = $app['eccube.repository.block']->findOneBy(array('file_name' => 'mainimg'));
        $blockPosition = new BlockPosition();
        $blockPosition->setPageLayout($page)->setPageId($page->getId())
            ->setTargetId(3)
            ->setBlock($block)->setBlockId($block->getId())
            ->setBlockRow(2)
            ->setAnywhere(0);
        $em->persist($blockPosition);
        $block->addBlockPosition($blockPosition);
        $em->persist($block);

        $block = $app['eccube.repository.block']->findOneBy(array('file_name' => 'page_pick'));
        $blockPosition = new BlockPosition();
        $blockPosition->setPageLayout($page)->setPageId($page->getId())
            ->setTargetId(3)
            ->setBlock($block)->setBlockId($block->getId())
            ->setBlockRow(3)
            ->setAnywhere(0);
        $em->persist($blockPosition);
        $block->addBlockPosition($blockPosition);
        $em->persist($block);

        $block = $app['eccube.repository.block']->findOneBy(array('file_name' => 'slider_main'));
        $blockPosition = new BlockPosition();
        $blockPosition->setPageLayout($page)->setPageId($page->getId())
            ->setTargetId(3)
            ->setBlock($block)->setBlockId($block->getId())
            ->setBlockRow(4)
            ->setAnywhere(0);
        $em->persist($blockPosition);
        $block->addBlockPosition($blockPosition);
        $em->persist($block);

        $block = $app['eccube.repository.block']->findOneBy(array('file_name' => 'breadcrumb'));
        $blockPosition = new BlockPosition();
        $blockPosition->setPageLayout($page)->setPageId($page->getId())
            ->setTargetId(3)
            ->setBlock($block)->setBlockId($block->getId())
            ->setBlockRow(1)
            ->setAnywhere(1);
        $em->persist($blockPosition);
        $block->addBlockPosition($blockPosition);
        $em->persist($block);
        $em->flush();

        // Target 5
        $block = $app['eccube.repository.block']->findOneBy(array('file_name' => 'main_panel'));
        $blockPosition = new BlockPosition();
        $blockPosition->setPageLayout($page)->setPageId($page->getId())
            ->setTargetId(5)
            ->setBlock($block)->setBlockId($block->getId())
            ->setBlockRow(2)
            ->setAnywhere(0);
        $em->persist($blockPosition);
        $block->addBlockPosition($blockPosition);
        $em->persist($block);

        $blockR = $app['eccube.repository.block']->findOneBy(array('file_name' => 'recommend_product_block'));
        if ($blockR) {
            $blockPosition = new BlockPosition();
            $blockPosition->setPageLayout($page)->setPageId($page->getId())
                ->setTargetId(5)
                ->setBlock($blockR)->setBlockId($blockR->getId())
                ->setBlockRow(1)
                ->setAnywhere(0);
            $em->persist($blockPosition);
            $blockR->addBlockPosition($blockPosition);
            $em->persist($blockR);
        }
        $em->flush();

        $block = $app['eccube.repository.block']->findOneBy(array('file_name' => 'top_side'));
        $blockPosition = new BlockPosition();
        $blockPosition->setPageLayout($page)->setPageId($page->getId())
            ->setTargetId(7)
            ->setBlock($block)->setBlockId($block->getId())
            ->setBlockRow(1)
            ->setAnywhere(0);
        $em->persist($blockPosition);
        $block->addBlockPosition($blockPosition);
        $em->persist($block);
        $em->flush();

        $block = $app['eccube.repository.block']->findOneBy(array('file_name' => 'top_link'));
        $blockPosition = new BlockPosition();
        $blockPosition->setPageLayout($page)->setPageId($page->getId())
            ->setTargetId(8)
            ->setBlock($block)->setBlockId($block->getId())
            ->setBlockRow(1)
            ->setAnywhere(0);
        $em->persist($blockPosition);
        $block->addBlockPosition($blockPosition);
        $em->persist($block);
        $em->flush();

        $block = $app['eccube.repository.block']->findOneBy(array('file_name' => 'footer'));
        $blockPosition = new BlockPosition();
        $blockPosition->setPageLayout($page)->setPageId($page->getId())
            ->setTargetId(9)
            ->setBlock($block)->setBlockId($block->getId())
            ->setBlockRow(1)
            ->setAnywhere(1);
        $em->persist($blockPosition);
        $block->addBlockPosition($blockPosition);
        $em->persist($block);
        $em->flush();

        // list page
        $pageList = $app['eccube.repository.page_layout']->find(2);

        $blockSale = $app['eccube.repository.block']->findOneBy(array('file_name' => 'sales_ranking'));
        if ($blockSale) {
            $blockPosition = new BlockPosition();
            $blockPosition->setPageLayout($pageList)->setPageId($pageList->getId())
                ->setTargetId(4)
                ->setBlock($blockSale)->setBlockId($blockSale->getId())
                ->setBlockRow(1)
                ->setAnywhere(0);
            $em->persist($blockPosition);
            $blockSale->addBlockPosition($blockPosition);
            $em->persist($blockSale);
        }

        $blockPR = $app['eccube.repository.block']->findOneBy(array('file_name' => 'plg_product_list_recommend'));
        if ($blockPR) {
            $blockPosition = new BlockPosition();
            $blockPosition->setPageLayout($pageList)->setPageId($pageList->getId())
                ->setTargetId(4)
                ->setBlock($blockPR)->setBlockId($blockPR->getId())
                ->setBlockRow(2)
                ->setAnywhere(0);
            $em->persist($blockPosition);
            $blockPR->addBlockPosition($blockPosition);
            $em->persist($blockPR);
        }
        $em->flush();

        // detail page
        $pageDetail = $app['eccube.repository.page_layout']->find(3);

        $blockChecked = $app['eccube.repository.block']->findOneBy(array('file_name' => 'checkeditem'));
        if ($blockChecked) {
            $blockPosition = new BlockPosition();
            $blockPosition->setPageLayout($pageDetail)->setPageId($pageDetail->getId())
                ->setTargetId(6)
                ->setBlock($blockChecked)->setBlockId($blockChecked->getId())
                ->setBlockRow(2)
                ->setAnywhere(0);
            $em->persist($blockPosition);
            $blockChecked->addBlockPosition($blockPosition);
            $em->persist($blockChecked);
        }

        $blockHSD = $app['eccube.repository.block']->findOneBy(array('file_name' => 'hsd_related_product'));
        if ($blockHSD) {
            $blockPosition = new BlockPosition();
            $blockPosition->setPageLayout($pageDetail)->setPageId($pageDetail->getId())
                ->setTargetId(6)
                ->setBlock($blockHSD)->setBlockId($blockHSD->getId())
                ->setBlockRow(1)
                ->setAnywhere(0);
            $em->persist($blockPosition);
            $blockHSD->addBlockPosition($blockPosition);
            $em->persist($blockHSD);
        }
        $em->flush();

        // Index B
        $pageB = $app['eccube.repository.page_layout']->findOneBy(array('file_name' => 'index_b'));
        if ($pageB) {
            $block = $app['eccube.repository.block']->findOneBy(array('file_name' => 'bottom_main_type_b'));
            $blockPosition = new BlockPosition();
            $blockPosition->setPageLayout($pageB)->setPageId($pageB->getId())
                ->setTargetId(6)
                ->setBlock($block)->setBlockId($block->getId())
                ->setBlockRow(1)
                ->setAnywhere(0);
            $em->persist($blockPosition);
            $block->addBlockPosition($blockPosition);
            $em->persist($block);
        }
        $em->flush();

        // Index C
        $pageC = $app['eccube.repository.page_layout']->findOneBy(array('file_name' => 'index_c'));
        if ($pageC) {
            $block = $app['eccube.repository.block']->findOneBy(array('file_name' => 'top_sale_slider_c'));
            $blockPosition = new BlockPosition();
            $blockPosition->setPageLayout($pageC)->setPageId($pageC->getId())
                ->setTargetId(6)
                ->setBlock($block)->setBlockId($block->getId())
                ->setBlockRow(1)
                ->setAnywhere(0);
            $em->persist($blockPosition);
            $block->addBlockPosition($blockPosition);
            $em->persist($block);

            $block = $app['eccube.repository.block']->findOneBy(array('file_name' => 'main_c'));
            $blockPosition = new BlockPosition();
            $blockPosition->setPageLayout($pageC)->setPageId($pageC->getId())
                ->setTargetId(6)
                ->setBlock($block)->setBlockId($block->getId())
                ->setBlockRow(2)
                ->setAnywhere(0);
            $em->persist($blockPosition);
            $block->addBlockPosition($blockPosition);
            $em->persist($block);
        }
        $em->flush();

        $app['eccube.repository.master.tag'];

        $tag = new Tag();
        $tag->setId(4)->setName('期間限定格')->setRank(4);
        $em->persist($tag);
        $em->flush();

        $tag = new Tag();
        $tag->setId(5)->setName('プレイスダワン商品')->setRank(5);
        $em->persist($tag);
        $em->flush();

        $tag = new Tag();
        $tag->setId(6)->setName('キャンページ中')->setRank(6);
        $em->persist($tag);
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
