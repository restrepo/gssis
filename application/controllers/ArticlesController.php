<?php

class ArticlesController extends Zend_Controller_Action
{

    public $csvFile = null;

    public $form = null;
    public $doc_key="0AjqGPI5Q_Ez6dDA3ajhtYVVDOWdBckVhWm1MSFRET1E";
    public function init()
    {
        /* Initialize action controller here */
        $this->form = new Zend_Form;
    }
    private function getCsvButton($url)
    {
        $formb = new Zend_Form;
        $formb->setAction("/gds/public/articles/downloadcsv")->setMethod('post');
        $formb->addElement('submit', 'csv', array('label' => 'Descargar csv'));
        return $formb;
    }
    private function getForm()
    {
//       $form = new Zend_Form;
        $this->form->setAction($this->view->url(array('controller' => 'articles', 'action' => 'search')))->setMethod('post');
        $this->form->addElement('text', 'article', array('label' => 'Articulo'));
        $this->form->addElement('text', 'autor', array('label' => 'Autor'));
        $this->form->addElement('hidden', 'yearLabel', array('label' => 'Años [Año inicial - Año final]'));
        $this->form->addElement('text', 'yearInit',array('disableLoadDefaultDecorators' => true,'decorators'=>Array('ViewHelper','Errors')));
        $this->form->addElement('text', 'yearEnd', array('disableLoadDefaultDecorators' => true,'decorators'=>Array('ViewHelper')));
        $this->form->addElement('text', 'journal', array('label' => 'Revista'));
        $this->form->addElement('text', 'group', array('label' => 'Grupo'));
        $this->form->addElement('select','type',array('label'=>'Tipo de Artículo','value'=>'Todos','autocomplete'=>false,'multiOptions'=>array(''=>'Todos','(D+contains+\'Internacional\')+and+'=>'Internacionales',
                                '(D+contains+\'Nacional\')+and+'  => 'Nacionales')));

        $this->form->addElement('submit', 'search', array('label' => 'Buscar'));
        return $this->form;
    }
    public function indexAction()
    {
        // action body
        $this->view->form = $this->getForm();
        echo $this->view->form;
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

        $values = urlencode(serialize($form->getValues()));

        $this->view->headScript()->appendFile($this->view->baseUrl().'/js/json.js');
        $this->view->headScript()->appendFile($this->view->baseUrl().'/js/articulos.php?s='.$values);

// 	$yearInitCode='';
// 	$yearEndCode='';
// 	if(!empty($values['yearInit']))
// 	{
// 	  $yearInitCode="(C>=".$values['yearInit'].")+and+";
// 	}
// 	if(!empty($values['yearEnd']))
// 	{
// 	  $yearEndCode="(C<=".$values['yearEnd'].")+and+";
//
// 	}
// // 	  "(C+contains+'".$values['yearInit']."')+and+".
// // 	print_r($values);
// 	$this->view->resultscsv = "<iframe style=\"height:100%;width:100%\"  src=\"".
// 	 "https://spreadsheets.google.com/tq?tqx=out:csv&tq=select+E,F,D,G,H,C,J,M+where+".
// 	  "(E+contains+'".$values['autor']."')+and+".
// 	  "(F+contains+'".$values['journal']."')+and+".
// 	  $yearInitCode.$yearEndCode.$values['type'].
// 	  "(J+contains+'".$values['article']."')+order+by+A+desc&key=$this->doc_key\"> Searching</iframe>";
//
// 	$this->view->results = "<iframe style=\"height:100%;width:100%\"  src=\"".
// 	 "https://spreadsheets.google.com/tq?tqx=out:html&tq=select+E,F,D,G,H,C,J,M+where+".
// 	  "(E+contains+'".$values['autor']."')+and+".
// 	  "(F+contains+'".$values['journal']."')+and+".
// 	  $yearInitCode.$yearEndCode.$values['type'].
// 	  "(J+contains+'".$values['article']."')+order+by+A+desc&key=$this->doc_key\"> Searching</iframe>";
// 	echo $this->getForm();
// 	echo $this->getCsvButton("https://spreadsheets.google.com/tq?tqx=out:csv&tq=select+E,F,G,H,C,J+where+".
// 	  "(E+contains+'".$values['autor']."')+and+".
// 	  $yearInitCode.$yearEndCode.$values['type'].
// 	  "(J+contains+'".$values['article']."')+order+by+A+desc&key=$this->doc_key");
// 	echo $this->view->results;
// 	return $this->_forward('index');
    }
}
