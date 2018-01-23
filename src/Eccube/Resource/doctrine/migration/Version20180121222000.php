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
        $this->addSql("INSERT INTO dtb_page_layout (device_type_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots, meta_tags) VALUES (10, 'TOPページ', 'homepage_b', 'index_b', 2, NULL, NULL, NULL, NULL, '2018-01-21 14:18:31', '2018-01-21 14:18:31', NULL, NULL);");
        $this->addSql("INSERT INTO dtb_page_layout (device_type_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots, meta_tags) VALUES (10, 'TOPページ', 'homepage_c', 'index_c', 2, NULL, NULL, NULL, NULL, '2018-01-21 14:18:31', '2018-01-21 14:18:31', NULL, NULL);");
        $this->addSql("INSERT INTO dtb_page_layout (device_type_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots, meta_tags) VALUES (10, '初めての方へ', 'help_recommend', 'Help/recommend', 2, NULL, NULL, NULL, NULL, '2018-01-21 14:18:31', '2018-01-21 14:18:31', NULL, NULL);");
        $this->addSql("INSERT INTO dtb_page_layout (device_type_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots, meta_tags) VALUES (10, 'News', 'news_list', 'News/list', 2, NULL, NULL, NULL, NULL, '2018-01-21 14:18:31', '2018-01-21 14:18:31', NULL, NULL);");
        $this->addSql("INSERT INTO dtb_page_layout (device_type_id, page_name, url, file_name, edit_flg, author, description, keyword, update_url, create_date, update_date, meta_robots, meta_tags) VALUES (10, 'News detail', 'news_detail', 'News/detail', 2, NULL, NULL, NULL, NULL, '2018-01-21 14:18:31', '2018-01-21 14:18:31', NULL, NULL);");
        $this->addSql("INSERT INTO dtb_block (device_type_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (10, 'Header In', 'header_in', '2018-01-21 16:45:14', '2018-01-21 16:45:14', 0, 1);");
        $this->addSql("INSERT INTO dtb_block (device_type_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (10, 'tool bar', 'tool_bar', '2018-01-21 16:46:35', '2018-01-21 16:46:35', 1, 1);");
        $this->addSql("INSERT INTO dtb_block (device_type_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (10, 'navi', 'navi', '2018-01-21 16:47:14', '2018-01-21 16:47:14', 0, 1);");
        $this->addSql("INSERT INTO dtb_block (device_type_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (10, 'mainimg', 'mainimg', '2018-01-21 16:48:43', '2018-01-21 16:48:43', 0, 1);");
        $this->addSql("INSERT INTO dtb_block (device_type_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (10, 'page pick', 'page_pick', '2018-01-21 16:49:29', '2018-01-21 16:49:29', 0, 1);");
        $this->addSql("INSERT INTO dtb_block (device_type_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (10, 'slider main', 'slider_main', '2018-01-21 16:50:55', '2018-01-21 16:50:55', 0, 1);");
        $this->addSql("INSERT INTO dtb_block (device_type_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (10, 'Main panel', 'main_panel', '2018-01-21 16:51:48', '2018-01-21 16:51:48', 0, 1);");
        $this->addSql("INSERT INTO dtb_block (device_type_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (10, 'top side', 'top_side', '2018-01-21 16:52:43', '2018-01-21 16:52:43', 0, 1);");
        $this->addSql("INSERT INTO dtb_block (device_type_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (10, 'top link', 'top_link', '2018-01-21 16:53:39', '2018-01-21 16:53:39', 0, 1);");
        $this->addSql("INSERT INTO dtb_block (device_type_id, block_name, file_name, create_date, update_date, logic_flg, deletable_flg) VALUES (10, 'breadcrumb', 'breadcrumb', '2018-01-21 19:39:44', '2018-01-21 23:38:56', 0, 1);");

        $this->addSql("DELETE FROM dtb_block_position");
        $this->addSql("INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 9, 7, 1, 1);");
        $this->addSql("INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 2, 11, 1, 1);");
        $this->addSql("INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 2, 12, 2, 1);");
        $this->addSql("INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 2, 13, 3, 1);");
        $this->addSql("INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 3, 14, 2, 0);");
        $this->addSql("INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 3, 15, 3, 0);");
        $this->addSql("INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 3, 16, 4, 0);");
        $this->addSql("INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 5, 17, 1, 0);");
        $this->addSql("INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 7, 18, 1, 0);");
        $this->addSql("INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 8, 19, 1, 0);");
        $this->addSql("INSERT INTO dtb_block_position (page_id, target_id, block_id, block_row, anywhere) VALUES (1, 3, 20, 1, 1);");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
