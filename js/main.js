$(document).ready(function(){
    var container = $("#container");
    container.isotope({
        itemSelector : 'ul#container > li',
        layoutMode : 'fitRows'
    });

    $.filtrify("container", "placeHolder", {
        close    : true,
        hide     : false,
        /*query :{"mes" : ["10"]}, */
        callback : function ( query, match, mismatch ) {
            container.isotope({ filter : $(match) });
        }
    });


    $('.fb').click(function(){
        $.fancybox( $(this),{
            href : '#dlg_frame',
            type: 'inline',
            maxWidth    : 640,
            maxHeight    : 450,
            fitToView    : false,
            modal        : true,
            width        : 640,
            height        : 450,
            autoSize    : false,
            closeClick    : false,
            openEffect    : 'elastic',
            closeEffect    : 'elastic',
            helpers : {

            }}

        );
        $('#frm_programas')[0].reset();
        $('#response').html('');
        $('#head').text('WS '+($(this).closest("div").children('#cidade').text()));
        $('#local').text($(this).closest("div").children('#endereco').text());
        $('#id_evento').text($(this).closest("div").children('#programa').text())
        $('#dta_evento').text('Realização: '+$(this).closest("div").attr('data-evento'));
        $('#eventoid').val($('#id_evento').text());
        $('#descricao').val($('#head').text());
        $('#data_evento').val($(this).closest("div").attr('data-evento'));


    });

    $('#close').click(function(){
        $.fancybox.close();

    });
    $('#cancel').click(function(){
        $.fancybox.close();

    });

    $('#apply').click(function(){ 
        var url = "http://bodysystems.net/_dev/iso/service/data_source.php";
    $.ajax({
        type: "POST",
        url: url,
        data: $('#frm_programas').serialize(), // serializes the form's elements.
        dataType: "html" ,
        beforeSend: function(load) {
            $('#response').html("<img src='http://bodysystems.net/_ferramentas/workshop/images/activity.gif' />");
        } ,
        success: function(data)
        {
            console.log(data);
            
            if(data=='Success') {
                $.fancybox.close();
            } else {
                $('#response').html(data);
            }
        } ,
        error: function (request, status, error) 
        {
            $('#response').html('<span style="color: red">Não foi possivel salvar a informação.<br/><pre>'+ request.responseText + '</pre><br/>' + status+'</span>');
            console.log($('#response').text());
        }
    });

    });




});