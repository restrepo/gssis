<?php

class ArticlesController extends Zend_Controller_Action
{
    public $form = null;

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
    public function links()
    {
        echo "<a href=''>Insertar</a> - <a href='https://docs.google.com/spreadsheet/ccc?key=0AjqGPI5Q_Ez6dDA3ajhtYVVDOWdBckVhWm1MSFRET1E&hl=es#gid=0' target='_blank'>Editar</a><br>";
    }
    public function indexAction()
    {
        // action body
        $this->view->form = $this->getForm();
        echo $this->view->form;
        $this->links();
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

        $this->links();
    }
}
