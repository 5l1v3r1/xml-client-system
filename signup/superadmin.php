<?php
session_start();
if (!isset($_SESSION['attempts'])) {
	$_SESSION['attempts'] = 0;
}
if (isset($_SESSION['attempts']) && $_SESSION['attempts'] > 3) {
	die("You've been banned.");
}
if (isset($_POST['auth'])) {
	if ($_POST['username'] == "root" && $_POST['password'] == "toor") {
		$_SESSION['superadmin'] = true;
		$message = "<strong style='color: green;'>success, superadmin permissions granted.</strong>";
	} else {
		$_SESSION['superadmin'] = false;
		$_SESSION['attempts']++;
		$message = "<strong style='color: red;'>incorrect username/password.</strong>";
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Super Admin Access</title>
</head>
<body>

<center>
	<?php if (isset($message)) { echo $message; } ?>
	<?php if (isset($_SESSION['superadmin'])) { if ($_SESSION['superadmin'] == true) { echo "<strong style='color: green;'>superadmin permissions already granted.</strong>"; } } ?>
	<form action="" method="post">
		<label for="user">Username: </label>
		<input type="text" name="username" id="user" required="" autofocus="" />
		
		<br />

		<label for="pass">Password: </label>
		<input type="password" name="password" id="pass" required="" />

		<br />
		
		<button type="submit" name="auth">login</button>
	</form>
</center>

</body>
</html>