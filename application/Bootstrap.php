<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

   protected function _initView()
   {
      $view = new Zend_View();
      $view->setEncoding('utf-8');
      $view->doctype();
      $view->headLink()->appendStylesheet('css/global.css');
      $view->headScript()->appendFile('js/json.js');
      $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
      $viewRenderer->setView($view);
      return $view;
   }
}

