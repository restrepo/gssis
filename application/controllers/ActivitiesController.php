<?php

class ActivitiesController extends Zend_Controller_Action
{

    private function getForm()
    {
        $this->form->setAction($this->view->url(array('controller' => 'activities', 'action' => 'search')))->setMethod('post');
        $this->form->addElement('text', 'name', array('label' => 'Nombre'));
        $this->form->getElement('name')->setRequired(true);
        $this->form->addElement('text', 'year', array('label' => 'Año'));
        $this->form->getElement('year')->setRequired(true);
        $this->form->getElement('year')->addValidator("Digits");
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
        }
        echo $form;

        $values = urlencode(serialize($form->getValues()));


    }


}



