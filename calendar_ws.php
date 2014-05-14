<?php
//include('../../exec_in_joomla.inc') ;
//------------------------------
    function template_eval(&$template, &$vars) { return strtr($template, $vars); }
//------------------------------

$user=&JFactory::getUser();
$document=&JFactory::getDocument();

//$document->addStyleSheet('http://code.jquery.com/ui/1.7.0/themes/base/jquery-ui.css');
$document->addScript('http://code.jquery.com/jquery-1.8.2.js');
$document->addScript('http://code.jquery.com/ui/1.9.0/jquery-ui.js') ;

$document->addStyleSheet('http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css');

//$document->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.js');
//$document->addScript('http://code.jquery.com/ui/1.7.0/jquery-ui.js') ;
$document->addScript('_ferramentas/workshop/js/grid_ws_calendar.js');
$document->addScript("_ferramentas/contratos/source/jquery.fancybox.js?v=2.1.5");
$document->addStyleSheet("_ferramentas/contratos/source/jquery.fancybox.css?v=2.1.5");


$document->addScript('_ferramentas/calendario/js/jquery.multiselect.min.js');
$document->addStyleSheet('_ferramentas/calendario/js/jquery.multiselect.css');
//$document->addStyleSheet('_calendarios/treino/lib/dp/css/redmond/jquery-ui-1.7.1.custom.css');

//TIPO DE VISUALIZAÇÃO
if(isset($_GET['v'])){
    $view = $_GET['v'] ;
} else {
    $view = 2;
}

//TIPO DE TREINO
if(isset($_GET['t'])){
    $treino = $_GET['t'] ;
} else {
    $treino = 1;
}

//PROGRAMA
if(isset($_GET['p'])){
    $programa = $_GET['p'] ;
} else {
    $programa = "";
}

  
$template_main = <<<EOT
<form id="frm_calendar" style="display:none;" name="frm_calendar" action="http://bodysystems.net/_ferramentas/workshop/services/evo_ws_calendar.php" method="post">
<table width="980">
<tr>
    <td width="250" valign="top">
    
    </td>
    <td width="670" valign="top">
        <div id="cog" style='margin-top:30px;margin-left:35%;display:none;'><img src='http://bodysystems.net/_ferramentas/calendario/images/cloud.gif' /></div>
        <div id="grid_response"></div>
    </td>
</tr>
</table>
<input type="hidden" id="v" name="v" value="$view"  />
<input type="hidden" id="t" name="t" value="7"  />
</form>
EOT;

//=================================
header("Expires: Mon, 26 Jul 12012 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
//=================================

echo $template_main ;

?>
