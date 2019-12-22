<?php
header('Content-Type: application/json');

define('__WEBROOT__', dirname(__FILE__));
define('__ROOT__', $baseUri);
define('__DOCUMENT_ROOT__', $_SERVER['DOCUMENT_ROOT']);

$result = [
	'temperature'=> '',
    'index' => 0,
	'list' => [],
    'date' => ''
];
	
try {
	$array = explode("\n", file_get_contents('/home/pi/share/php/temp/py/temps.txt'));
	//print("<pre>".print_r($array, true)."</pre>");
	$index = (int)$array[1];
	$queue = explode('|', $array[2]);
	//calculate first value, the oldest is next to index
	$i = ($index >= (60 -1)) ? 0 : $index + 1;
	
	if (count($array) >= 3) {
		header("HTTP/1.1 200 OK");
		
		$result['index'] = $index;
		$result['temperature'] = $array[0];
		$result['date'] = $array[3];
		
		$arr = array();
		for($r = 1; $r <= 6; $r++) {
			for($c = 1; $c <= 10; $c++) {
				$arr[] = empty($queue[$i]) ? 0 : floatval($queue[$i]);
				//
				$i = ($i >= (60 -1)) ? 0 : $i + 1;
			}
		}
		$result['list'] = $arr;
	} else {
		header("HTTP/1.1 400 Bad Request");
		
		$result['error'] = 'Invalid data storage';
	}
}
catch (Exception $e) {
	header("HTTP/1.1 400 Internal Server Error");
	
	//echo '<h3>Server error</h3>';
	//echo 'Caught exception: ',  $e->getMessage(), "\n";
	$result['error'] = 'Caught exception: '.$e->getMessage();
}
echo json_encode($result);
?>