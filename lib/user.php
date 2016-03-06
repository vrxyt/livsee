<?php

/*  **************	*
 * 	/lib/user.php	*
 * 	**************	*/

class user extends database {

	public $user_table = 'users';

	// create a new account and send registration verification email
	public function register($email, $password, $displayname) {
		$emailcheck = $this->emailcheck($email);
		if ($emailcheck === true) {
			$status = 'Account already exists!';
		} else {
			$dncheck = $this->dncheck($email, $displayname);
			if ($dncheck === true) {
				$status = 'Display name already exists!';
			} else {
				$authcode = random_int(100000, 999999);
				$hash = password_hash($password, PASSWORD_DEFAULT);
				$sql = "INSERT INTO users (email, password, auth_code, verified, channel_name, channel_title, display_name) VALUES ($1, $2, $authcode, 0, $3, $4, $5)";
				$params = array($email, $hash, "$displayname's channel", "Welcome to $displayname's stream!", $displayname);
				$result = pg_query_params($this->link, $sql, $params);
				if ($result === false) {
					$message = 'Error in: class:user | function:register';
					$code = 4;
					throw new Exception($message, $code);
					//$error = pg_last_error($pglink);
					//echo '<pre>pgSQL error: '.$error.'<br />Query string: '.$sql.'<br />params: '.print_r(array_values($params)).'</pre>';
				} else {
					$subject = 'DM Stream Account Verification';
					$message = file_get_contents('inc/emailbefore.html');
					$message .= "<a href=\"https://stream.rirnef.net/verify.php?email=$email&c=$authcode\" style=\"color: #fff!important;padding: 12px 24px;font-size: 29px;line-height: 1.3333333;border-radius: 3px;background-color: #df691a;border-color: transparent;display: inline-block;margin: auto;font-weight: normal;text-align: center;vertical-align: middle;touch-action: manipulation;cursor: pointer;background-image: none;border: 1px solid transparent;white-space: nowrap;-webkit-user-select: none;text-decoration: none;\">Verify Account</a>";
					$message .= file_get_contents('inc/emailafter.html');
					$headers = "From: DM Stream <noreply@rirnef.net>\r\n";
					$headers .= "Reply-To: issues@rirnef.net\r\n";
					$headers .= 'X-Mailer: PHP/' . phpversion();
					//$headers .= "BCC: registration@rirnef.net\r\n"; * doesn't seem to work anyway
					$headers .= "MIME-Version: 1.0\r\n";
					$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
					mail($email, $subject, $message, $headers);
					$status = true;
					//header('Location: login.php?action=account_created');
				}
			}
		}
		return $status;
	}

	// verify account from email URL
	public function verify($email, $code) {
		$params = array($email, $code);
		$sql = "SELECT * FROM users WHERE email = $1 AND auth_code = $2";
		$result = pg_query_params($this->link, $sql, $params);
		$row_cnt = pg_num_rows($result);
		if ($row_cnt >= 1) {

			// simple function to generate steam key. TODO: Make this less predicatble
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
			$sql = "UPDATE users SET auth_code = null, verified = 1, stream_key = $1 WHERE email = $2 AND auth_code = $3";
			$check = pg_query_params($this->link, $sql, $params);
			//print_r($check);
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

	// check if email exists
	public function emailcheck($email) {
		$params = array($email);
		$sql = "SELECT * FROM users WHERE email = $1";
		$check = pg_query_params($this->link, $sql, $params);
		//print_r($check);
		if ($check === false) {
			$message = 'Error in: class:user | function:emailcheck';
			$code = 4;
			throw new Exception($message, $code);
		} else {
			$row_cnt = pg_num_rows($check);
			if ($row_cnt >= 1) {
				$status = true;
			} else {
				$status = false;
			}
		}
		return $status;
	}

	// check if display name exists
	public function dncheck($email, $displayname) {
		$params = array($displayname, $email);
		$sql = "SELECT * FROM users WHERE display_name = $1 AND email != $2";
		$check = pg_query_params($this->link, $sql, $params);
		//print_r($dncheck);
		if ($check === false) {
			$message = 'Error in: class:user | function:dncheck';
			$code = 3;
			throw new Exception($message, $code);
		} else {
			$row_cnt = pg_num_rows($check);
			if ($row_cnt >= 1) {
				$status = true;
			} else {
				$status = false;
			}
			return $status;
		}
	}

	// grab account info
	public function info($email) {
		$sql = "SELECT email, stream_key, channel_name, channel_title, display_name FROM users WHERE email = $1";
		$params = array($email);
		$info = pg_fetch_assoc(pg_query_params($this->link, $sql, $params));
		//print_r($info);
		if ($info === null) {
			$message = 'Error in: class:user | function:info';
			$code = 2;
			throw new Exception($message, $code);
		} else {
			return $info;
		}
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
			//print_r($result);
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

