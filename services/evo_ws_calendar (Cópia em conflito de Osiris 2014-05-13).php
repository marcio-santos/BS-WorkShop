<?php

    include('../../../exec_in_joomla.inc') ;


    //include('lib/block_treino.inc') ;
    //------------------------------
    function template_eval(&$template, &$vars) { return strtr($template, $vars); }
    //------------------------------
    function getMon($id_month) {
        switch($id_month) {
            CASE 1: $vM= 'JAN' ; BREAK;
            CASE 2: $vM= 'FEV' ; BREAK;
            CASE 3: $vM= 'MAR' ; BREAK;
            CASE 4: $vM= 'ABR' ; BREAK;
            CASE 5: $vM= 'MAI' ; BREAK;
            CASE 6: $vM= 'JUN' ; BREAK;
            CASE 7: $vM= 'JUL' ; BREAK;
            CASE 8: $vM= 'AGO' ; BREAK;
            CASE 9: $vM= 'SET' ; BREAK;
            CASE 10: $vM= 'OUT' ; BREAK;
            CASE 11: $vM= 'NOV' ; BREAK;
            CASE 12: $vM= 'DEZ' ; BREAK;

        }
        return  $vM ;

    }  

    function getFinish($dia_do_treino,$tipo_treino){

        $dia_w_hoje =  date('w',strtotime($dia_do_treino));
        $dif = 2 - $dia_w_hoje." days";
        $terca = date('Y-m-d',strtotime($dia_do_treino.$dif));
        return $terca;

    }

    function diffDate($d1, $d2, $type = '', $sep = '-')  {


        $dd2 = $d2 ;
        $d1=explode($sep, $d1);
        $d2=explode($sep, $d2);

        switch ($type)
        {
            case 'A':
                $X=31536000;

                break;

            case 'M':
                $X=2592000;

                break;

            case 'D':
                $X=86400;

                break;

            case 'H':
                $X=3600;

                break;

            case 'MI':
                $X=60;

                break;

            default: $X=1;

        }

        $vD1     =mktime(0, 0, 0, $d2[1], $d2[2], $d2[0]);
        $vD2     =mktime(0, 0, 0, $d1[1], $d1[2], $d1[0]);

        $interval=floor(($vD1 - $vD2) / $X);

        //calcula dia da semana
        //SE O EVENTO CAI NO DOMINGO, DIMINUI O PRAZO DE FECHAMENTO (FECHA 1 DIA ANTES)
        $wD = date('w',strtotime($dd2)) ;

        if ($wD == 0){
            $interval = $interval - 1 ;
        }


        return $interval;

    }

    function getDeadLine($data_treino,$intervalo_cfg){
        //SE O TREINAMENTO CAIR NO DOMINGO
        $dia_w = date('w',strtotime($data_treino));
        if($dia_w==0){ $intervalo_cfg++; }

        $date = strtotime($data_treino);
        $dead_line = date('Y-m-d',strtotime('-'.$intervalo_cfg.' days',$date)) ;
        return $dead_line ;
    }
    

    $template_main = <<<EOT
<div style="float:left; position:relative;width:320px;height:165px;border:1px solid #CCC; font-family:Tahoma, Geneva, sans-serif;-moz-border-radius: 5px; border-radius:5px;margin-right:12px;">
<table id="frame" cellpadding="0" cellspacing="0" width="320">
    <tr>
        <td style="background-image:url({BG_IMAGE}); background-repeat:no-repeat;height:150px; width:70px"><div id="data" style="font-size:36px;color:#fff;padding-top:60px; text-align:center;vertical-align:bottom">{DIA}</div>
        <div align="center" id="mes" style="font-size:20px; font-weight:bolder;color:#fff; text-align:top; ">{MES}</div></td>
        <td>
            <table style="margin-left:5px;">
                <tr>
                    <td valign="top">
                        
                        <div id="cidade" style="font-size:18px; color:#333; vertical-align:top"><span style="font-size:11px;color:#F00;">{DS_SERVICO}</span><br/>{CIDADE}</div>
                        <div id="local" style="font-size:10px;">{ENDERECO}</div>
                        <div id="price" style="margin-top:10px;"><img src="{IMG_PRECO}" />
                        <form id="{FORMID}" method="post" action="_calendarios/treino/land_detalhe.php" >
                            <input type="hidden" id="banner" name="banner" value='{BANNER}'/>
                            <input type="hidden" id="cidade" name="cidade" value='{CIDADE}'/>
                            <input type="hidden" id="local" name="local" value='{LOCAL}'/>
                            <input type="hidden" id="id-evento" name="id-evento" value='{ID_TREINAMENTO}' />
                            <input type="hidden" id="nome-evento" name="nome-evento" value='{NOME}'/>
                            <input type="hidden" id="ende-evento" name="ende-evento" value="{ENDERECO}" />
                            <input type="hidden" id="dt-m1" name="dt-m1" value='{DT-M1}'/>
                            <input type="hidden" id="dt-m2" name="dt-m2" value='{DT-M2}'/>
                            <input type="hidden" id="obs" name="obs" value='{OBSERVACOES}'/>
                            <input type="hidden" id="aviso" name="aviso" value='{AVISO}'/>
                            {INTERESSEI}
                        </form>
                        </div>
                    </td>
                </tr>        
            </table>
        </td>
    </tr>
</table>
</div>
EOT;

    $template_grid = <<<EOT
<table id="tb_{FORMID}" style="width:680px;height:80px;border-bottom:1px solid #CCC;" cellpadding="15">
    <tr>
        <td width="153" align="left"><img src="{BANNER}" width="150" height="38"/></td>
        <td><strong>{NOME}</strong><br/>{ENDERECO}<br/><span style="font-size:10px;">{DT-M1}</span><br/><span style="font-size:11px;font-weight:bold; color:red;">{AVISO}</span></td>
        <td width="122">
        <div style="color:red;background-color:#FFFF8C;font-size:11px;display:none;" align="center">{INTERVALO}</div>
        <form id="{FORMID}" name="{FORMID}" class="interest" method="post" action="index.php?option=com_jumi&fileid=99" >
            <input type="hidden" id="banner" name="banner" value='{BANNER}'/>
            <input type="hidden" id="cidade" name="cidade" value='{CIDADE}'/>
            <input type="hidden" id="horario" name="horario" value='{HORARIO}'/>
            <input type="hidden" id="local" name="local" value='{LOCAL}'/>
            <input type="hidden" id="id-evento" name="id-evento" value='{ID_TREINAMENTO}' />
            <input type="hidden" id="nome-evento" name="nome-evento" value='{NOME}'/>
            <input type="hidden" id="ende-evento" name="ende-evento" value="{ENDE}" />
            <input type="hidden" id="dt-m1" name="dt-m1" value='{DT-M1}'/>
            <input type="hidden" id="dt-m2" name="dt-m2" value='{DT-M2}'/>
            <input type="hidden" id="intervalo" name="intervalo" value='{INTERV}'/>
            <input type="hidden" id="sigla" name="sigla" value='{ABREVIACAO}'/>
            <input type="hidden" id="frete_gratis" name="frete_gratis" value='{FRETE_GRATIS}'/>
            <input type="hidden" id="treino" name="treino" value='{SIGLA}'/>            
            <input type="hidden" id="img_valor" name="img_valor" value='{IMG_VALOR}'/>            
            <input type="hidden" id="obs" name="obs" value='{OBSERVACOES}'/>
            <input type="hidden" id="aviso" name="aviso" value='{AVISO}'/>
            <input type="hidden" id="plantao" name="plantao" value='{PLANTAO}'/>
            
            {INTERESSEI}
        </form>
        </td>
    </tr>     
</table>
EOT;
  
    if(isset($_POST['firstLoad'])) {

        $fl = $_POST['firstLoad'];
        $tipo_treino = $_POST['t'] ;
        $programa = $_POST['p'] ;

        if($fl == 1) {
            //ALTERAÇÃO PARA O IG
            if($tipo_treino==13) {
                $inicio = date('Ymd',strtotime("+1 days"));
                $fim =  date('Ymd',strtotime("+90 days"));
                $dta_inicio = date('d/m',strtotime("+1 days"));
                $dta_fim = date('d/m',strtotime("+90 days"));
            } else if($tipo_treino==3) {
                $inicio = date('Ymd',strtotime("+4 days"));
                $fim =  date('Ymd',strtotime("+90 days"));
                $dta_inicio = date('d/m',strtotime("+4 days"));
                $dta_fim = date('d/m',strtotime("+90 days"));    
            } else {
                //$inicio = date('Ymd',strtotime("-120 days"));
                $inicio = date('Ymd',strtotime("2014-05-05"));
                $fim =  date('Ymd',strtotime("+90 days"));
                $dta_inicio = date('d/m',strtotime("+11 days"));
                $dta_fim = date('d/m',strtotime("+90 days"));    
            }


        } else {
            $inicio = date('Ymd');
            $fim =  date('Ymd',strtotime("+90 days"));
            $dta_inicio = date('d/m');
            $dta_fim =  date('d/m',strtotime("+90 days"));
        }

        $estado = "";


    } else {


        //VALORES DEFAULT PARA BUSCA SEM DATA
        $inicio_default = date('Ymd',strtotime("-120 days")) ; //'20130101'
        $fim_default = date('Ymd',strtotime("+120 days")) ; //'20130131' ;
        //----------------------------------------------------------------


        $tipo_treino = (isset($_POST['tipo_treino'])) ? $_POST['tipo_treino']: '7' ;
        $inicio = (strlen($_POST['inicio'])>0) ? date('Ymd',strtotime($_POST['inicio'])): $inicio_default  ;
        $fim =  (strlen($_POST['fim'])>0) ? date('Ymd',strtotime($_POST['fim'])): $fim_default  ;
        $dta_inicio = date('d/m',strtotime($inicio)) ;
        $dta_fim = date('d/m',strtotime($fim)) ;

        $estado = (isset($_POST['estado'])) ? $_POST['estado']: ''  ;
        $programa = (isset($_POST['programa'])) ? $_POST['programa']: ''  ;
        $view = $_POST['v'];

    }
    //echo "inicio: ",$_POST['inicio'],"  fim: ",$_POST['fim'];
    //die();

    //$interessei = '<a style="border:0px;" href="#"><img src="http://bodysystems.net/_ferramentas/calendario/images/inscreva_se.png" /></a>'  ;
    
    
    //ADICIONADOS DIRETAMENTE NO LOOP
    //$encerrado = '<img src="http://bodysystems.net/_ferramentas/calendario/images/encerrado.png" style="float: right; position: relative;" />' ;
    //$confirmado = '<img src="http://bodysystems.net/_ferramentas/calendario/images/confirmado.png" style="float: right; position: relative;" />' ;

    /*
    echo $tipo_treino.'<br/>'; 
    echo $inicio.'<br/>';
    echo $fim.'<br/>';
    echo $estado.'<br/>';
    echo $programa.'<br/>';

    exit(); 

    */

    try {
        $client = new 
        SoapClient( 
            "http://177.154.134.90:8084/WCF/_BS/wcfBS.svc?wsdl",array('cache_wsdl'=>WSDL_CACHE_NONE) 
        ); 
        $params = array('IdClienteW12'=>229, 'IdTipoTreinamento'=>7, 'Inicio'=>$inicio, 'Fim'=>$fim, 'Estado'=>$estado, 'Programa'=>$programa); 
        $webService = $client->ListarTreinamentosWebsite($params); 
        $wsResult = $webService->ListarTreinamentosWebsiteResult->VOBS; 
        file_put_contents('workshops.log',$inicio." - ".$fim."\n".print_r($wsResult,true)) ;
   
    } catch (Exception $e) {
        echo "<div align='center'><img src='http://bodysystems.net/_ferramentas/calendario/images/erp_error.png' /></div>" ;
        exit();
    }

    $retar[]='' ;
    $count =0 ;

    //CARREGA VALORES DO ARQUIVO DE CONFIGURAÇÃO
    $ini_file = parse_ini_file('../../calendario/calendario.cfg',true) ;

    switch($tipo_treino) {
        case 1: $tipo_t = 'M1'; break;
        case 2: $tipo_t = 'M2'; break;
        case 3: $tipo_t = 'MTA'; break;
        case 7: $tipo_t = 'WS'; break;
        case 13: $tipo_t = 'IG'; break;
        case 20: $tipo_t = 'INT'; break;
        default: $tipo_t = 'M1'; break;    
    }

    $treino = $ini_file[$tipo_t] ;

    $intervalo_cfg = $treino['intervalo'] ;
    $horario_treino = $treino['horario'] ;
    //$frete_gratis = 0;

     
    if(count($wsResult)>0) {
        if(count($wsResult)==1) {
            //Crido para contornar o erro qdo existia um único evento no calendário.
            $wsResult=array($wsResult);
        }
        $total_treinos = count($wsResult) ;
        foreach($wsResult as $obj){
            //20305
			if($obj->ID_TREINAMENTO=='20305'){
				//print"<!-- <pre>";
				//print_r($obj);
				//print"</pre> -->\n";
			}
            //tamanho do endereço de acordo com o tipo de visualização
            $vlen = ($view==1) ? 100 : 160 ;
            $m1 = ($obj->INICIO == $obj->FIM)? date('d-m-Y',strtotime($obj->INICIO)) : date('d-m-Y',strtotime($obj->INICIO));
            $m2 = date('d-m-Y',strtotime($obj->DT_MODULO2)) ;//(strlen($obj->MODULO2)!= 0)? 'Treinamento Módulo 2 em '.date('d-m-Y',strtotime($obj->MODULO2)): '' ;
            $ende = mb_convert_case($obj->ENDERECO, MB_CASE_LOWER, 'UTF-8');
            $endereco = (strlen($ende) >= $vlen) ? substr( ucwords($ende),0,$vlen)."..." : ucwords($ende) ;
            $formid = 'start'.$count ;
            $d1=date("Y-m-d", time());
            
            //MUDA O INTERVALO PARA SÃO PAULO
             if($obj->ID_NSA == '28') {
                $intervalo_cfg = $intervalo_cfg + 10 ;
                }

            //MUDA O INTERVALO PARA ARACAJU
             if($obj->ID_NSA == '1') {
                $intervalo_cfg = $intervalo_cfg - 9 ;
                }


            $d2= getDeadLine($obj->INICIO,$intervalo_cfg);  //date("Y-m-d", strtotime($obj->INICIO));
            $intervalo = diffDate($d1, $d2, 'D');
            $cidade = $obj->CIDADE;
            $nsa = strtolower($obj->ENDERECO_IMAGEM);
            //$intervalo = (int)$intervalo - 4;
            
            $interessei = "<input alt='Clique aqui para saber como participar deste Workshop' title='Clique aqui para saber mais detalhes sobre este Workshop' type='image' src='http://bodysystems.net/_ferramentas/workshop/images/inscreva_se.png' id='submit' name='submit' /><br/><a rel='fancybox' href='http://bodysystems.net/_ferramentas/workshop/images/programacao/$nsa' title='Workshop $cidade' ><img src='http://bodysystems.net/_ferramentas/workshop/images/veja_programacao.png'  style='margin-top:5px;'/></a>
                
                <a href='http://bodysystems.net/groundworks' title='Inscreva-se para participar de uma aula prática com Feedback de nossos Treinadores!'><img src='http://bodysystems.net/_ferramentas/workshop/images/inscreva_se_groundworks.png'  style='margin-top:5px;'/></a>";
            
            //MUDA A INFORMAÇÃO VISUAL DO ITEM DO CALENDARIO
            $obs = trim($obj->OBSERVACOES);
            
            if(strtoupper($obs)=='CANCELADO') {
                $encerrado = '<img src="http://bodysystems.net/_ferramentas/calendario/images/encerrado.png" style="float: right; position: relative;" alt="Este treino foi encerrado. Caso você tenha se inscrito neste Treinamento e não tenha recebido informações a respeito disto entre em contato com nossa Central para maiores detalhes." title = "Este Treinamento foi encerrado. Caso você tenha se inscrito neste Treinamento e não tenha recebido informações a respeito disto entre em contato com nossa Central para maiores detalhes." />' ;
            } elseif(strtoupper($obs)=='CONFIRMADO') {
                $encerrado = '<input alt="Clique aqui para saber mais detalhes sobre este Workshop" title="Clique aqui para saber mais detalhes sobre este Workshop" type="image" src="http://bodysystems.net/_ferramentas/workshop/images/inscreva_se.png" id="submit" name="submit" />';
                //$encerrado = '<img src="http://bodysystems.net/_ferramentas/calendario/images/confirmado.png" style="float: right; position: relative;" />' ;
            } else {
                $encerrado = '<input alt="Clique aqui para saber mais detalhes sobre este Workshop" title="Clique aqui para saber mais detalhes sobre este Workshop" type="image" src="http://bodysystems.net/_ferramentas/workshop/images/inscreva_se.png" name="submit" />
                    <a rel="fancybox" href="http://bodysystems.net/_ferramentas/workshop/images/programacao/'.$nsa.'" title="Workshop '.$cidade.'" ><img src="http://bodysystems.net/_ferramentas/workshop/images/veja_programacao.png"  style="margin-top:5px;"/></a>';
                
                //$encerrado = '<img src="http://bodysystems.net/_ferramentas/calendario/images/atencao.png" style="float: right; position: relative;" alt="Este treinamento encontra-se processo de confirmação, como existe a possibilidade dele não ser confirmado, sugerimos que você consulte este calendário novamente, ou entre em contato conosco na sexta-feira que antecede o mesmo. Mesmo assim, caso não ocorra a confirmação, nossa central entrará em contato para avisá-lo." title="Este treinamento encontra-se processo de confirmação, como existe a possibilidade dele não ser confirmado, sugerimos que você consulte este calendário novamente, ou entre em contato conosco na sexta-feira que antecede o mesmo. Mesmo assim, caso não ocorra a confirmação, nossa central entrará em contato para avisá-lo." />' ;
            } 
            
           
            //GERENCIA QUAIS TREINAMENTOS ESTÃO DISPONIVEIS
            $uinter = ((int)$intervalo > 0) ?  $interessei : $encerrado ;
            $no_interval = false;


            $ini_file = parse_ini_file('../../calendario/calendario.cfg',true) ;

            $abrir = $ini_file['abrir'] ;
            //$ids = $abrir['id'];
            $ids = explode(',',$abrir['id']);
            if(in_array($obj->ID_TREINAMENTO,$ids)) {
                $uinter = $interessei ;
            } 

            $encerrar = $ini_file['encerrar'] ;
            $ids = $encerrar['id'];
            if(in_array($obj->ID_TREINAMENTO,$ids)) {
                $uinter = $encerrado ;
                $no_interval = true;
            }

            //OCULTA EVENTO DO CALENDARIO
            $ocultar = $ini_file['ocultar'] ;
            $ids = explode(',',$ocultar['id']);
            if(in_array($obj->ID_TREINAMENTO,$ids)) {
                $uinter = 'oculto' ;
            }



            //ALTERA CAMINHO PARA O IG
            if($tipo_treino==13 && $intervalo <0 && $intervalo > ($intervalo_cfg * -1)) {
                $vIntervalo = "Última Hora" ;
            } else {
                $texto_encerra = ($intervalo == 1)? '<strong>Encerra hoje</strong>' : ' Encerra em '.$intervalo.' dia(s)' ;
                $vIntervalo = ($intervalo > 0)? $texto_encerra : '' ;

            }
            
            //SE O TREINAMENTO FOR ENCERRADO MANUALMENTE
            if($no_interval) {
                $vIntervalo = '';
            }
            
            //echo $obj->NOME,'-->', $intervalo,'--->',$uinter,'<br/>' ;

            //DESCRICOES ADICIONAIS
            switch ($tipo_treino) {
                //TREINAMENTO
                Case 1: 
                    $dias = date('d',strtotime($obj->INICIO)).' e '.date('d',strtotime($obj->DT_MODULO2));
                    $dtm1 = 'Treinamento Inicial em '.$m1. ' e '.$m2;
                    $nome_do_evento = $obj->DS_SERVICO.' '.$obj->CIDADE.' - '.date('d/m',strtotime($obj->INICIO)).' e '.date('d/m',strtotime($obj->DT_MODULO2)) ;
                    $img_valor = "valores.png";
                    $bg_image = 'http://bodysystems.net/_ferramentas/calendario/images/rb_'.str_replace(" ","_",strtolower($obj->DS_SERVICO)).'.png';
                    $banner_image = 'http://bodysystems.net/_ferramentas/calendario/images/programas/ban.'.str_replace(" ","",strtolower($obj->DS_SERVICO)).'.jpg';
                    break;

                    //MTA
                Case 3: 
                    $dias = date('d',strtotime($obj->INICIO));
                    $dtm1 = 'Modulo de Treinamento Avançado em '.$m1;
                    $nome_do_evento = 'MTA '.$obj->DS_SERVICO.' '.$obj->CIDADE.' - '.date('d/m',strtotime($obj->INICIO));
                    $cidade = 'MTA '.$cidade;
                    $img_valor = "valores_mta.png";
                    $bg_image = 'http://bodysystems.net/_ferramentas/calendario/images/rb_'.str_replace(" ","_",strtolower($obj->DS_SERVICO)).'.png';
                    $banner_image = 'http://bodysystems.net/_ferramentas/calendario/images/programas/ban.'.str_replace(" ","",strtolower($obj->DS_SERVICO)).'.jpg';
                    break;
                    //WORKSHOP
                Case 7: 
                    $dias = date('d',strtotime($obj->INICIO));
                    $dtm1 = 'Realização em '.$m1;
                    $nome_do_evento = $obj->DS_SERVICO.' '.$obj->CIDADE.' - '.date('d/m',strtotime($obj->INICIO));
                    //$bg_image = 'http://bodysystems.net/_ferramentas/calendario/images/rb_'.str_replace(" ","_",strtolower($obj->DS_SERVICO)).'.png';
                    if(strtoupper(SUBSTR($obj->ENDERECO,0,5))=="PLANT") {
                        $plantao = 1;
                        $banner_ws = 'workshop_plantao.png';
                    } else if($obj->ENDERECO=='') {
                        $plantao = 0;
                        $banner_ws = 'workshop_remessa.png';
                    } else {
                        $plantao = 0;
                        $banner_ws = 'workshop_evento.png' ;
                    }
                    
                    $banner_image = 'http://bodysystems.net/_ferramentas/calendario/images/programas/ban.'.$banner_ws;
                    break;
                    $img_valor = "";
                    //IG
                Case 13:
                    $dias = date('d',strtotime($obj->INICIO));
                    $dtm1 = 'Iniciação à Ginástica em '.$m1 ;
                    $nome_do_evento = $obj->DS_SERVICO.' '.$obj->CIDADE.' - '.date('d/m',strtotime($obj->INICIO));
                    $img_valor = "valores_ig.png";
                    $bg_image = 'http://bodysystems.net/_ferramentas/calendario/images/rb_'.str_replace(" ","_",strtolower($obj->DS_SERVICO)).'.png';
                    $banner_image = 'http://bodysystems.net/_ferramentas/calendario/images/programas/ban.'.str_replace(" ","",strtolower($obj->DS_SERVICO)).'.jpg';
                    $bin_data = base64_encode($nome_do_evento);
                    break;

            }



            $params  = array (
                '{FORMID}' => $formid,
                '{NOME}'=> $nome_do_evento, //$obj->NOME,
                '{ENDERECO}'=> $endereco, //ENDERECO PARA A GRADE
                '{ENDE}' => $ende,
                '{BAIRRO}'=>$obj->BAIRRO,
                '{CIDADE}'=>$cidade,
                '{ESTADO}'=>$obj->ESTADO,
                '{DESCRICAO}'=>$obj->DESCRICAO,
                '{ABREVIACAO}'=>$obj->DS_ABREVIACAO,
                '{DS_SERVICO}'=>$obj->DS_SERVICO,
                '{ID_TREINAMENTO}'=>$obj->ID_TREINAMENTO,
                '{INICIO}'=> date('d/m/Y',strtotime($m1)),
                '{MODULO2}'=>date('d/m/Y',strtotime($obj->DT_MODULO2)),
                '{FIM}'=>date('d/m/Y',strtotime($obj->FIM)),
                '{SIGLA}'=>$obj->SIGLA,
                '{VAGAS}'=>$obj->VAGAS,
                '{OBSERVACOES}'=>$obj->OBSERVACOES,
                '{AVISO}'=>$obj->AVISO,
                '{BG_IMAGE}' => $bg_image,
                '{BANNER}' => $banner_image,
                '{DT-M1}' => $dtm1,
                '{DT-M2}' => $m2,
                '{IMG_VALOR}' => $img_valor,
                '{IMG_PRECO}' => '_ferramentas/calendario/images/p470.png' ,
                '{DIAS}' => $dias,
                '{MES}' => getMon(date('m',strtotime($obj->INICIO))),
                '{INTERVALO}' => $vIntervalo ,
                '{FRETE_GRATIS}' => $frete_gratis,
                '{INTERV}' => $intervalo ,
                '{HORARIO}' => $horario_treino,
                '{INTERESSEI}' => $uinter,
                '{PLANTAO}' => $plantao,
                '{BIN_DATA}' => $bin_data,
                '{IG_INTERESSEI}' => $interessei  //SEMPRE LEVA VENCIDO PARA INSCRIÇAO

            );


            //OCULTA TREINAMENTO DO CALENDARIO
            if($uinter != 'oculto'){
                //ALTERA CAMINHO PARA O IG
                if($tipo_treino==13 && $intervalo <0 && $intervalo > ($intervalo_cfg * -1)) {
                    //DESVIA IG FORA DO PRAZO PARA FORMULARIO
                    $calendario.= template_eval($template_grid_ig,$params) ;

                } else {

                    //SELECIONA QUAL A VISUALIZAÇÀO
                    if($view==1){
                        $calendario.= template_eval($template_main,$params) ;
                    }  else {

                        $calendario.= template_eval($template_grid,$params) ;  
                    }

                  $count++ ;
                }

            }
            
        }
        //MOSTRA A QUANTIDADE DE TREINOS RETORNADA
        $total ="<div style='background-color:#000;color:#fff;font-size:18px;;
        -webkit-border-radius: 10px; -moz-border-radius: 10px; border-radius: 10px;
        width:680px;height:30px; vertical-align:central;padding-top:12px;' align='center'>Visualizando ".$count." Workshops</div>" ;


    }  else {                                                                                                                                               
        $calendario = "<center><img src='http://bodysystems.net/_ferramentas/calendario/images/no_search.gif' />" ;
        //$retar[0]= array('TOTAL'=>$count) ;
    }

    //MOSTRA O CALENDÁRIO
    echo $total,$calendario ;

    //echo '<pre>' ;
    //print_r($retar) ;
    //echo '</pre>' ;



?>
