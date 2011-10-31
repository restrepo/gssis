<?php

class ProjectsController extends Zend_Controller_Action
{

    public $form = null;
    public $doc_key="0AjqGPI5Q_Ez6dFE1S2pWQkZJdFkycWFvNXdaMDhkWFE";
    public function init()
    {
        $this->form = new Zend_Form;
    }

    private function getForm()
    {
        $this->form->setAction($this->view->url(array('controller' => 'projects', 'action' => 'search')))->setMethod('post');
        $this->form->addElement('text', 'name', array('label' => 'Nombre'));
        $this->form->addElement('text', 'id', array('label' => 'Id'));
        $this->form->addElement('hidden', 'yearLabel', array('label' => 'Años (Intervalo)'));
        $this->form->addElement('text', 'yearInit',array('disableLoadDefaultDecorators' => true,'decorators'=>Array('ViewHelper','Errors')));
        $this->form->addElement('text', 'yearEnd', array('disableLoadDefaultDecorators' => true,'decorators'=>Array('ViewHelper')));
        $this->form->addElement('text', 'manager', array('label' => 'Investigador Principal'));
        $this->form->addElement('text', 'group', array('label' => 'Grupo'));
        $this->form->addElement('select','type',array('label'=>'Tipo de Proyecto','value'=>'Todos','autocomplete'=>false,'multiOptions'=>array(''=>'Todos','Sostenibilidad'=>'Sostenibilidad',
                                'Colciencias'  => 'Colciencias')));
        $this->form->addElement('select', 'format', array('label' => 'Formato', 'value'=>'CODI', 'autocomplete'=>false, 'multiOptions'=>array('codi'=>'CODI', 'list' => 'Lista')));

        $this->form->addElement('submit', 'search', array('label' => 'Buscar'));
        return $this->form;
    }

    public function links()
    {
        echo "<a href=''>Insertar</a> - <a href='https://docs.google.com/spreadsheet/ccc?key=0AjqGPI5Q_Ez6dFE1S2pWQkZJdFkycWFvNXdaMDhkWFE&hl=es#gid=0' target='_blank'>Editar</a><br>";
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
        $this->view->headScript()->appendFile($this->view->baseUrl().'/js/proyectos.php?s='.$values);

        $this->links();
    }
}
