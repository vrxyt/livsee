<?php

/* * *************	*
 * 	/lib/auth.php	*
 * 	**************	 */

class auth extends database {

	// check if the cookie is valid. This prevents deleted users with a cookie from being able to log in the site.
	public function cookiecheck($email) {
		if ($email) {
			$params = array($email);
			$sql = "SELECT * FROM users WHERE email = $1";
			$result = pg_query_params($this->link, $sql, $params);
			$row_cnt = pg_num_rows($result);
			if ($row_cnt < 1) {
				session_destroy();
				setcookie('rememberMe', null, -1, '/');
				setcookie('email', null, -1, '/');
				header("Location: login.php");
			}
		}
	}

	public function session_authenticate($email, $action = null) {

		if ($action == 'logout') {
			session_destroy();
			setcookie('rememberMe', null, -1, '/');
			setcookie('email', null, -1, '/');
			header('Location: login.php');
		} else {
			echo 'All good?';
			//header('Location: login.php');
		}
	}

	public function login($email, $password) {
		$params = array($email);
		$sql = "SELECT * FROM users WHERE email = $1";
		$result = pg_fetch_assoc(pg_query_params($this->link, $sql, $params));
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

}
