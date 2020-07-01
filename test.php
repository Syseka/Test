<?php

include 'notes.php';

$db = new PDO ($infdb, $logdb, $passdb);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$_SESSION['login'] = 'Комментатор';
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
	<title>Тестовая страница</title>
</head>
<body>
<form id='answ' method='post'></form>
<?php

//Таблица с постами
$sh_po = $db->prepare(
"SELECT * FROM `postes` 
ORDER BY `id` DESC");
$ex_po = $sh_po->execute();
$fa_po = $sh_po->fetchAll(PDO::FETCH_ASSOC);

/* Тестовая талица
$post = $db->prepare("
SELECT * 
FROM `testpost`");
	$post->execute();
	$f_post = $post->fetchAll(PDO::FETCH_ASSOC);*/


//Таблица с комментариями
$comm = $db->prepare("
SELECT * 
FROM `testcom`");
	$comm->execute();
	$f_comm = $comm->fetchAll(PDO::FETCH_ASSOC);

print "Сессия: ".$_SESSION['login'];



//Вывод постов и комментариев


$co_po = count($fa_po);
$n = 0;

while ($n < $co_po)
	{	
		echo "<table cellpadding='3px' 
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
		<td colspan='2'>
		<div style='
			border-bottom: 1px solid #cccccc;
			padding-bottom: 3px;'>
				<b>#{$fa_po[$n]['id']}</b>
				<b>{$fa_po[$n]['autor']}</b> {$fa_po[$n]['time-post']}
				Тип: {$fa_po[$n]['type']}
		</div></td>
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
			</tr>";

// Комментарии
		$postcomm = $db->prepare("
		SELECT * 
		FROM `testcom`
		WHERE `idop`=?");
		$postcomm->BindParam(1, $fa_po[$n]['id']);
//Комментарий привязан к своему посту через id этого поста
		$postcomm->execute();
		$f_poco = $postcomm->fetchAll(PDO::FETCH_ASSOC);
//При наличии массива с нужными id создается ветвь комментариев
//Создание ветки комментариев под постом	
		if($f_poco)
			{
				$fc = count($f_poco);
				$b=0;
				print "
					<tr>
						<td colspan='2' style='background: LightGray;'><details><summary>Комментарии {$fc}</summary>";
				while ($fc != $b)
					{
						print"
							<span>
							<b>{$f_poco[0+$b]['comname']}: </b>
							{$f_poco[0+$b]['message']}<span><br>";
						$b++;
					}
//var_dump($f_poco[$n]['message']);
//$b=0;
				/*print "<input size='20' placeholder='150' type='text'>
							<button type='sumbit'>Ответить на комментарий</button>
							</details>
							</td>
						</tr>";*/
			}
//Форма ответа на пост
//Берет:
//	$fa_po[$n]['id'] - id поста, для передаче и привязке к нему (посту) комментария

		echo "<tr>
				<td colspan='2'>
					<input form='answ' name='{$n}' size='30%' placeholder='150' type='text'>
					<button form='answ' name='btn' type='submit' value='{$fa_po[$n]['id']}'>Ответить</button>
				</td>
			</tr>
			</table>";
		$n++;

//Получение данных из формы ответа
if ($_POST['btn'] and $_POST[$n-1])
	{

		$postid = $_POST['btn']; 
//Оригинальный id поста, пердается при нажатии кнопки "Ответ"
		$login = $_SESSION['login'];
//логин из сессии для записи комментируещего
		$mess = $_POST[$n-1];
//Текст сообщения

		$addcomm = $db->prepare("
		INSERT INTO `testcom`
		(`idop`, `comname`, `message`) 
		VALUES (?, ?, ?)");
		$addcomm->BindParam(1, $postid);
		$addcomm->BindParam(2, $login);
		$addcomm->BindParam(3, $mess);
		$addex = $addcomm->execute();

		header('Location: test.php');
		//exit;
	}

		echo "</table>";
		$n++;
	}


/* часть тестовой таблицы
$countOfPosts = count($f_post);
$n = 0;

while ($n < $countOfPosts)
	{	
		echo "<table cellpadding='7px' 
			style='
			margin: 10px;
			border: 2px solid black;
			width: 20%;'>";
		print "
		<tr>
			<td><b>{$f_post[$n]['autor']}</b> пишет:</td>
		</tr>
		<tr>
		<td>{$f_post[$n]['id']} - {$f_post[$n]['letter']}</td>
		</tr>";

// Комментарии
		$postcomm = $db->prepare("
		SELECT * 
		FROM `testcom`
		WHERE `idop`=?");
		$postcomm->BindParam(1, $f_post[$n]['id']);
//Комментарий привязан к своему посту через id этого поста
		$postcomm->execute();
		$f_poco = $postcomm->fetchAll(PDO::FETCH_ASSOC);
//При наличии массива с нужными id создается ветвь комментариев
//Создание ветки комментариев под постом	
		if($f_poco)
			{
				$fc = count($f_poco);
				$b=0;
				print "
					<tr>
						<td style='background: LightGray;'><details><summary>Комментарии {$fc}</summary>";
				while ($fc != $b)
					{
						print"
							<span>
							<b>{$f_poco[0+$b]['comname']}: </b>
							{$f_poco[0+$b]['message']}<span><br>";
						$b++;
					}
//var_dump($f_poco[$n]['message']);
//$b=0;
				print "<input size='15' placeholder='150' type='text'>
							<button type='sumbit'>Ответить</button>
							</details>
							</td>
						</tr>";
			}
//Форма ответа на пост
//Берет:
//	$f_post[$n]['id'] - id поста, для передаче и привязке к нему (посту) комментария

		echo "<tr>
				<td>
					<input form='answ' name='{$n}' style='size:auto;' placeholder='150' size='auto' type='text'>
				</td>
				<td>
					<button form='answ' name='btn' type='submit' value='{$f_post[$n]['id']}'>Ответить</button>
				</td>
			</tr>
			</table>";
		$n++;

//Получение данных из формы ответа
if ($_POST['btn'] and $_POST[$n-1])
	{

		$postid = $_POST['btn']; 
//Оригинальный id поста, пердается при нажатии кнопки "Ответ"
		$login = $_SESSION['login'];
//логин из сессии для записи комментируещего
		$mess = $_POST[$n-1];
//Текст сообщения

		$addcomm = $db->prepare("
		INSERT INTO `testcom`
		(`idop`, `comname`, `message`) 
		VALUES (?, ?, ?)");
		$addcomm->BindParam(1, $postid);
		$addcomm->BindParam(2, $login);
		$addcomm->BindParam(3, $mess);
		$addex = $addcomm->execute();

		header('Location: test.php');
		//exit;
	}
}*/
	
?>
</body>
</html>