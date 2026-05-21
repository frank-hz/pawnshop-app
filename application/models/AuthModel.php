<?php
    class AuthModel extends CI_Model {
        
        function validate_user($username){			
			$this->db->where("u.usu_usuario", $username);
            $this->db->select("u.usu_codigo as id");
            $this->db->select("u.usu_usuario as usuario");
            $this->db->select("u.usu_usuario as username");
            $this->db->select("u.usu_clave as clave");
            $this->db->select("u.usu_clave as password");
            $this->db->select("u.usu_dev_work as dev");
            $this->db->select("DATE_FORMAT(u.usu_creacion, '%d/%m/%Y') as creacion");
            $this->db->select("DATE_FORMAT(u.create_at, '%d/%m/%Y %H:%i:%s') as create_at");
			$this->db->select("s.suc_codigo as sucursal_id");
			$this->db->select("s.suc_nombre as sucursal_nombre");
			$this->db->select("s.suc_nombre as sucursal_serie");
			$this->db->select("r.rol_codigo as rol_id");
			$this->db->select("r.rol_titulo as rol_nombre");
			$this->db->select("e.emp_imagen as imagen");
			$this->db->select("e.emp_codigo as empleado_id");
			$this->db->select("e.emp_nombres as nombres");
            $this->db->select("e.emp_paterno as paterno");
            $this->db->select("e.emp_materno as materno");
            $this->db->select("
                CONCAT(
                    e.emp_nombres,' ', 
                    e.emp_paterno,' ',
                    e.emp_materno
                ) as nom_ape
            ");
            $this->db->select("
                CONCAT(
                    e.emp_nombres,' ', 
                    e.emp_paterno,' ',
                    e.emp_materno
                ) as nombre_apellido
            ");
            $this->db->select("r.rol_titulo as nombre");
            $this->db->from("usuario u");
            $this->db->join("empleado e", "e.emp_codigo = u.usu_empleado");     
            $this->db->join("sucursal s", "e.emp_sucursal = s.suc_codigo");     
            $this->db->join("roles r", "u.usu_rol = r.rol_codigo");     
            $res = $this->db->get();
            return $res->row();
        }
        public function get_role_max($usuario_id){
            $this->db->where("usuario_id", $usuario_id);
            $this->db->select("r.id");
            $this->db->from("role r");
            $this->db->join("usuario_role ur", "ur.rol_id = r.id");
            $this->db->order_by("r.nivel_acceso", "DESC");
            $this->db->limit(1);
            return $this->db->get()->row();
        }        
        public function get_permits_role($role_id){
            $this->db->select('
                m.id AS module_id,
                m.nombre AS modulo,
                m.alias AS modulo_alias,
                m.ruta AS ruta_modulo,
                m.icono,
                a.keyname AS accion_key,
                a.nombre AS accion,
                p.descripcion AS permiso_descripcion
            ');
            
            $this->db->from('role_permits rp');
            $this->db->join('permits p', 'rp.permit_id = p.id');
            $this->db->join('modules m', 'p.module_id = m.id');
            $this->db->join('actions a', 'p.action_id = a.id');
            
            $this->db->where('rp.rol_id', $role_id);
            $this->db->where('m.estado', 'A');
            $this->db->where('m.is_visible', TRUE);
            
            $this->db->order_by('m.orden', 'ASC');
            $this->db->order_by('m.nombre', 'ASC');

            return $this->db->get()->result();
        }
        public function get_menu_modules_v1($role_id){
            $this->db->distinct();
            $this->db->select(' 
                m.id, 
                m.nombre, 
                m.alias, 
                m.descripcion, 
                m.icono, 
                m.ruta, 
                m.orden');
            
            $this->db->from('role_permits rp');
            $this->db->join('permits p', 'rp.permit_id = p.id');
            $this->db->join('modules m', 'p.module_id = m.id');
            
            $this->db->where('rp.rol_id', $role_id);
            $this->db->where('m.estado', 'A');
            $this->db->where('m.is_visible', TRUE);
            
            $this->db->order_by('m.orden', 'ASC');
            $this->db->order_by('m.nombre', 'ASC');

            return $this->db->get()->result();
        }
        public function get_menu_modules($role_id){
            $this->db->select(' 
                m.id, 
                m.nombre, 
                m.alias, 
                m.descripcion, 
                m.icono, 
                m.ruta, 
                m.orden');
            
            $this->db->from('role_permits rp');
            $this->db->join('role r', 'rp.rol_id = r.id');
            $this->db->join('permits p', 'rp.permit_id = p.id');
            $this->db->join('modules m', 'p.module_id = m.id');
            $this->db->join('actions a', 'p.action_id = a.id');
            
            $this->db->where('r.id', $role_id);
            $this->db->where('m.estado', 'A');
            $this->db->where('m.is_visible', TRUE);
            $this->db->where('a.nombre', 'Acceso');
            
            $this->db->order_by('m.orden', 'ASC');
            $this->db->order_by('m.nombre', 'ASC');

            return $this->db->get()->result();
        }
        public function get_rol_permisos_completo($usuario_id){
            // Primero obtenemos el rol de mayor nivel
            $rol = $this->get_role_max($usuario_id);
            
            if (!$rol) {
                return ['rol' => null, 'menu' => [], 'permisos' => []];
            }

            $permisos = $this->get_permisos_rol($rol->id);
            $menu     = $this->get_menu_modulos($rol->id);

            return [
                'rol'      => $rol,
                'menu'     => $menu,
                'permisos' => $permisos
            ];
        }


    
    }

?>