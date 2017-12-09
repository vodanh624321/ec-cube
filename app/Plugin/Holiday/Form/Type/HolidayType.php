<?php
namespace Plugin\Holiday\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class HolidayType extends AbstractType{

	private $app;

	public function __construct(\Eccube\Application $app){
		$this->app = $app;
	}

	/**
	* Build config type form
	*
	* @param FormBuilderInterface $builder
	* @param array $options
	* @return type
	**/
	public function buildForm(FormBuilderInterface $builder, array $options){
		$builder
			->add('title', 'text', array(
				'label' => '休日タイトル',
				'required' => false,
				'constraints' => array(new Assert\NotBlank(array('message' => ' 休日タイトルが入力されていません。')),),
			))
			->add('month', 'choice', array(
				'label' => '日付(月)',
				'choices' => array(1=>'1', 2=>'2', 3=>'3', 4=>'4', 5=>'5', 6=>'6', 7=>'7', 8=>'8', 9=>'9', 10=>'10', 11=>'11', 12=>'12'),
				'required' => false,
				'constraints' => array(new Assert\NotBlank(array('message' => ' 日付(月)が選択されていません。')),),
			))
			->add('day', 'choice', array(
				'label' => '日付(日)',
				'choices' => array(1=>'1', 2=>'2', 3=>'3', 4=>'4', 5=>'5', 6=>'6', 7=>'7', 8=>'8', 9=>'9', 10=>'10', 11=>'11', 12=>'12', 13=>'13', 14=>'14', 15=>'15', 16=>'16', 17=>'17', 18=>'18', 19=>'19', 20=>'20', 21=>'21', 22=>'22', 23=>'23', 24=>'24', 25=>'25', 26=>'26', 27=>'27', 28=>'28', 29=>'29', 30=>'30', 31=>'31'),
				'required' => false,
				'constraints' => array(new Assert\NotBlank(array('message' => ' 日付(日)が選択されていません。')),),
			))
			->add('id', 'hidden', array())
			->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
	}

	/**
	* {@inheritdoc}
	**/
	public function getName(){
		return 'admin_holiday';
	}
}
