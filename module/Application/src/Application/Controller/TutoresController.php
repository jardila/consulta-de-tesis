<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Adapter\Adapter;
use Zend\Session\Container;

class TutoresController extends AbstractActionController
{
    public function indexAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login',array('redirect' => $this->getRequest()->getUri()));
        }
        $this->layout('layout/admin');
        
    	$this->adaptador = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $result = $this->adaptador->query('select * from TUTORES',Adapter::QUERY_MODE_EXECUTE);
        $mensaje = new Container("msg")	;
    	if(!$mensaje->msgbox){
    		$mensaje->msgtemporal = "";
    	}
    	$mensaje->msgbox = false;
        return new ViewModel(array(
            'activo' 	=> 'tutor-1',
            'lista'		=> $result,
            'msg'		=> $mensaje->msgtemporal,
            'satis' 	=> $mensaje->satisfactorio,
        ));
    }
    public function addAction() {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login',array('redirect' => $this->getRequest()->getUri()));
        }
        $this->layout('layout/admin');
        $request = $this->getRequest();
    	$this->adaptador = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        //metodo para extraer las categorias
    	$result = $this->adaptador->query('select * from TUTORES',Adapter::QUERY_MODE_EXECUTE);

        $mensaje = new Container("msg");
    	$mensaje->msgbox = true;
    	$mensaje->msgtemporal = "";
    	$mensaje->satisfactorio = "success";
        if ($request->isPost()) {
            $datos = $request->getPost();
            $cedula     = $datos["cedula"];
            $nombre     = $datos["nombre"];
            $tipo       = $datos["tipo"];
            
            if ($nombre == ""){
                    $mensaje->msgtemporal = "Debe escribir el nombre del tutor!";
                    $mensaje->satisfactorio = "warning";
                    $mensaje->msgbox = false;
                    return new ViewModel(array(
                        'activo' 	=> 'tutor-1',
                        'lista'		=> $result,
                        'msg'		=> $mensaje->msgtemporal,
                        'satis' 	=> $mensaje->satisfactorio,
                    ));
            }
            $sql = "INSERT INTO TUTORES (CEDULA,NOMBRES,TIPO_TUTOR,USER_CRE)
    				VALUES('".$cedula."','".$nombre."','".$tipo."','ADMIN')";
            $this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE);
            $mensaje->msgtemporal = "Se ha agregado el tutor satisfactoriamente";
            $mensaje->satisfactorio = "success";
            /**********Captura los enventos para la auditoria*********/
            /*********************************************************/
            $user = $this->zfcUserAuthentication()->getIdentity()->getUsername();
            $sql = "INSERT INTO EVENTO_AUDIT (EVNT_ACCION,EVNT_USER,EVNT_MODULO,EVNT_ID_ELEMENT,EVNT_DESCRI,EVNT_FECHA) "
                    . "VALUES ('AGREGAR','$user','TUTORES','$cedula','EL Usuario Agrego un Tutor',SYSDATE())";
            $this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE);
            /*********************************************************/
            /*********************************************************/
            return $this->redirect()->toRoute('tutores');
        }
        return new ViewModel(array(
            'activo' 	=> 'tutor-1',
            'msg'		=> $mensaje->msgtemporal,
            'satis'             => $mensaje->satisfactorio,
    	));
    }
    
    public function editAction() {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login',array('redirect' => $this->getRequest()->getUri()));
        }
        $this->layout('layout/admin');
        $id = (int) $this->params()->fromRoute('id', 0);

    	//Si no hay ningun id activo redirecciona a la accion add
    	if (!$id) {
    		return $this->redirect()->toRoute('tutores');
    	}
        $request = $this->getRequest();
    	$this->adaptador = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        //metodo para extraer las categorias
    	$result = $this->adaptador->query('select * from TUTORES WHERE ID = '.$id,Adapter::QUERY_MODE_EXECUTE);

        $mensaje = new Container("msg");
    	$mensaje->msgbox = true;
    	$mensaje->msgtemporal = "";
    	$mensaje->satisfactorio = "success";
        if ($request->isPost()) {
            $datos = $request->getPost();
            $cedula     = $datos["cedula"];
            $nombre     = $datos["nombre"];
            $tipo       = $datos["tipo"];
            
            if ($nombre == ""){
                    $mensaje->msgtemporal = "Debe escribir el nombre del tutor!";
                    $mensaje->satisfactorio = "warning";
                    $mensaje->msgbox = false;
                    return new ViewModel(array(
                        'activo' 	=> 'tutor-1',
                        'lista'		=> $result,
                        'msg'		=> $mensaje->msgtemporal,
                        'satis' 	=> $mensaje->satisfactorio,
                    ));
            }
            $sql = "UPDATE TUTORES SET CEDULA = '".$cedula."',NOMBRES = '".$nombre."',TIPO_TUTOR = '".$tipo."'"
                    . " WHERE ID = $id";
            $this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE);
            $mensaje->msgtemporal = "Se ha editado el tutor ".$nombre." satisfactoriamente";
            $mensaje->satisfactorio = "success";
            /**********Captura los enventos para la auditoria*********/
            /*********************************************************/
            $user = $this->zfcUserAuthentication()->getIdentity()->getUsername();
            $sql = "INSERT INTO EVENTO_AUDIT (EVNT_ACCION,EVNT_USER,EVNT_MODULO,EVNT_ID_ELEMENT,EVNT_DESCRI,EVNT_FECHA) "
                    . "VALUES ('EDITAR','$user','TUTORES','$cedula','EL Usuario Edito un Tutor',SYSDATE())";
            $this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE);
            /*********************************************************/
            /*********************************************************/
            return $this->redirect()->toRoute('tutores');
        }
        return new ViewModel(array(
            'activo' 	=> 'tutor-1',
            'lista'		=> $result,
            'msg'		=> $mensaje->msgtemporal,
            'satis'             => $mensaje->satisfactorio,
    	));
    }
    public function eliminarAction(){
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login',array('redirect' => $this->getRequest()->getUri()));
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        $this->adaptador = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $mensaje = new Container("msg");
    	$mensaje->msgbox = true;
        if(id){
            $sql ="DELETE FROM TUTORES WHERE ID = $id";
            $result = $this->adaptador->query('select COUNT(*) TOT from TESIS WHERE TUTOR_T = '.$id.' OR TUTOR_M = '.$id,Adapter::QUERY_MODE_EXECUTE);
            foreach ($result as $val) {
                $contador = $val['TOT'];
            }
            if ($contador > 0){
                $mensaje->msgtemporal = "Perdon! pero no podemos eliminar este registro ya que una tesis esta relacionada a el.";
                $mensaje->satisfactorio = "warning";
                return $this->redirect()->toRoute('tutores');
            }
            $this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE);
        }
        $mensaje->msgtemporal = "Se ha eliminado el tutor satisfactoriamente";
        $mensaje->satisfactorio = "success";
        /**********Captura los enventos para la auditoria*********/
        /*********************************************************/
        $user = $this->zfcUserAuthentication()->getIdentity()->getUsername();
        $sql = "INSERT INTO EVENTO_AUDIT (EVNT_ACCION,EVNT_USER,EVNT_MODULO,EVNT_ID_ELEMENT,EVNT_DESCRI,EVNT_FECHA) "
                . "VALUES ('ELIMINAR','$user','TUTORES','$id','EL Usuario Elimino un Tutor',SYSDATE())";
        $this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE);
        /*********************************************************/
        /*********************************************************/
        return $this->redirect()->toRoute('tutores');
    }
}
