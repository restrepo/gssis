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
        $this->form->setAction($this->view->url(array('controller' => 'visitors', 'action' => 'search')))->setMethod('post');
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

    public function links()
    {
        echo "<a href=''>Insertar</a> - <a href='https://docs.google.com/spreadsheet/ccc?key=0AjqGPI5Q_Ez6dGllR2x3NHEwU1VaV3oyb2pFVl9Vc0E&hl=es' target='_blank'>Editar</a><br>";
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
        $this->view->headScript()->appendFile($this->view->baseUrl().'/js/visitantes.php?s='.$values);

        $this->links();
    }
}



