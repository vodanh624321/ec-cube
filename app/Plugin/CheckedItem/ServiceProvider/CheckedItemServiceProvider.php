<?php
/*
* Plugin Name : CheckedItem
*
* Copyright (C) 2015 BraTech Co., Ltd. All Rights Reserved.
* http://www.bratech.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Plugin\CheckedItem\ServiceProvider;

use Eccube\Application;
use Eccube\Common\Constant;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class CheckedItemServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {

    	// Routing
        $admin = $app['controllers_factory'];
        $front = $app['controllers_factory'];
        // 強制SSL
        if ($app['config']['force_ssl'] == Constant::ENABLED) {
            $admin->requireHttps();
            $front->requireHttps();
        }

        $admin->match('/plugin/checkeditem/config','Plugin\CheckedItem\Controller\Admin\ConfigController::index')->bind('plugin_CheckedItem_config');
    	$front->match('/block/checkeditem', '\Plugin\\CheckedItem\Controller\CheckedItemController::index')->bind('block_checkeditem');
        $front->match('/block/checkeditem/delete', '\Plugin\\CheckedItem\Controller\CheckedItemController::delete')->bind('block_checkeditem_delete');

        $app->mount('/'.trim($app['config']['admin_route'], '/').'/', $admin);
        $app->mount('', $front);

        // Form/Type
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use($app) {
            $types[] = new \Plugin\CheckedItem\Form\Type\Admin\ConfigType($app);
            return $types;
        }));

        // Repositoy
        $app['eccube.checkeditem.repository.config'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\CheckedItem\Entity\Config');
        });

        $app['eccube.checkeditem.service.util'] = $app->share(function () use ($app) {
            return new \Plugin\CheckedItem\Service\UtilService($app);
        });

        // locale message
        $app['translator'] = $app->share($app->extend('translator', function ($translator, \Silex\Application $app) {
            $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());

            $file = __DIR__ . '/../Resource/locale/message.' . $app['locale'] . '.yml';
            if (file_exists($file)) {
                $translator->addResource('yaml', $file, $app['locale']);
            }

            return $translator;
        }));
    }

    public function boot(BaseApplication $app)
    {
    }
}
