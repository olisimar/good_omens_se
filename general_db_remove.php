<?php
	/*
		Created: 09-08-20
		Authour: Werner
		This file is for the explicit use of remove items out of the database.
		This will be rechecked by the check_allowed() function that the user has
		rights for this. It should also be restricted on the site to limit the
		options of the user there to even see the option.

		check_allowed() -> general_security.php
	*/

	//--- Required for security reasons.
	include( 'general_security.php' );
	
	//=======================================================================//
	/*
		Will actually remove an article as well as any related comments.
	*/
	function remove_article() {
		$success = FALSE;
		$id_art = clean( $_POST[ 'wanted_article' ] );
		$allow = check_allowed( 'rem_art', $_SESSION[ 'user' ] );

		if( $allow ) {
			$del_a = "DELETE FROM `GO-article` WHERE `id_article`='$id_art' LIMIT 1";
			query_db( $del_a );
			$success = TRUE;
		}

		return $success;
	}

	//=======================================================================//
	/*
		This will remove a blog and all it's attached comments. It's a one way
		deal, all goes.
	*/
	function remove_blog() {
		$success = FALSE;
		$id_blog = $_POST[ 'wanted_blog' ];
		$allow =check_allowed( 'rem_blog', $_SESSION[ 'user' ] );

		if( $allow ) {
			$del_b = "DELETE FROM `GO-blog` WHERE `id_blog`='$id_blog' LIMIT 1";
			query_db( $del_b );
			$success = TRUE;
		}

		return $success;
	}
	
	//=======================================================================//
	/*
		Will remove a category but not the articles or their comments.
		The articles will have their category set to 'NoCategory' done
		by an internal function in 'general_db_alter.php'.
		Since there are a number of options here this will be a very long function
		towards the end if not broken up even further.
	*/
	function remove_category() {
		$success = FALSE;
		$name = clean( $_POST[ 'wanted_category' ] );
		$allow = check_allowed( 'rem_cat', $_SESSION[ 'user' ] );
		
		$get_c = "SELECT `type` FROM `GO-category` WHERE `name`='$name'";
		$res_c = query_db( $get_c );
		$categ = mysql_fetch_assoc( $res_c );

		// Fixing top level category, removing if empty otherwise denying service.
		if( $categ[ 'type' ] == 'top' ) {
			$sel_c = "SELECT * FROM `GO-category` WHERE `parent`='$name'";
			$get_c = query_db( $sel_c );
			if( mysql_num_rows( $get_c ) != 0 ) {
				$_SESSION[ 'error_mess' ] =	'The category was not empty and therefore
																		 not dealt with. Please remove or change
																		 the categories below before attempting
																		 this again.';
			}
			else {
				$sel_c = "DELETE FROM `GO-category` WHERE `name`='$name'";
				$success = query_db( $sel_c );
			}
		}

		// Fixing articles, shifts them into the NoCategory fictional category.
		else if( $categ[ 'type' ] == 'Articles' ) {
			$sel_a = "SELECT * FROM `GO-articles` WHERE `category`='$name'";
			$res_a = query_db( $sel_a );
			if( mysql_num_rows( $res_a ) != 0 ) {
				$sel_a = "SELECT * FROM `GO-articles` WHERE `category`='$name'";
				$res_a = query_db( $sel_a );
				alter_article_category( 'NoCategory', $name );
			}
			$sel_c = "DELETE FROM `GO-category` WHERE `name`='$name'";
			$success = query_db( $sel_c );
		}

		// Fixing with galleries, not implemented yet.
		else if( $categ[ 'type' ] == 'Galleries' ) {
			$_SESSION[ 'error_mess' ] =	'Sorry, this just isn\'t possible right now
																	 as I haven\'t implemented this feature yet.
																	 However you came to this choice I don\'t
																	 know.';
		}
		// Something went badly wrong here, no type shouldn't be possible.
		else {
			$_SESSION[ 'error_mess' ] = 'Unknown quantity, nothing happens here.';
		}
		
		if( $allow ) {
			$del_b = "DELETE FROM `GO-category` WHERE `name`='$name'";
			query_db( $del_b );
			$success = TRUE;
		}

		return $success;
	}

	//=======================================================================//
	/*
		Will remove a comment.
	*/
	function remove_comment( $id_comment ) {
		$success = FALSE;
		$allow =check_allowed( 'rem_comment', $_SESSION[ 'user' ] );

		if( $allow ) {
			$del_b = "DELETE FROM `GO-comment` WHERE `id_comment`='$id_comment'";
			query_db( $del_b );
			$success = TRUE;
		}

		return $success;
	}

	//=======================================================================//
	/*
		Won't actually remove a user, just mark them as inactive or banned.
		This can only come from an admin page, ideally, hence it's done
		like this.
	*/
	function remove_user() {
		$success = FALSE;
		$status = clean( $_POST[ 'status' ] );
		$user = clean( $_POST[ 'account' ] );

		// Special case, no more checks.
		if( ( $_SESSION[ 'rem_user' ] == 'Y' ) && ( $_SESSION[ 'loggedin' ] == $_SERVER[ 'REMOTE_ADDR' ] ) )	{
			$rem = "UPDATE `GO-user` SET `active`='$status' WHERE `email`='$user'";
			query_db( $rem );
			$success = TRUE;
		}
		else {
			$_SESSION[ 'error_mess' ] = 'insuffient_rights_violation';
		}

		return $success;
	}
?>