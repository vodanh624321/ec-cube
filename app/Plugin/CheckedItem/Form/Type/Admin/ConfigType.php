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

namespace Plugin\CheckedItem\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConfigType extends AbstractType
{
    public $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $app = $this->app;

        $arrDelete[0] = "表示";
        $arrDelete[1] = "非表示";

        $builder
            ->add('term', 'number', array(
                'label' => '保存日数',
                'required' => true,
            ))
            ->add('display_num', 'number', array(
                'label' => '表示個数',
                'required' => true,
            ))
            ->add('delete', 'choice', array(
                'label' => '履歴削除ボタン',
                'required' => true,
                'expanded' => true,
                'multiple' => false,
                'empty_value' => false,
                'choices'  => $arrDelete,
            ))
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber())
        ;

    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_setting_checkeditem';
    }
}
