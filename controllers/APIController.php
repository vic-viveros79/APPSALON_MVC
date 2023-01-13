<?php

namespace Controllers;

use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;

class APIController {
    public static function index() {
        $servicios = Servicio::all();
        //debuguear($servicios);
        echo json_encode($servicios);
    }

    public static function guardar() {
        //almacena la cita y el id
        $cita = new Cita($_POST);
        $resultado = $cita->guardar();

        $id = $resultado['id'];

        //almacena los servicios con el id de la cita
        $idServicios = explode(",", $_POST['servicios'] );

        $resultado = [
            'servicios' => $idServicios
        ];

        foreach($idServicios as $idServicio) {
            $args= [
                'citaId' => $id,
                'servicioId' => $idServicio
            ];

            $citaServicio = new CitaServicio($args);
            $citaServicio -> guardar();

           
        }        
        //retorna una respuesta
        echo json_encode(['resultado' => $resultado]);
    }

    public static function eliminar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];

            $cita = Cita::find($id);
            $cita->eliminar();
            header('Location:' . $_SERVER['HTTP_REFERER']);
        }
    }
} 

?>