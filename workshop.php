<?php


//include('../../exec_in_joomla.inc');
$document = &JFactory::getDocument();
$document->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js');
$document->addScript("_ferramentas/contratos/source/jquery.fancybox.js?v=2.1.5");
$document->addStyleSheet("_ferramentas/contratos/source/jquery.fancybox.css?v=2.1.5");


    //CARREGA AS INFORMAÃ‡Ã•ES DO TREINO ESCOLHIDO
    $cidade = $_POST['cidade'];
    $local = $_POST['local'];
    $horario = $_POST['horario'] ;
    $eventoid= $_POST['id-evento'];
    $nome_evento = $_POST['nome-evento'];
    $endereco= $_POST['ende-evento'];
    $dtm1 = $_POST['dt-m1'];
    $obs = $_POST['obs'];
    $aviso = $_POST['aviso'];
    $intervalo = $_POST['intervalo'] ;
    $plantao = $_POST['plantao'] ;
    $correio = (strlen($endereco)==0)? 1:0;
    if(!isset($_POST['id-evento']) || !isset($_POST['nome-evento'])) {
    Header("Location: http://bodysystems.net/calendarios/workshop") ;
    exit();
}




/*     
     if($eventoid = '18406') {
         file_put_contents('eventoid',$eventoid."\n",FILE_APPEND);
         $intervalo = $intervalo + 7 ;
     }
*/     
     //VARIAVEIS
    //$intervalo = $_REQUEST['intervalo'];
    
    
    $dead_line = 13; //INTERVALO MÍNIMO DE CORTE
    if($intervalo <=0 || $correio == 1){
        $valor_ws = '0.00';
    } else {
        $valor_ws = '0.00' ;
    }
    $valor_ref_ws = '0.00' ;
    
     function diffDate($d1, $d2, $type='', $sep='-')
    {
        $d1 = explode($sep, $d1);
        $d2 = explode($sep, $d2);
        switch ($type)
        {
            case 'A':
                $X = 31536000;
                break;
            case 'M':
                $X = 2592000;
                break;
            case 'D':
                $X = 86400;
                break;
            case 'H':
                $X = 3600;
                break;
            case 'MI':
                $X = 60;
                break;
            default:
                $X = 1;
        }

        $vD1 = mktime(0, 0, 0, $d2[1], $d2[2], $d2[0]) ;
        $vD2 = mktime(0, 0, 0, $d1[1], $d1[2], $d1[0] ) ;

        $interval = floor(( $vD1 - $vD2 )/$X ) ;

        return $interval ;

    }

    function uuid(){
        // version 4 UUID
        $get =  sprintf(
            '%08x-%04x-%04x-%02x%02x-%012x',
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

    function template_eval(&$template, &$vars)
    {
        return strtr($template, $vars);
    }

    //CALCULA O BONUS E NÍVEL DE BONUS
    function getBonus() {

    }

    function getRetirada($retirar_evento,$retirar_plantao,$retirar_correio,$intervalo) {

        $w ='<option id="WS" value="EV">Retirar no GroundWorks</option>';
        //$w ='<option id="WS" value="EV">Retirar no Workshop</option>';
       // $p ='<option id="PL" value="EV">Retirar no GroundWorks</option>';
        $p ='<option id="PL" value="EV">Retirar no Plant&atilde;o</option>';
        $c ='<option id="CO" value="CO">Receber pelos Correios</option>';

        if($retirar_evento && $intervalo > 0) {
            $ret .= $w;
        }
        if($retirar_plantao && $intervalo > 0) {
            $ret .= $p;
        }
        if($retirar_correio) {
            $ret .= $c;
        }
        
        // SE O EVENTO JÁ TIVE PASSADO
        if($intervalo <=0) {
            $ret = $c;
        }
        $ret = '<select id="sel_retirada" name="sel_retirada"><option id="ini" value="INI">Selecione uma opção</option>'.$ret.'</select><span id="activity"></span>' ;
        
        return $ret;

    }

    //NOSSO NUMERO PARA BOLETO
    function nosso_numero() {
        do {
            $ultimo_id = file_get_contents('nosso_numero.count');
        } while ($ultimo_id === false);
        $ultimo_id = $ultimo_id +1 ;
        file_put_contents('nosso_numero.count',$ultimo_id,LOCK_EX) ;

        return $ultimo_id ;
    } 

    //------COMUNICACAO ---------------------
    //ENVIA EMAILS TRANSACIONAIS PARA TREINO
    function sendEmail($to,$subject,$msg) {

        $from = 'noreply@bodysystems.net';
        $body = <<<EOT
      <html>
      <p>Caro Webmaster,</p>
      <p>Existem <strong>assuntos que requerem sua atenção</strong>.<br/>
      <h3>$subject</h3>
      <pre>
        $msg
      </pre>
      <hr/>
      <small>Este email é uma comunicação automática. Não responda diretamente o mesmo</small>
      </html>
EOT;
        // To send HTML mail, the Content-type header must be set
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'From: Site Body Systems <noreplay@bodysystems.net>' . "\r\n";
        $response = mail($to,$subject,$body,$headers);


    } 

    //--------------------------------------------------------------------------------------

    $document =& JFactory::getDocument();

    $document->addStyleSheet('_ferramentas/workshop/style/token-input.css" type="text/css');
    $document->addStyleSheet('_ferramentas/workshop/style/token-input-facebook.css" type="text/css');
    $document->addStyleSheet('_ferramentas/workshop/style/wscss.css');

    $document->addScript('http://code.jquery.com/jquery-1.8.2.js');
    $document->addScript('_ferramentas/workshop/js/jquery.blockUI.js') ;
    $document->addScript('http://code.jquery.com/ui/1.9.0/jquery-ui.js') ;
    $document->addScript('_ferramentas/workshop/js/wsjs.js');
    $document->addScript('http://bodysystems.net/_ferramentas/workshop/js/jquery.tokeninput.js');
    $document->addScript('http://bodysystems.net/_ferramentas/workshop/js/profacad.js');
    $document->addScript('_ferramentas/calendario/js/jquery.meio.mask.js');



    $template_main = file_get_contents('_ferramentas/workshop/pages/checkout.html') ;

    //FAZ AS ALTERÇÕES RELATIVAS AO INTERVALO
    switch(true) {
              
        Case ($correio == 1):
            $paga_ws = false;
            $mostra_ws = 'none';
            $encerrado = false;
            $pagamento = true;
            $retirar_evento = false;
            $retirar_plantao = false;
            $retirar_correio = true;
            $pato_cartao = true;
            $pagto_boleto = true;
            break;
            
        Case ($intervalo <= 0):
            $paga_ws = false;
            $mostra_ws =  'none';
            $encerrado = true;
            $pagamento = false;
            $retirar_evento = false;
            $retirar_plantao = false;
            $retirar_correio = false;
            $pato_cartao = false;
            $pagto_boleto = false;
            break;   
        /*Case ($intervalo < $dead_line) && ($intervalo > 0):
            $paga_ws = false;
            $mostra_ws = 'none';
            $encerrado = false;
            $pagamento = true;
            $retirar_evento = false;
            $retirar_plantao = false;
            $retirar_correio = true;
            $pato_cartao = true;
            $pagto_boleto = true;
            break;    */
        //Case ($intervalo >= $dead_line):
        Case ($intervalo > 0):
            $paga_ws = ($plantao==1)? false:true;
            $mostra_ws = ($plantao==1)? 'none': 'none';
            $encerrado = false;
            $pagamento = true;
            $retirar_evento = ($plantao==1)? false:true;
            $retirar_plantao = ($plantao==1)? true: false;
            $retirar_correio = true;
            $pato_cartao = true;
            $pagto_boleto = true;
            break;

    }

   
  
  //=================================================================================
  //DATAS DE BONUS PARA O WORKSHOP
   
   //hoje
    $today = date('Y-m-d') ; 
    //bonus máximo
    //$bonus3 = date('Y-m-d',strtotime('2013-05-13')) ;  
    //bonus
    $bonus2 = date('Y-m-d',strtotime('2014-05-12')) ;
    //valor normal
    $bonus1 = date('Y-m-d',strtotime('2014-07-07')) ;
    //juros
    $bonus0 = date('Y-m-d',strtotime('2014-07-07')) ;
    
 //=====NÃO EDITAR ABAIXO ===========================================================   
    /*
    if(diffDate($today,$bonus3)>=0) {
        $nivel_bonus = 3;
    } else 
    */
    $mDif = diffDate($today,$bonus2,'D','-');
    file_put_contents('_ferramentas/workshop/nivel_bonus.log',$mDif) ;
    if(diffDate($today,$bonus2,'D','-')>=0) {
        $nivel_bonus = 2;
    } else if(diffDate($today,$bonus1)>=0) {
        $nivel_bonus = 1;
    } else {
        $nivel_bonus = 0;
    }
    
  //==================================================================================

    //GERA O ID DA TRANSACAO
    $transacaoid = uuid();
    //FORMAS DE ENVIO/RETIRADA
    $remessa = getRetirada($retirar_evento,$retirar_plantao,$retirar_correio,$intervalo) ; 

    $vars = array(
        '{REMESSA_KIT_DIDATICO}' => $remessa,
        '{MOSTRA_WS}' => $mostra_ws,
        '{VALOR_REF_WS}' => $valor_ref_ws,
        '{EVENTO}' => $nome_evento,
        '{LOCAL}' => $endereco,
        '{DATA}' => $datas,
        '{UUID}' => $transacaoid,
        '{SIGLA}' => $sigla,
        '{EVENTO_NOME}' => $nome_evento,
        '{EVENTO_ID}' => $eventoid,
        '{NIVEL_BONUS}' => $nivel_bonus,
        '{VALOR_WS}' => 'R$'.$valor_ws,
        '{AVISO}'    => $aviso,
        '{NOME_EVENTO}' => $nome_evento      //.' '.$dtm1
    );

    //==============================================================
    header("Expires: Mon, 26 Jul 12012 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    //==============================================================
    
    echo template_eval($template_main,$vars);


?>
