<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Form\Type;

use Eccube\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SearchProductType to search product.
 */
class SearchProductType extends AbstractType
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * SearchProductType constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $priceRange = array(100, 500, 1000, 5000, 10000, 20000);
        $searchPlaceHolderMessage = '製品名とキーワードで検索します';
        // if has install plugin, get it
        if (isset($this->app['crowl_plg_cust_prod_search_b.repository.crowl_plg_cust_prod_search_b_cfg'])) {
            /** @var \Plugin\CRowlPlgCustProdSearchB\Repository\CRowlPlgCustProdSearchBCfgRepository $repo */
            $repo = $this->app['crowl_plg_cust_prod_search_b.repository.crowl_plg_cust_prod_search_b_cfg'];
            /** @var \Plugin\CRowlPlgCustProdSearchB\Entity\CRowlPlgCustProdSearchBCfg $config */
            $config = $repo->find(1);
            if ($config) {
                if ($config->getSearchProductPrice() && $config->getPriceRange()) {
                    $priceRange = explode(',', $config->getPriceRange());
                }
            }

            $searchPlaceHolderMessage = '商品名やお申込番号、型番で検索';
        }

//        $Categories = $this->app['eccube.repository.category']
//            ->getList(null, true);

        $builder->add('mode', 'hidden', array(
            'data' => 'search',
        ));
//        $builder->add('category_id', 'entity', array(
//            'class' => 'Eccube\Entity\Category',
//            'property' => 'NameWithLevel',
//            'choices' => $Categories,
//            'empty_value' => '全ての商品',
//            'empty_data' => null,
//            'required' => false,
//            'label' => '商品カテゴリから選ぶ',
//        ));
        $builder->add('category_id', 'hidden', array());

        $builder->add('name', 'search', array(
            'required' => false,
            'label' => '商品名を入力',
            'empty_data' => null,
            'attr' => array(
                'maxlength' => 50,
                'placeholder' => $searchPlaceHolderMessage,
            ),
        ));
        $builder->add('pageno', 'hidden', array());
        $builder->add('disp_number', 'hidden', array());
        $builder->add('orderby', 'hidden', array());
        $builder->add('price_range_from', 'choice', array(
            'choices' => array_combine($priceRange, $priceRange),
            'empty_value' => '指定ない',
            'empty_data' => null,
            'required' => false,
            'label' => '価格',
        ));
        $builder->add('price_range_to', 'choice', array(
            'choices' => array_combine($priceRange, $priceRange),
            'empty_value' => '指定ない',
            'empty_data' => null,
            'required' => false,
            'label' => '価格',
        ));
        $builder->add('recommend_id', 'collection', array('type' => 'hidden'));
        $builder->add('tag_id', 'hidden', array());
        $builder->add('fast_search');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'search_product';
    }
}
