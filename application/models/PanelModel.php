<?php
    class PanelModel extends CI_Model
    {
        function getPrendasByState($e){
			$this->db->where("pren_estado", $e);
			$this->db->select('pren_codigo as id');
			$this->db->select('CONCAT(cli_nombres," ",cli_paterno," ", cli_materno) as cliente');
			$this->db->select('pren_descripcion as descripcion');
			$this->db->select('pren_estado as estado');
			$this->db->select('tpren_nombre as tipo');
			$this->db->select('cat_nombre as categoria');

			$this->db->join('categoria', 'cat_codigo = pren_categoria');
			$this->db->join('tipo_prenda', 'tpren_codigo = cat_tipo');
			$this->db->join('cliente', 'cli_codigo = pren_cliente');
			$this->db->from('prenda');
			$this->db->order_by('pren_codigo', 'ASC');
			$res = $this->db->get();
			return $res->result();
		}

        function getArticuloByPrenda($id){
			$this->db->where("art_prenda", $id);
			$this->db->select('art_id as id');
			$this->db->from('articulo');
			$res = $this->db->get();
			return $res->row();
		}

		function GetLastCredito($id){
			$sql = "
				SELECT 
					cred_monto as monto
				FROM credito
				WHERE cred_prenda = ? 
				ORDER BY cred_codigo DESC LIMIT 1
			";
			$res = $this->db->query($sql, [$id]);
			return $res->row();
		}

    }
    
?>
