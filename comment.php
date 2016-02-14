<?php 
	//connect database, set cookie and authority
	$db = new mysqli('localhost', 'root', '', 'toydb');
	if (mysqli_connect_errno()){
		echo 'Error: could not connect to database ';
		exit;
	}
	$ban = 1;
	if(isset($_COOKIE['user_id'])){
		$user_id = $_COOKIE['user_id'];
		$query = "select * from users where user_id = '$user_id'";
		$result = $db->query($query);
		if($result->num_rows){
			$row = $result->fetch_assoc();
			$ban = $row['is_ban'];
		}else {
			$query = "insert into users(user_id) values ('$user_id')";
			$result = $db->query($query);
		}
	} else {
		do{
			$user_id = rand();
			$query = "select * from users where user_id = '$user_id'";
			$result = $db->query($query);
		} while ($result->num_rows != 0);
		$ip = $_SERVER["REMOTE_ADDR"];
		$query = "insert into users(user_id, ip) values ('$user_id', '$ip')";
		$result = $db->query($query);
		setcookie('user_id', $user_id);
	}
	if (isset($_POST['comment']) && isset($_POST['text'])){
		$content = trim($_POST['text']);
		if(!get_magic_quotes_gpc()){
			$content = addslashes($content);
		}
		if ( $content){
			$query = "insert into comments(content, user_id) values ('$content', '$user_id')";
			$result = $db->query($query);
			if(!$result){
				echo "Error: could not add comment";
			}
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
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
<div align="center">
    <h1>message board</h1>
    <table border="0">
<?php
	if($ban){
		echo "<tr><td>you can not add message, contact manager please</td></tr>";
	} else {
		
		echo '<tr><td>input message:</td></tr>
    		<tr><td colspan="2"><textarea rows="3" name="text"></textarea></td></tr>
    		<tr><td><input type="submit" name="comment" value="comment">';
		
	}
	echo "<tr><td>&nbsp</td></tr>";
	
	$query = "select * from comments order by comment_id desc";
	$result = $db->query($query);
	$num_rows = $result->num_rows;
	for($i=0; $i<$num_rows; $i++){
		$row = $result->fetch_assoc();
		echo '<tr><td>'.$row['comment_id']."</td> <td> ".$row['date']."</td></tr>";
		echo '<tr><td colspan="2">'.$row['content']."</td></tr>";
		echo "<tr><td>&nbsp</td></tr>";
	}
?>
   </table>
</div>
</form>
</body>
</html>