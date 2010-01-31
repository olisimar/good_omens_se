<?php

	function add_article() {
	}

	function add_blog() {
	}

	function add_category() {
	}
	
	function add_comment {
	}

	function add_user() {
		$added = FALSE; //preset, for error handling.
		
		if( check_security() ) {
			if( isset( $_POST[ 'consent' ] ) ) {
				$mail = clean( $_POST[ 'email' ] );
				$name = clean( $_POST[ 'name' ] );
				$pass = clean( $_POST[ 'passwd' ] );

				$role = 'comment'; //default value as I only want commenters autojoined.
				$ip = $_SERVER[ 'REMOTE_ADDR' ];

				$in_u = "INSERT INTO `GO-user` (`email`,`name`,`passwd`,`theme`,`ip`,`role`) VALUES ('$mail','$name','$pass','default','$ip','$role')";
				query_db( $in_u );
				$added = TRUE;
			}
			else {
				$_SESSION[ 'error_mess' ] = 'Did not consent to the rules.';
			}
		}
		else {
			$_SESSION[ 'error_mess' ] = 'Wrong answer to the security question.';
		}
		return $added;
	}
?>