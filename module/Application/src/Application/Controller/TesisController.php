<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Adapter\Adapter;
use Zend\Session\Container;

class TesisController extends AbstractActionController
{
    public function indexAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login',array('redirect' => $this->getRequest()->getUri()));
        }
        $this->layout('layout/admin');
        
    	$this->adaptador = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $sql = "SELECT  T.ID ID,"
                . "     T.TITULO TITULO,"
                . "     T.ANO ANO,"
                . "     T.AUTORES AUTORES,"
                . "     E.NOMBRE ESPECIALIDAD  "
                . "FROM TESIS T,ESPECIALIDAD E "
                . "WHERE T.ESPECIALIDAD = E.ID";
        $result = $this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE);
        $mensaje = new Container("msg")	;
    	if(!$mensaje->msgbox){
    		$mensaje->msgtemporal = "";
    	}
    	$mensaje->msgbox = false;
        return new ViewModel(array(
                        'activo' 	=> 'tesis-1',
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
        $result2 = $this->adaptador->query('select * from TUTORES WHERE TIPO_TUTOR = 1',Adapter::QUERY_MODE_EXECUTE);
        $result3 = $this->adaptador->query('select * from TUTORES WHERE TIPO_TUTOR = 2',Adapter::QUERY_MODE_EXECUTE);

        $mensaje = new Container("msg");
    	$mensaje->msgbox = true;
    	$mensaje->msgtemporal = "";
    	$mensaje->satisfactorio = "success";
        $carpeta = "./public/upload/tesis";
        if (!is_dir($carpeta)){
            @mkdir($carpeta, 0777,true);
        }
        $carpeta = "./public/upload/tesis";
        if ($request->isPost()) {
            $datos = $request->getPost();
            $titulo         = $datos["titulo"];
            $ano            = $datos["ano"];
            $especialidad   = $datos["especialidad"];
            $autores        = $datos["autores"];
            $tutor_t        = $datos["tutor_t"];
            $tutor_m        = $datos["tutor_m"];
            $resumen        = $datos["resumen"];
            $t_documento    = $datos["t_documento"];
            //Variables para los nombres temporales de los archivos
            $completo_tn       = $_FILES["completo"]["tmp_name"];
            $intro_tn          = $_FILES["intro"]["tmp_name"];
            $cap1_tn           = $_FILES["cap1"]["tmp_name"];
            $cap2_tn           = $_FILES["cap2"]["tmp_name"];
            $cap3_tn           = $_FILES["cap3"]["tmp_name"];
            $cap4_tn           = $_FILES["cap4"]["tmp_name"];
            $concl_tn          = $_FILES["concl"]["tmp_name"];
            //Variables para el tipo de archivo
            $completo_t       = $_FILES["completo"]["type"];
            $intro_t          = $_FILES["intro"]["type"];
            $cap1_t           = $_FILES["cap1"]["type"];
            $cap2_t           = $_FILES["cap2"]["type"];
            $cap3_t           = $_FILES["cap3"]["type"];
            $cap4_t           = $_FILES["cap4"]["type"];
            $concl_t          = $_FILES["concl"]["type"];
            //Variables para el tamaño de archivo
            $completo_s       = $_FILES["completo"]["size"];
            $intro_s          = $_FILES["intro"]["size"];
            $cap1_s           = $_FILES["cap1"]["size"];
            $cap2_s           = $_FILES["cap2"]["size"];
            $cap3_s           = $_FILES["cap3"]["size"];
            $cap4_s           = $_FILES["cap4"]["size"];
            $concl_s          = $_FILES["concl"]["size"];
            
            
            //Verificamos que no exista una tesis con el mismo titulo
            /*********************************************************/
            $record = $this->adaptador->query('select COUNT(*) TOT from TESIS WHERE TITULO LIKE "'.$titulo.'"',Adapter::QUERY_MODE_EXECUTE);
            foreach ($record as $val) {
                $contador = $val['TOT'];
            }
            if($contador > 0){
                $mensaje->msgtemporal = "Ya existe una tesis registrada con el mismo titulo!";
                $mensaje->satisfactorio = "warning";
                $mensaje->msgbox = false;
                return new ViewModel(array(
                    'activo' 	=> 'tesis-1',
                    'lista'	=> $result,
                    'lista2'	=> $result2,
                    'lista3'	=> $result3,
                    'msg'	=> $mensaje->msgtemporal,
                    'satis' 	=> $mensaje->satisfactorio,
                ));
            }
            /*********************************************************/
            
            //Procedemos a ingresar la tesis al sistema con los datos basicos
            /****************************************************************/
            if($t_documento == "on"){
                $t_documento1 = 1;
            }else{
                $t_documento1 = 2;
            }
            $sql = "INSERT INTO TESIS (TITULO,AUTORES,ANO,ESPECIALIDAD,TUTOR_T,TUTOR_M,RESUMEN,TIPO_DOCUMENTO,USUARIO_CREACION) "
                    . " VALUES('$titulo','$autores','$ano','$especialidad','$tutor_t','$tutor_m',"
                    . "'$resumen','$t_documento1','ADMIN')";
            if(!$this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE)){
                $mensaje->msgtemporal = "Ocurrio un problema al momento de agregar la tesis!";
                $mensaje->satisfactorio = "warning";
                $mensaje->msgbox = false;
                return new ViewModel(array(
                    'activo' 	=> 'tesis-1',
                    'lista'	=> $result,
                    'lista2'	=> $result2,
                    'lista3'	=> $result3,
                    'msg'	=> $mensaje->msgtemporal,
                    'satis' 	=> $mensaje->satisfactorio,
                ));
            }
            $record = $this->adaptador->query('select ID from TESIS WHERE TITULO LIKE "'.$titulo.'"',Adapter::QUERY_MODE_EXECUTE);
            foreach ($record as $val) {
                $id = $val['ID'];
            }
            /****************************************************************/
            
            /***********************************************************************/
            /***********************************************************************/
            /***********************************************************************/
            /******Proceso para subir los documentos de tesis al sistema************/
            /***********************************************************************/
            /***********************************************************************/
            /***********************************************************************/
            //Verificamos que tipo de tesis es: Completa o dividida
            $sql = "INSERT INTO DOCUMENTOS ";
            if($t_documento == "on"){
                //Tesis Completa y verificamos que sea PDF
                if ($completo_t != "application/pdf" && $completo_tn != ""){
                    $mensaje->msgtemporal = "Tipo de Documento en Tesis Completa debe ser <strong>PDF</strong>";
                    $mensaje->satisfactorio = "warning";
                    $mensaje->msgbox = false;
                    /***** Elimina la tesis si ha ocurrido algun error con los documentos*******/
                    $this->adaptador->query("DELETE FROM TESIS WHERE ID = '$id' LIMIT 1",Adapter::QUERY_MODE_EXECUTE);
                    /***************************************************************************/
                    return new ViewModel(array(
                        'activo' 	=> 'tesis-1',
                        'lista'	=> $result,
                        'lista2'	=> $result2,
                        'lista3'	=> $result3,
                        'msg'	=> $mensaje->msgtemporal,
                        'satis' 	=> $mensaje->satisfactorio,
                    ));
                }
                
                $ruta = $carpeta."/".$id."_.PDF";
                if(move_uploaded_file($completo_tn, $ruta)){
                    $sql .= " (ID_TESIS,COMPLETO,INTRO,CAP1,CAP2,CAP3,CAP4,CONCLUSION,COMPLETO_ST,INTRO_ST,CAP1_ST,CAP2_ST,CAP3_ST,CAP4_ST,CONCLUSION_ST) "
                            . "VALUES('$id','$ruta','','','','','','','1','0','0','0','0','0','0')";
                }else{
                    $sql .= " (ID_TESIS,COMPLETO,INTRO,CAP1,CAP2,CAP3,CAP4,CONCLUSION,COMPLETO_ST,INTRO_ST,CAP1_ST,CAP2_ST,CAP3_ST,CAP4_ST,CONCLUSION_ST) "
                            . "VALUES('$id','$ruta','','','','','','','0','0','0','0','0','0','0')";
                }
            }else{
                $intro = "0";
                $cap1  = "0";
                $cap2  = "0";
                $cap3  = "0";
                $cap4  = "0";
                $concl = "0";
                $ruta1 = $carpeta."/".$id."_INTRO_.PDF";
                $ruta2 = $carpeta."/".$id."_CAP1_.PDF";
                $ruta3 = $carpeta."/".$id."_CAP2_.PDF";
                $ruta4 = $carpeta."/".$id."_CAP3_.PDF";
                $ruta5 = $carpeta."/".$id."_CAP4_.PDF";
                $ruta6 = $carpeta."/".$id."_CONCL_.PDF";
                //Dividida y Verificamos cada archivo que sea PDF
                if ($intro_t != "application/pdf" && $intro_tn != ""){
                    $mensaje->msgtemporal = "Tipo de Documento Introduccion debe ser <strong>PDF</strong>";
                    $mensaje->satisfactorio = "warning";
                    $mensaje->msgbox = false;
                    /***** Elimina la tesis si ha ocurrido algun error con los documentos*******/
                    $this->adaptador->query("DELETE FROM TESIS WHERE ID = '$id' LIMIT 1",Adapter::QUERY_MODE_EXECUTE);
                    /***************************************************************************/
                    return new ViewModel(array(
                        'lista'	=> $result,
                        'activo' 	=> 'tesis-1',
                        'lista2'	=> $result2,
                        'lista3'	=> $result3,
                        'msg'	=> $mensaje->msgtemporal,
                        'satis' 	=> $mensaje->satisfactorio,
                    ));                    
                }
                if(move_uploaded_file($intro_tn,$ruta1)){
                    $intro = "1";
                }
                if ($cap1_t != "application/pdf" && $cap1_tn != ""){
                    $mensaje->msgtemporal = "Tipo de Documento Capitulo 1 debe ser <strong>PDF</strong>";
                    $mensaje->satisfactorio = "warning";
                    $mensaje->msgbox = false;
                    /***** Elimina la tesis si ha ocurrido algun error con los documentos*******/
                    $this->adaptador->query("DELETE FROM TESIS WHERE ID = '$id' LIMIT 1",Adapter::QUERY_MODE_EXECUTE);
                    /***************************************************************************/
                    return new ViewModel(array(
                        'activo' 	=> 'tesis-1',
                        'lista'	=> $result,
                        'lista2'	=> $result2,
                        'lista3'	=> $result3,
                        'msg'	=> $mensaje->msgtemporal,
                        'satis' 	=> $mensaje->satisfactorio,
                    ));
                }
                if(move_uploaded_file($cap1_tn, $ruta2)){
                    $cap1 = "1";
                }
                if ($cap2_t != "application/pdf" && $cap2_tn != ""){
                    $mensaje->msgtemporal = "Tipo de Documento Capitulo 2 debe ser <strong>PDF</strong>";
                    $mensaje->satisfactorio = "warning";
                    $mensaje->msgbox = false;
                    /***** Elimina la tesis si ha ocurrido algun error con los documentos*******/
                    $this->adaptador->query("DELETE FROM TESIS WHERE ID = '$id' LIMIT 1",Adapter::QUERY_MODE_EXECUTE);
                    /***************************************************************************/
                    return new ViewModel(array(
                        'activo' 	=> 'tesis-1',
                        'lista'	=> $result,
                        'lista2'	=> $result2,
                        'lista3'	=> $result3,
                        'msg'	=> $mensaje->msgtemporal,
                        'satis' 	=> $mensaje->satisfactorio,
                    ));
                }
                if(move_uploaded_file($cap2_tn, $ruta3)){
                    $cap2 = "1";
                }
                if ($cap3_t != "application/pdf" && $cap3_tn != ""){
                    $mensaje->msgtemporal = "Tipo de Documento Capitulo 3 debe ser <strong>PDF</strong>";
                    $mensaje->satisfactorio = "warning";
                    $mensaje->msgbox = false;
                    /***** Elimina la tesis si ha ocurrido algun error con los documentos*******/
                    $this->adaptador->query("DELETE FROM TESIS WHERE ID = '$id' LIMIT 1",Adapter::QUERY_MODE_EXECUTE);
                    /***************************************************************************/
                    return new ViewModel(array(
                        'activo' 	=> 'tesis-1',
                        'lista'	=> $result,
                        'lista2'	=> $result2,
                        'lista3'	=> $result3,
                        'msg'	=> $mensaje->msgtemporal,
                        'satis' 	=> $mensaje->satisfactorio,
                    ));
                }
                if(move_uploaded_file($cap3_tn, $ruta4)){
                    $cap3 = "1";
                }
                if ($cap4_t != "application/pdf" && $cap4_tn != ""){
                    $mensaje->msgtemporal = "Tipo de Documento Capitulo 4 debe ser <strong>PDF</strong>";
                    $mensaje->satisfactorio = "warning";
                    $mensaje->msgbox = false;
                    /***** Elimina la tesis si ha ocurrido algun error con los documentos*******/
                    $this->adaptador->query("DELETE FROM TESIS WHERE ID = '$id' LIMIT 1",Adapter::QUERY_MODE_EXECUTE);
                    /***************************************************************************/
                    return new ViewModel(array(
                        'activo' 	=> 'tesis-1',
                        'lista'	=> $result,
                        'lista2'	=> $result2,
                        'lista3'	=> $result3,
                        'msg'	=> $mensaje->msgtemporal,
                        'satis' 	=> $mensaje->satisfactorio,
                    ));
                }
                if(move_uploaded_file($cap4_tn, $ruta5)){
                    $cap4 = "1";
                }
                if (($concl_t != "application/pdf") && ($concl_tn != "")){
                    $mensaje->msgtemporal = "Tipo de Documento Conclusion debe ser <strong>PDF</strong>";
                    $mensaje->satisfactorio = "warning";
                    $mensaje->msgbox = false;
                    /***** Elimina la tesis si ha ocurrido algun error con los documentos*******/
                    $this->adaptador->query("DELETE FROM TESIS WHERE ID = '$id' LIMIT 1",Adapter::QUERY_MODE_EXECUTE);
                    /***************************************************************************/
                    return new ViewModel(array(
                        'activo' 	=> 'tesis-1',
                        'lista'	=> $result,
                        'lista2'	=> $result2,
                        'lista3'	=> $result3,
                        'msg'	=> $mensaje->msgtemporal,
                        'satis' 	=> $mensaje->satisfactorio,
                    ));
                }
                if(move_uploaded_file($concl_tn, $ruta6)){
                    $concl = "1";
                }
                $carpeta = "/upload/tesis";
                $ruta1 = $carpeta."/".$id."_INTRO_.PDF";
                $ruta2 = $carpeta."/".$id."_CAP1_.PDF";
                $ruta3 = $carpeta."/".$id."_CAP2_.PDF";
                $ruta4 = $carpeta."/".$id."_CAP3_.PDF";
                $ruta5 = $carpeta."/".$id."_CAP4_.PDF";
                $ruta6 = $carpeta."/".$id."_CONCL_.PDF";
                $sql .= " (ID_TESIS,COMPLETO,INTRO,CAP1,CAP2,CAP3,CAP4,CONCLUSION,COMPLETO_ST,INTRO_ST,CAP1_ST,CAP2_ST,CAP3_ST,CAP4_ST,CONCLUSION_ST) "
                        . "VALUES('$id','','$ruta1','$ruta2','$ruta3','$ruta4','$ruta5','$ruta6',"
                        . "'0','$intro','$cap1','$cap2','$cap3','$cap4','$concl')";
            }
            //var_dump($sql);
            if($this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE)){
                $mensaje->msgtemporal = "La tesis se agrego exitosamente!";
                $mensaje->satisfactorio = "success";
                $mensaje->msgbox = true;
                
                /**********Captura los enventos para la auditoria*********/
                /*********************************************************/
                $user = $this->zfcUserAuthentication()->getIdentity()->getUsername();
                $sql = "INSERT INTO EVENTO_AUDIT (EVNT_ACCION,EVNT_USER,EVNT_MODULO,EVNT_ID_ELEMENT,EVNT_DESCRI,EVNT_FECHA) "
                        . "VALUES ('AGREGAR','$user','TESIS','$id','EL Usuario Agrego una tesis al Sistema',SYSDATE())";
                $this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE);
                /*********************************************************/
                /*********************************************************/
                return $this->redirect()->toRoute('tesis');
            }
        }
        return new ViewModel(array(
            'activo' 	=> 'tesis-1',
            'lista'		=> $result,
            'lista2'		=> $result2,
            'lista3'		=> $result3,
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
        $sql = "SELECT  T.ID ID,"
                . "     T.TITULO TITULO,"
                . "     T.ANO ANO,"
                . "     T.AUTORES AUTORES,"
                . "     T.ESPECIALIDAD ESPECIALIDAD,"
                . "     T.TUTOR_T TUTOR_T,"
                . "     T.TUTOR_M TUTOR_M,"
                . "     T.RESUMEN RESUMEN,"
                . "     T.TIPO_DOCUMENTO T_D,"
                . "     D.COMPLETO COMP_1,"
                . "     D.INTRO INTRO_1,"
                . "     D.CAP1 CAP1_1,"
                . "     D.CAP2 CAP2_1,"
                . "     D.CAP3 CAP3_1,"
                . "     D.CAP4 CAP4_1,"
                . "     D.CONCLUSION CONC_1,"
                . "     D.COMPLETO_ST COMP,"
                . "     D.INTRO_ST INTRO,"
                . "     D.CAP1_ST CAP1,"
                . "     D.CAP2_ST CAP2,"
                . "     D.CAP3_ST CAP3,"
                . "     D.CAP4_ST CAP4,"
                . "     D.CONCLUSION_ST CONC "
                . "FROM TESIS T, DOCUMENTOS D "
                . "WHERE T.ID = $id "
                . "AND D.ID_TESIS = T.ID";
        
    	$result = $this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE);

        $result1 = $this->adaptador->query('select * from ESPECIALIDAD',Adapter::QUERY_MODE_EXECUTE);
        $result2 = $this->adaptador->query('select * from TUTORES WHERE TIPO_TUTOR = 1',Adapter::QUERY_MODE_EXECUTE);
        $result3 = $this->adaptador->query('select * from TUTORES WHERE TIPO_TUTOR = 2',Adapter::QUERY_MODE_EXECUTE);
        $mensaje = new Container("msg");
    	$mensaje->msgbox = true;
    	$mensaje->msgtemporal = "";
    	$mensaje->satisfactorio = "success";
        if ($request->isPost()) {
            $datos = $request->getPost();
            $titulo         = $datos["titulo"];
            $ano            = $datos["ano"];
            $especialidad   = $datos["especialidad"];
            $autores        = $datos["autores"];
            $tutor_t        = $datos["tutor_t"];
            $tutor_m        = $datos["tutor_m"];
            $resumen        = $datos["resumen"];
            $t_documento    = $datos["t_documento"];
            //Variables para los nombres temporales de los archivos
            $completo_tn       = $_FILES["completo"]["tmp_name"];
            $intro_tn          = $_FILES["intro"]["tmp_name"];
            $cap1_tn           = $_FILES["cap1"]["tmp_name"];
            $cap2_tn           = $_FILES["cap2"]["tmp_name"];
            $cap3_tn           = $_FILES["cap3"]["tmp_name"];
            $cap4_tn           = $_FILES["cap4"]["tmp_name"];
            $concl_tn          = $_FILES["concl"]["tmp_name"];
            //Variables para el tipo de archivo
            $completo_t       = $_FILES["completo"]["type"];
            $intro_t          = $_FILES["intro"]["type"];
            $cap1_t           = $_FILES["cap1"]["type"];
            $cap2_t           = $_FILES["cap2"]["type"];
            $cap3_t           = $_FILES["cap3"]["type"];
            $cap4_t           = $_FILES["cap4"]["type"];
            $concl_t          = $_FILES["concl"]["type"];
            //Variables para el tamaño de archivo
            $completo_s       = $_FILES["completo"]["size"];
            $intro_s          = $_FILES["intro"]["size"];
            $cap1_s           = $_FILES["cap1"]["size"];
            $cap2_s           = $_FILES["cap2"]["size"];
            $cap3_s           = $_FILES["cap3"]["size"];
            $cap4_s           = $_FILES["cap4"]["size"];
            $concl_s          = $_FILES["concl"]["size"];
            
            
            
            //Procedemos a actualizar la tesis
            /****************************************************************/
            if($t_documento == "on"){
                $t_documento1 = 1;
            }else{
                $t_documento1 = 2;
            }
            $sql = "UPDATE TESIS "
                    . "SET TITULO = '$titulo',"
                    . "AUTORES = '$autores',"
                    . "ANO = '$ano',"
                    . "ESPECIALIDAD = '$especialidad',"
                    . "TUTOR_T = '$tutor_t',"
                    . "TUTOR_M = '$tutor_m',"
                    . "RESUMEN = '$resumen',"
                    . "TIPO_DOCUMENTO = '$t_documento1' "
                    . " WHERE ID = $id LIMIT 1";
            if(!$this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE)){
                $mensaje->msgtemporal = "Ocurrio un problema al momento de actualizar la tesis!";
                $mensaje->satisfactorio = "warning";
                $mensaje->msgbox = false;
                return new ViewModel(array(
                    'activo' 	=> 'tesis-1',
                    'lista'	=> $result,
                    'lista2'	=> $result2,
                    'lista3'	=> $result3,
                    'msg'	=> $mensaje->msgtemporal,
                    'satis' 	=> $mensaje->satisfactorio,
                ));
            }
            /****************************************************************/
            
            /***********************************************************************/
            /***********************************************************************/
            /***********************************************************************/
            /******Proceso para subir los documentos de tesis al sistema************/
            /***********************************************************************/
            /***********************************************************************/
            /***********************************************************************/
            //Verificamos que tipo de tesis es: Completa o dividida
            $sql = "UPDATE DOCUMENTOS SET ";
            //$carpeta = "/upload/tesis";
            $carpeta = "./public/upload/tesis";
            if($t_documento == "on"){
                //Tesis Completa y verificamos que sea PDF
                if ($completo_t != "application/pdf" && $completo_tn != ""){
                    $mensaje->msgtemporal = "Tipo de Documento en Tesis Completa debe ser <strong>PDF</strong>";
                    $mensaje->satisfactorio = "warning";
                    $mensaje->msgbox = false;
                    return new ViewModel(array(
                        'activo' 	=> 'tesis-1',
                        'lista'	=> $result,
                        'lista2'	=> $result2,
                        'lista3'	=> $result3,
                        'msg'	=> $mensaje->msgtemporal,
                        'satis' 	=> $mensaje->satisfactorio,
                    ));
                }
                if ($completo_tn != ""){
                    $ruta = $carpeta."/".$id."_.PDF";
                    if(move_uploaded_file($completo_tn, $ruta)){
                        $ruta = "/upload/tesis/".$id."_.PDF";
                        $sql .= "COMPLETO = '$ruta', COMPLETO_ST = '1' WHERE ID_TESIS = $id LIMIT 1";
                    }else{
                        $sql .= "COMPLETO = '$ruta', COMPLETO_ST = '0' WHERE ID_TESIS = $id LIMIT 1";
                    }
                }
                
            }else{
                if ($intro_tn == "" && $cap1_tn == "" && $cap2_tn == "" && $cap3_tn == "" && $cap4_tn == "" && $concl_tn == ""){
                    $mensaje->msgtemporal = "La tesis <strong>$titulo</strong> se actualizo exitosamente!";
                    $mensaje->satisfactorio = "success";
                    $mensaje->msgbox = true;
                    /**********Captura los enventos para la auditoria*********/
                    /*********************************************************/
                    $user = $this->zfcUserAuthentication()->getIdentity()->getUsername();
                    $sql = "INSERT INTO EVENTO_AUDIT (EVNT_ACCION,EVNT_USER,EVNT_MODULO,EVNT_ID_ELEMENT,EVNT_DESCRI,EVNT_FECHA) "
                            . "VALUES ('EDITAR','$user','TESIS','$id','EL Usuario Actualizo una tesis',SYSDATE())";
                    $this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE);
                    /*********************************************************/
                    /*********************************************************/
                    return $this->redirect()->toRoute('tesis');
                }
                $intro = "";
                $cap1  = "";
                $cap2  = "";
                $cap3  = "";
                $cap4  = "";
                $concl = "";
                $ruta1 = $carpeta."/".$id."_INTRO_.PDF";
                $ruta2 = $carpeta."/".$id."_CAP1_.PDF";
                $ruta3 = $carpeta."/".$id."_CAP2_.PDF";
                $ruta4 = $carpeta."/".$id."_CAP3_.PDF";
                $ruta5 = $carpeta."/".$id."_CAP4_.PDF";
                $ruta6 = $carpeta."/".$id."_CONCL_.PDF";
                
                $carpeta = "/upload/tesis";
                $ruta1_1 = $carpeta."/".$id."_INTRO_.PDF";
                $ruta2_1 = $carpeta."/".$id."_CAP1_.PDF";
                $ruta3_1 = $carpeta."/".$id."_CAP2_.PDF";
                $ruta4_1 = $carpeta."/".$id."_CAP3_.PDF";
                $ruta5_1 = $carpeta."/".$id."_CAP4_.PDF";
                $ruta6_1 = $carpeta."/".$id."_CONCL_.PDF";
                $sql2 ="";
                //Dividida y Verificamos cada archivo que sea PDF
                if ($intro_t != "application/pdf" && $intro_tn != ""){
                    $mensaje->msgtemporal = "Tipo de Documento Introduccion debe ser <strong>PDF</strong>";
                    $mensaje->satisfactorio = "warning";
                    $mensaje->msgbox = false;
                    return new ViewModel(array(
                        'activo' 	=> 'tesis-1',
                        'lista'	=> $result,
                        'lista2'	=> $result2,
                        'lista3'	=> $result3,
                        'msg'	=> $mensaje->msgtemporal,
                        'satis' 	=> $mensaje->satisfactorio,
                    ));                    
                }
                if ($intro_tn != ""){
                    if(move_uploaded_file($intro_tn,$ruta1)){
                        $sql2 .= ",INTRO = '$ruta1_1', INTRO_ST = '1'";
                    }
                }
                
                if ($cap1_t != "application/pdf" && $cap1_tn != ""){
                    $mensaje->msgtemporal = "Tipo de Documento Capitulo 1 debe ser <strong>PDF</strong>";
                    $mensaje->satisfactorio = "warning";
                    $mensaje->msgbox = false;
                    return new ViewModel(array(
                        'activo' 	=> 'tesis-1',
                        'lista'	=> $result,
                        'lista2'	=> $result2,
                        'lista3'	=> $result3,
                        'msg'	=> $mensaje->msgtemporal,
                        'satis' 	=> $mensaje->satisfactorio,
                    ));
                }
                if ($cap1_tn != ""){
                    if(move_uploaded_file($cap1_tn,$ruta2)){
                        $sql2 .= ",CAP1 = '$ruta2_1', CAP1_ST = '1'";
                    }
                }
                
                if ($cap2_t != "application/pdf" && $cap2_tn != ""){
                    $mensaje->msgtemporal = "Tipo de Documento Capitulo 2 debe ser <strong>PDF</strong>";
                    $mensaje->satisfactorio = "warning";
                    $mensaje->msgbox = false;
                    return new ViewModel(array(
                        'activo' 	=> 'tesis-1',
                        'lista'	=> $result,
                        'lista2'	=> $result2,
                        'lista3'	=> $result3,
                        'msg'	=> $mensaje->msgtemporal,
                        'satis' 	=> $mensaje->satisfactorio,
                    ));
                }
                if ($cap2_tn != ""){
                    if(move_uploaded_file($cap2_tn,$ruta3)){
                        $sql2 .= ",CAP2 = '$ruta3_1', CAP2_ST = '1'";
                    }
                }
               
                if ($cap3_t != "application/pdf" && $cap3_tn != ""){
                    $mensaje->msgtemporal = "Tipo de Documento Capitulo 3 debe ser <strong>PDF</strong>";
                    $mensaje->satisfactorio = "warning";
                    $mensaje->msgbox = false;
                    return new ViewModel(array(
                        'activo' 	=> 'tesis-1',
                        'lista'	=> $result,
                        'lista2'	=> $result2,
                        'lista3'	=> $result3,
                        'msg'	=> $mensaje->msgtemporal,
                        'satis' 	=> $mensaje->satisfactorio,
                    ));
                }
                if ($cap3_tn != ""){
                    if(move_uploaded_file($cap3_tn,$ruta4)){
                        $sql2 .= ",CAP3 = '$ruta4_1', CAP3_ST = '1'";
                    }
                }
                if ($cap4_t != "application/pdf" && $cap4_tn != ""){
                    $mensaje->msgtemporal = "Tipo de Documento Capitulo 4 debe ser <strong>PDF</strong>";
                    $mensaje->satisfactorio = "warning";
                    $mensaje->msgbox = false;
                    return new ViewModel(array(
                        'activo' 	=> 'tesis-1',
                        'lista'	=> $result,
                        'lista2'	=> $result2,
                        'lista3'	=> $result3,
                        'msg'	=> $mensaje->msgtemporal,
                        'satis' 	=> $mensaje->satisfactorio,
                    ));
                }
                if ($cap4_tn != ""){
                    if(move_uploaded_file($cap4_tn,$ruta5)){
                        $sql2 .= ",CAP4 = '$ruta5_1', CAP4_ST = '1'";
                    }
                }
                if (($concl_t != "application/pdf") && ($concl_tn != "")){
                    $mensaje->msgtemporal = "Tipo de Documento Conclusion debe ser <strong>PDF</strong>";
                    $mensaje->satisfactorio = "warning";
                    $mensaje->msgbox = false;
                    return new ViewModel(array(
                        'activo' 	=> 'tesis-1',
                        'lista'	=> $result,
                        'lista2'	=> $result2,
                        'lista3'	=> $result3,
                        'msg'	=> $mensaje->msgtemporal,
                        'satis' 	=> $mensaje->satisfactorio,
                    ));
                }
                if ($concl_tn != ""){
                    if(move_uploaded_file($concl_tn,$ruta6)){
                        $sql2 .= ",CONCLUSION = '$ruta6_1', CONCLUSION_ST = '1'";
                    }
                }
                $sql2 = substr($sql2, 1);
                $sql .= $sql2." WHERE ID_TESIS = $id LIMIT 1";
            }
            if($this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE)){
                /**********Captura los enventos para la auditoria*********/
                /*********************************************************/
                $user = $this->zfcUserAuthentication()->getIdentity()->getUsername();
                $sql = "INSERT INTO EVENTO_AUDIT (EVNT_ACCION,EVNT_USER,EVNT_MODULO,EVNT_ID_ELEMENT,EVNT_DESCRI,EVNT_FECHA) "
                        . "VALUES ('EDITAR','$user','TESIS','$id','EL Usuario Actualizo una tesis',SYSDATE())";
                $this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE);
                /*********************************************************/
                /*********************************************************/
                $mensaje->msgtemporal = "La tesis <strong>$titulo</strong> se actualizo exitosamente!";
                $mensaje->satisfactorio = "success";
                $mensaje->msgbox = true;
                return $this->redirect()->toRoute('tesis');
            }
        }
        return new ViewModel(array(
            'activo' 	=> 'tesis-1',
            'lista'     => $result,
            'lista1'    => $result1,
            'lista2'    => $result2,
            'lista3'    => $result3,
            'msg'       => $mensaje->msgtemporal,
            'satis'     => $mensaje->satisfactorio,
    	));
    }
    public function eliminarAction(){
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login',array('redirect' => $this->getRequest()->getUri()));
        }
        $id = (int) $this->params()->fromRoute('id', 0);
        $this->adaptador = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        if($id){
            $sql ="DELETE FROM TESIS WHERE ID = $id";
            $this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE);
        }
        /**********Captura los enventos para la auditoria*********/
        /*********************************************************/
        $user = $this->zfcUserAuthentication()->getIdentity()->getUsername();
        $sql = "INSERT INTO EVENTO_AUDIT (EVNT_ACCION,EVNT_USER,EVNT_MODULO,EVNT_ID_ELEMENT,EVNT_DESCRI,EVNT_FECHA) "
                . "VALUES ('ELIMINAR','$user','TESIS','$id','EL Usuario elimino una tesis del Sistema',SYSDATE())";
        $this->adaptador->query($sql,Adapter::QUERY_MODE_EXECUTE);
        /*********************************************************/
        /*********************************************************/
        $mensaje = new Container("msg");
    	$mensaje->msgbox = true;
        $mensaje->msgtemporal = "Se ha eliminado la Tesis satisfactoriamente";
        $mensaje->satisfactorio = "success";
        return $this->redirect()->toRoute('tesis');
    }
}


