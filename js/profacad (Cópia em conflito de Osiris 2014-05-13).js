$(document).ready(function(){

    $("#demo-input-facebook-theme").tokenInput("http://bodysystems.net/_ferramentas/workshop/services/query-academias.php", {
        theme: "facebook",
        allowFreeTagging: true,
        allowTabOut: false,
        propertyToSearch: "name",
        onAdd: function(item) {
            localStorage.setItem(item.id,item.name);
        },
        onDelete: function(item) {
            localStorage.removeItem(item.id);

        }
    });


    $('#sem_academia').click(function(){

        var Acads = $('.lista_academias').tokenInput("get");
        var jlista = JSON.stringify(Acads);
        var $url = "http://bodysystems.net/_ferramentas/workshop/services/academias.php" ;
        var $cpf = $('#h_cpf').val();
        var $eventoid = $('#h_evento_id').val() ;
        var $evento = $('#h_evento_descricao').val();


        $.ajax({
            type: 'post',
            url: $url,
            data: {cpf:$cpf,eventoid:$eventoid,evento:$evento,academias: jlista},
            dataType: "html" ,
            beforeSend: function() {

            }
        })
            .done(function(msg){
                    console.log(msg);
                    localStorage.clear();
            });


    });


});
