<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Adapter\Adapter;

class ConsultasController extends AbstractActionController
{
    public function indexAction()
    {
        $this->adaptador = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $result = $this->adaptador->query('select * from ESPECIALIDAD',Adapter::QUERY_MODE_EXECUTE);
        $this->layout('layout/consulta');
        return new ViewModel(array(
            'activo' 	=> 'home-1',
            'lista'     => $result,
    	));
    }
    public function buscarAction()
    {
        $this->adaptador = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $result = $this->adaptador->query('select * from ESPECIALIDAD',Adapter::QUERY_MODE_EXECUTE);
        $request = $this->getRequest();
        $this->layout('layout/consulta');
        if ($request->isPost()) {
            $criterio1 = "";
            $datos = $request->getPost();
            if(trim($datos["titulo"]) == ""){
                return $this->redirect()->toRoute('consultas');
            }
            $cadenas = explode(" ", $datos["titulo"]);
            for($i = 0; $i < count($cadenas);$i++){
                //echo $cadenas[$i]."<br>";
                $criterio1 .= "OR TITULO LIKE '%".$cadenas[$i]."%' ";
            }

            $criterio2 = substr($criterio1, 3,-1);
            $especialidad = $datos["especialidad"];
            $sql = "SELECT T.ID ID, "
                    . "T.TITULO TITULO, "
                    . "T.ANO ANO, "
                    . "T.RESUMEN RESUMEN, "
                    . "T.AUTORES AUTORES, "
                    . "T.TIPO_DOCUMENTO T_P, "
                    . "E.NOMBRE ESPECIALIDAD, "
                    . "(SELECT NOMBRES FROM TUTORES WHERE T.TUTOR_T = ID) NOMBRE_TUTOR_T, "
                    . "(SELECT NOMBRES FROM TUTORES WHERE T.TUTOR_M = ID) NOMBRE_TUTOR_M, "
                    . "(SELECT COMPLETO FROM DOCUMENTOS WHERE T.ID = ID_TESIS) COMPLETO, "
                    . "(SELECT INTRO FROM DOCUMENTOS WHERE T.ID = ID_TESIS) INTRO, "
                    . "(SELECT CAP1 FROM DOCUMENTOS WHERE T.ID = ID_TESIS) CAP1, "
                    . "(SELECT CAP2 FROM DOCUMENTOS WHERE T.ID = ID_TESIS) CAP2, "
                    . "(SELECT CAP3 FROM DOCUMENTOS WHERE T.ID = ID_TESIS) CAP3, "
                    . "(SELECT CAP4 FROM DOCUMENTOS WHERE T.ID = ID_TESIS) CAP4, "
                    . "(SELECT CONCLUSION FROM DOCUMENTOS WHERE T.ID = ID_TESIS) CONCLUSION "
                    . "FROM TESIS T, ESPECIALIDAD E "
                    . "WHERE T.ESPECIALIDAD = E.ID AND "
                    . "($criterio2) AND ESPECIALIDAD LIKE '%$especialidad%'";
            
            $result2 = $this->adaptador->query($sql,  Adapter::QUERY_MODE_EXECUTE);
            return new ViewModel(array(
                'activo'        => 'home-1',
                'lista'         => $result,
                'lista2'        => $result2,
                'title'         => $datos["titulo"],
                'espec'         => $datos["especialidad"],
                'sql'           => $sql,
            ));
        }
        return new ViewModel(array(
            'activo' 	=> 'home-1',
            'lista'     => $result,
    	));
    }
}
