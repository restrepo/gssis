<?php

class IndexController extends Zend_Controller_Action
{

    public $csvFile = null;

    public $form = null;

    public function init()
    {
        /* Initialize action controller here */
      $this->form = new Zend_Form;	
    }

    private function getForm()
    {
//       $form = new Zend_Form;
      $this->form->setAction("/gds/public/index/search")->setMethod('post');
      $this->form->addElement('text', 'article', array('label' => 'Articulo'));
      $this->form->addElement('text', 'autor', array('label' => 'Autor'));
      $this->form->addElement('text', 'yearInit', array('label' => 'Año Inicial','text', 'yearEnd', array('label' => 'Año Final')));
//       $this->form->addElement();
      $this->form->addElement('text', 'journal', array('label' => 'Revista'));
      $this->form->addElement('text', 'group', array('label' => 'Grupo'));
      $this->form->addElement('submit', 'search', array('label' => 'Buscar'));
      return $this->form;
    }

    private function getCsvButton($url)
    {
     $formb = new Zend_Form;
     $formb->setAction("/gds/public/index/downloadcsv")->setMethod('post');
//       $formb->setAction($url);
      $formb->addElement('submit', 'csv', array('label' => 'Descargar csv'));
      return $formb;
    }

    public function indexAction()
    {
        // action body
 
    }

    public function searchAction()
    {
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
	$this->view->resultscsv = "<iframe style=\"height:100%;width:100%\"  src=\"".
	 "https://spreadsheets.google.com/tq?tqx=out:csv&tq=select+E,F,G,H,C,J+where+".
	  "(E+contains+'".$values['autor']."')+and+".
	  "(F+contains+'".$values['journal']."')+and+".
	  "(C+contains+'".$values['year']."')+and+".
	  "(J+contains+'".$values['article']."')+order+by+A+desc&key=0AjqGPI5Q_Ez6dDA3ajhtYVVDOWdBckVhWm1MSFRET1E\"> Downloading</iframe>";
	$this->view->results = "<iframe style=\"height:100%;width:100%\"  src=\"".
	 "https://spreadsheets.google.com/tq?tqx=out:html&tq=select+E,F,G,H,C,J+where+".
	  "(E+contains+'".$values['autor']."')+and+".
	  "(F+contains+'".$values['journal']."')+and+".
	  "(C+contains+'".$values['year']."')+and+".
	  "(J+contains+'".$values['article']."')+order+by+A+desc&key=0AjqGPI5Q_Ez6dDA3ajhtYVVDOWdBckVhWm1MSFRET1E\"> Searching</iframe>";
// 	print_r($values);
// 	$form->addElement('hidden', 'csvcode', array('value' => $this->view->resultscsv));
	echo $this->getForm();
// 	print_r($values);
	echo "<!--<iframe style=\"height:100%;width:100%\"  src=\"https://spreadsheets.google.com/tq?tqx=out:html&tq=select+E,F,G,H,C,J+where+(F+contains+".$values['autor'].")+order+by+A+desc&key=0AjqGPI5Q_Ez6dDA3ajhtYVVDOWdBckVhWm1MSFRET1E\"> Searching</iframe>-->";
	echo $this->getCsvButton("https://spreadsheets.google.com/tq?tqx=out:csv&tq=select+E,F,G,H,C,J+where+".
	  "(E+contains+'".$values['autor']."')+and+".
	  "(F+contains+'".$values['journal']."')+and+".
	  "(C+contains+'".$values['year']."')+and+".
	  "(J+contains+'".$values['article']."')+order+by+A+desc&key=0AjqGPI5Q_Ez6dDA3ajhtYVVDOWdBckVhWm1MSFRET1E");
	echo $this->view->results;
// 	return $this->_forward('index');
	

    }

    public function downloadcsvAction()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->_forward('index');
        }
        $form = $this->getForm();
        if (!$form->isValid($_POST)) {
            // Failed validation; redisplay form
            $this->view->form = $form;
            return $this->render('form');
        }
	$values = $form->getValues();
	$this->view->resultscsv = "<iframe style=\"height:0%;width:0%\" type=\"hidden\" class=\"hidden\" src=\"".
	 "https://spreadsheets.google.com/tq?tqx=out:csv&tq=select+E,F,G,H,C,J+where+".
	  "(E+contains+'".$values['autor']."')+and+".
	  "(F+contains+'".$values['journal']."')+and+".
	  "(C+contains+'".$values['year']."')+and+".
	  "(J+contains+'".$values['article']."')+order+by+A+desc&key=0AjqGPI5Q_Ez6dDA3ajhtYVVDOWdBckVhWm1MSFRET1E\"> Downloading</iframe>";
// 	print_r($value); 
	echo $this->view->resultscsv;
        
	return $this->_forward('search');
    }

    public function menuAction()
    {
        // action body
	
    }


}







