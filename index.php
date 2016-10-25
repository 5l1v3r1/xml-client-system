<?php
include 'auth/php/constants.php';
include 'auth/php/includes.php';

if(!isset($_SESSION['username']) || !file_exists('users/' . $_SESSION['username'] . '.xml')){
	header("Location: login.php");
}

if (substr($_SERVER["REQUEST_URI"], -5) == "index.php") {
	header("Location: /");
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Logged in!</title>
</head>
<body>
	<h1>Successfully logged in!</h1>
	<h2>Welcome, <?php echo $_SESSION['username']; ?></h2>

	<hr />

	<a href="changepassword.php">Change Password</a> - <a href="logout.php">Logout</a>
</body>
</html>