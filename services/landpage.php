<?php

    $document =& JFactory::getDocument();
    $document->addStyleSheet('_ferramentas/workshop/style/receipt.css') ;

    //------------------------------
    // PAGINA PARA COMPARTILHAMENTO NO FACEBOOK
    $pagina = "http://bodysystems.net/noticias/165-3%C2%BA-workshop-2013-o-poder-do-exemplo";
    //--------------------------------------------------------------------------------------------
    // NÃO ALTERE AQUI
    $ws_url = "https://www.facebook.com/sharer/sharer.php?u=".urlencode($pagina);

    //------------------------------

    function template_eval(&$template, &$vars) { return strtr($template, $vars); }

    //------------------------------

    function getDadosEventos($transID) {


        $db = &JFactory::getDBO();
        $query = "SELECT produto_descricao,cnab, remessa_logradouro,remessa_numero,remessa_complemento,remessa_bairro,remessa_cidade,remessa_uf,remessa_cep,
        (SELECT name FROM wow_users WHERE id = userid) AS cliente, (SELECT email FROM wow_users WHERE id = userid) AS email FROM MercadoPagoTransacoes WHERE transacaoid LIKE ".$db->Quote($transID) ;


        $db->setQuery($query);
        $result = $db->loadObjectList();
        if($db->ErrorNum()>0){
            file_put_contents('_ferramentas/mp/landpage.log','['.date('Y-m-d H:i:s').'] DATABASE: '.$db->ErrorMsg()."\n",FILE_APPEND);
        } 

        $evento = $result[0]->produto_descricao;
        $cnab = $result[0]->cnab;
        $remessa = $result[0]->remessa_logradouro.','.$result[0]->remessa_numero.' '.$result[0]->remessa_complemento.
        '<br/>'.$result[0]->remessa_bairro.' - '.$result[0]->remessa_cidade.' / '.$result[0]->remessa_uf.'<br/>CEP '.$result[0]->remessa_cep;
        $Evento_array = explode('|',$cnab) ;
        $id_evento = substr($Evento_array[0],1);
        $envio = $Evento_array[3];
        $email = $result[0]->email;
        $cliente = $result[0]->cliente;

        //coleta dados do evento 
        $client = new SoapClient( 
            "http://177.154.134.90:8084/WCF/_BS/wcfBS.svc?wsdl" , array('cache_wsdl' => 0)
        ); 

        $params = array(
            'IdClienteW12' => 229 ,
            'IdTreinamento' => $id_evento
        );

        $webService = $client->ListarTreinamentoID($params) ;
        $evo_result = $webService->ListarTreinamentoIDResult;


        $endereco = $evo_result->ENDERECO;
        $evento = $evo_result->CIDADE." ".$evo_result->ESTADO." - ".date('d/M',strtotime($evo_result->INICIO)) ;
        $dia = date('d',strtotime($evo_result->INICIO));
        $mes = date('m',strtotime($evo_result->INICIO));
        $ano = date('Y',strtotime($evo_result->INICIO));

        switch($mes) {
            Case '1': $m = 'janeiro' ;break;
            Case '2': $m = 'fevereiro' ;break;
            Case '3': $m = 'março' ;break;
            Case '4': $m = 'abril' ;break;
            Case '5': $m = 'maio' ;break;
            Case '6': $m = 'junho' ;break;
            Case '7': $m = 'julho' ;break;
            Case '8': $m = 'agosto' ;break;
            Case '9': $m = 'setembro' ;break;
            Case '10': $m = 'outubro' ;break;
            Case '11': $m = 'novembro' ;break;
            Case '12': $m = 'dezembro' ;break;
        }
        $ext_d = $dia.' de '.$m.' de '.$ano;

        $ret = array (
            'cliente' => $cliente,
            'email' => $email,
            'endereco' => $endereco,
            'evento' => $evento,
            'dia' => $dia,
            'mes' => $m,
            'envio' => $envio,
            'ext_date' => $ext_d,
            'remessa' => $remessa
        );

        return $ret;
        

    }

    function qrcode($url){
        if($url){
            $size = 150 ;
            return "http://chart.apis.google.com/chart?cht=qr&chl=".$url."&chs=".$size."x".$size."";
        }
    }


    function trans_email($to,$message) {
        $headers  = "From: Body Systems<noreply@bodysystems.net>\r\n"; 
        $headers .= "Reply-To: noreply@bodysystems.net\r\n"; 
        $headers .= "Return-Path: noreply@bodysystems.net\r\n"; 
        $headers .= "X-Mailer: PHP\n"; 
        $headers .= 'MIME-Version: 1.0' . "\n"; 
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n"; 

        $additionalParameters = '-ODeliveryMode=d'; 
        $subject = "WORKSHOP BodySystems";
        
        $ret = mail($to, $subject, $message, $headers, $additionalParameters);
        if($ret == false) {
            file_put_contents('_ferramentas/logs/email_error.log','['.date('Y-m-d H:i:s').'] to:'.$to."\n-->".$headers."\n",FILE_APPEND);
        }

    }
    
    function create_msg($status) {
        if($status=='approved') {
            $msg  = file_get_contents('_ferramentas/workshop/pages/approved_email.html');
        } else if($status == 'pending') {
            $msg  = file_get_contents('_ferramentas/workshop/pages/pending_email.html');
        } else {    //fail
           $msg  = file_get_contents('_ferramentas/workshop/pages/fail_email.html'); 
        }
        
        return $msg;
    }
    
    
    $template = file_get_contents('_ferramentas/workshop/pages/landpage.html');
    $show_code = (isset($_GET['noqrcode']))? 'none':'block';
    $class_status = $_GET['collection_status'] ;
    $transacaoid = $_GET['external_reference'];

    $url= urlencode("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'&noqrcode');
    $qrCode = qrcode($url) ;

    $ws = getDadosEventos($transacaoid) ;
    file_put_contents('_ferramentas/workshop/dados_banco.log', print_r($ws,true)."\n",FILE_APPEND);

    if($ws['envio'] =='CO') {
        $texto = 'Agora conforme sua escolha, aguarde o envio de seu Kit Didático.' ;
        $texto_lembrese = 'Lembre-se!<br/>O despacho de seu material será efetuado pelos correios após a data de encerramento desta rodada de Workshops. Ele será enviado para o endereço determinado por você.' ;
    } else {
        $texto = 'Seu Kit Didático ficará disponível para retirada durante o evento que você se inscreveu.' ;
        $texto_lembrese = 'Lembre-se!<br/>Você deverá retirar seu material didático no evento selecionado. Então anote com cuidado a date e horário!' ;
    }


    if($class_status=='approved') {
        $head1 = "LICENCIAMENTO EFETUADO COM SUCESSO";
        $head2 = $texto;
        $card_day = $ws['dia'];
        $card_month = $ws['mes'];
        $extend_date = $ws['ext_date'] ;
        $evento = $ws['evento'];
        $email = $ws['email'] ;
        
        //$local = ;
        $endereco_evento = $ws['endereco'] ;
        $transacaoid =$transacaoid ;
        $lembre_se =  $texto_lembrese ;
        $remessa = $ws['remessa'] ;
        $show_fb = 'block' ;
        $fb_share = $ws_url;


    } else if($class_status == 'pending') {
        $head1 = "AGORA AGUARDAMOS A CONFIRMAÇÃO DE SEU PAGAMENTO";
        $head2 = '';
        $card_day = $ws['dia'];
        $card_month = $ws['mes'];
        $extend_date = $ws['ext_date'] ;
        $evento = $ws['evento'];
        //$local = ;
        $endereco_evento = $ws['endereco'] ;
        $transacaoid =$transacaoid ;
        $lembre_se =  $texto_lembrese ;
        $remessa = $ws['remessa'] ;
        $show_fb = 'block' ;
        $fb_share = $ws_url;

    } else if($class_status == 'fail') {
        $head1 = "OCORREU UM PROBLEMA COM SEU PAGAMENTO";
        $head2 = 'Por favor entre em contato conosco para que possamos lhe ajudar a entender o que aconteceu.';
        $card_day = $ws['dia'];
        $card_month = $ws['mes'];
        $extend_date = $ws['ext_date'] ;
        $evento = $ws['evento'];
        //$local = ;
        $endereco_evento = $ws['endereco'] ;
        $transacaoid =$transacaoid ;
        $lembre_se =  '';
        $remessa = '' ;
        $show_fb = 'none' ;

    } else {
        $class = 'alert' ;
        $head1 = 'O PAGAMENTO FOI NÃO CONCLUÍDO';
        $head2 = 'Sua transação não pode ser processada. Caso você tenha alguma dúvida quanto a realização de sua inscrição, por gentileza, entre em contato conosco para que possamos lhe auxiliar a entender o ocorrido.';
        $card_day = $ws['dia'];
        $card_month = $ws['mes'];
        $extend_date = $ws['ext_date'] ;
        $evento = $ws['evento'];
        $show_fb = 'none' ;

    }

    $params = array ( 
        '{HEAD1}' => $head1,
        '{HEAD2}' => $head2,
        '{CDY}' => $card_day,
        '{CMONTH}' => $card_month,
        '{EXTEND-DATE}' => $extend_date,
        '{EVENTO}' => $evento,
        '{LOCAL}' => $local,
        '{ENDERECO}' => $endereco_evento,
        '{TRANSACAOID}' => $transacaoid,
        '{LEMBRE-SE}' => $lembre_se,
        '{REMESSA}' => $remessa,
        '{CODE}' => $qrCode,
        '{SHOW_CODE}' => $show_code,
        '{SHOW_FB}' => $show_fb,
        '{FB_LINK}' => $fb_share
    );
    
     $email_vars = array(
        '{TRANSACAOID}' => $transacaoid,
        '{EVENTO}' => $evento,
        '{DATA_EVENTO}' => $extend_date,
        '{CLIENTE}' => $cliente
    );

    //envia email
    $msg = create_msg($class_status) ;
    $msg_t = template_eval($msg,$email_vars) ;
    trans_email($email,$msg_t) ;
    
    if($class_status=='null') {
        //DROPOUT - REMOVE A TRANSACAO DA BASE DE DADOS
        $db = &JFactory::getDBO();
        $query = "DELETE FROM MercadoPagoTransacoes WHERE transacaoid LIKE ".$db->Quote($transacaoid);
        $db->setQuery($query) ;
        $db->Query();
    }
    
    
    echo template_eval($template,$params) ;

?>
