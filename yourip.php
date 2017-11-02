<?php
    if (@$_SERVER['HTTP_X_FORWARDED_FOR']) {
        $remoIP=$_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $remoIP=$_SERVER['REMOTE_ADDR'];
    }
    //$str = "your ip: $remoIP " ;
    $str= mb_convert_encoding("YourIp: $remoIP ", "UTF-16LE", "auto")  ;

    echo $str ;
$v="/20171101_083449.txt" ;
$success = preg_match('/.+(\d{4}\d{2}\d{2}_\d{2}\d{2}\d{2})(\.txt$)/i', trim($v) ,$vpart);
echo "<pre>" ;
print_r($vpart) ;
$v=$vpart[1] ;
$success = preg_match('/(\d{4})(\d{2})(\d{2})_(\d{2})(\d{2})(\d{2})/i', trim($v) ,$vpart);
print_r($vpart) ;

echo "</pre>" ;
?>
