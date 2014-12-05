<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Adapter\Adapter;
use Zend\Session\Container;

class EspecialidadesController extends AbstractActionController
{
    public function indexAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login',array('redirect' => $this->getRequest()->getUri()));
        }
        $this->layout('layout/admin');
        
    	$this->adaptador = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $result = $this->adaptador->query('select * from ESPECIALIDAD',Adapter::QUERY_MODE_EXECUTE);
        $mensaje = new Container("msg")	;
    	if(!$mensaje->msgbox){
    		$mensaje->msgtemporal = "";
    	}
    	$mensaje->msgbox = false;
        return new ViewModel(array(
            'activo' 	=> 'especialidad-1',
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
    	$result = $this->adaptador->query('select * from ESPECIALIDAD',Adapter::QUERY_MODE_EXECUTE);

        $mensaje = new Container("msg");
    	$mensaje->msgbox = true;
    	$mensaje->msgtemporal = "";
    	$mensaje->satisfactorio = "success";
        if ($request->isPost()) {
            $datos = $request->getPost();
            $nombre     = $datos["nombre"];
            
            if ($nombre == ""){
                    $mensaje->msgtemporal = "Debe escribir el nombre del tutor!";
                    $mensaje->satisfactorio = "warning";
                    $mensaje->msgbox = false;
                    return new ViewModel(array(
                        'activo' 	=> 'especialidad-1',
                        'lista'		=> $result,
                        'msg'		=> $mensaje->msgtemporal,
                        'satis' 	=> $mensaje->satisfactorio,
                    ));
            }
            $sql = "INSERT INTO ESPECIALIDAD (NOMBRE,USER_CRE)
    				VALUES('".$nombre."','ADMIN')";
            $this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE);
            $mensaje->msgtemporal = "Se ha agregado a especialidad satisfactoriamente";
            $mensaje->satisfactorio = "success";
            /**********Captura los enventos para la auditoria*********/
            /*********************************************************/
            $record = $this->adaptador->query('select ID from ESPECIALIDAD WHERE NOMBRE LIKE "'.$nombre.'"',Adapter::QUERY_MODE_EXECUTE);
            foreach ($record as $val) {
                $id = $val['ID'];
            }
            $user = $this->zfcUserAuthentication()->getIdentity()->getUsername();
            $sql = "INSERT INTO EVENTO_AUDIT (EVNT_ACCION,EVNT_USER,EVNT_MODULO,EVNT_ID_ELEMENT,EVNT_DESCRI,EVNT_FECHA) "
                    . "VALUES ('AGREGAR','$user','ESPECIALIDADES','$id','EL Usuario Agrego una especialidad',SYSDATE())";
            $this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE);
            /*********************************************************/
            /*********************************************************/
            return $this->redirect()->toRoute('epecialidades');
        }
        return new ViewModel(array(
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
    		return $this->redirect()->toRoute('epecialidades');
    	}
        $request = $this->getRequest();
    	$this->adaptador = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        //metodo para extraer las categorias
    	$result = $this->adaptador->query('select * from ESPECIALIDAD WHERE ID = '.$id,Adapter::QUERY_MODE_EXECUTE);

        $mensaje = new Container("msg");
    	$mensaje->msgbox = true;
    	$mensaje->msgtemporal = "";
    	$mensaje->satisfactorio = "success";
        if ($request->isPost()) {
            $datos = $request->getPost();
            $nombre     = $datos["nombre"];
            
            if ($nombre == ""){
                    $mensaje->msgtemporal = "Debe escribir el nombre de la especialidad!";
                    $mensaje->satisfactorio = "warning";
                    $mensaje->msgbox = false;
                    return new ViewModel(array(
                        'activo' 	=> 'especialidad-1',
                        'lista'		=> $result,
                        'msg'		=> $mensaje->msgtemporal,
                        'satis' 	=> $mensaje->satisfactorio,
                    ));
            }
            $sql = "UPDATE ESPECIALIDAD SET NOMBRE = '".$nombre."'"
                    . " WHERE ID = $id";
            $this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE);
            $mensaje->msgtemporal = "Se ha editado la especialidad ".$nombre." satisfactoriamente";
            $mensaje->satisfactorio = "success";
            /**********Captura los enventos para la auditoria*********/
            /*********************************************************/
            $user = $this->zfcUserAuthentication()->getIdentity()->getUsername();
            $sql = "INSERT INTO EVENTO_AUDIT (EVNT_ACCION,EVNT_USER,EVNT_MODULO,EVNT_ID_ELEMENT,EVNT_DESCRI,EVNT_FECHA) "
                    . "VALUES ('EDITAR','$user','ESPECIALIDADES','$id','EL Usuario Edito una especialidad',SYSDATE())";
            $this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE);
            /*********************************************************/
            /*********************************************************/
            return $this->redirect()->toRoute('epecialidades');
        }
        return new ViewModel(array(
            'activo' 	=> 'especialidad-1',
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
            $result = $this->adaptador->query('select COUNT(*) TOT from TESIS WHERE ESPECIALIDAD = '.$id,Adapter::QUERY_MODE_EXECUTE);
            foreach ($result as $val) {
                $contador = $val['TOT'];
            }
            if ($contador > 0){
                $mensaje->msgtemporal = "Perdon! pero no podemos eliminar este registro ya que una tesis esta relacionada a el.";
                $mensaje->satisfactorio = "warning";
                return $this->redirect()->toRoute('epecialidades');
            }
            $sql ="DELETE FROM ESPECIALIDAD WHERE ID = $id";
            $this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE);
        }
        
        $mensaje->msgtemporal = "Se ha eliminado la especialidad satisfactoriamente";
        $mensaje->satisfactorio = "success";
        /**********Captura los enventos para la auditoria*********/
        /*********************************************************/
        $user = $this->zfcUserAuthentication()->getIdentity()->getUsername();
        $sql = "INSERT INTO EVENTO_AUDIT (EVNT_ACCION,EVNT_USER,EVNT_MODULO,EVNT_ID_ELEMENT,EVNT_DESCRI,EVNT_FECHA) "
                . "VALUES ('ELIMINAR','$user','ESPECIALIDADES','$id','EL Usuario Elimino una especialidad',SYSDATE())";
        $this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE);
        /*********************************************************/
        /*********************************************************/
        return $this->redirect()->toRoute('epecialidades');
    }
}


