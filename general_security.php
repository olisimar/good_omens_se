<?php
	/*
		Created: 09-08-28
		Authour: Werner Johansson
		This file is for checking security at different place during operation.
	*/


	/*
		This for checking when uppdating or other allowences for updates. Some
		should be correct but being overly carefull is never wrong.
		Returns TRUE if all checks out ok, otherwise FALSE.
		$db_email is the email addy of the person that wrote the original text.
		It should come provided by the function/script that calls this function.
	*/
	function check_allowed( $type, $db_email ) {
		$allow = TRUE;
		
		$email = $_SESSION[ 'user' ];
		$ip = $_SERVER[ 'REMOTE_ADDR' ];

		// System rights checking
		//=== Can the user add/alter an item ===============================//
		if( $email == $db_email ) { // It is our work, allowed to edit as well create.
			if( $type == 'na_article' && $_SESSION[ 'new_art' ] == 'N'  ) {
				$allow = FALSE;
			}
			else if( $type == 'na_blog' && $_SESSION[ 'new_blog' ] == 'N' ) {
				$allow = FALSE;
			}
			else if( $type == 'na_category' && $_SESSION[ 'new_cat' ] == 'N' ) {
				$allow = FALSE;
			}
			else if( $type == 'na_comment' && $_SESSION[ 'new_com' ] == 'N' ) {
				$allow = FALSE;
			}
		}
		else { // Someone elses work, if we can remove we can still edit.
			if( $type == 'na_article' && $_SESSION[ 'rem_art' ] == 'N'  ) {
				$allow = FALSE;
			}
			else if( $type == 'na_blog' && $_SESSION[ 'rem_blog' ] == 'N' ) {
				$allow = FALSE;
			}
			else if( $type == 'na_category' && $_SESSION[ 'rem_cat' ] == 'N' ) {
				$allow = FALSE;
			}
			else if( $type == 'na_comment' && $_SESSION[ 'rem_com' ] == 'N' ) {
				$allow = FALSE;
			}
		}

		//=== Removal of items within the database ============================//
		if( $type == 'rem_article' && $_SESSION[ 'rem_art' ] == 'N'  ) {
				$allow = FALSE;
		}
		else if( $type == 'rem_blog' && $_SESSION[ 'rem_blog' ] == 'N' ) {
			$allow = FALSE;
		}
		else if( $type == 'rem_category' && $_SESSION[ 'rem_cat' ] == 'N' ) {
			$allow = FALSE;
		}
		else if( $type == 'rem_comment' && $_SESSION[ 'rem_com' ] == 'N' ) {
			$allow = FALSE;
		}
		// All above are of the same kind of error.
		if( $allow == FALSE ) {
			$_SESSION[ 'error_mess' ] = 'no_permisssion';
		}

		// Checking if type and parent options match correctly if we add/alter a
		if( $type == 'na_category' ) {
			$allow = check_category_type_match();
		}
		
		// Are we on the same IP as we logged in on?
		if( $_SESSION[ 'loggedin' ] != $_SERVER[ 'REMOTE_ADDR' ] ) {
			$allow = FALSE;
			$_SESSION[ 'error_mess' ] = 'wrong_session';
		}

		// If all went well, still TRUE otherwise...
		return $allow;
	}

	//=======================================================================//
	/*
		This checks the correctness of the answer on a previous security check.
		Returns TRUE/FALSE depending on the correctness of the answer.
	*/
	function check_security() {
		$u_ans = clean( $_POST[ 'answer' ] );
		$quest = $_POST[ 'quest' ];

		$q_ans = "SELECT `answer` FROM `GO-security` WHERE `id_security`='$quest'";
		$r_ans = query_db( $q_ans );
		$d_ans = mysql_fetch_assoc( $r_ans );

		return( $u_ans == $d_ans[ 'answer' ] ); // True if alike.
	}

	//=======================================================================//
	/*
		This is to check that category type and parent match with what is selected.
		This is for security and integrity of the database.
	*/
	function check_category_type_match() {
		$allow = TRUE;
		$parent = clean( $_SESSION[ 'wanted_parent' ] );
		$type = clean( $_SESSION[ 'wanted_type' ] );
		
		if( $parent != 'none' && $type == 'top' ) {
			$allow = FALSE;
			$_SESSION[ 'error_mess' ] = 'Can only be a TOP level category if you have no parents.';
		}
		else if( $parent == 'none' && $type != 'top' ) {
			$allow = FALSE;
			$_SESSION[ 'error_mess' ] = 'Can only be a TOP level category when you have no parents.';
		}

		return $allow;
	}
	
	//=== util functions ======================================================
	/*
		This will change an articles category. It's not to be used by users but
		rather by internal functions that do this as part of a larger change.
	*/
	function alter_article_category( $to, $from ) {
		$success = FALSE;

		if( check_allowed( 'na_category', $_SESSION[ 'user' ] ) ) {
			return $success;
		} // Just making sure we don't get a unwanted access.

		$get_a = "SELECT * `GO-article` WHERE `category`='$from'";
		$res_a = query_db( $get_a );
		while( $art = mysql_fetch_assoc( $res_a ) ) {
			$id = $art[ 'id_article' ];
			$alt = "UPDATE `GO-article` SET `category`='$to' WHERE `category`='$from' AND `id_article`='$id'";
			$success = query_db( $alt );
		}

		return $success;
	}
?>