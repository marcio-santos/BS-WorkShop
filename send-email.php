<?php
/**
 * Created by PhpStorm.
 * User: mac
 * Date: 21/03/14
 * Time: 10:54
 */
    function sendEmail($to,$subject,$msg) {

        $from = 'noreply@mrideias.com';
        $body = <<<EOT
	  <html>
	  <p>Caro Webmaster,</p>
	  <p>Esta pessoa deixou seu contato.<br/>
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
        $headers .= 'From: Site MR Ideias <noreplay@bodysystems.net>' . "\r\n";
        $response = mail($to,$subject,$body,$headers);


    }


$to = 'marcio.a.santos@me.com';
$subject = 'Novo contato no site';
$msg = print_r($_POST,true);

sendEmail($to,$subject,$msg);
echo '<script>alert("Obrigado! Seu email foi enviado.");window.location = "http://mrideias.com"</script>';
