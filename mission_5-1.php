<?php // db connection
	$dsn = "mysql:dbname="; //サーバ情報
	$dbuser = "user";
	$dbpass = "passwd";
	$pdo = new PDO($dsn, $dbuser, $dbpass,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
?>
<?php
	$e_num = 0; // 編集以外は0
	$e_name = "";
	$e_comment = "";

	$message = "Enter your submission.";
	if (isset($_POST['flag'])) {
		if ($_POST['flag'] == 0) { //normal submission
			$e_num = $_POST['e_num'];
			if ($e_num == 0) {
				if ($_POST['name'] != "" && $_POST['comment'] != "" && $_POST['pass'] != "") {
					$name = $_POST['name'];
					$comment = $_POST['comment'];
					$pass = $_POST['pass'];
					$time = date("Y/m/d H:i:s");
					$sql = $pdo->prepare("insert into keijiban_tbl (name,comment,time,password) values (:name,:comment,:time,:password)");
					$sql->bindParam(':name', $name, PDO::PARAM_STR);
					$sql->bindParam(':comment', $comment, PDO::PARAM_STR);
					$sql->bindParam(':time', $time, PDO::PARAM_STR);
					$sql->bindParam(':password', $pass, PDO::PARAM_STR);
					$sql->execute();
					$message = "Your submission is completed.";
				} else {
					$message = "There is an empty item.";
				}
			} elseif($e_num > 0) { // 2nd phase of edit mode
				if ($_POST['name'] != "" && $_POST['comment'] != "" && $_POST['pass'] != "") {
					$name = $_POST['name'];
					$comment = $_POST['comment'];
					$pass = $_POST['pass'];
					$time = date("Y/m/d H:i:s");
					$sql = "select * from keijiban_tbl where id=".$e_num;
					$stmt = $pdo->query($sql);
					$resultset = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if ($resultset[0]['password'] == $pass) {
						$sql = $pdo->prepare("update keijiban_tbl set name=:name,comment=:comment,time=:time,password=:password where id=:id");
						$sql->bindParam(':name', $name, PDO::PARAM_STR);
						$sql->bindParam(':comment', $comment, PDO::PARAM_STR);
						$sql->bindParam(':time', $time, PDO::PARAM_STR);
						$sql->bindParam(':password', $pass, PDO::PARAM_STR);
						$sql->bindParam(':id', $e_num, PDO::PARAM_INT);
						$sql->execute();
						$message = "Your editting is completed.";
					} else {
						$message = "Password is not correct. Plese enter again from the edit number.";
					}
				} else {
					$message = "There is an empty item.";
				}
				$e_num = 0;
			}
		} elseif($_POST['flag'] == 1) { //edit mode
			if ($_POST['e_num'] != "") {
				$e_num = $_POST['e_num'];
				$sql = "select * from keijiban_tbl where id=".$e_num;
				$stmt = $pdo->query($sql);
				$resultset = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$e_name = $resultset[0]['name'];
				$e_comment = $resultset[0]['comment'];
				$message = "Your editing is processing. Plese enter edited submission.";
			} else {
				$message = "There is an empty item.";
			}
		} elseif ($_POST['flag'] == 2) {
			if ($_POST['d_num'] != "" && $_POST['d_pass'] != "") {
				$d_num = $_POST['d_num'];
				$d_pass = $_POST['d_pass'];
				$sql = "select * from keijiban_tbl where id=".$d_num;
				$stmt = $pdo->query($sql);
				$resultset = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if ($resultset[0]['password'] == $d_pass) {
					$sql = $pdo->prepare("delete from keijiban_tbl where id=:id");
					$sql->bindParam(':id', $d_num, PDO::PARAM_INT);
					$sql->execute();
					$message = "The submission No.".$d_num." is deleted.";
				} else {
					$message = "Password is not correct. Plese enter again from the delete number.";
				}
			} else {
				$message = "There is an empty item.";
			}																
		} elseif ($_POST['flag'] == 50) { // clear button
			$sql = $pdo->prepare("delete from keijiban_tbl;alter table keijiban_tbl auto_increment=1");
			$sql->execute();
			//$sql = $pdo->prepare("");
			//$sql->execute();
			$message = "Delete is completed.";

		}
	}
	//set @i := 0;update keijiban_tbl set id = (@i := @i +1);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>"keijiban"</title>
</head>

<body>
	<form action="mission_5-1.php" method="post">
		<input type="hidden" name="flag" value="50">
		<input type="submit" value="clear">
	</form>
	<?php
		echo ($message);
	?>
	<form action="mission_5-1.php" method="post"> <!-- write -->
		name:<input type="text" name="name" value="<?php echo $e_name ?>">
		comment:<input type="text" name="comment" value="<?php echo $e_comment ?>">
		password:<input type="password" name="pass" value="">
		<input type="hidden" name="flag" value=0>
		<input type="hidden" name="e_num" value="<?php echo $e_num ?>">
		<input type="submit" value="post">
	</form>
	<br>
	<form action="mission_5-1.php" method="post"> <!-- edit -->
		edit number:<input type="number" name="e_num" value="">
		<input type="hidden" name="flag" value=1>
		<input type="submit" value="edit">
	</form>
	<br>
	<form action="mission_5-1.php" method="post"> <!-- delete -->
		delete number:<input type="number" name="d_num" value="">
		password:<input type="text" name="d_pass" value="">
		<input type="hidden" name="flag" value=2>
		<input type="submit" value="delete">
	</form>
	<br>

	<?php
		$sql = "select * from keijiban_tbl";
		$stmt = $pdo->query($sql);
		$resultset = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if ($resultset[0] != "") {
			foreach ($resultset as $row) {
				echo $row['id'].":";
				echo $row['name']."  ".$row['time']."<br>";
				echo $row['comment']."<br>";
				echo "<hr>";
			}
		} else {
			echo ("There is no submission");
		}
		?>
</body>
</html>