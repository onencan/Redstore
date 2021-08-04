<?php include(INCLUDE_PATH . "/logic/common_functions.php"); ?>
<?php
require_once(INCLUDE_PATH."/logic/Auth.php");
require_once(INCLUDE_PATH."/logic/Util.php");

$auth = new Auth();
$db_handle = new DBController();
$util = new Util();

require_once(INCLUDE_PATH."/logic/authCookieSessionValidate.php");

// variable declaration
$username = "";
$email  = "";
$tel  = "";
$reside  = "";
$gender = "";
$errors  = [];

if ($isLoggedIn) {
	$util->redirect('./');
}

// SIGN UP USER
if (isset($_POST['signup_btn'])) {
	// validate form values
	$errors = validateUser($_POST, ['signup_btn']);

	// receive all input values from the form. No need to escape... bind_param takes care of escaping
	$username = $_POST['username'];
	$email = $_POST['email'];
	$tel = $_POST['tel'];
	$password = password_hash($_POST['password'], PASSWORD_DEFAULT); //encrypt the password before saving in the database
	$reside = $_POST['country'].', '.$_POST['city'];
	$gender= $_POST['gender'];
	$created_at = date('Y-m-d H:i:s');

	// if no errors, proceed with signup
	if (count($errors) === 0) {
		// insert user into database
		$query = "INSERT INTO users SET username=?, email=?, tel=?, password=?, location=?, gender=?, created_at=?";
		$stmt = $conn->prepare($query);
		$stmt->bind_param('sssssss', $username, $email, $tel, $password, $reside, $gender, $created_at);
		$result = $stmt->execute();
		if ($result) {
		  $user_id = $stmt->insert_id;
			$stmt->close();
			loginById($user_id); // log user in
		 } else {
			 $_SESSION['error_msg'] = "Database error: Could not register user";
		}
	 }
}

// USER LOGIN
if (isset($_POST['login_btn'])) {
	// validate form values
	$errors = validateUser($_POST, ['login_btn']);
	$username = $_POST['username'];
	$password = $_POST['password']; // don't escape passwords.

	if (empty($errors)) {
		$sql = "SELECT * FROM users WHERE username=? OR email=? LIMIT 1";
		$user = getSingleRecord($sql, 'ss', [$username, $username]);

		if (!empty($user)) { // if user was found
			if (password_verify($password, $user['password'])) { // if password matches
				// set login cookie
			setcookie("member_login", $username, $cookie_expiration_time);
            
            $random_password = $util->getToken(16);
            setcookie("random_password", $random_password, $cookie_expiration_time);
            
            $random_selector = $util->getToken(32);
            setcookie("random_selector", $random_selector, $cookie_expiration_time);
            
            $random_password_hash = password_hash($random_password, PASSWORD_DEFAULT);
            $random_selector_hash = password_hash($random_selector, PASSWORD_DEFAULT);
            
            $expiry_date = date("Y-m-d H:i:s", $cookie_expiration_time);
            
            // mark existing token as expired
            $userToken = $auth->getTokenByUsername($username, 0);
            if (! empty($userToken[0]["id"])) {
                $auth->markAsExpired($userToken[0]["id"]);
            }
            // Insert new token
            $auth->insertToken($username, $random_password_hash, $random_selector_hash, $expiry_date);

				loginById($user['id']);
			} else { // if password does not match
				$_SESSION['error_msg'] = "Wrong credentials";
			}
		} else { // if no user found
			$_SESSION['error_msg'] = "Wrong credentials";
		}
	}
}