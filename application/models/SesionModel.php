<?php
    class SesionModel extends CI_Model
    {
        function getUserData($iduser){
            $sql = "
                SELECT 
                    e.emp_codigo as 'empleado',
                    e.emp_estado as 'estado',
                    e.emp_nombres as 'nombres',
                    e.emp_paterno as 'paterno',
                    e.emp_materno as 'materno',
                    concat(e.emp_nombres,' ',e.emp_paterno) as 'nombre_full',
                    u.usu_codigo as 'id',
                    e.emp_imagen as 'imagen',
                    r.rol_titulo as 'rol',
                    e.emp_sucursal as 'sucursal',
                    s.suc_nombre as 'sucursal_nombre',
                    u.usu_clave as password,
                    u.usu_dev_work as dev
                FROM usuario u
                INNER JOIN roles r ON r.rol_codigo = u.usu_rol
                INNER JOIN empleado e ON e.emp_codigo = u.usu_empleado 
                INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal 
                LEFT JOIN config_user
                WHERE 
                    u.usu_usuario = ? 

            ";
        }
    }
    