<?php
//$c = $_SESSION['controller'];
$pageTitle = isset($c) ? $c->pageTitle : "Temperature logger";
//$min = $r['debug']['min'];
//header('Access-Control-Allow-Origin: http://diogny.com, http://mobile.diogny.com');
//<link href="highlight.css" type="text/css" rel="stylesheet">

//copy this file to apache server
//	 cp share/php/temps.php /var/www/html/temp/temps.php
//

define('__WEBROOT__', dirname(__FILE__));
define('__ROOT__', $baseUri);
define('__DOCUMENT_ROOT__', $_SERVER['DOCUMENT_ROOT']);

$ok = true;
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Multipurpose web page, comments, web development, opinions">
    <meta name="author" content="Diogny">
    <link rel="shortcut icon" href="<?php echo __ROOT__ ?>favicon.ico">
    <title><?php echo $pageTitle ?></title>
    <link href="<?php echo __ROOT__ ?>libs/bootstrap/css/bootstrap.min.css" type="text/css" rel="stylesheet">
	<style>
		.table {
			margin-bottom: 0 !important;
		}
	</style>
  </head>
  <body>
    <main class="container-fluid" role="main" style="min-height: 480px;">
		<div class="row">
			<table class="table table-hover" style="text-align: center;">
				<thead>
					<tr class="table-danger">
						<th>Temp</th>
						<th>Index</th>
						<th>Time</th>
					</tr>
				</thead>
				<tbody>
					<tr class="table-primary"><?php
			try {
				$array = explode("\n", file_get_contents('/home/pi/share/py/temps.txt'));
				//print("<pre>".print_r($array, true)."</pre>");
				$index = (int)$array[1];
				$queue = explode('|', $array[2]);
				//calculate first value, the oldest is next to index
				$i = ($index >= (60 -1)) ? 0 : $index + 1;
				
				$ok = count($array) >= 3;
?>
			
						<th scope="row"><?php echo $ok ? $array[0] : '*' ?></th>
						<td><?php echo $ok ? $index : '*' ?></td>
						<td><?php echo $ok ? $array[3] : '*' ?></td>
					</tr>
				</tbody>
<?php			if (!$ok) {
					echo '<caption class="table-danger">Invalid data storage</caption>';
				}?>
			</table>
			<div style="width: 100%; background-color: #F5DEB3;">
				<div id="chartContainer" style="width: 97%; height: 300px; margin: 5px auto;"></div>
			</div>
			<table class="table table-bordered table-sm table-dark table-striped" style="text-align: center;">
				<tbody>
<?php
				//echo '<h2>Below 85 degrees Celsius</h2>';
				//echo '<caption>CPU degress Celcius</caption>';
				for($r = 1; $r <= 6; $r++) {
?>
					<tr>
<?php
					for($c = 1; $c <= 10; $c++) {
						$style = ($i == $index) ? ' class="table-primary"': '';
?>
						<td<?php echo $style ?>><?php
						//echo '<td'.$style.'>';
						if (!empty($queue[$i])) {
							echo $queue[$i].' °C';
						} else {
							echo '0';
						}?></td>
<?php
						//echo '</td>'.PHP_EOL;
						$i = ($i >= (60 -1)) ? 0 : $i + 1;
					}
?>
					</tr>
<?php
				}
?>
				</tbody>
			</table>
<?php
				//print("<pre>".print_r($queue, true)."</pre>");
			}
			catch (Exception $e) {
				//echo '<h3>Server error</h3>';
				echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
?>
		</div>
<?php
		//'{ x: new Date()' +  
			$now = new DateTime();
			//$now->sub(new DateInterval('P59M'));
			/*print('<p>'.strftime($now).'</p>');
			$dt = getdate($now);
			print("<pre>".print_r($dt, true)."</pre>");
			$k = new Date( $dt.year, $dt.mon-1, $dt.mday, $dt.hours, $dt.minutes, 0);
			echo $k;
			print("<p>".$k."</p>");*/
?>
    </main>
    <script type="text/javascript" src="<?php echo __ROOT__?>libs/jquery/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="<?php echo __ROOT__?>libs/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo __ROOT__?>libs/canvasjs/jquery.canvasjs.min.js"></script>
	<script>
		//https://canvasjs.com/html5-javascript-line-chart/
		//https://canvasjs.com/docs/charts/basics-of-creating-html5-chart/
		
		var chart = new CanvasJS.Chart("chartContainer", {
			animationEnabled: true,
			animationDuration: 50,
			theme: "light2",
			backgroundColor: "#F5DEB3",
			title:{
				text: "Core Temperature per minute"
			},
			  toolTip:{             
				content: "{name}: {y}°C"
			  },
			axisY:{
				includeZero: false
			},
			data: [{        
				type: "line",
				name: "temp",
				dataPoints: [
<?php
					
					$d = Date('Y, n, d, G, i');
					$ndx = ($index >= (60 -1)) ? 0 : $index + 1;
					for($i=0; $i < 60; $i++) {
						$last = ($i < 60 -1);
						$comma = $last ? ',' : '';
						$marker = $last ? '' : ', markerColor: "red"';
?>
					{ y: <?php
						//echo '{ y: ';
						if (!empty($queue[$ndx])) {
							echo $queue[$ndx];
						} else {
							echo '0';
						}
						echo $marker.' }'.$comma.PHP_EOL;
						$ndx = ($ndx >= (60 -1)) ? 0 : $ndx + 1;
					}
?>
				]
			}]
		});
		chart.render();
				
		$.getJSON( "/temp/get_temp.php", function( data ) {
		  /*var items = [];
		  $.each( data, function( key, val ) {
			items.push( "<li id='" + key + "'>" + val + "</li>" );
		  });
		 
		  $( "<ul/>", {
			"class": "my-new-list",
			html: items.join( "" )
		  }).appendTo( "body" );
		  */
		  console.log(data);
		});
	</script>
  </body>
</html>