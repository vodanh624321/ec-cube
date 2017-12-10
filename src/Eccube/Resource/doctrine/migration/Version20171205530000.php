<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Eccube\Entity\Plugin;
use Eccube\Service\PluginService;
use Eccube\Util\Cache;

class Version20171205530000 extends AbstractMigration
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
        $pluginService = $app['eccube.service.plugin'];
        /** @var Plugin $plugin */
        $plugin = $app['eccube.repository.plugin']->findOneBy(array('code' => 'Recommend', 'del_flg' => 0));
        if ($plugin) {
            return;
        }
        $dir = $app['config']['plugin_realdir'].'/Recommend/';
        $config = $pluginService->readYml($dir.PluginService::CONFIG_YML);
        $event = $pluginService->readYml($dir.PluginService::EVENT_YML);
        $pluginService->registerPlugin($config, $event, 0); // dbにプラグイン登録
        $app->writePluginConfigCache();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
