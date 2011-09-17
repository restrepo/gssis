<?php

class ProjectsController extends Zend_Controller_Action
{

    public $form = null;
    public $doc_key="0AjqGPI5Q_Ez6dFE1S2pWQkZJdFkycWFvNXdaMDhkWFE";
    public function init()
    {
        /* Initialize action controller here */
	$this->form = new Zend_Form;
//         $this->view->headScript()->appendFile($this->view->baseUrl().'/js/proyectos.js');
    }

    private function getForm()
    {
//       $form = new Zend_Form;
      $this->form->setAction($this->view->url(array('controller' => 'projects', 'action' => 'search')))->setMethod('post');
      $this->form->addElement('text', 'name', array('label' => 'Nombre'));
      $this->form->addElement('text', 'id', array('label' => 'Id'));
      $this->form->addElement('hidden', 'yearLabel', array('label' => 'Años (Intervalo)'));
      $this->form->addElement('text', 'yearInit',array('disableLoadDefaultDecorators' => true,'decorators'=>Array('ViewHelper','Errors')));
//       $this->form->yearInit->addValidator('Regex', false, array('/^[0-9]{4,}$/i'));
      $this->form->addElement('text', 'yearEnd', array('disableLoadDefaultDecorators' => true,'decorators'=>Array('ViewHelper')));
      $this->form->addElement('text', 'manager', array('label' => 'Investigador Principal'));
      $this->form->addElement('text', 'group', array('label' => 'Grupo'));
      $this->form->addElement('select','type',array('label'=>'Tipo de Proyecto','value'=>'Todos','autocomplete'=>false,'multiOptions'=>array(''=>'Todos','Sostenibilidad'=>'Sostenibilidad',
	      'Colciencias'  => 'Colciencias')));


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
        $this->view->headScript()->appendFile($this->view->baseUrl().'/js/json.js');
        $this->view->headScript()->appendFile($this->view->baseUrl().'/js/proyectos.js');

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

//         $values = $form->getValues();

// 	$yearInitCode='';
// 	$yearEndCode='';

//         $this->view->headScript()->appendFile($this->view->baseUrl().'/js/if.js');
// 	$this->view->results = '<iframe id="query" onload="algo();" src="'.
// 	 "https://spreadsheets.google.com/tq?tqx=out:html&tq=select+C,D,H,E,F,G,K,L+where+".
// 	  "(B+contains+'".$values['id']."')+and+".
// 	  "(C+contains+'".$values['name']."')+and+".
//           "(D+contains+'".$values['manager']."')+and+".
// 	  "(H+contains+'".$values['type']."')+and+".  
// 	  "(J+contains+'".$values['group']."')+order+by+A+desc&key=$this->doc_key\">Searching</iframe>";
//     echo "<script type=\"text/javascript\">
//         function algo(){
//             // alert('termino de leer');
//             // jQuery('iframe').iframeAutoHeight({debug: true, diagnostics: false});
//         }
//     </script>";
	// echo $this->getForm();
// 	echo $this->getCsvButton("https://spreadsheets.google.com/tq?tqx=out:csv&tq=select+E,F,G,H,C,J+where+".
// 	  "(E+contains+'".$values['autor']."')+and+".
// 	  $yearInitCode.$yearEndCode.$values['type'].
// 	  "(J+contains+'".$values['article']."')+order+by+A+desc&key=$this->doc_key");
// 	echo $this->view->results;
    }
}
