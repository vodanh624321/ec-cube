<?php
namespace Plugin\Holiday\Controller;

use Plugin\Holiday\Form\Type\HolidayConfigType;
use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception as HttpException;

class ConfigController{

	public function edit(Application $app, Request $request, $id) {
		$repos = $app['eccube.plugin.holiday.repository.holidayconfig'];
		$TargetHolidayConfig = new \Plugin\Holiday\Entity\HolidayConfig();
		if($id) {
			$TargetHolidayConfig = $repos->find($id);
			if(!$TargetHolidayConfig) {
				throw new NotFoundHttpException();
			}
		}

		$form = $app['form.factory']->createBuilder('admin_holiday_config', $TargetHolidayConfig)->getForm();

		if('POST' === $request->getMethod()) {
			$form->handleRequest($request);
			if($form->isValid()) {
				$status = $repos->save($TargetHolidayConfig);
				if($status) {
					$app->addSuccess('admin.holiday.save.complete', 'admin');
					return $app->redirect($app->url('plugin_holiday_config'));
				} else {
					$app->addError('admin.holiday.save.error', 'admin');
				}
			}
		}

		return $app->render('Holiday/View/admin/holiday_config.twig', array(
			'form' => $form->createView(),
			'TargetHolidayConfig' => $TargetHolidayConfig,
		));

	}
}
