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

class HeaderInController
{
    public function index(Application $app, Request $request)
    {
        $keywords = array();

        if (isset($app['shiro8_hot_keyword3.repository.shiro8_hot_keyword_config'])) {
            $KeyWordConfig = $app['shiro8_hot_keyword3.repository.shiro8_hot_keyword_config']->find(1);
            if ($KeyWordConfig && $KeyWordConfig->getKeyword()) {
                $keywords = explode(',', $KeyWordConfig->getKeyword());
            }
        }

        return $app->render('Block/header_in.twig', array(
            'keywords' => $keywords
        ));
    }
}