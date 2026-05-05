<?php 
    class PrendaAPI extends CI_Controller {
        function __construct(){
            parent::__construct();

            // Models
            $this->load->model('api/PrendaAPIModel');

            // Helpers
            $this->load->helper('permission_helper');
        }



        function get_all(){
            $filters = Array(
                "tipo" => $_POST["tipo"] ?? null,
                "categoria" => $_POST["categoria"] ?? null,
                "marca" => $_POST["marca"] ?? null,
                "modelo" => $_POST["modelo"] ?? null,
                "serie" => $_POST["serie"] ?? null,
                "quilate" => $_POST["quilate"] ?? null,
                "placa" => $_POST["placa"] ?? null,
                "color" => $_POST["color"] ?? null,
                "descripcion" => $_POST["descripcion"] ?? null,
                "limite" => intval($_POST["limite"]) ?? null,
                "orden" => $_POST["orden"] ?? "DESC"
            );
            
            $filters["limite"] = ($filters["limite"] === 0) ? null : $filters["limite"];
            // echo json_encode($filters["limite"]);
            $data = $this->PrendaAPIModel->getAll($filters);
            echo json_encode($data);
        }



        // CUSTOM
        function get_all_marcas(){
            $tipo = $this->input->post("tipo");
            $data = $this->PrendaAPIModel->getAllMarcas($tipo);
            echo json_encode($data);
        }
        function get_all_quilates(){
            $data = $this->PrendaAPIModel->getAllQuilates();
            echo json_encode($data);
        }
    
    }