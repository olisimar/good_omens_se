<?php
	/*
		Created: 09-08-08
		Authour: Werner Johansson
		This file deals with checking anything that comes in through index.php
		It's put all values in the $_SESSION[] array to avoid double polling as
		increasing security as this is the point of entry for most things.
		However quite a few other files might pull on their own things and go
		through more checks to ensure they get things right.

		clean() is located in general_util.php
	*/

	/* Checking if we are loggedin and if so, are we trusted. */
	if( isset( $_SESSION[ 'loggedin' ] ) ) { //if true we enter here...
		if( $_SERVER[ 'REMOTE_ADDR' ] != $_SESSION[ 'loggedin' ] ) {
			$_SESSION[ 'loggedin' ] = 0;
			session_destroy();
			echo 'Bad...';
			// check unsetting this parameter.
		}
	}
	// Someone is logging in
	else if( isset( $_POST[ 'user' ] ) && isset( $_POST[ 'passwd' ] ) ) {
		$user = clean( $_POST[ 'user' ] ); //email
		$passwd = clean( $_POST[ 'passwd' ] );

		$query = "SELECT * FROM `GO-user` WHERE `email`='$user' AND `passwd`='$passwd'";
		$db = query_db( $query );

		if( mysql_num_rows( $db ) == 1 ) { // if only one turned up we got it right.
			//--- Generally used information ---//
			$db = mysql_fetch_assoc( $db );
			if( $db[ 'active' ] == 'Y' ) {
				$_SESSION[ 'user' ] = $user;						// Not to be used other as id.
				$_SESSION[ 'name' ] = $db[ 'name' ];		// Not login name but the moniker.
				$_SESSION[ 'role' ] = $db[ 'role' ];		// What role they have on the site.
				$_SESSION[ 'loggedin' ] = $_SERVER[ 'REMOTE_ADDR' ];

				//--- Security, rights to do things on the site ---//
				$role = $_SESSION[ 'role' ];
				$q_role = "SELECT * FROM `GO-rights` WHERE `role`='$role'";
				$r_role = query_db( $q_role );
				$res_role = mysql_fetch_assoc( $r_role );
				$_SESSION[ 'new_art' ] = $res_role[ 'new_art' ];
				$_SESSION[ 'rem_art' ] = $res_role[ 'rem_art' ];
				$_SESSION[ 'new_blog'] = $res_role[ 'new_blog'];
				$_SESSION[ 'rem_blog'] = $res_role[ 'rem_blog'];
				$_SESSION[ 'new_cat' ] = $res_role[ 'new_cat' ];
				$_SESSION[ 'rem_cat' ] = $res_role[ 'rem_cat' ];
				$_SESSION[ 'new_com' ] = $res_role[ 'new_com' ];
				$_SESSION[ 'rem_com' ] = $res_role[ 'rem_com' ];
				$_SESSION[ 'rem_user'] = $res_role[ 'rem_user'];

				//--- Personalized settings ---//
				$get_s = "SELECT * FROM `GO-settings` WHERE `email`='$user'";
				$res_s = query_db( $get_s );
				$sets = mysql_fetch_assoc( $res_s );
				$_SESSION[ 'theme' ] = $sets[ 'theme' ];
				$_SESSION[ 'show_comments' ] = $sets[ 'show_comments' ];
			}
			else if( $_SESSION[ 'active' ] == 'N' ) {
				$_SESSION[ 'last_action' ] = 'FALSE';
				$_SESSION[ 'error_mess' ] = 'Your account was not active, contact a site-admin.';
			}
			else if( $_SESSION[ 'active' ] == 'B' ) {
				$_SESSION[ 'last_action' ] = 'FALSE';
				$_SESSION[ 'error_mess' ] = 'Your account has been banned, contact a site-admin.';
			}
		}
	}
	// Someone is logging out
	if( isset( $_POST[ 'logout' ] ) ) {
		$_SESSION[ 'loggedin' ] = 0;
		session_unset( 'loggedin' );
		session_destroy();
	}

	/*
		Setting up the first time a session is created. This also helps if someone
		got their $_SESSION[] removed from being logged out or some such.
	*/
	if( !isset( $_SESSION[ 'id_art' ] ) ) {
		$_SESSION[ 'id_art' ] = 1; // Main article, welcome message
		$_SESSION[ 'cat_type' ] = 'Articles'; // What type to expect to show.
		$_SESSION[ 'id_menu' ] = 'None'; // No menu is preset, show only top catergories.
		$_SESSION[ 'id_blogmenu' ] = 0; // No blogmenu selected, show none.
		$_SESSION[ 'id_image' ] = 0; // No image was requested so no need to look there.
		$_SESSION[ 'show_comments' ] = 'N'; // No comments will be shown.
	}

	//--- Presetting what theme it should be used ---//
	if( !isset( $_SESSION[ 'theme' ] ) ) {
		$_SESSION[ 'theme' ] = 'default';
	}

	//=======================================================================//
	/*
		Checks on whats new from the user via the main menu. First part is menu
		actions. More or less a nice way to avoid pulling things more than once.
		Also makes it more secure for me as this is the one-stop place to get
		the information.
	*/
	if( isset( $_GET[ 'id_menu' ] ) ) {
		$_SESSION[ 'id_menu' ] = clean( $_GET[ 'id_menu' ] );
		$menu = $_SESSION[ 'id_menu' ];
		$get_m = "SELECT * FROM `GO-category` WHERE `name`='$menu'";
		$res_m = query_db( $get_m );
		$menu = mysql_fetch_assoc( $res_m );
		$_SESSION[ 'cat_type' ] = $menu[ 'type' ];

		// If we have
		if( isset( $_GET[ 'id_art' ] ) ) {
			$_SESSION[ 'id_art' ] = clean( $_GET[ 'id_art' ] );
		}
		else {
			$_SESSION[ 'id_art' ] = '1';
		}
	}

	if( !isset( $_GET[ 'id_art' ] ) ) {
		$_SESSION[ 'id_art' ] = 1;
	}

	//=======================================================================//
	/*
		This is reused through out the site as a note on what to do. If an
		action is encountered this is were I clean it up. It also notes what
		the site is supposed to show.
	*/
	if( isset( $_GET[ 'action' ] ) ) {
		$_SESSION[ 'action' ] = clean( $_GET[ 'action' ] );
	}


	//========================================================================//
	/*
		Form actions, article, blog, category, comment and user
		All functions return TRUE for success and FALSE otherwise.
		If it didn't compute properly it will be taken care of in the
		generate_info file, for the user to see it's failure and why.

		$_POST[ 'submit...' ] Come from full on forms, and should be generated
	 		in generate_form.php or generate_user.php
		$_GET[ 'action' ] are from links added inside the page at various points
			to gain quick access to delete. They will be few in this place as they
			are intended for specific use on the page.
	*/
	//=== Article actions ====================================================//
	if( isset( $_POST[ 'submit_new_article' ] ) ) {
		include( 'general_db_insert.php' );
		$_SESSION[ 'last_action'] = add_article();
	}
	if( isset( $_POST[ 'submit_edited_article' ] ) ) {
		include( 'general_db_alter.php' );
		$_SESSION[ 'last_action' ] = alter_article();
	}
	if( isset( $_POST[ 'submit_remove_article' ] ) ) {
		include( 'general_db_remove.php' );
		$_SESSION[ 'last_action' ] = remove_article();
	}

	//=== Blog actions =======================================================//
	if( isset( $_POST[ 'submit_new_blog' ] ) ) {
		include( 'general_db_insert.php' );
		$_SESSION[ 'last_action' ] = add_blog();
	}
	if( isset( $_POST[ 'submit_edited_blog' ] ) ) {
		include( 'general_db_alter.php' );
		$_SESSION[ 'last_action' ] = alter_blog();
	}
	if( isset( $_POST[ 'submit_remove_blog' ] ) ) {
		include( 'general_db_remove.php' );
		$_SESSION[ 'last_action' ] = remove_blog();
	}

	//=== Category actions ===================================================//
	if( isset( $_POST[ 'submit_new_category' ] ) ) {
		include( 'general_db_insert.php' );
		$_SESSION[ 'last_action' ] = add_category();
	}
	if( isset( $_POST[ 'submit_edited_category' ] ) ) {
		include( 'general_db_alter.php' );
		$_SESSION[ 'last_action' ] = alter_category();
	}
	if( isset( $_POST[ 'submit_remove_category' ] ) ) {
		include( 'general_db_remove.php' );
		$_SESSION[ 'last_action' ] = remove_category();
	}

	//=== Comment actions ====================================================//
	if( isset( $_POST[ 'submit_new_comment' ] ) ) {
		include( 'general_db_insert.php' );
		$_SESSION[ 'last_action' ] = add_comment();
	}
	if( isset( $_POST[ 'submit_edited_comment' ] ) ) {
		include( 'general_db_alter.php' );
		$_SESSION[ 'last_action' ] = alter_comment();
	}
	if( isset( $_POST[ 'submit_remove_comment' ] ) ) {
		include( 'general_db_remove.php' );
		$id = clean( $_POST[ 'wanted_comment_id' ] );
		$_SESSION[ 'last_action' ] = remove_comment( $id );
	}
	if( $_SESSION[ 'action' ] == 'remove_comment' ) {
		$id = clean( $_GET[ 'id' ] );
		include( 'general_db_remove.php' );
		$_SESSION[ 'last_action' ] = remove_comment( $id );
	}
	//=== User actions =======================================================//
	if( isset( $_POST[ 'register_new_user' ] ) ) {
		include( 'general_db_insert.php' );
		$_SESSION[ 'last_action'] = add_user();
	}
	if( isset( $_POST[ 'submit_alter_own_user' ] ) ) {
		include( 'general_db_alter.php' );
		$_SESSION[ 'last_action' ] = alter_own_user();
	}
	if( isset( $_POST[ 'submit_alter_other_user' ] ) ) {
		include( 'general_db_alter.php' );
		$_SESSION[ 'last_action' ] = alter_other_user();
	}
	if( isset( $_POST[ 'submit_remove_user' ] ) ) {
		include( 'general_db_remove.php' );
		$_SESSION[ 'last_action' ] = remove_user();
	}
?>