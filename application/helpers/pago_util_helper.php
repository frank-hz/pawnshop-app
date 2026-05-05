<?php 
    function recognize_forma_pago($val){
        $formas = [
            'TIEN' => 'En Tienda',
            'AGEN' => 'Agente',
            'TRANF' => 'Transferencia',
            'REV' => 'Revision',
            'DEP' => 'Deposito',
            '' => 'Desconocido'
        ];
        return in_array($val,['TIEN','AGEN','TRANF','REV','DEP']) ? $formas[$val] : '';
    }
    function recognize_concepto($val){
        $conceptos = [
            'REG' => 'Regularizacion',
            'REC' => 'Recuperacion',
            'AMOR' => 'Amortizacion',
            'ADEL' => 'Adelanto',
            'DEB_AUTO' => 'D. auto',
            'ADEL_AUTO' => 'A. auto',
            '' => 'Desconocido'
        ];
        return $conceptos[$val];
    }
