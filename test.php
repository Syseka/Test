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

//Таблица с постами
$post = $db->prepare("
SELECT * 
FROM `testpost`");
	$post->execute();
	$f_post = $post->fetchAll(PDO::FETCH_ASSOC);
	
//Таблица с комментариями
$comm = $db->prepare("
SELECT * 
FROM `testcom`");
	$comm->execute();
	$f_comm = $comm->fetchAll(PDO::FETCH_ASSOC);

print "Логин: ".$_SESSION['login'];

/*
var_dump($f_post); print "<br><br><br>";
var_dump($f_comm); print "<br>";
*/

//Вывод постов и комментариев
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
/*Берет:
	$f_post[$n]['id'] - id поста, для передаче и привязке к нему (посту) комментария
*/
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

//Получение данных и зформы ответа
if ($_POST['btn'] and $_POST[$n-1])
	{
//$addex = 1;

	$postid = $_POST['btn']+0; 
//Оргинальный id поста, пердается при нажатии кнопки "Ответ"
//+0, так как изначально подается строка, вместо целого числа
	$login = $_SESSION['login'];
//логин из сессии для записи комментируещего
	$mess = $_POST[$n-1];
//Текст сообщения


		var_dump ($postid); print "<br>";
		var_dump ($login); print "<br>";
		var_dump ($mess); print "<br>";
//Вардампы отображаются с введенными данными и нужными типами

//эта часть не срабатывает:
		$addcomm = $db("
		INSERT INTO `testcom`
		(`idop`, `comname`, `message`) 
		VALUES (?, ?, ?)");
var_dump ($addcomm); print "<br>";
		$addcomm->BindParam(1, $postid);
		$addcomm->BindParam(2, $login);
		$addcomm->BindParam(3, $mess);
var_dump ($addcomm); print "<br>";
		$addex = $addcomm->execute();
		
$addex ? print "Yes" : print "No";
		header('Location: test.php');
		exit;
	}
/*elseif ($_POST['btn'] and empty($_POST[$n-1]) )
	{
		print "Надо вести сообщение.";
	}
		
var_dump($f_poco[$n]['message']); print"<br>";
var_dump($f_poco); print"<br>";
var_dump($b); print"<br>";*/
	}
	
?>
</body>
</html>