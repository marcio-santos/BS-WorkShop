<?

#
# Example PHP server-side script for generating
# responses suitable for use with jquery-tokeninput
#


# Connect to the database
mysql_pconnect("localhost", "body2013_mm", "7codemiguel777") or die("Could not connect");
mysql_select_db("body2013_body2013") or die("Could not select database");




# Perform the query
//$query = sprintf("SELECT (ID_CLIENTE + 0) AS id,NOME_FANTASIA AS name from academias_ativas WHERE NOME_FANTASIA LIKE '%%%s%%' ORDER BY NOME_FANTASIA DESC LIMIT 10", $_GET["q"]);
$query = sprintf("SELECT (ID_CLIENTE) AS id,NOME_FANTASIA AS name from academias_ativas WHERE NOME_FANTASIA LIKE '%%%s%%' ORDER BY NOME_FANTASIA DESC LIMIT 10", mysql_real_escape_string($_GET["q"]));

/*

$arr = array();
$rs = mysql_query($query);


file_put_contents('academias.log',$query);

# Collect the results
while($obj = mysql_fetch_object($rs)) {
    $arr[] = $obj;
}

*/


include('../../../exec_in_joomla.inc') ;
$db = &JFactory::getDBO();

$db->setQuery($query);
$result = $db->loadObjectList();


# JSON-encode the response
$json_response = json_encode($result);

//$json_response = json_encode($arr);

file_put_contents('json_response.log',$json_response);

# Optionally: Wrap the response in a callback function for JSONP cross-domain support
if($_GET["callback"]) {
    $json_response = $_GET["callback"] . "(" . $json_response . ")";
}


# Return the response
echo $json_response;



?>
