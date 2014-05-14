<?php
    include('../../../exec_in_joomla.inc') ;

    //DADOS PROVENIENTES DO FORMULARIO PRINCIPAL
    $transacaoid = $_POST['h_transacao'];
    $progs = $_POST['h_progs'];
    $valor_por_programa = $_POST['h_valor_por_programa'];
    $valor_boleto = $_POST['h_valor_boleto'];
    $valor_cartao = $_POST['h_valor_cartao'];
    $valor_ws = $_POST['h_valor_ws'];
    $valor_cobrado = $_POST['h_valor_cobrado'];
    $retirada_kit = $_POST['h_retirada_kit'];
    $nivel_bonus = $_POST['h_nivel_bonus'];
    $valor_frete = $_POST['h_valor_frete'];
    $formaPagto = $_POST['h_formaPagto'];
    $cpf = $_POST['h_cpf'];
    $siteid = $_POST['h_siteid'];
    $evoid = $_POST['h_evoid'];
    $email = $_POST['h_email'];
    $nome = $_POST['h_nome'];
    $promocode = $_POST['h_promocode'];
    $logradouro = $_POST['h_logradouro'];
    $numero = $_POST['h_numero'];
    $compl = $_POST['h_complemento']; 
    $bairro = $_POST['h_bairro'];
    $cidade = $_POST['h_cidade'];
    $estado = $_POST['h_uf'];
    $cep = $_POST['h_cep'];
    $evento = $_POST['h_evento_descricao'] ;
    $eventoid = $_POST['h_evento_id'] ;
    $cnab = $_POST['h_cnab'] ;

    $endereco = $logradouro.','.$numero.' '.$compl.' - '.$bairro.' '.$cidade.' / '.$estado.' - CEP '.$cep ;
    $promocode = 'np';

    //ELIMINA PONTOS E TRAÇOS
    $cpf = str_replace('-','',str_replace('.','',$cpf));
    $cep = str_replace('-','',str_replace('.','',$cep));
    $nascimento = str_replace('/','-',$nascimento) ;
    $nascimento = date('Y-m-d',strtotime($nascimento)) ;
    $fone = str_replace('-','',$fone);
    $celular = str_replace('-','',$celular);


    //---------UTILITARIOS-------------------------------

    function template_eval(&$template, &$vars){
        return strtr($template, $vars);
    }



    //------BOLETOS ---------------------  
    //CRIA O NOSSO NÚMERO PARA BOLETO	
    function nosso_numero() {

        do {
            $ultimo_id = file_get_contents('../../calendario/services/nosso_numero.count');
        } while ($ultimo_id === false);
        $ultimo_id = $ultimo_id +1 ;
        file_put_contents('../../calendario/services/nosso_numero.count',$ultimo_id,LOCK_EX) ;

        return $ultimo_id ;
    } 


    //INSERE O BOLETO NA TABELA BOLETOS_BS 
    function inserirBoletoBs($evento,$tipo_evento,$cnab,$evoid,$siteid,$nome,$email,$endereco,$promocode,$data_documento,$data_vencimento,$nnum,$docnum,$linha,$gvalor,$gfrete,$nivel_bonus,$transacaoid) {
		
		$codigoBanco = substr($linha,0,3);
        //REGISTRA O IP DO USUÁRIO

        if ( isset($_SERVER["REMOTE_ADDR"]) )    { 

            $ip=$_SERVER["REMOTE_ADDR"] . ' '; 

        } else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) )    { 

            $ip=$_SERVER["HTTP_X_FORWARDED_FOR"] . ' '; 

        } else if ( isset($_SERVER["HTTP_CLIENT_IP"]) )    { 

            $ip=$_SERVER["HTTP_CLIENT_IP"] . ' '; 

        } 


        try {

            $db = &JFactory::getDBO() ;

            if($gvalor!=0 && $evoid!=0 && $cnab!='') {

                $query = "INSERT INTO boletos_bs (id,evento,tipo_evento,cnab,idcliente,userid,nome_evo,user_email,endereco_remessa,promo_code,data_geracao,data_vencimento,codigoBanco,nosso_numero,doc_numero,linha_digitavel,valor_cobrado,valor_frete,nivel_bonus,ip,compensado,data_compensado,transacaoid) VALUES ('null', '$evento','$tipo_evento', '$cnab','$evoid','$siteid','$nome','$email','$endereco','$promocode','$data_documento','$data_vencimento','$codigoBanco','$nnum','$docnum','$linha','$gvalor','$gfrete','$nivel_bonus','$ip','false','0000-00-00','$transacaoid')" ;
                $db->setQuery($query) ;
                $db->Query();

                if ($db->getErrorNum()) {
                    $ret = array(false,$db->getErrorMsg());
                    $thefile =  'erros_boletos_site.log' ;
                    $msg = "[".date("Y-m-d | H:i:s", time()) .'] - '.$db->getErrorMsg()."\n";
                    file_put_contents ($thefile, $msg, FILE_APPEND | LOCK_EX);
                }  else { 
                    $ret = array(true,'Boleto inserido na base com sucesso.') ;
                }  

            } else {
                $ret = array(false,'Existe algum problema com o valor do boleto.');
            }

            return $ret ;

        } catch (Exception $e) {
            $msg = "[".date("Y-m-d | H:i:s", time()) .'] - '.$e->getMessage()." - Origem -> IdCliente:".$evoid." - SiteID:".$siteid." - NNum:".$nnum." - CNAB:".$cnab."\r\n";
            $thefile =  'erros_boletos_site.log' ;
            file_put_contents ($thefile, $msg, FILE_APPEND | LOCK_EX);
            $ret = array(false,$e->getMessage()) ;
            return $ret ;

        } 

    } 

    //INSERE O BOLETO NA TABELA DO EVO
    function inserirBoletosEvo($evento,$cnab,$evoid,$promocode,$data_documento,$data_vencimento,$nnum,$docnum,$gvalor,$gfrete,$endereco){

        $client = new SoapClient( 
            "http://177.154.134.90:8084/WCF/_BS/wcfBS.svc?wsdl" , array('cache_wsdl' => 0)
        ); 

        $params = array(

            'Evento'=>$evento,
            'CNAB'=>$cnab,
            'IdCliente'=>$evoid,
            'CodigoPromotor'=>$promocode,
            'DataGeracao'=> date('Ymd His', strtotime($data_documento)),
            'DataVencimento' => date('Ymd His', strtotime($data_vencimento)),
            'NossoNumero' =>$nnum,
            'DocNumero' =>$docnum,
            'ValorCobrado' =>$gvalor,
            'ValorFrete' =>$gfrete,
            'Endereco' =>$endereco

        ); 

        $parametros = print_r($params,true) ;
        $thefile =  'parametros.log' ;
        file_put_contents ($thefile, "[".date("Y-m-d | H:i:s", time()) .'] - '."\n".$parametros."\n", FILE_APPEND | LOCK_EX);

        try {
            $webService = $client->InserirBoleto($params); 
            $result = $webService->InserirBoletoResult ;

            if($result==0){
                $ret = array(true,'Boleto Inserido com Sucesso');
            } else {
                $ret = array(false,$result) ;
                $thefile =  'erros_boletos_evo.log' ;
                file_put_contents ($thefile, "[".date("Y-m-d | H:i:s", time()) .'] - '.$ret[1]."\n", FILE_APPEND | LOCK_EX);
            }

            return $ret ;

        } catch (Exception $e) {
            $msg = "[".date("Y-m-d | H:i:s", time()) .'] - '.$e->getMessage()." - Origem -> IdCliente:".$evo_id." - SiteID:".$siteid." - NNum:".$nnum." - CNAB:".$cnab."\r\n";
            $thefile =  'erros_boletos_evo.log' ;
            file_put_contents ($thefile, $msg, FILE_APPEND | LOCK_EX);
            $ret = array(false,$e->getMessage()) ;
            return $ret ;

        } 

    }


    //------CUPONS --------------------
    //LANCA PROFESSOR NA LISTA DE TREINO
    function lancarCupom($transacaoid,$evoid,$Item_desc,$code,$evento_id) {

        //CHAMA O SERVIÇO DO EVO

        $params = array( 
            'IdClienteW12' => 229,
            'IdTransacao'=> $transacaoid,
            'IdCliente'=> $evoid,
            'NomeProduto'=> $Item_desc.' '.$code ,
            'Descricao'=> $evento_id,
            'Valor'=> 0,
            'FreteValor'=> 0,
        );

        try {

            $client = new SoapClient("http://177.154.134.90:8084/WCF/_BS/wcfBS.svc?wsdl" ,array('cache_wsdl'=>WSDL_CACHE_NONE)); 
            $webService = $client->TratarEvento($params); 
            $result = $webService->TratarEventoResult ;

            $thefile = 'parametros_cupom_evo.log' ;
            $msg = date('Y-m-d H:i:s')."\n" ;
            $msg .= print_r($params,true) ;
            file_put_contents ($thefile, $msg, FILE_APPEND | LOCK_EX);
            $result = substr($result,0,1);
            if($result== '0'){
                $ret = array(true,'Boleto Inserido com Sucesso');

            } else {
                $ret = array(false,$result) ;
                $thefile =  'erros_envio_lista_presenca_treinamento.log' ;
                $msg = date('Y-m-d H:i:s').' ----> TRANSACAO ID:'.$transacaoid.' | ID_CLIENTE:'.$evoid.' | MENSAGEM EVO:'.$result ;
                file_put_contents ($thefile, $msg, FILE_APPEND | LOCK_EX);
            }


        } catch (Exception $e) {
            $msg =  time().' - '.$e->getMessage();
            $thefile =  'erros_envio_lista_presenca_treinamento.log' ;
            //$msg = date('Y-m-d H:i:s').' ----> TRANSACAO ID:'.$transacaoid.' | ID_CLIENTE:'.$evoid.' | MENSAGEM EVO:'.$webService ;
            file_put_contents ($thefile, $msg, FILE_APPEND | LOCK_EX);
            $ret = array(false,$msg) ;
        }    
        return $ret ;
    }

    //BAIXA CUPOM DA TABELA CUPONS
    function baixarCupom($cupom,$destino,$siteid,$decricao) {
        $db = &JFactory::getDBO();
        //EXECUTA O PAGAMENTO COM O CUPOM
        $vDestino = $destino;
        $Item_desc = $descricao;
        $query="UPDATE cupons SET beneficiario=".$siteid.", 
        destino=".$db->Quote($vDestino).", utilizado=True,data_utilizado=now() WHERE cupon=".$db->Quote($cupom) ;

        try {
            $db->setQuery($query);
            $db->Query();

            $affected_row = $db->getAffectedRows();

            if($affected_row > 0 && $db->getErrorNum()==0){
                $ret = array(true,'Cupom baixado com Sucesso');
            } else {
                $ret = array(false, 'Erro baixando cupom') ;
                $dbError = $db->getErrorMsg()."\n".$query."\n\n";
                $thefile = 'erros_baixa_cupom.log' ;
                file_put_contents($thefile,$dbError);

            }
            return $ret ;

        } catch(Exception $e) {
            $ret = array(false,$e->getMessage()) ;
            return $ret ;
        }


    }



    //------PAGTO PAGSEGURO-------------
    //CRIA PAGAMENTO PARA PAGSEGURO
    function setPagSeg($evoid,$siteid,$eventoid,$descricao,$promocode,$valor,$frete,$nome,$email) {
        $template = <<<EOT
		<form id="pag_1" name="pag_1" target="_blank" method="post" action="https://pagseguro.uol.com.br/checkout/checkout.jhtml">
			  <input type="image" name="submit" src="http://bodysystems.net/images/pagseguro_btn.png" alt="Pagar com Cartão" />  
			  <input type="hidden" name="email_cobranca" value="workshop2@bodysystems.net" />
			  <input type="hidden" name="evoid" value="{EVO_ID}" />
			  <input type="hidden" name="siteid" value="{SITE_ID}" />
			  <input type="hidden" name="tipo" value="CP" />
			  <input type="hidden" name="moeda" value="BRL" />
			  <input type="hidden" name="item_id_1" value="{ID_TREINO}" />
			  <input type="hidden" name="item_descr_1" value="{EVENTO}" />
			  <input type="hidden" name="ref_transacao" value="{PROMOCODE};{EVO_ID};{SITE_ID};{VALOR_FRETE}" />
			  <input type="hidden" name="item_quant_1" value="1" />
			  <input type="hidden" name="anotacao" value="" />
			  <input type="hidden" name="item_valor_1" value="{VALOR}" />
			  <input type="hidden" name="tipo_frete" value="SD"  />
			  <input type="hidden" name="nome" value="{NOME}"  />
			  <input type="hidden" name="email" value="{EMAIL}"  />
			  <!-- <input type="hidden" name="item_peso_1" value="300" /> -->
			  <input type="hidden" name="item_peso_1" value="0" />
			  <input type="hidden" name="encoding" value="UTF-8" />
	   </form>

EOT;

        $params = array(
            '{EVO_ID}' => $evoid,
            '{SITE_ID}' => $siteid,
            '{ID_TREINO}'=> $eventoid,
            '{EVENTO}' => $descricao,
            '{PROMOCODE}' => $promocode,
            '{VALOR}'=> $valor*100, //PRECISA REMOVER O PONTO DECIMAL E 
            '{VALOR_FRETE}' => $frete,
            '{NOME}'=> $nome,
            '{EMAIL}' => $email

        );

        $botao = template_eval($template,$params) ;
        return $botao;
    }

    //------PAGTO BOLETO ---------------------
    //CRIA PAGAMENTO VIA BOLETOS
    function ProcBoleto($nnum,$data_documento,$data_vencimento,$valor_cobrado,$nome,$endereco,$cpf,$i1,$i2,$i3,$i4,$i5) {

        $valor_cobrado = str_replace(',','.', $valor_cobrado) ;

        $codigobanco = '341'; // O Itau sempre será este número
        $agencia = '0350'; // 4 posições
        $conta = '37578';  // 5 posições sem dígito
        $carteira = '175'; // A sem registro é 175 para o Itaú
        $moeda = '9'; // Sempre será 9 pois deve ser em Real
        $nossonumero = $nnum; // Número de controle do Emissor (pode usar qq número de até 8 digitos);
        $data = $data_documento;  //'05/03/2005'; // Data de emissão do boleto
        $vencimento = $data_vencimento;   //'05/03/2006'; // Data no formato dd/mm/yyyy
        $valor = $valor_cobrado; // Colocar PONTO no formato REAIS.CENTAVOS (ex: 666.01)

        // NOS CAMPOS ABAIXO, PREENCHER EM MAIÚSCULAS E DESPREZAR ACENTUAÇÃO, CEDILHAS E
        // CARACTERES ESPECIAIS (REGRAS DO BANCO)

        $cedente = 'BODY SYSTEMS LTDA.';

        $sacado = $nome;
        $endereco_sacado = $endereco;
        //$cidade = 'UBERLANDIA';
        //$estado = 'MG';
        //$cep = '38400-000';
        $cpf_cnpj = $cpf;
        $instrucoes1 = $i1;
        $instrucoes2 = $i2;
        $instrucoes3 = $i3;
        $instrucoes4 = $i4;
        $instrucoes5 = $i5;

        // FIM DA ÁREA DE CONFIGURAÇÃO

        function Modulo11($valor) {
            $multiplicador = '4329876543298765432987654329876543298765432';
            for ($i = 0; $i<=42; $i++ ) {
                $parcial = $valor[$i] * $multiplicador[$i];
                $total += $parcial;
            }
            $resultado = 11-($total%11);
            if (($resultado >= 10)||($resultado == 0)) {
                $resultado = 1;
            }

            return $resultado;
        }


        function calculaDAC ($CalculaDAC) {
            $tamanho = strlen($CalculaDAC);
            for ($i = $tamanho-1; $i>=0; $i--) {
                if ($multiplicador !== 2) {
                    $multiplicador = 2;
                }
                else {
                    $multiplicador = 1;
                }
                $parcial = strval($CalculaDAC[$i] * $multiplicador);

                if ($parcial >= 10) {
                    $parcial = $parcial[0] + $parcial[1];
                }
                $total += $parcial;
            }
            $total = 10-($total%10);
            if ($total >= 10) {
                $total = 0;
            }
            return $total;
        }

        function calculaValor ($valor) {
            $valor = str_replace('.','',$valor);
            return str_repeat('0',(10-strlen($valor))).$valor;
        }

        function calculaNossoNumero ($valor) {
            return str_repeat('0',(8-strlen($valor))).$valor;
        }

        function calculaFatorVencimento ($dia,$mes,$ano) {
            $vencimento = mktime(0,0,0,$mes,$dia,$ano)-mktime(0,0,0,7,3,2000);
            return ceil(($vencimento/86400)+1000);
        }

        // CALCULO DO CODIGO DE BARRAS (SEM O DAC VERIFICADOR)
        $codigo_barras = $codigobanco.$moeda.calculaFatorVencimento(substr($vencimento,0,2),substr($vencimento,3,2),substr($vencimento,6,4));
        $codigo_barras .= calculaValor($valor).$carteira.calculaNossoNumero($nossonumero).calculaDAC($agencia.$conta.$carteira.calculaNossoNumero($nossonumero)).$agencia.$conta.calculaDAC($agencia.$conta).'000';



        // CALCULO DA LINHA DIGITÁVEL
        $parte1 = $codigobanco.$moeda.substr($carteira,0,1).substr($carteira,1,2).substr(calculaNossoNumero($nossonumero),0,2);
        $parte1 = substr($parte1,0,5).'.'.substr($parte1,5,4).calculaDAC($parte1);

        $parte2 = substr(calculaNossoNumero($nossonumero),2,5).substr(calculaNossoNumero($nossonumero),7,1).calculaDAC($agencia.$conta.$carteira.calculaNossoNumero($nossonumero)).substr($agencia,0,3);
        $parte2 = substr($parte2,0,5).'.'.substr($parte2,5,5).calculaDAC($parte2);

        $parte3 = substr($agencia,3,1).$conta.calculaDAC($agencia.$conta).'000';
        $parte3 = substr($parte3,0,5).'.'.substr($parte3,5,8).calculaDAC($parte3);

        $parte5 = calculaFatorVencimento(substr($vencimento,0,2),substr($vencimento,3,2),substr($vencimento,6,4)).calculaValor($valor);

        $numero_boleto = $parte1.' '.$parte2.' '.$parte3.' '.Modulo11($codigo_barras).' '.$parte5;

        // INSERÇÃO DO DAC NO CODIGO DE BARRAS

        $codigo_barras = substr($codigo_barras,0,4).Modulo11($codigo_barras).substr($codigo_barras,4,43);
        $m_codigo_barras = Modulo11($codigo_barras);
        //   print Modulo11($codigo_barras);
        //   exit;

        $ret = array($numero_boleto,$m_codigo_barras) ;
        return $ret ;

    }

    function setBoleto($cnab,$nnum,$data_documento,$data_vencimento,$valor_cobrado,$sac,$endereco,$cpf,$evento,$transacaoid) {
        $template = <<<EOT
		 <form id="form_boleto" target="_blank" name="form_boleto" method="post" action="http://bodysystems.net/_ferramentas/workshop/services/boletos_ws.php">
			  <input type="image" name="submit" src="http://bodysystems.net/images/bt_boletos.png" alt="Pagar com Boleto" />
			  <input type="hidden" name="nnum" value="{NNUM}" />
			  <input type="hidden" name="data_documento" value="{DATA_DOCUMENTO}" />
			  <input type="hidden" name="data_vencimento" value="{DATA_VENCIMENTO}" />
			  <input type="hidden" name="cnab" value="Uso interno BS: {CNAB}" />
			  <input type="hidden" name="valor_cobrado" value="{VALOR_COBRADO}" />
			  <input type="hidden" name="sac" value="{SAC}" />
			  <input type="hidden" name="endereco" value="{ENDERECO}" />
			  <input type="hidden" name="cpf" value="{CPF}" />
			  <input type="hidden" name="evento" value="{EVENTO}" />
			  <input type="hidden" name="transacaoid" value="{TRANSACAOID}" />
		 </form>

EOT;
        $endereco = utf8_decode($endereco) ;
        $params = array(
            '{CNAB}' => $cnab,
            '{NNUM}' => $nnum,
            '{DATA_DOCUMENTO}' => $data_documento,
            '{DATA_VENCIMENTO}'=> $data_vencimento,
            '{EVENTO}' => $evento,
            '{ENDERECO}' => $endereco,
            '{CPF}' => $cpf,
            '{VALOR_COBRADO}'=> $valor_cobrado,
            '{SAC}'=> $sac,
            '{TRANSACAOID}' => $transacaoid,
        );

        $botao = template_eval($template,$params) ;
        return $botao;
    }

    //-----PAGTO MERCADO PAGO -----------------
    function setMP($transacaoid,$userid,$evoid,$cpf_cnpj,$remessa_cep,$remessa_logradouro,$remessa_numero,$remessa_complemento,$remessa_bairro,$remessa_cidade,$remesssa_uf,$remessa_tipo,$cnab,$produto_descricao,$valor_transacao,$valor_frete,$codigo_promotor,$nsa){

        $logo = 'http://bodysystems.net/_ferramentas/workshop/images/logo_ws.gif';
         require_once("../../mp/lib/mercadopago.php");
   
        //CONTA WORKSHOP
        $mp = new MP("5881367263919648", "xp2ZkzmI5Dl2mtRQqCIcLU60P47t6dUr");
        $mp->sandbox_mode(FALSE);

            //CRIA A SAIDA DO MERCADO PAGO
            try {

                $preference = array(
                    'items'=> 
                    array(array(
                        'id'=> '232',//$cnab,
                        'title'=> $produto_descricao,
                        'description'=> $cnab,
                        'quantity'=> 1,
                        'unit_price'=> $valor_transacao,
                        'currency_id'=> 'BRL',
                        'picture_url'=> $logo
                    )),
                    'external_reference'=> $transacaoid,
                    'payer'=> 
                    array(
                        'name'=> $nome,
                        'surname'=> $sobrenome,
                        'email'=> $email,
                    ),
                    'shipments' => array( 
                        'receiver_address' => array(
                            'zip_code'=> $remessa_cep,
                            'street_number'=> $remessa_numero
                    )),
                    'back_urls'=> 
                    array(
                        'success'=> 'http://bodysystems.net/final-mp?success',
                        'failure'=> 'http://bodysystems.net/final-mp?fail',
                        'pending'=> 'http://bodysystems.net/final-mp?pending'
                    ),
                    'payment_methods'=> 
                    array(
                        'excluded_payment_methods'=>array(array( 

                            'id'=> 'bolbradesco'

                            )
                        ),'excluded_payment_types'=>array(array( 

                            'id'=> 'ticket'
                            )
                        ),'installments'=> 12
                    )
                );

                $preferenceResult = $mp->create_preference($preference);
                $preference['response']['init_point'];
                $pref = $preferenceResult['response']['init_point'];
                $data_criado = date('Y-m-d H:i:s');
                
                 //INSERE AS INFORMAÇÕES NO DATABASE
                $query = "INSERT INTO MercadoPagoTransacoes (transacaoid,userid,evoid,cpf_cnpj,remessa_cep,remessa_logradouro,
                remessa_numero,remessa_complemento,remessa_bairro,remessa_cidade,remessa_uf,remessa_tipo,cnab,produto_descricao,
                valor_transacao,valor_frete,codigo_promotor,nsa,prefs,data_criado) VALUES('$transacaoid','$userid','$evoid','$cpf_cnpj','$remessa_cep','$remessa_logradouro',
                '$remessa_numero','$remessa_complemento','$remessa_bairro','$remessa_cidade','$remesssa_uf','$remessa_tipo','$cnab','$produto_descricao',
                '$valor_transacao','$valor_frete','$codigo_promotor','$nsa','$pref','$data_criado')";

                $db = &JFactory::getDBO();
                $db->setQuery($query) ;
                $db->Query();
                
                if($db->getErrorNum()>0) {
                        return '<p class="alert">Ocorreu um erro registrando sua operação</p><pre>'.$db->getErrorMsg().'</pre>' ;
                        file_put_contents('_ferramentas/workshop/services/ErrosMP.log','['.date('Y-m-d').'] '.$db->getErrorMsg()."\n",FILE_APPEND);

                } else {
                
                    $h_ref= str_replace('https:','',$preferenceResult['response']['init_point']);
                    //echo $bt_template;
                    $btn = "<a href='". $h_ref."' ><img src='http://bodysystems.net/_ferramentas/workshop/images/bt_mercadopago.png' /></a>";
                    return $btn;
                
                }

            } catch(Exception $e) {

                return $e->getMessage()."<br/><pre>".print_r($preference,true)."</pre>";
            }
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

    function calcTotal($valor_ws,$valor_cartao,$valor_boleto, $valor_frete,$forma_pagto) {

        $cobrar = ($forma_pagto=='boleto')? $valor_boleto : $valor_cartao ;
        $total = $cobrar + $valor_ws + $valor_frete ;
        return $total;

    }

    /* ======================================		
    * 	FORMAS DE PAGAMENTO 
    * ========================================
    */	

    //DETERMINA SE O PAGAMENTO FOI FEITO POR CUPOM
    if($formaPagto=='cupom') {

        //LANCA NA LISTA DE PRESENCA
        $lancadoOK = lancarCupom($transacaoid,$evoid,$evento,$cupom,$eventoid) ;
        $mArr = "LISTA DE PRESENCA EVO (LancadoOK):\n".print_r($lancadoOK,true)."\n" ;
        file_put_contents('cupom_treino.log',$mArr,FILE_APPEND);

        if(!$lancadoOK[0]) {
            $to ='debug@bodysystems.net' ;
            $subject = 'Erro cadastrando cliente na lista de Treinamento';
            $msg = 'Consulte o arquivo erros_envio_lista_presenca_treinamento.log na pasta _ferramentas/calendario para detalhes.' ;
            sendEmail($to,$subject,$msg) ;
        }

        //DA BAIXA NO CUPOM
        $baixaOK = baixarCupom($cupom,$evento,$siteid,$cnab);
        $mArr = "LISTA CUPONS SITE (BaixaOK):\n".print_r($baixaOK,true)."\n" ;
        file_put_contents('cupom_treino.log',$mArr,FILE_APPEND);

        if(!$baixaOK[0]) {
            $to ='debug@bodysystems.net' ;
            $subject = 'Erro baixando Cupom de Cliente';
            $msg = 'Consulte o arquivo erros_baixa_cupom.log na pasta _ferramentas/calendario para detalhes.' ;
            sendEmail($to,$subject,$msg) ;
        }

        if($lancadoOK[0]==false || $baixaOK[0]==false) {
            $ret = "PROBLEMAS REALIZANDO SUA INSCRIÇÃO COM CUPOM<br/>POR FAVOR ENTRE EM CONTATO COM NOSSA CENTRAL
            <br/>E INFORME SEUS DADOS E O NÚMERO DO CUPOM:<br/><h2>".$cupom."</h2>" ;
        } else {
            $ret = "<div class='cadastro' style='margin-left:20%'><h1>".$cupom."</h1><br/><p>Sua inscrição foi ativada com sucesso 
            com o cupom acima. Em caso de dúvidas entre em contato com nossa central.</p>" ;
        }

        echo $ret ;

    } 

    else if($formaPagto == 'boleto') {

        $nnum = nosso_numero() ;

        $i1 = 'NÃO RECEBER ESTE DOCUMENTO APÓS VENCIMENTO';
        $i2 = 'PAGAMENTO REFERENTE A INSCRIÇÃO' ;
        $i3 = $evento ;
        $i4 = 'NÃO SE ESQUEÇA DE INSCREVER-SE NO GROUNDWORKS!';
        $i5 = 'USO INTERNO: '.$cnab ;

        //PROCESSA OS DADOS DO BOLETO
        // $boleto[0] ==> linha digitavel
        // $boleto[1] ==> codigo de barras
        $data_documento = date('d/m/Y');
        $data_vencimento = date('d/m/Y',strtotime('+1 day'));
        $docnum = $evoid;

        $data_doc_db = date('Y-m-d');
        $data_venc_db = date('Y-m-d',strtotime('+1 day'));

        $valor_cobrado = calcTotal($valor_ws,$valor_cartao,$valor_boleto,$valor_frete,$formaPagto) ;
        $valor_cobrado = sprintf("%01.2f", $valor_cobrado);

        $boleto = ProcBoleto($nnum,$data_documento,$data_vencimento,$valor_cobrado,$nome,$endereco,$cpf,$i1,$i2,$i3,$i4,$i5) ;
        $linha = $boleto[0] ;

        //envia dados do boleto para o site (tabela boleto_bs)
        $boleto_bs = inserirBoletoBs($evento,'WS',$cnab,$evoid,$siteid,$nome,$email,$endereco,$promocode,$data_doc_db,$data_venc_db,$nnum,$docnum,$linha,$valor_cobrado,$valor_frete,'1',$transacaoid) ;

        if(!$boleto_bs[0]) {
            $to ='debug@bodysystems.net' ;
            $subject = 'Erro cadastrando boleto no Site';
            $msg = 'Consulte o arquivo erros_boletos_site.log na pasta _ferramentas/calendario para detalhes.' ;
            sendEmail($to,$subject,$msg) ;
        }

        //envia dados do boleto para o evo
        $boleto_evo = inserirBoletosEvo($evento,$cnab,$evoid,$promocode,$data_doc_db,$data_venc_db,$nnum,$docnum,$valor_cobrado,$valor_frete,$endereco) ;

        if(!$boleto_evo[0]) {
            $to ='debug@bodysystems.net' ;
            $subject = 'Erro cadastrando boleto no EVO';
            $msg = 'Consulte o arquivo erros_boletos_evo.log na pasta _ferramentas/calendario para detalhes.' ;
            sendEmail($to,$subject,$msg) ;
        }

        //gera o botào do boleto
        $botao = setBoleto($cnab,$nnum,$data_documento,$data_vencimento,$valor_cobrado,$nome,$endereco,$cpf,$evento,$transacaoid) ;
        $mArr = "BOTAO BOLETO: "."\n".$botao."\n" ;
        file_put_contents('incricao_treino.log',$mArr,FILE_APPEND) ;

        echo $botao ;

        //FIM DO BOLETO //////

    } 

    else if($formaPagto == 'mp') {
        $valor_cobrado = calcTotal($valor_ws,$valor_cartao,$valor_boleto,$valor_frete,$formaPagto) ;
        $botao = setMP($transacaoid,$siteid,$evoid,$cpf,$cep,$logradouro,$numero,$compl,$bairro,$cidade,$estado,$tipo_remessa,$cnab,$evento,$valor_cobrado,$valor_frete,$promocode,$nsa) ;

        echo $botao;

    } 

    else if($formaPagto == 'pagseguro') {
        $valor_cobrado = calcTotal($valor_ws,$valor_cartao,$valor_boleto,$valor_frete,$formaPagto) ;
        $botao = setPagSeg($evoid,$siteid,$cnab,$evento,$promocode,$valor_cobrado,$valor_frete,$nome,$email) ;
        $mArr = "PAGSEGURO: "."\n".$botao."\n" ;
        file_put_contents('incricao_treino.log',$mArr,FILE_APPEND) ;
        echo $botao ;

    }

?>
