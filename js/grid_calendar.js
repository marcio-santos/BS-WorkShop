jQuery(document).ready(function() {
	jQuery.noConflict() ;

	function firstLoad($opt,$treino,$programa){
		//jQuery.preventDefault();

		var url = "http://bodysystems.net/_ferramentas/calendario/services/evo_calendar.php"; // the script where you handle the form input.
		var $firstLoad = true;
		$firstLoad = $opt ;
		jQuery.ajax({
			type: "POST",
			url: url,
			data: {firstLoad:$firstLoad,t:$treino,p:$programa}, // serializes the form's elements.
			dataType: "html" ,
			beforeSend: function(load) {
				jQuery('#grid_response').hide();
				jQuery('#cog').show('fast');
			} ,
			success: function(data)
			{
				jQuery( 'html, body' ).animate( { scrollTop: 100 }, 'slow' );
				jQuery('#grid_response').html(''); 
				jQuery('#grid_response').append(data);
				jQuery('#cog').hide('fast');
				jQuery('#grid_response').show();
				return false;  

			} ,
			error: function (request, status, error) 
			{
				jQuery('#cog').hide('fast');
				jQuery('#grid_response').html('<div style="color: red"><pre>'+ request.responseText + '<br/>' + status+'<br/>'+error+'</pre></div>');
				jQuery('#grid_response').show('fast');
				return false;
			}
		});
	}

	//CAIXAS DE SELEÇÃO    
	jQuery("select").multiselect({
		multiple: false,
		header: "Selecione uma opção",
		noneSelectedText: "Selecione uma opção",
		selectedList: 1
	}); 


	if(jQuery('#tipo_treino').multiselect({
		click: function(event, ui){
			if(ui.value == 13){
				jQuery("#programa").multiselect('disable');    
			} else {
				jQuery("#programa").multiselect('enable');    
			}
		}

	}));

	//DATEPICKER
	jQuery(function(){
		//create config object
		jQuery("input.datepicker").datepicker({
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
	jQuery('#frm_calendar').fadeIn('fast');

	//Carrega a primeira visualização
	//verifica se é link específico de algum treinamento
	$p = jQuery('#h_programa').val();
	$t = jQuery('#t').val();
	firstLoad(1,$t,$p);

	//BOTÃO PARA TREINAMENTOS ENCERRADOS
	jQuery('#bt_encerrados').click(function(b){
		firstLoad(2,$t,$p);
	});

	//CARREGA A GRADE DO CALENDARIO
	jQuery('#view_calendar').click(function(event){
		event.preventDefault();
		var url = "http://bodysystems.net/_ferramentas/calendario/services/evo_calendar.php"; // the script where you handle the form input.

		jQuery.ajax({
			type: "POST",
			url: url,
			data: jQuery("#frm_calendar").serialize(), // serializes the form's elements.
			dataType: "html" ,
			beforeSend: function(load) {
				
				console.debug(jQuery("#frm_calendar").serialize());
				
				jQuery('#grid_response').hide();
				jQuery('#cog').show('fast');
			} ,
			success: function(data)
			{
				jQuery( 'html, body' ).animate( { scrollTop: 100 }, 'slow' );
				jQuery('#grid_response').html(''); 
				jQuery('#grid_response').append(data);
				jQuery('#cog').hide('fast');
				jQuery('#grid_response').show();
				return false;  

			} ,
			error: function (request, status, error) 
			{
				jQuery('#cog').hide('fast');
				jQuery('#grid_response').html('<div style="color: red"><pre>'+ request.responseText + '<br/>' + status+'<br/>'+error+'</pre></div>');
				jQuery('#grid_response').show('fast');
				return false;
			}
		});
	});
	
   
});
		
