<?php
// verify
session_start ();

$db = new mysqli ( 'localhost', 'root', '', 'toydb' );
if (mysqli_connect_errno ()) {
	echo 'Error: could not connect to database';
	exit ();
}

$is_admin = 0;
if (isset ( $_POST ['username'] ) && isset ( $_POST ['password'] )) {
	$uname = $_POST ['username'];
	$pword = $_POST ['password'];
	
	$query = "select * from admins where admin_id='$uname' and password=sha1('$pword')";
	$result = $db->query ( $query );
	if ($result->num_rows) {
		$_SESSION ['valid_user'] = $uname;
		$is_admin = 1;
	}
} elseif (isset($_SESSION['valid_user'])){
	$is_admin = 1;
}
?>
<?php

if ($is_admin) {
	if (isset ( $_COOKIE ['user_id'] )) {
		$user_id = $_COOKIE ['user_id'];
		$query = "select * from users where user_id = '$user_id'";
		$result = $db->query ( $query );
		if ($result->num_rows) {
			$row = $result->fetch_assoc ();
			$query = 'update users set is_ban=0 where user_id=' . $user_id;
			$db->query ( $query );
		} else {
			$query = "insert into users(user_id, is_ban) values ('$user_id', 0)";
			$db->query ( $query );
		}
	} else {
		do {
			$user_id = rand ();
			$query = "select * from users where user_id = '$user_id'";
			$result = $db->query ( $query );
		} while ( $result->num_rows != 0 );
		$ip = $_SERVER ["REMOTE_ADDR"];
		$query = "insert into users(user_id, ip, is_ban) values ('$user_id', '$ip', 0)";
		$result = $db->query ( $query );
		setcookie ( 'user_id', $user_id );
	}
	
	if (isset ( $_POST ['comment'] )) {
		if (isset ( $_POST ['text'] )) {
			$content = trim ( $_POST ['text'] );
			if (! get_magic_quotes_gpc ()) {
				$content = addslashes ( $content );
			}
			if ($content) {
				$query = "insert into comments(content, user_id) values ('$content', '$user_id')";
				$result = $db->query ( $query );
				if (! $result) {
					echo "Error: could not add comment";
				}
			}
		}
	} elseif (isset ( $_POST ['delete'] ) && isset ( $_POST ['select'] )) {
		$select = $_POST ['select'];
		foreach ( $select as $element ) {
			$query = 'delete from comments where comment_id=' . $element;
			$db->query ( $query );
		}
	} elseif (isset ( $_POST ['ban'] ) && isset ( $_POST ['select'] )) {
		$select = $_POST ['select'];
		foreach ( $select as $element ) {
			$query = 'update users, comments set users.is_ban=1 where users.user_id=comments.user_id and comments.comment_id=' . $element;
			$db->query ( $query );
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
<title>message board</title>
<script type="text/javascript">
        function goback(){
            window.history.back();
        }
    </script>

</head>
<body>
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
		method="post">
		<div align="center">
			<h1>message board</h1>;
   
<?php
	if ($is_admin) {
		echo '<table border="0">';
		echo '<tr><td>input message:</td></tr>
			<tr><td colspan="2"><textarea rows="3" name="text"></textarea></td></tr>
			<tr><td><input type="submit" name="comment" value="comment">';
		
		echo "<tr><td>&nbsp</td></tr>";
		echo '<tr><td><input type="submit" name="delete" value="delete"></td>';
		echo '<td><input type="submit" name="ban" value="ban"></td></tr>';
		echo '<tr><td><input type="button" name="manage_user" value="manage user"
			 onclick="window.location.href=\'manage_user.php\'"></td></tr>';
		echo '<td><input type="submit" name="log_out" value="log out"></td></tr>';
		echo "<tr><td>&nbsp</td></tr>";
	
		$query = "select * from comments order by comment_id desc";
		$result = $db->query ( $query );
		$num_rows = $result->num_rows;
		for($i = 0; $i < $num_rows; $i ++) {
			$row = $result->fetch_assoc ();
			echo '<tr><td><input type="checkbox" name="select[]" value="' 
					. $row ['comment_id'] . '">' . $row ['comment_id'] 
			. "</td><td>user id:".$row['user_id']."  </td> <td> &nbsp" . $row ['date'] . "</td></tr>";
			echo '<tr><td colspan="3">' . $row ['content'] . "</td></tr>";
			echo "<tr><td>&nbsp</td></tr>";
		}
		echo '</table>';
	} else {
		echo '<strong>you have not authorizaton';
	}
?>
	<input type="button" name="return" value="return"
				onclick="window.location.href='login.html'">


		</div>
	</form>
</body>
</html>