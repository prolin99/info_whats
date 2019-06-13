<?php
    if (@$_SERVER['HTTP_X_FORWARDED_FOR']) {
        $remoIP=$_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $remoIP=$_SERVER['REMOTE_ADDR'];
    }
    //$str = "your ip: $remoIP " ;
    $str= mb_convert_encoding("YourIp: $remoIP ", "UTF-16LE", "auto")  ;

    echo $str ;

?>
