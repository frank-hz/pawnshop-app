<?php
    
    // instancia global
    $CI =& get_instance();
    $CI->load->model('RevisionModel');


    function revision_cuota($cuota) {
        try {
            if (!$id) return;
            $cuota = $this->RevisionModel->getCuota($id);

            // $nextCuota = date('Y-m-d', strtotime())        

        } catch (PDOException $e) {
            //throw $th;
        }

    }

    // function 
    
    
?>