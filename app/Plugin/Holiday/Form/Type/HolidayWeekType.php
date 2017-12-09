<?php
namespace Plugin\Holiday\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class HolidayWeekType extends AbstractType{

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
			->add('week', 'choice', array(
				'label' => '日付(日)',
				'choices' => array(0=>'日', 1=>'月', 2=>'火', 3=>'水', 4=>'木', 5=>'金', 6=>'土'),
				'required' => false,
				'expanded' => true,
				'multiple' => true,
			))
			->add('id', 'hidden', array())
			->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
	}

	/**
	* {@inheritdoc}
	**/
	public function getName(){
		return 'admin_holiday_week';
	}
}
