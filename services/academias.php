<?php
/**
 * Created by PhpStorm.
 * User: MárcioAlex
 * Date: 12/05/14
 * Time: 16:37
 *  Rotina para inclusão das academias onde os professores trabalham
 */



include('../../../exec_in_joomla.inc') ;


$cpf = $_POST['cpf'] ;
$eventoid = $_POST['eventoid'] ;
$evento = $_POST['evento'] ;
$academias = $_POST['academias'] ;
$dta_registro = date('Y-m-d H:i:s') ;

$cpf = str_replace('-','',str_replace('.','',str_replace('/','',str_replace(' ','',$cpf))));


$db = &JFactory::getDBO();


//VERIFICA A RODADA
$query = "SELECT rodada FROM rodadas ORDER BY id DESC LIMIT 1" ;
$db->setQuery($query);
$rodada = $db->loadResult();



//Verifica se já houve lançamento para o mesmo evento - positivo atualiza
$query = "SELECT count(eventoid) FROM treinadores_academias WHERE cpf LIKE ".$db->Quote($cpf)." AND rodada LIKE ".$db->Quote($rodada) ;
$db->setQuery($query);
$result = $db->loadResult();

if($result == 0) {
    $query = "INSERT INTO treinadores_academias (cpf,eventoid,evento,academias,dta_registro,rodada) VALUES ('$cpf','$eventoid','$evento','$academias','$dta_registro','$rodada') ";
}   else {
    $query = "UPDATE treinadores_academias SET academias =".$db->Quote($academias).", dta_registro = ".$db->Quote($dta_registro)." WHERE cpf LIKE ".$db->Quote($cpf)."  AND rodada LIKE ".$db->Quote($rodada);
}

file_put_contents('query_acad.log',$query);

$db->setQuery($query) ;
$db->Query();

file_put_contents('query_academias.log',$query);

if($db->getErrorNum() > 0) {
    $ret = "Erro: ".$db->getErrorMsg();
} else {
    $ret = "Registro inserido com sucesso!";
}


