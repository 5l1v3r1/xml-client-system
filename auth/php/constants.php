<?php

$signup_lock = false; // Switch to "true" and fix the constant named 'signup_lock_password' to fit your password.
// This enables the password-protected signup on the register page.

/* Company/Business Information Organizing */
define('company_title', 'Your company title goes here!');


/* Below here is intended for developers, this is more of security and such */
define('algorithm', 'aes-256-cbc'); // this is the encryption algorithm used for creating/verifying user accounts
define('g_secret', ''); // this would be your Google Secret Code for the Google reCaptcha implementation
define('signup_lock_password', 'root'); // this is to lock the registration area to certain people of your choice, just share this password with them so they are able to signup!


?>