<?php
namespace Plugin\Holiday\Controller;

use Plugin\Holiday\Form\Type\HolidayType;
use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception as HttpException;

class HolidayController{
	private $main_title;
	private $sub_title;

	public function __construct(){
	}

	public function index(Application $app, Request $request, $id){

		$repos = $app['eccube.plugin.holiday.repository.holiday'];
		$TargetHoliday = new \Plugin\Holiday\Entity\Holiday();
		if($id) {
			$TargetHoliday = $repos->find($id);
			if(!$TargetHoliday) {
				throw new NotFoundHttpException();
			}
		}

		$form = $app['form.factory']->createBuilder('admin_holiday', $TargetHoliday)->getForm();

		if('POST' === $request->getMethod()) {
			$form->handleRequest($request);
			if($form->isValid()) {
				$status = $repos->save($TargetHoliday);

				if($status) {
					$app->addSuccess('admin.holiday.save.complete', 'admin');
					return $app->redirect($app->url('admin_holiday'));
				} else {
					$app->addError('admin.holiday.save.error', 'admin');
				}
			}
		}

		$Holidays = $app['eccube.plugin.holiday.repository.holiday']->findAll();

		return $app->render('Holiday/View/admin/holiday.twig', array(
			'form'   		=> $form->createView(),
			'Holidays' 		=> $Holidays,
			'TargetHoliday' 	=> $TargetHoliday,
		));

	}

	public function delete(Application $app, Request $request, $id){

		$repos = $app['eccube.plugin.holiday.repository.holiday'];
		$TargetHoliday = $repos->find($id);
		if(!$TargetHoliday) {
			throw new NotFoundHttpException();
		}

		$form = $app['form.factory']->createNamedBuilder('admin_holiday', 'form', null, array('allow_extra_fields' => true,))->getForm();
		$status = false;

		if('DELETE' === $request->getMethod()) {
			$form->handleRequest($request);
			$status = $repos->delete($TargetHoliday);
		}
		if($status === true) {
			$app->addSuccess('admin.holiday.delete.complete', 'admin');
		} else {
			$app->addError('admin.holiday.delete.error', 'admin');
		}
		return $app->redirect($app->url('admin_holiday'));

	}

}
