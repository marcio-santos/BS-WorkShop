<?php
/**
 * Created by PhpStorm.
 * User: MárcioAlex
 * Date: 20/03/14
 * Time: 12:21
 */

function uuid(){
    // version 4 UUID
    $get =  sprintf(
        '%08x-%04x-%04x',
        mt_rand(),
        mt_rand(0, 65535),
        bindec(substr_replace(
                sprintf('%016b', mt_rand(0, 65535)), '0100', 11, 4)
        ),
        bindec(substr_replace(sprintf('%08b', mt_rand(0, 255)), '01', 5, 2)),
        mt_rand(0, 255),
        mt_rand()
    );
    return strtoupper($get) ;
}

//QUANTIDADE DE VOUCHERS PARA GERAÇÃO
$limit = isset($_GET[q])? $_GET[q] : 1;


for($i=0;$i<$limit;$i++){
    $code = uuid();
    $str .= $code."\n" ;
}
  file_put_contents('vouchers.csv',$str);
  header('Location:http://bodysystems.net/_ferramentas/voucher_generator/vouchers.csv');