<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Adapter\Adapter;
use Zend\Session\Container;

class EventosController extends AbstractActionController
{
    public function indexAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login',array('redirect' => $this->getRequest()->getUri()));
        }
        $this->layout('layout/admin');
        
    	$this->adaptador = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $result = $this->adaptador->query('select * from EVENTO_AUDIT',Adapter::QUERY_MODE_EXECUTE);
        $result2 = $this->adaptador->query('SELECT count(*) TOTAL,EVNT_USER FROM EVENTO_AUDIT group by EVNT_USER',Adapter::QUERY_MODE_EXECUTE);
        $mensaje = new Container("msg")	;
    	if(!$mensaje->msgbox){
    		$mensaje->msgtemporal = "";
    	}
    	$mensaje->msgbox = false;
        return new ViewModel(array(
            'activo'            => 'reportes-1',
            'lista'		=> $result,
            'lista2'		=> $result2,
            'msg'		=> $mensaje->msgtemporal,
            'satis'             => $mensaje->satisfactorio,
        ));
    }
}