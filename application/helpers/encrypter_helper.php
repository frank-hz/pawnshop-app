<?php 
    
    
    function encrypt_openssl($val){
        $init_vector = base64_decode("C9fBxl1EWtYTL1/M8jfstw==");
        $method = 'aes-256-cbc';
        $key = "ecashcrypter";
        $result = openssl_encrypt($val, $method, $key, false, $init_vector);
        return $result;
    }
    function encrypt_compare($v){
        $hs = "YCW0rjjZ0AXtlw3JYm4sxA==";
        $init_vector = base64_decode("C9fBxl1EWtYTL1/M8jfstw==");
        $method = 'aes-256-cbc';
        $key = "ecashcrypter";
        $hsv = openssl_encrypt($v, $method, $key, false, $init_vector); 

        return ($hs === $hsv);
    }
?>