<?php

include 'notes.php';

$db = new PDO ($infdb, $logdb, $passdb);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$_SESSION['login'] = 'Комментатор';
?>
<!DOCTYPE html>
<html>
<head>
	<title>Тестовая страница</title>
</head>
<body>
<form id='answ' method='post'></form>
<?php

$sh_po = $db->prepare(
"SELECT * FROM `postes` 
ORDER BY `id` DESC");
// ORDER BY `id` DESC - сортировка по убыванию
$ex_po = $sh_po->execute();
$fa_po = $sh_po->fetchAll(PDO::FETCH_ASSOC);

$co_po = count($fa_po);
$n = 0;

while ($n < $co_po)
	{	
		echo "<table cellpadding='5px' 
		style='
			width: 33%; 
		
			border: 2px solid black;
			padding-left: 3px;
			padding-top: 2px;

			table-layout: fixed;
			border-radius: 10px;
			background-color: rgba(0, 0, 0, 0.3);
			margin: 3px;'>";
			
		print 
		"<tr>
		<td colspan='2'><b>Пост №{$fa_po[$n]['id']}</b> Тип: {$fa_po[$n]['type']}</td>
		</tr>";
	
		if (!empty($fa_po[$n]['pict']))
			{
				print "<tr><td colspan='2'>
				<img class='pict' style='
					border: 2px #708090;
					table-layout: fixed; 
					margin-left: 4px;
					height: 130px;
					wight: 130px;' 
				
				src='picture/".
				$fa_po[$n]['pict']."'>
				</td></tr>";
			}

		print "
			<tr>
				<td colspan='2'>
					{$fa_po[$n]['letter']}
				</td>
			</tr>
		<tr>
			<td colspan='2'>
			<div style='
				border-top: 1px solid #cccccc;
				padding-top: 4px;'>
			Годнопостил: <b>{$fa_po[$n]['autor']}</b> {$fa_po[$n]['time-post']}
			</td>
		</tr>";
		
		echo "</table>";
		$n++;
	}

?>
</body>
</html>