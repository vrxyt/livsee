<?php

/*
 * ---------------------------------------------------------------
 * User functions
 * ---------------------------------------------------------------
 *
 * This page Is all the user-based actions such as login, logout,
 * session verification, etc. This needs to be included on any page.
 * 
 * TODO:
 *
 *     -Clean up functions with too many nested if statements
 *     -General code clean up
 *	   -Maybe make exception codes mean something.
 *
 */

class user extends database {

	public $user_table = 'users';

	/* 	Login Functions	 */

	// Check if the cookie is valid. If valid, set as session. This prevents deleted users with a cookie from being able to log in the site.
	public function session_check($email) {
		if (empty($_SESSION['authenticated']) && $_COOKIE['rememberMe'] === true && !empty($email)) {
			$_SESSION['authenticated'] = $email;
		}
		$params = array($email);
		$sql = "SELECT * FROM $this->user_table WHERE email = $1";
		$result = pg_query_params($this->link, $sql, $params);
		if ($result === false) {
			$message = 'Error in: class:user | function:session_check';
			$code = 1;
			throw new Exception($message, $code);
		}
		$row_cnt = pg_num_rows($result);
		return $row_cnt < 1 ? false : true;
	}

	// Do stuff, I guess? right now only checks if logout was used. This needs to be expanded
	public function session_authenticate($email, $action = null) {
		if ($action == 'logout') {
			session_destroy();
			setcookie('rememberMe', null, -1, '/');
			setcookie('email', null, -1, '/');
			return false;
		}
		return true;
	}

	// Process the login, set the session and cookies (if desired)
	public function login($email, $password) {
		$params = array($email);
		$sql = "SELECT * FROM $this->user_table WHERE email = $1";
		$result = pg_fetch_assoc(pg_query_params($this->link, $sql, $params));
		if ($result === false) {
			$message = 'Error in: class:user | function:login';
			$code = 1;
			throw new Exception($message, $code);
		}
		$hash = $result['password'];
		$verified = $result['verified'];
				if ($verified === '1') {
			if (password_verify($password, $hash)) {
				$status = 'Login successful.';
				$_SESSION['authenticated'] = $email;
				if ($_POST['rememberMe'] === 'true') {
					setcookie('rememberMe', true, time() + 31000000);
					setcookie('email', $email, time() + 31000000);
				}
				header('Location: index.php');
			} else {
				$status = 'Login failed.';
			}
		} else {
			$status = 'Account not verified/account doesn\'t exist.';
		}
		return $status;
	}

	// create a new account and send registration verification email
	public function register($email, $password, $displayname) {
		$emailcheck = $this->emailcheck($email);
		if ($emailcheck === true) {
			return 'Account already exists!';
		}

		$dncheck = $this->dncheck($email, $displayname);
		if ($dncheck === true) {
			return 'Display name already exists!';
		}

		$authcode = random_int(100000, 999999);
		$hash = password_hash($password, PASSWORD_DEFAULT);
		$sql = "INSERT INTO $this->user_table (email, password, auth_code, verified, channel_name, channel_title, display_name) VALUES ($1, $2, $authcode, 0, $3, $4, $5)";
		$params = array($email, $hash, "$displayname's channel", "Welcome to $displayname's stream!", $displayname);
		$result = pg_query_params($this->link, $sql, $params);
		if ($result === false) {
			$message = 'Error in: class:user | function:register';
			$code = 1;
			throw new Exception($message, $code);
		}
		// emailbefore.html and emailafter.html are used as the main email, and the line for the verify link is added separately
		$subject = 'DM Stream Account Verification';
		$message = file_get_contents('inc/emailbefore.html');
		$message .= "<a href=\"$furl/verify.php?email=$email&c=$authcode\" style=\"color: #fff!important;padding: 12px 24px;font-size: 29px;line-height: 1.3333333;border-radius: 3px;background-color: #df691a;border-color: transparent;display: inline-block;margin: auto;font-weight: normal;text-align: center;vertical-align: middle;touch-action: manipulation;cursor: pointer;background-image: none;border: 1px solid transparent;white-space: nowrap;-webkit-user-select: none;text-decoration: none;\">Verify Account</a>";
		$message .= file_get_contents('inc/emailafter.html');
		$headers = "From: DM Stream <noreply@rirnef.net>\r\n";
		$headers .= "Reply-To: issues@rirnef.net\r\n";
		$headers .= 'X-Mailer: PHP/' . phpversion();
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
		mail($email, $subject, $message, $headers);
		return true;
	}

	// verify account from verification link in registration email
	public function verify($email, $code) {
		$params = array($email, $code);
		$sql = "SELECT * FROM $this->user_table WHERE email = $1 AND auth_code = $2";
		$result = pg_query_params($this->link, $sql, $params);
		$row_cnt = pg_num_rows($result);
		if ($row_cnt >= 1) {

			// simple function to generate stream key. TODO: Make this less predicatble
			function generateRandomString($length = 10) {
				$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$charactersLength = strlen($characters);
				$randomString = '';
				for ($i = 0; $i < $length; $i++) {
					$randomString .= $characters[rand(0, $charactersLength - 1)];
				}
				return $randomString;
			}

			$streamkey = generateRandomString();

			$params = array($streamkey, $email, $code);
			$sql = "UPDATE $this->user_table SET auth_code = null, verified = 1, stream_key = $1 WHERE email = $2 AND auth_code = $3";
			$check = pg_query_params($this->link, $sql, $params);
			if ($check === false) {
				$message = 'Error in: class:user | function:verify';
				$code = 5;
				throw new Exception($message, $code);
			} else {
				$status = 'true';
			}
		} else {
			$status = 'Invalid email/authentication code!';
		}
		return $status;
	}

	// Check if email exists
	public function emailcheck($email) {
		$params = array($email);
		$sql = "SELECT * FROM $this->user_table WHERE email = $1";
		$result = pg_query_params($this->link, $sql, $params);
		if ($result === false) {
			$message = 'Error in: class:user | function:emailcheck';
			$code = 1;
			throw new Exception($message, $code);
		}
		$row_cnt = pg_num_rows($result);
		return $row_cnt >= 1 ? true : false;
	}

	// Check if display name exists
	public function dncheck($email, $displayname) {
		$params = array($displayname, $email);
		$sql = "SELECT * FROM $this->user_table WHERE display_name = $1 AND email != $2";
		$result = pg_query_params($this->link, $sql, $params);
		if ($result === false) {
			$message = 'Error in: class:user | function:dncheck';
			$code = 1;
			throw new Exception($message, $code);
		}
		$row_cnt = pg_num_rows($result);
		return $row_cnt >= 1 ? true : false;
	}

	// Grab account info
	public function info($email) {
		$sql = "SELECT email, stream_key, channel_name, channel_title, display_name FROM $this->user_table WHERE email = $1";
		$params = array($email);
		$info = pg_fetch_assoc(pg_query_params($this->link, $sql, $params));
		if ($info === null) {
			$message = 'Error in: class:user | function:info';
			$code = 1;
			throw new Exception($message, $code);
		}
		return $info;
	}

	// updates channel_name, channel_title, and display_name.
	public function update($email, $channelname, $channeltitle, $displayname) {
		$dncheck = $this->dncheck($email, $displayname);
		if ($dncheck === true) {
			$status = 'Display name already exists!';
		} else {
			$sql = "UPDATE $this->user_table SET channel_name = $1, channel_title = $2, display_name = $3 WHERE email = $4";
			$params = array($channelname, $channeltitle, $displayname, $email);
			$result = pg_query_params($this->link, $sql, $params);
			if ($result === false) {
				$message = 'Error in: class:user | function:update';
				$code = 1;
				throw new Exception($message, $code);
			} else {
				$status = 'Updated!';
			}
		}
		return $status;
	}

}
