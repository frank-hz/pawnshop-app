<?php
    function cuotau_tag_estado($state){
        $resp = "";
        $big = "stag-lg";
        switch ($state) {
            case 'E':
                $resp = "<span class='stag stag-cu-E'>En espera</span>";
                break;
                
            case 'V':
                $resp = "<span class='stag stag-cu-V'>Vencido</span>";
                break;

            case 'P':
                $resp = "<span class='stag stag-cu-P'>Pagado</span>";
                break;

            case 'CC':
                $resp = "<span class='stag stag-cu-CC'>Congelado</span>";
                break;

            case 'AN':
                $resp = "<span class='stag stag-cu-AN'>Anulado</span>";
                break;
        }
        return $resp;
    }
?>