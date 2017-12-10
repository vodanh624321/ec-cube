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
use Eccube\Common\Constant;
use Eccube\Entity\Plugin;
use Eccube\Service\PluginService;
use Eccube\Util\Cache;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171202200000 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // Install plugin
        $app = \Eccube\Application::getInstance();
        $app->removePluginConfigCache();
        Cache::clear($app, false);
        /** @var Plugin $repoPlugin */
        $repoPlugin = $app['eccube.repository.plugin']->findOneBy(array('code' => 'RelatedProduct'));
        if ($repoPlugin) {
            /** @var PluginService $pluginService */
            $pluginService = $app['eccube.service.plugin'];
            if ($repoPlugin->getEnable() == Constant::ENABLED) {
                $pluginService->disable($repoPlugin);
            }
            $pluginService->enable($repoPlugin);
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
