<?php

if (isset($_GET['reset']) && isset($_POST['username']) && isset($_POST['email'])) {

	$raw_username = $_POST['username'];
	$raw_email = $_POST['email'];

	// authenticate
	if (!file_exists("users/".$raw_username.".xml")) {
		$error[] = "Username [".htmlspecialchars($raw_username)."] doesn't exist.";
	}
	if (!filter_var($raw_email, FILTER_VALIDATE_EMAIL)) {
		if (isset($raw_email)) {
			$error[] = "Email [".htmlspecialchars($raw_email)."] is invalid.";
		}
	}
	$files = glob('users/*.xml');
	foreach($files as $file){
		$xml = new SimpleXMLElement($file, 0, true);
		$existing_emails[] = $xml->email;
	}
	if (in_array($raw_email, $existing_emails)) {
		// email is good
	} else {
		$error[] = "That email address isn't associated with any accounts.";
	}
	if (file_exists("users/".$raw_username.".xml")) {
		$xml = new SimpleXMLElement("users/".$raw_username.".xml", 0, true);
		if ($xml->email != $raw_email) {
			if (isset($error) && in_array("That email address isn't associated with any accounts.", $error)) {
				// do nothing, error already defined.
			} else {
				$error[] = "Supplied email doesn't match the username [".htmlspecialchars($raw_username)."].";
			}
		}
	}

	if (!isset($error)) {
		// all details must be good
		
	}

}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Forgot password?</title>
	<link rel="icon" type="image/png" href="../assets/images/logo.png">

	<link rel="stylesheet" type="text/css" href="../assets/css/main.css">
	<link rel="stylesheet" type="text/css" href="../assets/css/materialize.min.css">
</head>
<body>

<div class="container"><center>
	<h2>UIL Computer Science Techie's!</h2>
	<strong>reset account password</strong>
	<div class="row">
		<form class="col s12" action="<?php echo $_SERVER['PHP_SELF']; ?>?reset" method="post">
			<div class="row">
				<?php
				if (isset($error)) {
				?>
					<div class="col s12">
						<blockquote>
							<ul>
							<?php
							foreach ($error as $e) {
								echo "<li>" . $e . "</li>";
							}
							?>
							</ul>
						</blockquote>
					</div>
				<?php
				}
				?>
				<div class="input-field col s6">
					<input id="username" name="username" type="text" class="validate" required="" autofocus="">
					<label for="username">Username associated with account:</label>
				</div>
				<div class="input-field col s6">
					<input id="email" name="email" type="email" class="validate" required="">
					<label for="email">Email associated with account:</label>
				</div>
			</div>

			<button type="submit" class="btn waves-effect waves-light" style="width: 25%;">request reset</button><br /><br />
			<a href="dev_docs/signup">Setup a new account!</a>
		</form>
	</div>
</center></div>

<script type="text/javascript" src="../assets/js/jquery.min.js"></script>
<script type="text/javascript" src="../assets/js/materialize.min.js"></script>
<script>
$("body").on("keydown", function(e) {
	if (e.which !== 9 && e.keyCode !== 9) {
	return;
}
	// console.log("Which Value:", e.which);
	// console.log("KeyCode Value:", e.keyCode)

	e.preventDefault();
	// console.log($('#username').is(":focus") + " | " + $('#email').is(":focus"));
	if ($('#username').is(":focus")) {
		$('#email').focus();
	} else if ($('#email').is(":focus")) {
		$('#username').focus();
	} else {
		$('#username').focus();
	}
});
</script>
</body>
</html>