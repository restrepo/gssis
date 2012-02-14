<?php

class ActivitiesController extends Zend_Controller_Action
{
    public $form = null;
    public $doc_keyProjects="0AjqGPI5Q_Ez6dFE1S2pWQkZJdFkycWFvNXdaMDhkWFE";

    private function getForm()
    {
        $this->form->setAction($this->view->url(array('controller' => 'activities', 'action' => 'search')))->setMethod('post');
        $this->form->addElement('text', 'name', array('label' => 'Nombre'));
        $this->form->addElement('hidden', 'yearLabel', array('label' => 'Años (Intervalo)'));
        $this->form->addElement('text', 'yearInit',array('disableLoadDefaultDecorators' => true,'decorators'=>Array('ViewHelper','Errors')));
        $this->form->addElement('text', 'yearEnd', array('disableLoadDefaultDecorators' => true,'decorators'=>Array('ViewHelper')));
        $this->form->getElement('yearInit')->addValidator("Digits");
        $this->form->getElement('yearEnd')->addValidator("Digits");
        $this->form->addElement('select', 'format', array('label' => 'Formato', 'autocomplete'=>false, 'multiOptions'=>array('list' => 'Lista','csv'=>'CSV')));
        $this->form->addElement('submit', 'search', array('label' => 'Buscar'));
        return $this->form;
    }


    public function init()
    {
        /* Initialize action controller here */
        $this->form = new Zend_Form;
    }

    public function indexAction()
    {
        // action body
        $this->view->form = $this->getForm();
        echo $this->view->form;
    }

    public function searchAction()
    {
        // action body
        if (!$this->getRequest()->isPost()) {
            return $this->_forward('index');
        }

        $form = $this->getForm();
        if (!$form->isValid($_POST)) {
            // Failed validation; redisplay form
            echo $form;
            return;
        }
        echo $form;
        $values = urlencode(serialize($form->getValues()));

        $this->view->headScript()->appendFile($this->view->baseUrl().'/js/json.js');
        $this->view->headScript()->appendFile($this->view->baseUrl().'/js/actividades.php?activities='.$values);



    }


}



