<?php
namespace Plugin\Holiday\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class HolidayConfigType extends AbstractType{

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
			->add('config_data', 'choice', array(
				'label' => 'カレンダー表示月数(○ヵ月分)',
				'choices' => array(1=>'1', 2=>'2', 3=>'3', 4=>'4', 5=>'5', 6=>'6', 7=>'7', 8=>'8', 9=>'9', 10=>'10', 11=>'11', 12=>'12'),
				'required' => true,
				'constraints' => array(new Assert\NotBlank(array('message' => ' 表示月数が選択されていません。')),),
			))
			->add('id', 'hidden', array())
			->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
	}

	/**
	* {@inheritdoc}
	**/
	public function getName(){
		return 'admin_holiday_config';
	}
}
