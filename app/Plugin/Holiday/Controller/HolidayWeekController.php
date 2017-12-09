<?php
namespace Plugin\Holiday\Controller;

use Plugin\Holiday\Form\Type\HolidayWeekType;
use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception as HttpException;

class HolidayWeekController{
	private $main_title;
	private $sub_title;

	public function __construct(){
	}

	public function index(Application $app, Request $request, $id){
		$repos = $app['eccube.plugin.holiday.repository.holidayweek'];
		$TargetHolidayWeek = new \Plugin\Holiday\Entity\HolidayWeek();
		if($id) {
			$TargetHolidayWeek = $repos->find($id);
			if(!$TargetHolidayWeek) {
				throw new NotFoundHttpException();
			}
			$TargetHolidayWeek->week = unserialize($TargetHolidayWeek->week);
		}

		$form = $app['form.factory']->createBuilder('admin_holiday_week', $TargetHolidayWeek)->getForm();

		if('POST' === $request->getMethod()) {
			$form->handleRequest($request);
			if($form->isValid()) {
				// 取得した曜日選択情報をシリアライズ
				$TargetHolidayWeek->week = serialize($TargetHolidayWeek->week);

				$status = $repos->save($TargetHolidayWeek);
				if($status) {
					$app->addSuccess('admin.holiday.save.complete', 'admin');
					return $app->redirect($app->url('admin_holiday_week'));
				} else {
					$app->addError('admin.holiday.save.error', 'admin');
				}
			}
		}

		return $app->render('Holiday/View/admin/week.twig', array(
			'form' => $form->createView(),
			'TargetHolidayWeek' => $TargetHolidayWeek,
		));
	}










}
