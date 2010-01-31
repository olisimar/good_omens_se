<?php
	/*
		Created: 09-08-10
		Authour: Werner Johansson
		For the use of inserting any kind of new material into the datebase.
	*/

	// Included to get things rolling as they should be.
	include( 'general_security.php' );
	
	//=====================================================================
	// A new article is added to the datebase.
	function add_article() {
		$added = FALSE;
		$allow = check_allowed( 'na_article', $_SESSION[ 'user' ] );
		
		if( $allow ) {
			$email = $_SESSION[ 'user' ];
			$auth  = $_SESSION[ 'name' ];
			$order = clean( $_POST[ 'wanted_order' ] );
			$categ = clean( $_POST[ 'wanted_category'] );
			$title = clean( $_POST[ 'wanted_title' ] );
			$text = clean( $_POST[ 'wanted_article' ] );
			$ip = $_SERVER[ 'REMOTE_ADDR' ];

			$ins = "INSERT INTO `GO-article` (`order`,`category`,`title`,`article`,`authour`,`ip`,`email`) VALUES ('$order','$categ','$title','$text','$auth','$ip','$email')";
			query_db( $ins );
			$added = TRUE;
		}

		return $added;
	}

	//=====================================================================
	function add_blog() {
		$added = FALSE;
		$allow = check_allowed( 'na_blog', $_SESSION[ 'user' ] );

		if( $allow ) {
			$ip = $_SERVER[ 'REMOTE_ADDR' ];
			$auth = $_SESSION[ 'name' ];
			$email = $_SESSION[ 'user' ];
			$title = clean( $_POST[ 'wanted_title'] );
			$text = clean( $_POST[ 'wanted_blog' ] );
		
			$ins_b = "INSERT INTO `GO-blog` (`ip`,`email`,`authour`,`title`,`text`) VALUES ('$ip','$email','$auth','$title','$text')";
			query_db( $ins_b );
			$added = TRUE;
		}
		
		return $added;
	}

	//=====================================================================
	function add_category() {
		$added = FALSE;
		$allow = check_allowed( 'na_category', $_SESSION[ 'user' ] );
		
		if( $allow ) {
			$name = clean( $_POST[ 'wanted_name' ] );
			$type = clean( $_POST[ 'wanted_type' ] );
			$order = clean( $_POST[ 'wanted_order' ] );
			$parent = clean( $_POST[ 'wanted_parent' ] );
			$text = clean( $_POST[ 'wanted_description' ] );
			
			$put_c = "INSERT INTO `GO-category` (`name`,`order`,`parent`,`type`,`description`) VALUES ('$name','$order','$parent','$type','$text')";
			query_db( $put_c );
			$added = TRUE;
		}
		
		return $added;
	}
	
	//=====================================================================
	function add_comment() {
		$added = FALSE;
		$allow = check_allowed( 'na_category', $_SESSION[ 'user' ] );

		if( $allow ) {
			$blog_id = clean( $_POST[ 'wanted_blog_id' ] );
			$comment = clean( $_POST[ 'wanted_comment' ] );
			$user = $_SESSION[ 'user' ]; //email
			$name = $_SESSION[ 'name' ]; //display name
			$ip = $_SERVER[ 'REMOTE_ADDR' ];

			$put_c = "INSERT INTO `GO-comment` (`id_blog`,`authour`,`comment`,`email`,`ip`) VALUES ('$blog_id','$name','$comment','$user','$ip')";
			query_db( $put_c );
			$added = TRUE;
		}

		return $added;
	}

	//=====================================================================
	// This function is to add a new user, it checks for a number of things.
	function add_user() {
		$added = FALSE; //preset, for error handling. We assume a failure.
		
		if( check_security() ) {
			$mail = clean( $_POST[ 'email' ] );
			$chk_u = "SELECT * FROM `GO-user` WHERE `email`='$mail'";
			$res_u = query_db( $chk_u );
			
			if( mysql_num_rows( $res_u) != 0 ) {
				$_SESSION[ 'error_mess' ] = 'Email already in use.';
			}
			else if( !isset( $_POST[ 'consent' ] ) ) {
				$_SESSION[ 'error_mess' ] = 'Did not consent to abide by the rules.';
			}
			else {
				$name = clean( $_POST[ 'name' ] );
				$pass = clean( $_POST[ 'password' ] );

				$role = 'comment'; //default value as I only want commenters autojoined.
				$ip = $_SERVER[ 'REMOTE_ADDR' ];

				//--- Creating a new user in the DB ---//				
				$in_u = "INSERT INTO `GO-user` (`email`,`name`,`passwd`,`ip`,`role`) VALUES ('$mail','$name','$pass','$ip','$role')";
				query_db( $in_u );
				//--- All settings have defaults ---//
				$in_s = "INSERT INTO `GO-settings` (`email`) VALUES ('$email')";
				query_db( $in_s );
				$added = TRUE;
			}
		}
		else {
			$_SESSION[ 'error_mess' ] = 'failed_security';
		}
		return $added;
	}
?>