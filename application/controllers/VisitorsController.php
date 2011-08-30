<?php

class VisitorsController extends Zend_Controller_Action
{

    public $doc_key = '0AjqGPI5Q_Ez6dGllR2x3NHEwU1VaV3oyb2pFVl9Vc0E';

    public function init()
    {
        /* Initialize action controller here */
	$this->form = new Zend_Form;
    }

    private function getForm()
    {
//       $form = new Zend_Form;
      $this->form->setAction("/gds/public/visitors/search")->setMethod('post');
      $this->form->addElement('text', 'name', array('label' => 'Nombre'));
      $this->form->addElement('text', 'institution', array('label' => 'Institución'));
      $this->form->addElement('text', 'country', array('label' => 'Pais'));
      $this->form->addElement('hidden', 'yearLabel', array('label' => 'Años (Intervalo)'));
      $this->form->addElement('text', 'yearInit',array('disableLoadDefaultDecorators' => true,'decorators'=>Array('ViewHelper','Errors')));
//       $this->form->yearInit->addValidator('Regex', false, array('/^[0-9]{4,}$/i'));
      $this->form->addElement('text', 'yearEnd', array('disableLoadDefaultDecorators' => true,'decorators'=>Array('ViewHelper')));
      $this->form->addElement('text', 'projectid', array('label' => 'Id del proyecto'));
      $this->form->addElement('text', 'group', array('label' => 'Grupo'));
      $this->form->addElement('text', 'seminar', array('label' => 'Seminario'));
      $this->form->addElement('submit', 'search', array('label' => 'Buscar'));
      return $this->form;
    }

    public function indexAction()
    {
     $this->view->form = $this->getForm();
     echo $this->view->form;
    }

    public function searchAction()
    {
        // action body
    }


}



