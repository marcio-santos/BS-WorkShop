$(document).ready(function() {
    //$.noConflict() ;

    function firstLoad($opt,$treino,$programa){
        //$.preventDefault();

        var url = "http://bodysystems.net/_ferramentas/workshop/services/evo_ws_calendar.php"; // the script where you handle the form input.
        var $firstLoad = true;
        $firstLoad = $opt ;
        $.ajax({
            type: "POST",
            url: url,
            data: {firstLoad:$firstLoad,t:$treino,p:$programa}, // serializes the form's elements.
            dataType: "html" ,
            beforeSend: function(load) {
                $('#grid_response').hide();
                $('#cog').show('fast');
            } ,
            success: function(data)
            {
                $( 'html, body' ).animate( { scrollTop: 100 }, 'slow' );
                $('#grid_response').html(''); 
                $('#grid_response').append(data);
                $('#cog').hide('fast');
                $('#grid_response').show();
                $('a[rel="fancybox"]').fancybox({
                    'arrows':false,
                    'closeClick': true,
                    'closeBtn':false
                });
                return false;  

            } ,
            error: function (request, status, error) 
            {
                $('#cog').hide('fast');
                $('#grid_response').html('<div style="color: red"><pre>'+ request.responseText + '<br/>' + status+'<br/>'+error+'</pre></div>');
                $('#grid_response').show('fast');
                return false;
            }
        });
    }

    //CAIXAS DE SELEÇÃO    
    $("select").multiselect({
        multiple: false,
        header: "Selecione uma opção",
        noneSelectedText: "Selecione uma opção",
        selectedList: 1
    }); 


    if($('#tipo_treino').multiselect({
        click: function(event, ui){
            if(ui.value == 13){
                $("#programa").multiselect('disable');    
            } else {
                $("#programa").multiselect('enable');    
            }
        }

    }));

    //DATEPICKER
    $(function(){
        //create config object
        $("input.datepicker").datepicker({
            dateFormat: 'dd-mm-yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'
            ],
            dayNamesMin: [
                'D','S','T','Q','Q','S','S','D'
            ],
            dayNamesShort: [
                'Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'
            ],
            monthNames: [  'Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro',
                'Outubro','Novembro','Dezembro'
            ],
            monthNamesShort: [
                'Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set',
                'Out','Nov','Dez'
            ],
            nextText: 'Próximo',
            prevText: 'Anterior'
        });
    });

    //mostra o calendario
    $('#frm_calendar').fadeIn('fast');

    //Carrega a primeira visualização
    //verifica se é link específico de algum treinamento
    $p = $('#h_programa').val();
    $t = $('#t').val();
    firstLoad(1,$t,$p);

    //BOTÃO PARA TREINAMENTOS ENCERRADOS
    $('#bt_encerrados').click(function(b){
        firstLoad(2,$t,$p);
    });

    //CARREGA A GRADE DO CALENDARIO
    $('#view_calendar').click(function(event){
        event.preventDefault();
        var url = "http://bodysystems.net/_ferramentas/workshop/services/evo_ws_calendar.php"; // the script where you handle the form input.

        $.ajax({
            type: "POST",
            url: url,
            data: $("#frm_calendar").serialize(), // serializes the form's elements.
            dataType: "html" ,
            beforeSend: function(load) {
                
                $('#grid_response').hide();
                $('#cog').show('fast');
            } ,
            success: function(data)
            {
                $( 'html, body' ).animate( { scrollTop: 100 }, 'slow' );
                $('#grid_response').html(''); 
                $('#grid_response').append(data);
                $('#cog').hide('fast');
                $('#grid_response').show();
                $('a[rel="fancybox"]').fancybox();
                return false;  

            } ,
            error: function (request, status, error) 
            {
                $('#cog').hide('fast');
                $('#grid_response').html('<div style="color: red"><pre>'+ request.responseText + '<br/>' + status+'<br/>'+error+'</pre></div>');
                $('#grid_response').show('fast');
                return false;
            }
        });
    });
    
   
});
        
