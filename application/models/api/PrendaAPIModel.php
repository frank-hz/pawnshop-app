<?php
    class PrendaAPIModel extends CI_Model {
        
        function __construct(){
            $this->sede_id = $this->session->userdata("sucursal_id");
        }

        function getAll($params = []){            
            if (!empty($params["limite"])){
                $this->db->limit($params["limite"]);
            }
            if (!empty($params["tipo"])){
                $this->db->group_start()
                ->where("tpren_codigo", $params["tipo"])
                ->or_where("tpren_nombre", $params["tipo"])
                ->group_end();
            }
            if (!empty($params["categoria"])){
                $this->db->where("cat_codigo", $params["categoria"]);
            }
            if (!empty($params["marca"])){
                $this->db->where("pren_marca", $params["marca"]);
            }
            if (!empty($params["modelo"])){
                $this->db->where("pren_modelo", $params["modelo"]);
            }
            if (!empty($params["serie"])){
                $this->db->where("pren_serial", $params["serie"]);
            }
            if (!empty($params["quilate"])){
                $this->db->where("pren_quilate", $params["quilate"]);
            }
            if (!empty($params["peso"])){
                $this->db->where("pren_peso", $params["peso"]);
            }
            if (!empty($params["placa"])){
                $this->db->where("pren_placa", $params["placa"]);
            }
            if (!empty($params["kilometraje"])){
                $this->db->where("pren_kilometraje", $params["kilometraje"]);
            }
            if (!empty($params["color"])){
                $this->db->where("pren_color", $params["color"]);
            }
            if (!empty($params["descripcion"])){
                $this->db->like("pren_descripcion", $params["descripcion"],"both");
            }            

            $this->db->select("p.pren_codigo as id");
            $this->db->select("p.pren_descripcion as descripcion");
            $this->db->select("p.pren_serial as serial");
            $this->db->select("p.pren_marca as marca");
            $this->db->select("p.pren_modelo as modelo");
            $this->db->select("p.pren_quilate as quilate");
            $this->db->select("p.pren_peso as peso");
            $this->db->select("p.pren_placa as placa");
            $this->db->select("p.pren_anio as anio");
            $this->db->select("p.pren_kilometraje as kilometraje");
            $this->db->select("p.pren_color as color");
            $this->db->select("p.pren_observacion as observacion");
            $this->db->select("p.pren_comentario as comentario");
            $this->db->select("p.pren_valoracion as valoracion");
            $this->db->select("p.pren_estado as estado");
            $this->db->select("DATE_FORMAT(p.pren_fecha_registro, '%d/%m/%Y') as create_at");
            $this->db->select("c.cli_codigo as cliente_id");
            $this->db->select("
                CONCAT(
                    c.cli_nombres,' ', 
                    c.cli_paterno,' ',
                    c.cli_materno
                ) as cliente_nombre
            ");
            $this->db->select("ct.cat_codigo as categoria_id");
            $this->db->select("ct.cat_nombre as categoria_nombre");
            $this->db->select("t.tpren_codigo as tipo_id");
            $this->db->select("t.tpren_nombre as tipo_nombre");
            $this->db->join("cliente c", "c.cli_codigo = p.pren_cliente");
            $this->db->join("categoria ct", "ct.cat_codigo = p.pren_categoria");
            $this->db->join("tipo_prenda t", "t.tpren_codigo = ct.cat_tipo");
            $this->db->order_by("descripcion", "ASC");
            $this->db->order_by("create_at", "DESC");
            $res = $this->db->get("prenda p");
            return $res->result();
        }



        // CUSTOM
        function getAllMarcas($tipo){
            $sql = "
                SELECT 
                    DISTINCT p.pren_marca as marca
                FROM prenda p 
                JOIN categoria c ON c.cat_codigo = p.pren_categoria 
                JOIN tipo_prenda tp ON tp.tpren_codigo = c.cat_tipo 
                WHERE tp.tpren_nombre = ? and p.pren_marca != ''
            ";
            $res = $this->db->query($sql, Array($tipo));
            return $res->result();
        }

        function getAllQuilates(){
            $sql = "
                SELECT DISTINCT pren_quilate as quilate from prenda 
                where pren_quilate != ''    
            ";
            $res = $this->db->query($sql);
            return $res->result();
        }
    
    }