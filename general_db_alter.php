<?php
	/*
		Created: 09-08-08
		Authour: Werner Johansson
		This is to alter any part of the datebase from the the outside. This
		includes all elements. They will be sorted in such a way that each subject
		is grouped.

		check_allowed() -> located in general_security.php
		alter_article_category() -> located in general_security.php
	*/

	// To make security checks easier, they are in this file.
	include( 'general_security.php' );

	//========================================================================//
	/*
		This will change an article in all it's basic forms. For now it's left
		without the security question and a simple ip check is done.
	*/
	function alter_article() {
		$added = FALSE; // Assumed failure.
		$allow = FALSE; // If allowed to change the article.

		$art_id = clean( $_POST[ 'wanted_id_article' ] );
		$sel_a = "SELECT * FROM `GO-article` WHERE `id_article`='$art_id'";
		$res_a = query_db( $sel_a );
		$artic = mysql_fetch_assoc( $res_a );

		$allow = check_allowed( 'na_article', $artic[ 'email' ] );

		// Update or not
		if( $allow ) {
			$cat = clean( $_POST[ 'wanted_category' ] );
			$order = clean( $_POST[ 'wanted_order' ] );
			$title =  clean( $_POST[ 'wanted_title' ] );
			$text = clean( $_POST[ 'wanted_article' ] );
			$email = $_SESSION[ 'user' ];
			$auth = $_SESSION[ 'name' ];
			$ip = $_SERVER[ 'REMOTE_ADDR' ];

			$alt = "UPDATE `GO-article` SET `order`='$order',`category`='$cat',`title`='$title',`article`='$text',`ip`='$ip' WHERE `id_article`='$art_id'";
			query_db( $alt );
			$added = TRUE;
		}

		return $added;
	}

	//========================================================================//
	/*
		Alters a blog entry. It will also change name, email and authour name
		if edited by someone else than the blogger. It does however make sure
		only an admin can alter it as check_allowed() has checks for this.
	*/
	function alter_blog() {
		$added = FALSE;
		$id_blog = clean( $_POST[ 'id_blog' ] );
		$get_b = "SELECT * FROM `GO-blog` WHERE `id_blog`='$id_blog'";
		$res_b = query_db( $get_b );
		$blog = mysql_fetch_assoc( $res_b );
		$email = $blog[ 'email' ];

		$allow = check_allowed( 'na_blog', $email );
		if( $allow ) {
			$email = $_SESSION[ 'user' ];
			$title = clean( $_POST[ 'wanted_title' ] );
			$text = clean( $_POST[ 'wanted_blog' ] );
			$ip = $_SERVER[ 'REMOTE_ADDR' ];

			$upd_b = "UPDATE `GO-blog` SET `email`='$email',`title`='$title',`text`='$text',`ip`='$ip' WHERE `id_blog`='$id_blog'";

			$_SESSION[ 'requested_action' ] = 'blog';
			$_SESSION[ 'requested_id' ] = $id_blog;

			$added = query_db( $upd_b ); // TRUE if it went in as supposed.
		}

		return $added;
	}

	//========================================================================//
	/*
		This function deals with the alteration of a category in different manners.
		The function to change an articles category is located in
		general_security.php. Since there are different ways to change a category
		and since it affect so many things in different ways this will be dealt with
		at a later point.
	*/
	function alter_category() {
		$added  = FALSE;
		$old_cn = clean( $_POST[ 'wanted_category_old_name' ] );
		$new_cn = clean( $_POST[ 'wanted_category_new_name' ] );
		$allow  = check_allowed( 'na_category', $_SESSION[ 'user' ] );

		if( ( $old_c == $new_c ) && $allow ) {
			$desc = clean( $_POST[ 'wanted_description' ] );
			$order = clean( $_POST[ 'wanted_order' ] );
			$put_c = "UPDATE `GO-category` SET `description`='$desc',`order`='$order' WHERE `name`='$old_cn'";
			$added = query_db( $put_c );
		}
		else if ( $allow ) {
			$get_c = "SELECT * FROM `GO-category` WHERE `name`='$old_cn'";
			$res_c = query_db( $get_c );
			$categ = mysql_fetch_assoc( $res_c );
			if( $categ[ 'type' ] == 'Articles' ) {
				$desc = clean( $_POST[ 'wanted_description' ] );
				$order = clean( $_POST[ 'wanted_order' ] );

				alter_article_category( $new_cn, $old_cn ); // new name, old name

				$put_c = "UPDATE `GO-category` SET `name`='$new_cn',`description`='$desc',`order`='$order' WHERE `name`='$old_cn'";
				$added = query_db( $put_c );
			}
			else if( $categ[ 'type' ] == 'Galleries' ) {
				$_SESSION[ 'error_mess' ] = 'Not implemented yet, redo.';
			}
			else {
				$_SESSION[ 'error_mess' ] = 'Unknown type of category.';
			}
		}

		return $added;
	}

	//========================================================================//
	/*
		Changes a comment. If an admin edits it his/her info will be there as
		the commenters. I haven't made an catches or alters of this. This is for
		a later version.
	*/
	function alter_comment() {
		$added = FALSE;
		$id_com = clean( $_POST[ 'wanted_comment_id' ] );
		$get_c = "SELECT * FROM `GO-comment` WHERE `id_comment`='$id_com'";
		$res_c = query_db( $get_c );
		$comment = mysql_fetch_assoc( $res_c );
		$email = $comment[ 'email' ];

		if( check_allowed( 'na_comment', $email ) ) {
			$text = clean( $_POST[ 'wanted_comment' ] );
			$auth = $_SESSION[ 'name' ];
			$email = $_SESSION[ 'user' ];
			$put_c = "UPDATE `GO-comment` SET `comment`='$text',`ip`='$ip',`authour`='$auth' WHERE `id_comment`='$id_com'";
			$added = query_db( $put_c ); // True if it went well.

			$_SESSION[ 'requested_action' ] = 'blog';
			$_SESSION[ 'requested_id' ] = $comment[ 'id_blog' ];

			}

		return $added;
	}

	//========================================================================//
	/*
		This will change the personal info of the person logged in right now.
	*/
	function alter_own_user() {
		$added = FALSE; // Assumed failure.
		$email = $_SESSION[ 'user' ];

		$get_u = "SELECT * FROM `GO-user` WHERE `email`='$email'";
		$res_u = query_db( $get_u );
		$user = mysql_fetch_assoc( $res_u );

		if( $_SESSION[ 'loggedin'] == $_SERVER[ 'REMOTE_ADDR' ] ) {
			$old_pw = clean( $_POST[ 'old_password' ] );
			$nw1_pw = clean( $_POST[ 'n1_password' ] );
			$nw2_pw = clean( $_POST[ 'n2_password' ] );

			if( ( $old_pw == $user[ 'passwd' ] ) && ( $nw1_pw == $nw2_pw ) ) {
				$name = $user[ 'name' ];
				$show_com = 'N';
				$put_u = "UPDATE `GO-user` SET `name`='$name',`passwd`='$nw2_pw' WHERE `email`='$email'";
				$added = query_db( $put_u );

				if( isset( $_POST[ 'show_com' ] ) ) {
					$show_com = 'Y';
					$_SESSION[ 'show_comments' ] = 'Y';
				}
				else {
					$show_com = 'N';
					$_SESSION[ 'show_comments' ] = 'N';
				}
				$put_s = "UPDATE `GO-settings` SET `show_comments`='$show_com' WHERE `email`='$email'";
				$added = query_db( $put_s );
			}
			else {
				$_SESSION[ 'error_mess' ] = 'password_mismatch';
			}
		}
		else {
			$_SESSION[ 'error_mess' ] = 'wrong_session';
		}

		return $added;
	}

?>