//CALCULA O VALOR DO BONUS PARA CADA DATA
function getValProg() {
    //CALCULA OS DIFERENTES VALORES PARA OS PROGRAMAS
    var $num = $('#h_progs').val();
    var $bonus = $('#h_nivel_bonus').val() ;

    //Max Bonus

    if($bonus == 3) {

        //Max Bonus
        var $boleto = [73.00,103.00,126.00,148.00,169.00,192.00,214.00,234.00,252.00,270.00] ;
        var $cartao = [77.00,108.00,129.00,158.00,191.00,224.00,255.00,289.00,311.00,321.00] ;

    } else if ($bonus==2) {

        //Bonus
        var $boleto = [73.00,103.00,126.00,148.00,169.00,192.00,214.00,234.00,252.00,270.00] ;
        var $cartao = [77.00,108.00,129.00,158.00,191.00,224.00,255.00,289.00,311.00,321.00] ;
		//var $boleto = [79.00,110.00,133.00,163.00,199.00,237.00,274.00,312.00,346.00,382.00] ;
        //var $cartao = [82.00,115.00,138.00,169.00,210.00,251.00,292.00,333.00,374.00,409.00] ;

    } else if ($bonus ==1 ) {
        //Normal
        var $boleto = [79.00,110.00,133.00,163.00,199.00,237.00,274.00,312.00,346.00,382.00] ;
        var $cartao = [82.00,115.00,138.00,169.00,210.00,251.00,292.00,333.00,374.00,409.00] ;

    } else {
        //Juros de 15%
        var $boleto = [90.85,126.50,152.95,187.45,228.85,272.55,315.10,358.80,397.90,439.30] ;
        var $cartao = [94.30,132.25,158.70,194.35,241.50,288.65,335.80,382.95,430.10,470.35] ;

    }

    $('#h_valor_boleto').val($boleto[$num-1].toFixed(2));
    $('#h_valor_cartao').val($cartao[$num-1].toFixed(2));

    if($('#h_formaPagto').val()=='boleto') {
        $total = $boleto[$num-1].toFixed(2);
    } else {
        $total = $cartao[$num-1].toFixed(2);
    }
    $('#valor_licenciamento').html('Licenciamento: R$'+$total);


}

//CALCULA VALORES DA TRANSAÇÃO
function ValorTransacao($frete,$boleto) {


    var $valor_certificacao = jQuery('#h_certificacao').val()
    var $valor_total = parseFloat($valor_curso) + parseFloat($frete)+parseFloat($valor_certificacao);
    $valor_total = $valor_total.toFixed(2);
    console.log($valor_total) ;
    //--------------------------------
    var $ret = new Array($valor_curso,$valor_certificacao,$valor_total) ;
    return $ret ;

}

//CALCULA O CNAB
function cnab() {
    $ini = 'E';
    $evento_id = $('#h_evento_id').val();
    $despacho = $('#h_retirada_kit').val();
    //CADEIA DOS PROGRAMAS
    var $progs = '';
    $('.prog').each(function(){
        if($(this).attr('checked')) {
            $progs += $(this).attr('id')+";";
        }
    });
    //RETIRA O ÚLTIMO CARACTER;
    $progs = $progs.replace(/(\s+)?.$/, '');

    $SecPos = ($despacho=='EV')? 'WS' : '00' ;

    $t_cnab = $ini+$evento_id+'|'+$SecPos+'|'+$progs+'|'+$despacho ;

    return $t_cnab;

    //  E13303|WS|BC;BV;BS;PJ;SB;BJ|EV


}

//CALCULA O VALOR DO FRETE
function getFrete(){
    var $cep = $("#h_cep").val();
    var $frete = 0;
    var url = "http://bodysystems.net/_ferramentas/services/getFrete.php"

    $.ajax({
        type: "POST",
        url: url,
        data: {cep:$cep}, // serializes the form's elements.
        dataType: "json" ,
        async:false,
        beforeSend: function(load) {
            $('#activity').html("<img src='http://bodysystems.net/_ferramentas/workshop/images/activity.gif' style='margin-left:15px;' />");
        } ,

        success: function(data)
        {
            $('#activity').html('');
            $temp = parseFloat(data[0]);
            if($temp == 0) {
                alert("Erro calculando frete!\nTente novamente em alguns instantes.");
            } else {
                var $p1 = data[0];
                var $p2 = data[1];
                var $total_progs = $('#h_progs').val();
                if($total_progs==1) {
                    $frete = $p1;
                } else if($total_progs>1) {
                    $frete = $p1;;
                }
                return $frete;
            }


        } ,
        error: function (request, status, error)
        {
            $('#activity').html('');
            $('#response').html('<span style="color: red">Erro na comunicação com os Correios.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>');
        }
    });

    return $frete;

}

//VALIDA INTEIRAMENTE O FORMULARIO
function validar(){
    //VERIFICA SE TODAS AS ENTRADAS ESTÃO PREENCHIDAS
    if ($('#h_retirada_kit').val() != 'INI') {
        $msgRetiradaKit2 = '' ;
    } else {
        $msgRetiradaKit2 = "Você precisa selecionar a Forma de Recebimento de seu material.\n\n";
    }

    //if($msgRetiradaKit2 == '') {
        $msgProg = ($('#h_progs').val()>0)? '': "Você precisa selecionar pelo menos 1 programa para seu Licenciamento.\n\n";
        $msgValores = ($('#h_valor_boleto').val()>0 && $('#h_valor_cartao').val()>0)? '': "Ocorreu um erro inesperado. Por favor atualize a página em seu navegador e caso o problema persista, por favor entre em contato com nosso suporte técnico.(0x0001)\n\n";
        $msgValorFrete = ($('#h_valor_frete').val()==0  && $('#h_retirada_kit').val()=='CO')? "Existe um problema com a informação proveniente dos correios.Por favor atualize a página em seu navegador e caso o problema persista, por favor entre em contato com nosso suporte técnico.(0x0003)\n\n":'';
        $msgEvoid = ($('#h_evoid').val()>0)? '': "Ocorreu um problema inesperado na comunicação com nosso servidor.Por favor atualize a página em seu navegador e caso o problema persista, por favor entre em contato com nosso suporte técnico.(0x0004)\n\n";
        $msgSiteid = ($('#h_siteid').val()>0)? '': "Ocorreu um problema inesperado na comunicação com nosso servidor.Por favor atualize a página em seu navegador e caso o problema persista, por favor entre em contato com nosso suporte técnico.(0x0005)\n\n";
        $msgRetiradaKit = ($('#sel_retirada').val()!='INI')? '':  "Você precisa selecionar a maneira de obtenção de seu Kit Didático.(0x0002)\n\n";
        $msgCorreio = ($('#h_logradouro').val()=='' && $('#h_retirada_kit').val()=='CO')? "Ocorreu um problema de comunicação com o site dos correios.\nPor favor atualize a página em seu navegador e caso o problema persista, por favor entre em contato com nosso suporte técnico.(0x0006)\n\n" : '';
        $msgNumero = ($('#h_numero').val()!='')? '': "Você precisa informar o número de sua residência.\n\n";
        $msgConcordo = ($('#concordo').is(":checked"))? '': "Você precisa concordar com os Termos e Condições antes de finalizar.\n\n";
        $Stat = $msgProg + $msgValores + $msgRetiradaKit + $msgRetiradaKit2 +$msgValorFrete + $msgEvoid + $msgSiteid + $msgCorreio + $msgNumero + $msgConcordo ;
    //}  else {
     //   $Stat = $msgRetiradaKit2;
   // }


    $Stat = $.trim($Stat);
    if($Stat == ''){
        $valida = new Array(1,'');
    } else {
        $valida = new Array(0,$Stat);
    }

    return $valida;


}

//FINALIZA A INSCRICAO NO WS
function finishHim(){
    var url = "http://bodysystems.net/_ferramentas/workshop/services/inscricao_ws.php";
    $.ajax({
        type: "POST",
        url: url,
        data: $('#frm_resume').serialize(), // serializes the form's elements.
        dataType: "html" ,
        beforeSend: function(load) {
            $('#finish').html("<div style='margin-top:30px;margin-left:20px;'><img src='http://bodysystems.net/_ferramentas/workshop/images/activity.gif' /></div>");
            jQuery.blockUI({
                message: '<h2>Processando...</h2>',
                css: { border: '2px solid #333' }
            });
        } ,
        success: function(data)
        {
            $('#frame').hide();
            $('#table_end').fadeIn('fast');
            $('#botao').html(data);
            jQuery.unblockUI();
        } ,
        error: function (request, status, error)
        {
            $('#finish').html('<span style="color: red">Não foi possivel gerar o botão para pagamento.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>');
        }
    });

}

$(document).ready(function() {


    //MASCARAS PARA OS CAMPOS
    $('#cpf').setMask('cpf');
    $('#novo_cep').setMask('cep');

    //AUTENTICA O USUARIO E RECUPERA O LICENCIAMENTO
    $("#lnk_login").click(function() {

        var url = "http://bodysystems.net/_ferramentas/workshop/services/login_ws.php"; // the script where you handle the form input.
        var $cpf = $("#cpf").val();
        var $password = $('#password').val();

        $.ajax({
            type: "POST",
            url: url,
            data: {cpf:$cpf,password:$password}, // serializes the form's elements.
            dataType: "json" ,
            beforeSend: function(load) {
                $('#response').html("<div style='margin-top:30px;margin-left:20px;'><img src='http://bodysystems.net/_ferramentas/workshop/images/activity.gif' /></div>");
            } ,
            success: function(data)
            {
                //console.log(data);
                if(data[0]==0 || data[4]==0 || typeof(data[2])=='undefined') {
                    var $ht='';
                    if(typeof(data[2])=='undefined'){
                        $ht += '<center><<p class="error">Este CNPJ não está cadastrado em nossa base<br/>ou está incorreto.Por favor verifique.</p><center><br/>' ;
                    } else if(data[0]==0) {
                        $ht = data[1] ;
                    } else if(data[4]==0) {
                        $ht = '<center><p class="error">Senha incorreta. Tente novamente.</p><center><br/>' ;
                    }
                    $('#response').html($ht);
                    return false;
                } else {
                    $ht = data[1] ;
                    $('#dados_pessoais').css('width','33%');
                    $('#frame1').fadeIn('fast');
                    $('#dados_pagamento').addClass('tdx myborder');
                    $('#frame2').fadeIn('fast');
                    $('#cpf').attr('disabled', '');
                    $('#password').attr('disabled', '');

                    var $evoid = data[2]['evoid'];
                    var $siteid = data[4];
                    var $nome = data[2]['nome'];
                    var $email = data[2]['email'];
                    var $numero = data[2]['numero'];
                    var $cep = data[2]['cep'];
                    var $complemento = data[2]['complemento'];
                    var $endereco = data[2]['endereco'];
                    var $bairro = data[2]['bairro'];
                    var $cidade = data[2]['cidade'];
                    var $estado = data[2]['estado'];
                    var $numProgs = data[3] ;

                    $('#h_nome').val($nome);
                    $('#h_evoid').val($evoid);
                    $('#h_siteid').val($siteid);
                    $('#h_email').val($email);
                    $('#h_numero').val($numero);
                    $('#h_cep').val($cep);
                    $('#h_complemento').val($complemento);
                    $('#h_logradouro').val($endereco);
                    $('#h_bairro').val($bairro);
                    $('#h_cidade').val($cidade);
                    $('#h_uf').val($estado);
                    $('#h_cpf').val($('#cpf').val());
                    $('#h_progs').val($numProgs) ;

                    $('#response').html('<ul class="main"><li>ID:'+$evoid+'</li><li><strong>'+$nome+'</strong></li><li><strong>'+$email+'</strong></li><li>'+$endereco+','+$numero+' '+$complemento+'</li><li>'+$bairro+ ' - '+$cidade+' / '+$estado+'</li><li>CEP: '+$cep+'</li></ul>');
                    $('#certificacao').html(data[1]);
                    $('#opt_remessa').show();


                }
                //Carrega os valores para cálculo
                getValProg() ;
            } ,
            error: function (request, status, error)
            {
                $('#response').html('<span style="color: red">Não foi possivel executar o login.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>');
            }
        });

        //ATUALIZA O CNAB
        $('#h_cnab').val(cnab());

        return false; // avoid to execute the actual submit of the form.
    });


    //ENDERECO DE ENTREGA ALTERNATIVO
    $('#chk_entrega').click(function(){
        if ($('#chk_entrega').is(":checked")){
            $('#frame_entrega').fadeOut('fast');
        } else {
            $('#frame_entrega').fadeIn('slow');
        }
    });

    //VERIFICAÇÃO DO CEP PARA ENDERECO ALTERNATIVO
    jQuery("#lnk_checar_cep").click(function() {

        var url = "http://bodysystems.net/_ferramentas/services/getCorreios.php"; // the script where you handle the form input.
        $('#sel_retirada').val('ini');
        $('#h_retirada_kit').val('');
        $('#h_numero').val('');
        $('#h_complemento').val('');
        $('#h_valor_frete').val('0');
        $('#valor_frete').html('Valor do frete: R$0.00') ;
        jQuery.ajax({
            type: "POST",
            url: url,
            data: {cep:$('#novo_cep').val()}, // serializes the form's elements.
            dataType: "json" ,
            beforeSend: function(load) {
                $('#correios').html("<div style='margin-top:30px;margin-left:20px;'><img src='http://bodysystems.net/_ferramentas/workshop/images/activity.gif' /></div>");
            } ,
            success: function(data)
            {

                if(data[4]==0 || data[3]=='' || data[2]=='') {
                    //jQuery('#cog').hide();
                    alert("Problemas com a conexão com os Correios.\nPor favor, tente novamente em instantes")
                    $('#correios').html('');
                }   else {
                    if(data[0]===false) {
                        alert('INFORME UM CEP VÁLIDO ANTES DE PROSSEGUIR')  ;
                        //jQuery('#cog').hide();
                    } else {
                        //jQuery('#cog').hide();
                        //jQuery('#div_result_correios').show()  ;
                        //jQuery('#div_result_correios').html(data);

                        var $logradouro = data[0];
                        var $bairro = data[1];
                        var $cidade = data[2];
                        var $uf = data[3];

                        $('#h_logradouro').val($logradouro);
                        $('#h_bairro').val($bairro);
                        $('#h_cidade').val($cidade);
                        $('#h_uf').val($uf)

                        $('#novo_logradouro').val($logradouro);
                        $('#novo_bairro').val($bairro);
                        $('#novo_cidade').val($cidade);
                        $('#novo_uf').val($uf);



                        var $correios = $logradouro+"<br/>"+$bairro+"<br/>"+$cidade+" - "+$uf+"<br/>";
                        $('#novo_logradouro').show();
                        $('#novo_bairro').show();
                        $('#novo_cidade').show();
                        $('#novo_uf').show();
                        $('#novo_numero').show();
                        $('#novo_complemento').show();
                        $('#novo_numero').focus();
                        $('#h_cep').val($('#novo_cep').val());
                    }
                }

                if($('#sel_retirada').val()=='CO'){
                    var $bFrete = getFrete()
                    $('#valor_frete').html('Valor do frete: R$'+$bFrete);
                    $('#h_valor_frete').val($nFrete);
                } else if($('#sel_retirada').val()=='EV') {
                    $v_ws = $('#h_valor_referencia_ws').val();
                    $('#valor_ws').html('Valor do frete: R$'+$v_ws);
                    $('#h_valor_ws').val($v_ws);
                }
            },
            error: function (request, status, error)
            {
                $('#correios').html('');
                jQuery('#correios').html('<div style="color: red">Não foi possivel executar a conexão com os correios.<br/><pre>'+ request.responseText + '<br/>' + status+'<br/>'+error+'</pre></div>');

            }
        });

        return false; // avoid to execute the actual submit of the form.
    });

    //ATUALIZA NÚMERO E COMPLEMENTO ALTERNATIVOS
    $('#novo_numero').change(function(){
        $('#h_numero').val($('#novo_numero').val())
        $('#h_complemento').val($('#novo_complemento').val())
    });

    //ATUALIZA TUDO PELO ENDEREÇO NOVO SE MUDAR
    $('.novo_ende').change(function(){
                        $('#h_logradouro').val($('#novo_logradouro').val());
                        $('#h_bairro').val($('#novo_bairro').val());
                        $('#h_cidade').val($('#novo_cidade').val());
                        $('#h_uf').val($('#novo_uf').val())
    }) ;

    //SE ESCOLHER CORREIO CALCULA O FRETE
    $('#sel_retirada').change(function() {
        if($('#sel_retirada option:selected').attr('id')=='CO') {
            $nFrete = getFrete()
            $('#valor_frete').html('Valor do frete: R$'+$nFrete);
            $('#valor_ws').html('Ingresso do WS: R$0.00');
            $('#h_valor_frete').val($nFrete);
            $('#h_retirada_kit').val('CO');
            $('#h_valor_ws').val(0);
        } else if($('#sel_retirada option:selected').attr('id') =='WS') {
            $('#valor_frete').html('Valor do frete: R$0.00');
            $('#valor_ws').html('Ingresso do GW: R$0.00');
            $('#h_valor_frete').val('0');
            $('#h_retirada_kit').val('EV');
            $('#h_valor_ws').val('0.00');
        } else  if($('#sel_retirada option:selected').attr('id') =='PL') {  //WS
            $('#valor_frete').html('Valor do frete: R$0.00');
            $('#h_valor_frete').val('0');
            $('#h_retirada_kit').val('');
            $('#h_valor_ws').val('');
            $('#valor_ws').html('Ingresso do GW: R$ 0.00') ;

        } else  {  //WS
            alert('Você precisa escolher uma forma para a obtenção de seu material didático! Selecione entre os itens disponíveis.')
            $('#valor_frete').html('Valor do frete: R$0.00');
            $('#h_valor_frete').val('0');
            $('#h_retirada_kit').val('');
            $('#h_valor_ws').val('');
            $('#valor_ws').html('Ingresso do GW: R$ 0.00') ;

        }
        //INTERAÇAO COM A FORMA DE ENTREGA DO KIT
        $('#h_cnab').val(cnab());
    });

    /*
    //ENDERECO DE ENTREGA ALTERNATIVO
    $('#ir_ws').click(function(){
    if ($('#ir_ws').is(":checked")){
    $('#valor_ws').html('Ingresso do WS: R$25.00');
    } else {
    $('#valor_ws').html('Ingresso do WS: R$0.00');
    }
    });
    */

    //INTERACAO COM A FORMA DE PAGTO
    $('.opt').click(function(){
        $('#h_formaPagto').val($(this).attr('id'));
        if($(this).attr('id')== 'boleto') {
            $('#valor_licenciamento').html('Licenciamento: R$'+$('#h_valor_boleto').val());
        } else {
            $('#valor_licenciamento').html('Licenciamento: R$'+$('#h_valor_cartao').val());
        }


    })

    //INTERAGE COM OS PROGRAMAS
    $(document).on("change", ".prog", function(){
        var num_prog = $('.prog:checked').length;
        $('#h_progs').val(num_prog);
        $('#h_valor_por_programa').val(getValProg(num_prog));
        $('#h_cnab').val(cnab());
    });

    //INTERAGE COM O BOTAO DE FINALIZAÇAO
    $('#bt_finish').click(function(){
        $valida = validar();
        if($valida[0]== 1) {
            finishHim() ;
        } else {
            alert($valida[1]) ;
        }
    });

    /*
    //TERMOS E CONDIÇÕES
    $('#lnk_termos').fancybox(
        {'openEffect'    :    'fade',
        maxWidth    : 800,
        maxHeight    : 600,
        width        : '50%',
        height        : '50%',
        padding     :   25,
        'closeEffect'    :    'elastic',
        'modal' : true,
        'speedIn'        :    200,
        'speedOut'        :    200,
        'overlayShow'    :    true,
        'title' : '<a class = "btn btn-inverse" href="javascript:$.fancybox.close();" id="addproject-close">Fechar</a>',
        helpers : {
        title: {
            type: 'inside'
        },
         overlay : {
            css : {
                'background' : 'rgba(255, 255, 255, 0.90)'
            }
        }
        }
    });
*/

    var added = function(item){alert(item.name)};
    var deleted = function(item){alert(item.name)};

    $('#lista_academias').tokenInput("GET",{
        onAdd: added,
        onDelete: deleted
    });

});

//muda o titulo da pagina
document.title = 'WorkShop Ckeckout' ;