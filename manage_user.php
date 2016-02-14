<?php 
	session_start();
	$db = new mysqli('localhost', 'root', '', 'toydb');
	if (mysqli_connect_errno()){
		echo 'Error: could not connect to database ';
		exit;
	}
	$is_admin = 0;
	if (isset($_SESSION['valid_user'])){
		$is_admin = 1;
	}
	if ($is_admin){
		if (isset($_POST['ban_cookie']) && isset($_POST['select'])){
			$select = $_POST['select'];
			foreach ($select as $element){
				$query = 'update users set is_ban=1 where user_id='.$element;
				$db->query($query);
			}
		} elseif (isset($_POST['allow_cookie']) && isset($_POST['select'])){
			$select = $_POST['select'];
			foreach ($select as $element){
				$query = 'update users set is_ban=0 where user_id='.$element;
				$db->query($query);
			}
		} elseif (isset($_POST['log_out'])){
			unset($_SESSION['valid_user']);
			session_destroy();
			$is_admin = 0;
		}
	}
?>
<html>
<head>
<title>manage user</title>
</head>
<body>
<div align="center">
<h1>manage user</h1>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
<?php
	if ($is_admin){	
		
		echo '<table>';
		echo '<tr><td><input type="submit" name="ban_cookie" value="ban cookie"></td>';
		echo '<td><input type="submit" name="allow_cookie" value="allow cookie"></td>';
		echo '<td><input type="submit" name="log_out" value="log out"></td></tr>';
		$query = 'select * from users order by time desc';
		$result = $db->query($query);
		$num_rows = $result->num_rows;
		for($i=0; $i<$num_rows; $i++){
			$row = $result->fetch_assoc();
			echo '<tr><td><input type="checkbox" name="select[]" value="'.$row['user_id'].'"> </td>';
			$authority = $row['is_ban']? "banned" : "allowed";
			echo '<td>'.$row['user_id'].' '.$authority.'</td></tr>';
			echo '<tr><td>'.$row['ip'].'</td><td>'.$row['time'].'</td></tr>';
			echo '<tr><td>&nbsp</td></tr>';
		}
		echo '</table>';
	} else {
		echo '<strong>you have not authorizaton';
	}
?>
	<input type="button" name="return" value="return" onclick="window.location.href='login.html'">
	
</form>
</div>
</body>
</html>
