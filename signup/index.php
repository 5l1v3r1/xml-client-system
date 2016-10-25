<?php
include '../auth/php/constants.php';
include '../auth/php/includes.php';

($signup_lock == false) ? $_GET['acode'] = signup_lock_password : $continue = true;

if (isset($attempts) && $attempts > 3) {
	if (!isset($_SESSION['banned'])) {
		$_SESSION['banned'] = true;
	}
}

if (isset($_SESSION['banned']) && $_SESSION['banned'] == true) {
	die("We've detected spam! You've been banned!");
}

include '../auth/class/class.tools.php';
include '../auth/class/class.encryption.php';

if (isset($_GET['acode']) && $_GET['acode'] == signup_lock_password) { // my super duper secret password
	$_SESSION['valid'] = true;
}

if (!isset($_SESSION['valid'])) {
?>
<!-- not valid -->
<!DOCTYPE html>
<html>
<head>
	<title>Access Permissions</title>
</head>
<body>

<center>
<?php if (isset($_GET['acode']) && $_GET['acode'] != signup_lock_password) { echo "<strong style='color: red;'>Incorrect Password</strong>"; } ?>
<form action="" method="get">
	<label>Access Code: </label><input type="text" name="acode" required="" autofocus="" />
	<button type="submit">submit</button>
</form>
</center>

</body>
</html>
<?php
die();
} else if (isset($_SESSION['valid']) && $_SESSION['valid'] == true) {
?>
<!-- valid -->
<?php
}

if (isset($_GET['register'])) {
	error_reporting(0);

	$g_recaptcha_response = $_POST['g-recaptcha-response'];
	
	$response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".g_secret."&response=".$g_recaptcha_response."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
	
	if($response['success'] == false) {
	
		$attempts++;
		$error[] = "Google reCaptcha couldn't be verified!";
	
	} else {
		// client is good :)
	}

	/*
	Request the client/user's:
	First name,
	Last name,
	Email,
	Password,
	Confirm password.
	*/
	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	$raw_username = $_POST['username'];
	$raw_password = $_POST['password'];
	$raw_conf_password = $_POST['password_conf'];
	$email = $_POST['email'];

	// Let's authenticate some data
	if (!isset($fname)) {
		$error[] = "First name is empty.";
	}
	if (!isset($lname)) {
		$error[] = "Last name is empty.";
	}
	if (!isset($raw_username)) {
		$error[] = "Username is empty.";
	}
	if (!isset($email)) {
		$error[] = "Email is empty.";
	}
	if (isset($raw_password) && $raw_password == $raw_conf_password) {
		# password check out
	} else if (!isset($raw_password)) {
		$error[] = "Password is empty.";
	} else if (!isset($raw_conf_password)) {
		if (!isset($raw_password)) {
			# do nothing, error already stated
		} else if (isset($raw_password)) {
			$error[] = "Confirmation password is empty.";
		}
	}

	// check string length and validity
	if (isset($fname) && strlen($fname) < 3) {
		$error[] = "First name is not long enough.";
	}
	if (isset($lname) && strlen($lname) < 3) {
		$error[] = "Last name is not long enough.";
	}
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		if (isset($email)) {
			$error[] = "Email [".htmlspecialchars($email)."] is invalid.";
		}
	}
	if (isset($raw_password) && strlen($raw_password) < 8) {
		$error[] = "Password need to be at least 8 characters.";
	}

	// check for pre-existing information
	if (isset($raw_username) && file_exists("../users/" . $raw_username . ".xml")) {
		$error[] = "Username already exists, would you like to <a href='../login.php'>login</a>";
	}
	$users = glob('../users/*.xml');
	foreach($users as $u){
		$xml = new SimpleXMLElement($u, 0, true);
		
		$pre_users[] = basename($file, '.xml');
		$pre_emails[] = $xml->email;
		$pre_ips[] = $xml->creation_ip;
	}
	if (in_array($email, $pre_emails)) {
		if (in_array("Username already exists.", $error)) {
			# code...
		} else if (!in_array("Username already exists.", $error)) {
			$error[] = "Whoops, that email address belongs to somebody! Click <a href='../forgot.php'>here</a> to reset your password.";
		}
	}
	if (!isset($error) && in_array($_SERVER['REMOTE_ADDR'], $pre_ips)) {
		if (isset($_SESSION['superadmin']) && $_SESSION['superadmin'] == true) {
			// client is super admin, they can create unlimited number of accounts!
		} else {
			$error[] = "You've created an account previously. Go <a href='../login.php'>login</a>. Click <a href='superadmin.php'>here</a> to request super admin access.";
		}
	}

	// after this, all data should be not null
	$random = new random();
	$hashlib = new hash();
	$crypt = new crypt();
	// generate some encryption details
	$salt = $random->string(74); // randomized salt
	$raw_iv = $random->string(16);

	// $iv = $hashlib->hash($raw_iv, 'sha512');

	if (!isset($error)) {
		date_default_timezone_set('UTC');

		$raw_enc_password = $crypt->encrypt_string($raw_password, 'aes-256-cbc', $salt, $raw_iv); // encrypt the password
		$enc_password = base64_encode($raw_enc_password);
		file_put_contents("../auth/accounts/keys/".htmlspecialchars($raw_username).".encryption.salt", $salt); // save the salt
		file_put_contents("../auth/accounts/keys/".htmlspecialchars($raw_username).".encryption.iv", $raw_iv); // save the iv

		// echo $enc_password;
		// create a user record
		$xml = new SimpleXMLElement('<user></user>');
		$xml->addChild('fname', $fname);
		$xml->addChild('lname', $lname);
		$xml->addChild('username', $raw_username);
		$xml->addChild('email', $email);
		$xml->addChild('password', $enc_password);
		$xml->addChild('creation_ip', $_SERVER['REMOTE_ADDR']);
		$xml->addChild('creation_date', date("D M j g:i:s T Y"));
		$xml->asXML('../auth/accounts/users/' . $raw_username . '.xml');

		header("Location: ../");

	}

	// for testing purposes, let's print the details
	/*
	if (!isset($error)) {
		echo htmlspecialchars('First name: '.$fname);
		echo "<br />";
		echo htmlspecialchars('Last name: '.$lname);
		echo "<br />";
		echo htmlspecialchars('Username: '.$raw_username);
		echo "<br />";
		echo htmlspecialchars('Email: '.$email);
		echo "<br />";
		echo htmlspecialchars('Password: '.$raw_password);
		echo "<br />";
		echo htmlspecialchars('Confirm password: '.$raw_conf_password);
		echo "<br />";
		echo 'Encryption salt: '.$salt;
		echo "<br />";
		echo 'Encryption IV: '.$iv;
	} else {
		echo 'Encryption salt: '.$salt.'<br />';
		echo 'Encryption IV: '.$iv.'<br />';
		echo '<br /><br />'.strlen($salt);
		echo '<br /><br />'.strlen($iv);
	}
	*/
}

?>
<!--
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?register" method="post">
	<label>First name: </label><input type="text" name="fname" required=""><br />
	<label>Last name: </label><input type="text" name="lname" required=""><br />
	<label>Username: </label><input type="text" name="username" required=""><br />
	<label>Email: </label><input type="email" name="email" required=""><br />
	<label>Password: </label><input type="password" name="password" required=""><br />
	<label>Confirm password: </label><input type="password" name="password_conf" required=""><br />

	<button type="submit">register</button>
	<button type="reset">reset</button>
</form>
-->


<!DOCTYPE html>
<html>
<head>
	<title>Register as a UIL Techie!</title>
	<link rel="stylesheet" type="text/css" href="../assets/css/materialize.min.css">

	<!-- Google reCaptcha -->
	<script src='https://www.google.com/recaptcha/api.js'></script>
</head>
<body>

<div class="container"><center>
	<h2>Register Today ~ <?php echo company_title; ?>!</h2>
	<div class="row">
		<form class="col s12" action="<?php echo $_SERVER['PHP_SELF']; ?>?register" method="post">
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
				<div class="row">
					<div class="input-field col s6">
						<input id="fname" name="fname" type="text" class="validate" required="" minlength="3" autofocus="">
						<label for="fname">First name:</label>
					</div>
					<div class="input-field col s6">
						<input id="lname" name="lname" type="text" class="validate" required="" minlength="3">
						<label for="lname">Last name:</label>
					</div>
				</div>
				<div class="row">
					<div class="input-field col s6">
						<input id="username" name="username" type="text" class="validate" required="">
						<label for="username">Username:</label>
					</div>
					<div class="input-field col s6">
						<input id="email" name="email" type="email" class="validate" required="">
						<label for="email">Email:</label>
					</div>
				</div>
				<div class="row">
					<div class="input-field col s6">
						<input id="keypass" name="password" type="password" class="validate" minlength="8" title="Password min 8 characters. At least one UPPERCASE and one lowercase letter" pattern="(?=^.{8,}$)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$" required="">
						<label for="keypass">Password:</label>
					</div>
					<div class="input-field col s6">
						<input id="keypass_confirm" name="password_conf" type="password" class="validate" onchange="check(this)" required="">
						<label for="keypass_confirm">Confirm password:</label>
					</div>
				</div>
				<div class="row">
					<div class="col s12">
						<div class="g-recaptcha" data-sitekey="6LdYLQgUAAAAAKgGnOhrtu5PL7n_fhIFoWHFQ2AA"></div>
					</div>
				</div>
			</div>

			<button type="submit" class="btn waves-effect waves-light" style="width: 25%;">Register!</button><br /><br />
		</form>
	</div>
</center></div>

<script type="text/javascript" src="../assets/js/jquery.min.js"></script>
<script type="text/javascript" src="../assets/js/materialize.min.js"></script>
<script>
function check(input) {
	if (input.value != document.getElementById('keypass').value) {
		input.setCustomValidity('Password must be the same.');
	} else {
		// input is valid -- reset the error message
		input.setCustomValidity('');
	}
}
</script>
</body>
</html>