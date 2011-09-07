<?php

class StudentsController extends Zend_Controller_Action
{

   
    public $doc_key = '0AjqGPI5Q_Ez6dHJwV0tzaHRzOXlHcHVZSmwySnRTU1E';

    public function init()
    {
        /* Initialize action controller here */
	$this->form = new Zend_Form;
    }

    private function getForm()
    {
//       $form = new Zend_Form;
      $this->form->setAction("/gds/public/students/search")->setMethod('post');
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

    public function indexAction()
    {
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
        if (!$this->form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return $this->render('form');
        }

        $values = $form->getValues();
	
	
	$yearInitCode='';
	$yearEndCode='';
// 	if(!empty($values['yearInit']))
// 	{
// 	  $yearInitCode="(C>=".$values['yearInit'].")+and+";
// 	}
// 	if(!empty($values['yearEnd']))
// 	{
// 	  $yearEndCode="(C<=".$values['yearEnd'].")+and+";
// 
// 	}

	$this->view->results = "<iframe style=\"height:100%;width:100%\"  src=\"".
	 "https://spreadsheets.google.com/tq?tqx=out:html&tq=select+A,B,C,D,E,F,G,H,I+where+".
	  "(C+contains+'".$values['name']."')+and+".
	  "(D+contains+'".$values['id']."')+and+".
          "(G+contains+'".$values['advisor']."')+and+".
	  "(H+contains+'".$values['group']."')+and+".  
	  "(I+contains+'".$values['projectid']."')+order+by+A+desc&key=$this->doc_key\"> Searching</iframe>";
	echo $this->getForm();
	echo $this->view->results;
    }


}



