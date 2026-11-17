<?php
class ExporterModel extends CI_Model {

    
    function get_clientes($prefs = []){
        $orden = $this->validar_orden_busqueda($prefs['orden_tipo'] ?? null);
        $limite = !empty($prefs['limite']) ? $prefs['limite'] : 1000;
        
        $this->db->select("c.cli_codigo as id");
        $this->db->select("c.cli_documento as documento");
        $this->db->select("c.cli_tipo_documento as tipo_documento");
        $this->db->select("c.cli_nombres as nombres");
        $this->db->select("c.cli_paterno as paterno");
        $this->db->select("c.cli_materno as materno");
        $this->db->select("u.ubi_departamento as departamento");
        $this->db->select("u.ubi_provincia as provincia");
        $this->db->select("u.ubi_distrito as distrito");
        $this->db->select("c.cli_direccion as direccion");
        $this->db->select("c.cli_telefono as telefono");
        $this->db->select("c.cli_email as email");
        $this->db->select("cli_codigo as id");
        $this->db->join("ubigeo u", "u.ubi_codigo = c.cli_ubigeo");
        $this->db->limit($limite);
        $this->db->order_by("c.cli_nombres", $orden);

        if ($prefs && !empty($prefs)) {
            $this->aplicar_filtros("c", $prefs);
        }
        $res = $this->db->get("cliente c");
        return $res->result_array();
    }
    function get_prendas($prefs = []){
        $orden = $this->validar_orden_busqueda($prefs['orden_tipo'] ?? null);
        $limite = !empty($prefs['limite']) ? $prefs['limite'] : 1000;
        
        $this->db->select("p.pren_codigo as id");
        $this->db->select("DATE_FORMAT(p.fecha_creacion, '%d/%m/%Y') as fecha_creacion");
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
        $this->db->select("c.cli_documento as cliente_documento");
        $this->db->select("
            CONCAT_WS(
                ' ', c.cli_nombres, c.cli_paterno, c.cli_materno
            ) as cliente_nombre
        ");
        $this->db->select("t.tpren_nombre as tipo_nombre");
        $this->db->select("ct.cat_nombre as categoria_nombre");
        $this->db->join("cliente c", "c.cli_codigo = p.pren_cliente");
        $this->db->join("categoria ct", "ct.cat_codigo = p.pren_categoria");
        $this->db->join("tipo_prenda t", "t.tpren_codigo = ct.cat_tipo");
        $this->db->limit($limite);
        $this->db->order_by("p.pren_descripcion", $orden);

        if ($prefs && !empty($prefs)) {
            $this->aplicar_filtros("p", $prefs);
        }
        $res = $this->db->get("prenda p");
        return $res->result_array();
    }


    

    // UTILIDAD
    private function aplicar_filtros($tabla_nomen = "", array $filtros = []): void {
        $nomen = !empty($tabla_nomen) ? "$tabla_nomen." : ""; 
        $CAMPO_FECHA = "$nomen"."fecha_creacion";
        $fecha = $this->normalizar_fecha($filtros['fecha'] ?? null);
        $desde = $this->normalizar_fecha($filtros['fecha_desde'] ?? null);
        $hasta = $this->normalizar_fecha($filtros['fecha_hasta'] ?? null);

        // Año y mes (validados)
        $anio = isset($filtros['anio']) ? (int) $filtros['anio'] : 0;
        $mes  = isset($filtros['mes'])  ? (int) $filtros['mes']  : 0;

        if ($anio < 1900 || $anio > 2100) {
            $anio = 0;
        }
        if ($mes < 1 || $mes > 12) {
            $mes = 0;
        }

        // Fecha fija: se convierte en rango [dia 00:00:00, dia siguiente 00:00:00)
        if ($fecha) {
            $desde = $fecha;
            $hasta = $fecha;
        }

        // Año/mes: solo se aplican si no hay fecha fija ni rango explícito
        elseif (!$desde && !$hasta && $anio) {
            if ($mes) {
                // Mes específico de un año: del día 1 al último día del mes
                $desde = sprintf('%04d-%02d-01', $anio, $mes);
                $hasta = date('Y-m-t', strtotime($desde));
            } else {
                // Año completo
                $desde = sprintf('%04d-01-01', $anio);
                $hasta = sprintf('%04d-12-31', $anio);
            }
        }

        // Protección ante rango invertido
        if ($desde && $hasta && $desde > $hasta) {
            [$desde, $hasta] = [$hasta, $desde];
        }

        if ($desde) {
            $this->db->where($CAMPO_FECHA . ' >=', $desde . ' 00:00:00');
        }

        if ($hasta) {
            // Límite exclusivo del día siguiente: cubre fracciones de segundo
            $siguiente = date('Y-m-d', strtotime($hasta . ' +1 day'));
            $this->db->where($CAMPO_FECHA . ' <', $siguiente . ' 00:00:00');
        }

         // Mes sin año: "ese mes en cualquier año" (no se puede expresar como rango)
        if ($mes && !$anio) {
            $this->db->where("MONTH($CAMPO_FECHA)", $mes);
        }
    }
    /** Devuelve la fecha en formato Y-m-d o null si es vacía/inválida. */
    private function normalizar_fecha(?string $valor): ?string {
        if ($valor === null || $valor === '') {
            return null;
        }
        $dt = DateTime::createFromFormat('Y-m-d', $valor);
        return ($dt && $dt->format('Y-m-d') === $valor) ? $valor : null;
    }



    private function validar_orden_busqueda($value = null){
        $tipos = ['ASC','DESC'];
        if (empty($value) || $value === null) {
            return FALSE;
        }
        $value = trim(mb_strtoupper($value));
        return in_array($value, $tipos) ? $value : 'ASC';
    }
}