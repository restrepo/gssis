<?php

class StudentsController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
	$this->form = new Zend_Form;
    }

    private function getForm()
    {
//       $form = new Zend_Form;
      $this->form->setAction($this->view->url(array('controller' => 'students', 'action' => 'search')))->setMethod('post');
      $this->form->addElement('text', 'name', array('label' => 'Nombre'));
      $this->form->addElement('text', 'id', array('label' => 'Identificación'));
      $this->form->addElement('text', 'advisor', array('label' => 'Director'));
      $this->form->addElement('hidden', 'yearLabel', array('label' => 'Años (Intervalo)'));
      $this->form->addElement('text', 'yearInit',array('disableLoadDefaultDecorators' => true,'decorators'=>Array('ViewHelper','Errors')));
//       $this->form->yearInit->addValidator('Regex', false, array('/^[0-9]{4,}$/i'));
      $this->form->addElement('text', 'yearEnd', array('disableLoadDefaultDecorators' => true,'decorators'=>Array('ViewHelper')));
      $this->form->addElement('text', 'group', array('label' => 'Grupo'));
      $this->form->addElement('text', 'projectid', array('label' => 'Id del proyecto'));
      $this->form->addElement('submit', 'search', array('label' => 'Buscar'));
      return $this->form;
    }

    public function links()
    {
	echo "<a href='https://docs.google.com/spreadsheet/ccc?key=0AjqGPI5Q_Ez6dHJwV0tzaHRzOXlHcHVZSmwySnRTU1E&hl=es#gid=0' target='_blank'>Ir a Google Docs</a><br>";
    }

    public function indexAction()
    {
     $this->view->form = $this->getForm();
     echo $this->view->form;
      $this->links();
    }

    public function searchAction()
    {
        // action body
        if (!$this->getRequest()->isPost()) {
            return $this->_forward('index');
        }
        $form = $this->getForm();
        if (!$this->form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return $this->render('form');
        }

        $values = urlencode(serialize($form->getValues()));

        $this->view->headScript()->appendFile($this->view->baseUrl().'/js/json.js');
        $this->view->headScript()->appendFile($this->view->baseUrl().'/js/estudiantes.php?s='.$values);

	$this->links();
    }

}