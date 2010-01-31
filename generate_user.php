<?php
	/*
		Created: 09-08-25
		Authour: Werner
		This produces a number of different forms relating to user and admin of
		this site. It does not deal with editing anything aside user info and
		contains admin forms only.
	*/

	//--- New user form ------------------------------------------------------//
	function form_new_user() {
		/* Presets */
		$mode = 'new'; // if not logged in.
		if( $_SESSION[ 'loggedin' ] == $_SERVER[ 'REMOTE_ADDR' ] ) {
			$mode = 'alter';
		}

		$sec = get_security();
		$form = '';

		$form .= '<div id="user">
								<h3> Rules of Conduct </h3>
								<ol>
									<li>
										Any content you add, comments most likely, will be logged to
										your account. Every comment will also be logged to the IP
										you currently used for that session. This is because I am
										responsible for your actions on my site.
									</li>
									<li>
										If I deem your comments inappropiate I will delete them.
										As stated above I am responsible for them unless I pass
										them over to the legal authouritives. <br />
										I am however a person who likes a good discussion so a well
										formed argument will always be treated as such.
									</li>
									<li>
										Phobic comments are always deleted. It is always grounds
										for expulsion.
									</li>
									<li>
										Illegalities will be forwarded to the proper authouritives
										for their consideration as to what action should be taken.
										Ignorance of the law or rules has never been a defence, let
										common sense guide you.
									</li>
									<li>
										I won\'t sell, transmit or use your email for any other
										reason than to log your actions or contact you for the
										actions	you have make on my site while logged in. <br />
										I will retain the right to contact you.										
									</li>
								</ol>
								
								<form method="post" action="index.php">
									<fieldset class="border">
										<legend> Create your own account </legend>
										<label name="email"> Email </label> <input type="text" name="email" /> <br />
										<label name="name"> Screen Name </label> <input type="text" name="name" /> <br />
										<label name="password"> Password </label> <input type="password" name="password" /> <br />
										'.$sec.'
										<label class="consent" name="consent"> I hereby consent to the rules above. </label>
										<input type="checkbox" name="consent" /> <br />
										<label name="register"> </label> <br />
										<input type="submit" name="register_new_user" value="Register New User" />
									</fieldset>
								</form>
						 	</div>
						';


		return $form;
	}

	//--- Altering current user form ------------------------------------------------------//
	function form_edit_user() {
		$user = $_SESSION[ 'user' ]; 
		$get_u = "SELECT * FROM `GO-user` WHERE `email`='$user'";
		$u_da = query_db( $get_u );
		$data = mysql_fetch_assoc( $u_da );

		$checked = '';
		$get_s = "SELECT * FROM `GO-settings` WHERE `email`='$user'";
		$res_s = query_db( $get_s );
		$sets = mysql_fetch_assoc( $res_s );
		if( $sets[ 'show_comments' ]  == 'Y' ) {
			$checked = 'checked="checked"';
		}
		$form = '';
		
		if( $user == $data[ 'email' ] ) {
			$name = $data[ 'name' ];
			$form .=	'<div id="user">
						<form method="post" action="index.php">
							<fieldset class="border">
								<legend> Edit your details </legend>
								<label name="email"> Email </label> <input type="text" name="email" value="'.$user.'" disabled="disabled"/> <br />
								<label name="name"> Screen Name </label> <input type="text" name="name" value="'.$name.'" /> <br />
								<label name="password"> Old Password </label> <input type="password" name="old_password" /> <br />
								<label name="password"> New Password </label> <input type="password" name="n1_password" /> <br />
								<label name="password"> New Password </label> <input type="password" name="n2_password" /> <br />
								<label name="show_com"> </label> <input type="checkbox" name="show_com" '.$checked.' />
								<span> Show comments when first viewing a blog </span> <br />
								<label name="alter_user"> </label>
								<input type="submit" name="submit_alter_own_user" value="Alter User" />
							</fieldset>
						</form>
						';
			if( $data[ 'role' ] == 'admin' ) {
				$form .= '<br />
						<form method="post" action="index.php">
							<fieldset class="border">
								<label class="admin_chore" name="admin_chore"> Admin the users and the sites content. </label>
								<input type="submit" name="admin_chore" value="Admin tasks" />
							</fieldset>
						</form>
					';
			}
			$form .=	'</div>
';
			}
			else {
				$_SESSION[ 'last_action' ] = FALSE;
				$_SESSION[ 'error_mess' ] = 'wrong_session';
			}
							
		return $form;
	}

	//--- Site editing form --------------------------------------------------//
	/*
		Present a multitude of options here. This inlcudes creation,
		alteration and removing of all things in the database.
	*/
	function form_admin() {
		$form = '';

		//=== New items ===//
		$form .= '<div id="user">
						<form method="post" action="index.php">
							<fieldset class="border">
								<legend> Create new items </legend>
								<input type="submit" value="New Article" name="new_article" />
								<input type="submit" value="New Category" name="new_category" />
								<input type="submit" value="New Blog" name="new_blog" />
								<input type="submit" value="New Image" name="new_image" />
							</fieldset>
						</form>
						<br />
						';

		//=== Alter existing items ============================================//
		//--- Alter Articles ---//
		$form .= '
						<form method="post" action="index.php">
							<fieldset class="border">
								<legend> Edit/Remove Article </legend>
								<select name="wanted_article">
								';
						
		$g_cat = "SELECT * FROM `GO-category` WHERE `type`='Articles' ORDER BY `parent` ASC";
		$r_cat = query_db( $g_cat );
		$category = '';
		while( $cat = mysql_fetch_assoc( $r_cat ) ) {
			$category = $cat[ 'name' ];
			$form .=	'	<option value="0" disabled="disabled"> Category: '.$category.' </option>
								';
			$g_art = "SELECT * FROM `GO-article` WHERE `category`='$category' ORDER BY `order` ASC";
			$r_art = query_db( $g_art );
			while( $art = mysql_fetch_assoc( $r_art ) ) {
				$id = $art[ 'id_article' ];
				$title = $art[ 'title' ];
				$form .= '	<option value="'.$id.'"> +'.$title.' </option>
								';
			}
		}
		$form .=	'	<option value="0" disabled="disabled"> The unsorted articles </option>
								';
		
		$g_cat = "SELECT * FROM `GO-article` WHERE `category`='NoCategory' ORDER BY `id_article` ASC";
		$r_cat = query_db( $g_cat );
		while( $article = mysql_fetch_assoc( $r_cat ) ) {
			$id = $article[ 'id_article' ];
			$title = $article[ 'title' ];
			$form .= '	<option value="'.$id.'"> NoC - '.$title.' </option>
								';
		}
		$form .= '</select>
								<input type="submit" name="edit_article" value="Edit Article" />
								<input type="submit" name="submit_remove_article" value="Remove Article" />
							</fieldset>
						</form>
						<br />
						';

		//=== Alter Categories ================================================//
		$form .= '<form method="post" action="index.php">
							<fieldset class="border">
								<legend> Edit/Remove Category </legend>
								<select name="wanted_category">
								';
										
		$form .= category_select_list( '', '' );
		
		$form .='</select>
								<input type="submit" name="edit_category" value="Edit Category" />
								<input type="submit" name="submit_remove_category" value="Remove Category" />
							</fieldset>
						</form>
						<br />
						';

		//=== Alter Blog ======================================================//
		$form .= '<form method="post" action="index.php">
							<fieldset class="border">
								<legend> Edit/Remove Blog </legend>
								<select name="wanted_blog">
								';
		$g_blo = "SELECT `id_blog`,`title` FROM `GO-blog` ORDER BY `time` DESC";
		$r_blo = query_db( $g_blo );
		while( $blog = mysql_fetch_assoc( $r_blo ) ) {
			$id = $blog[ 'id_blog' ];
			$title = $blog[ 'title' ];
			$form .= '	<option value="'.$id.'"> '.$title.' </option>
								';
		}
		$form .='</select>
								<input type="submit" name="edit_blog" value="Edit Blog" />
								<input type="submit" name="submit_remove_blog" value="Remove Blog" />
							</fieldset>
						</form>
					';
		
		//=== Remove existing items ===========================================//

		$form .= '</div>
';
		return $form;
	}
?>