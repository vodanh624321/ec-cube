<?php
/* ActiveFusions 2015/10/05 9:44 */

namespace Plugin\Holiday\ServiceProvider;

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class HolidayServiceProvider implements ServiceProviderInterface{

	public function register(BaseApplication $app){

		// 定休日管理テーブル用リポジトリ
		$app['eccube.plugin.holiday.repository.holiday'] = $app->share(function () use ($app) {
			return $app['orm.em']->getRepository('Plugin\Holiday\Entity\Holiday');
		});

		// 定休曜日管理テーブル用レポジトリ
		$app['eccube.plugin.holiday.repository.holidayweek'] = $app->share(function () use ($app) {
			return $app['orm.em']->getRepository('Plugin\Holiday\Entity\HolidayWeek');
		});

		// 定休日基本設定テーブル用レポジトリ
		$app['eccube.plugin.holiday.repository.holidayconfig'] = $app->share(function () use ($app) {
			return $app['orm.em']->getRepository('Plugin\Holiday\Entity\HolidayConfig');
		});

		// Setting
		$app->match('/' . $app["config"]["admin_route"] . '/plugin/holiday/config/', '\\Plugin\\Holiday\\Controller\\ConfigController::edit')
			->value('id', 1)->assert('id', '\d+|')
			->bind('plugin_holiday_config');

		// 一覧・登録・修正
		$app->match('/' . $app["config"]["admin_route"] . '/setting/holiday/{id}', '\\Plugin\\Holiday\\Controller\\HolidayController::index')
			->value('id', null)->assert('id', '\d+|')
			->bind('admin_holiday');

		// 定休日　曜日
		$app->match('/' . $app["config"]["admin_route"] . '/setting/holidayweek/{id}', '\\Plugin\\Holiday\\Controller\\HolidayWeekController::index')
			->value('id', 1)->assert('id', '\d+|')
			->bind('admin_holiday_week');

		// 削除
		$app->match('/' . $app["config"]["admin_route"] . '/setting/holiday/{id}/delete', '\\Plugin\\Holiday\\Controller\\HolidayController::delete')
			->value('id', null)->assert('id', '\d+|')
			->bind('admin_holiday_delete');

		// 型登録
		$app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
			$types[] = new \Plugin\Holiday\Form\Type\HolidayConfigType($app);
			$types[] = new \Plugin\Holiday\Form\Type\HolidayType($app);
			$types[] = new \Plugin\Holiday\Form\Type\HolidayWeekType($app);
			return $types;
		}));

		// メッセージ登録
		$app['translator'] = $app->share($app->extend('translator', function ($translator, \Silex\Application $app) {
			$translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());
			$file = __DIR__ . '/../Resource/locale/message.' . $app['locale'] . '.yml';
			if(file_exists($file)) {
				$translator->addResource('yaml', $file, $app['locale']);
			}
			return $translator;
		}));


		// 定休日管理
		$app['config'] = $app->share($app->extend('config', function ($config) {
			$addNavi['id'] = "holiday";
			$addNavi['name'] = "定休日管理";
			$addNavi['has_child'] = true;
			$addNavi['child'] =
			array(
				array('id'=>'holiday_config', 'name'=>'基本設定', 'url'=>'plugin_holiday_config'),
				array('id'=>'holiday_week', 'name'=>'定休日管理', 'url'=>'admin_holiday_week'),
				array('id'=>'holiday', 'name'=>'休日管理', 'url'=>'admin_holiday')
			);
			foreach ($config['nav'] as $key => $val) {
				if("setting" == $val["id"]) {
					$config['nav'][$key]['child'][] = $addNavi;
				}
			}
			return $config;
		}));
	}

	public function boot(BaseApplication $app){

	}
}
