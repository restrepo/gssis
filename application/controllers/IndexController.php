<?php

class IndexController extends Zend_Controller_Action
{
    var $csvFile;
    public function init()
    {
        /* Initialize action controller here */
	
    }

    private function getForm()
    {
      $form = new Zend_Form;
//       $form = new Zend_Form;
      $form->setAction("/gds/public/index/search")->setMethod('post');
      $form->addElement('text', 'article', array('label' => 'Articulo'));
      $form->addElement('text', 'autor', array('label' => 'Autor'));
      $form->addElement('text', 'year', array('label' => 'Año'));
      $form->addElement('text', 'journal', array('label' => 'Revista'));
      $form->addElement('submit', 'search', array('label' => 'Buscar'));
      return $form;
    }

    private function getCsvButton($code)
    {
      $form = new Zend_Form;
      $form->setAction("/gds/public/index/downloadcsv")->setMethod('post');
//       $form->addElement('submit', 'csv', array('label' => 'Descargar csv'));
      $form->addElement('submit', 'csv', array('label' => 'Descargar csv'));
//       $form->code=$code;
      $this->view->resultscsv=$code;
      return $form;
    }

    public function indexAction()
    {
        // action body
      $this->view->form = $this->getForm();;
      echo $this->view->form;
    }

    public function searchAction()
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
	echo $this->getForm();
// 	print_r($values);
	echo "<!--<iframe style=\"height:100%;width:100%\"  src=\"https://spreadsheets.google.com/tq?tqx=out:html&tq=select+E,F,G,H,C,J+where+(F+contains+".$values['autor'].")+order+by+A+desc&key=0AjqGPI5Q_Ez6dDA3ajhtYVVDOWdBckVhWm1MSFRET1E\"> Searching</iframe>-->";
	echo $this->getCsvButton($this->view->resultscsv);
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
//         echo "Downloading".$form->code;
	return $this->_forward('search');
    }


}





