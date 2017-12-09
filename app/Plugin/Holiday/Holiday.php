<?php
namespace Plugin\Holiday;

use Eccube\Event\RenderEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\CssSelector\CssSelector;
use Symfony\Component\DomCrawler\Crawler;

class Holiday{

	private $app;

	public function __construct($app){
		$this->app = $app;
	}

	public function onRenderHoliday(){
		$HolidayConfig = $this->app['eccube.plugin.holiday.repository.holidayconfig']->findConfig(1);
		$HolidayWeekConfig = $this->app['eccube.plugin.holiday.repository.holidayweek']->findWeek(1);
		$HolidayWeekConfig[0]['week'] = unserialize($HolidayWeekConfig[0]['week']);

		/* 月毎の定休日情報の取得 */
		for($i=1; $i<=12; $i++){
			$GetHolidays = $this->app['eccube.plugin.holiday.repository.holiday']->findMonthDay($i);
			for($j=0; $j<count($GetHolidays); $j++){
				$Holidays[$i][$GetHolidays[$j]['day']] = $GetHolidays[$j]['day'];
			}
		}

		/* 曜日毎の定休日設定情報の取得 */
		for($i=0; $i<=6; $i++){
			if(is_array($HolidayWeekConfig[0]['week']) && in_array($i, $HolidayWeekConfig[0]['week'])){
				$HolidayWeek[$i] = true;
			} else {
				$HolidayWeek[$i] = false;
			}
		}

		$this->app['twig']->addGlobal('Holidays', $Holidays);
		$this->app['twig']->addGlobal('HolidayWeek', $HolidayWeek);
		$this->app['twig']->addGlobal('HolidayConfig', $HolidayConfig);
	}
}
