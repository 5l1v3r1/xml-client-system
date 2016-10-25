<?php
include 'auth/php/constants.php';
include 'auth/php/includes.php';

// $pass = hash('sha256', $pass);

if(isset($_GET['login'])){

	$username = htmlspecialchars($_POST['username']);
	$raw_password = $_POST['password'];

	if(file_exists('auth/accounts/users/' . $username . '.xml')){
		// user exist!
		$xml = new SimpleXMLElement('auth/accounts/users/' . $username . '.xml', 0, true);

		$algorithm = algorithm;
		$salt = file_get_contents("auth/accounts/keys/".$username.".encryption.salt");
		$iv = file_get_contents("auth/accounts/keys/".$username.".encryption.iv");

		$encrypted_password = openssl_encrypt ($raw_password, $algorithm, $salt, true, $iv);
		// echo $encrypted_password;

		$stored_password = $xml->password;
		$raw_stored_password = base64_decode($stored_password);

		$decrypted_password = openssl_decrypt ($encrypted_password, $algorithm, $salt, true, $iv);
		$decrypted_stored_password = openssl_decrypt ($raw_stored_password, $algorithm, $salt, true, $iv);

		if($decrypted_password == $decrypted_stored_password){
			session_start();
			$_SESSION['is_authed'] = true;
			$_SESSION['username'] = $xml->username;
			header("Location: index.php");
		} else {
			$error[] = "Invalid password.";
		}
	} else {
		$error[] = "Username doesn't exist.";
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Admin cPanel Login</title>

	<link rel="stylesheet" type="text/css" href="assets/css/materialize.min.css">
</head>
<body>

<div class="container"><center>
	<h2><?php echo company_title; ?></h2>
	<div class="row">
		<form class="col s12" action="<?php echo $_SERVER['PHP_SELF']; ?>?login" method="post">
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
					<label for="username">Username:</label>
				</div>
				<div class="input-field col s6">
					<input id="keypass" name="password" type="password" class="validate" required="">
					<label for="keypass">Password:</label>
				</div>
			</div>

			<button type="submit" class="btn waves-effect waves-light" style="width: 25%;">Login!</button><br /><br />
			<a href="signup">Setup a new account!</a>
		</form>
	</div>
</center></div>

<script type="text/javascript" src="assets/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/js/materialize.min.js"></script>
<script>
$("body").on("keydown", function(e) {
	if (e.which !== 9 && e.keyCode !== 9) {
	return;
}
	// console.log("Which Value:", e.which);
	// console.log("KeyCode Value:", e.keyCode)

	e.preventDefault();
	// console.log($('#username').is(":focus") + " | " + $('#keypass').is(":focus"));
	if ($('#username').is(":focus")) {
		$('#keypass').focus();
	} else if ($('#keypass').is(":focus")) {
		$('#username').focus();
	} else {
		$('#username').focus();
	}
});
</script>
</body>
</html>