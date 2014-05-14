<?php

    include('../../../exec_in_joomla.inc') ;

    //PARAMETROS DE ENTRADA
    $cpf = $_POST['cpf'] ;
    $password = $_POST['password'] ;

    $cpf = str_replace('-','',str_replace('.','',$cpf));

    function validaCPF($cpf)
    {    // Verifiva se o número digitado contém todos os digitos
        $cpf = str_pad(preg_replace('[^0-9]', '', $cpf), 11, '0', STR_PAD_LEFT);

        // Verifica se nenhuma das sequências abaixo foi digitada, caso seja, retorna falso
        if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999')
        {
            return false;
        }
        else
        {   // Calcula os números para verificar se o CPF é verdadeiro
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }

                $d = ((10 * $d) % 11) % 10;

                if ($cpf{$c} != $d) {
                    return false;
                }
            }

            return true;
        }
    }

    function getProgs($cpf) {


        //==========INTERAÃ‡Ã•ES COM O EVO====================================//
        $client = new
        SoapClient(
            "http://177.154.134.90:8084/WCF/Clientes/wcfClientes.svc?wsdl"
        );
        $params = array('IdClienteW12'=>229, 'IdFilial'=>1, 'CpfCnpj'=>$cpf, 'TipoCliente'=>2);
        $webService = $client->ListarClienteCPFCNPJ($params);
        $wsResult = $webService->ListarClienteCPFCNPJResult;


        file_put_contents('retorno.log',print_r($wsResult,true));

        // Recupera o IdCliente
        $w12id = $wsResult->ID_CLIENTE;
        $nome = mb_convert_case($wsResult->NOME, MB_CASE_TITLE, "UTF-8");
        $email = strtolower($wsResult->EMAIL);
        $cep = $wsResult->CEP;
        $numero =$wsResult->NUMERO;
        $complemento = mb_convert_case($wsResult->COMPLEMENTO, MB_CASE_TITLE, "UTF-8");
        $endereco = mb_convert_case($wsResult->ENDERECO, MB_CASE_TITLE, "UTF-8");
        $bairro = mb_convert_case($wsResult->BAIRRO, MB_CASE_TITLE, "UTF-8");
        $cidade = mb_convert_case($wsResult->CIDADE, MB_CASE_TITLE, "UTF-8");
        $estado = mb_convert_case($wsResult->ESTADO, MB_CASE_TITLE, "UTF-8");


        $cliente = array(
            'evoid'=>$w12id,
            'nome'=>$nome,
            'email'=>$email,
            'cep'=>$cep,
            'numero'=>$numero,
            'complemento'=>$complemento,
            'endereco' => $endereco,
            'bairro' => $bairro,
            'cidade' => $cidade,
            'estado' => $estado
        );


        //Recupera a Carreira do Cliente
        $client = new
        SoapClient(
            "http://177.154.134.90:8084/WCF/_BS/wcfBS.svc?wsdl"
        );

        $params = array('IdClienteW12'=>229, 'IdCliente'=>$w12id);
        $webService = $client->RetornarCertificacoesProfessor($params);
        $wsResult = $webService->RetornarCertificacoesProfessorResult->VOBS;
        $vQtde=0 ;

        foreach ($wsResult as $v1) {

            if($v1->DS_PROGRAMA_ABREV !='BV') {
            $iNivel=$v1->ID_NIVEL_PROGRAMA;
            $iMod1=$v1->FL_MODULO1 ;
            $iMod2=$v1->FL_MODULO2 ;

            //Se ele pode comprar este WS
            if($iNivel>0 ||$iMod1 || $iMod2) {

                $vQtde++ ;

                $HTML .= '<div style="padding-bottom:5px; padding-right:5px; display:block;width:150;float:left;">';
                $HTML .= '<div style="padding-bottom:5px; padding-right:5px; float:left;"><input class="prog" name="'.$v1->DS_PROGRAMA_ABREV.'" type="checkbox" id="'.$v1->DS_PROGRAMA_ABREV;
                $HTML .= '" checked="checked" /></div>';
                $HTML .= '<img src="http://bodysystems.net/_ferramentas/workshop/images/checkout/'.$v1->DS_PROGRAMA_ABREV.'.jpg" /></div>' ;

            }
        }
        }

        if ($vQtde==0) {

            $HTML="<div class='alert'><strong><p>IMPORTANTE!<br/>Antes de efetuar sua compra, &eacute; necess&aacute;rio <br/>que voc&ecirc; entre em contato conosco em nossa Central. <br/>Por favor, ligue para (11) 3529-2880.<br/>Obrigado!</strong></p></div>" ;
            $success = 0;

        } else {

            $success = 1;
        }

        $vArr = array($success,$HTML,$cliente,$vQtde);
        return $vArr;
    }


    function login($cpf,$password){

        $db =& JFactory::getDBO();
        $query = 'SELECT wow_users.id,name,username,email,password FROM	wow_users WHERE	REPLACE(REPLACE(username,".",""),"-","") LIKE '.$db->Quote($cpf).' ORDER BY id DESC LIMIT 1' ;
        $db->setQuery($query) ;
        $result =  $db->loadObjectList();

        $credentials = explode(':',$result[0]->password) ;
        $md5pass = $credentials[0] ;
        $saltpass = $credentials[1];

        $check_pass =  md5($password.$saltpass) ;


        $ret = ($check_pass==$md5pass) ? $result[0]->id : 0;
        return $ret;

    }
    //LOCALIZA A SENHA DO CLIENTE
    if(validaCPF($cpf))   {
        $ret = getProgs($cpf);
        $email = $ret[2]['email'] ;

        $login_ok = login($cpf,$password) ;

        array_push($ret,$login_ok);
    } else {
        $ret= array('CPF inválido',0) ;
    }


    echo json_encode($ret);

?>
