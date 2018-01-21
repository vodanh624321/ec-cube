<?php
/**
 * Created by PhpStorm.
 * User: WIN7
 * Date: 01/21/2018
 * Time: 3:47 PM
 */

namespace Eccube\Controller\Block;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class ToolBarController
{
    public function index(Application $app, Request $request)
    {
        $builder = $app['form.factory']
            ->createNamedBuilder('', 'search_product_block')
            ->setMethod('GET');

        /** @var $request \Symfony\Component\HttpFoundation\Request */
        $request = $app['request_stack']->getMasterRequest();

        /** @var $form \Symfony\Component\Form\Form */
        $form = $builder->getForm();
        $form->handleRequest($request);

        /** @var $Cart \Eccube\Entity\Cart */
        $Cart = $app['eccube.service.cart']->getCartObj();

        return $app->render('Block/tool_bar.twig', array(
            'form' => $form->createView(),
            'Cart' => $Cart
        ));
    }
}