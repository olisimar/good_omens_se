<?php
	/*
		Created: 09-08-27
		Authour: Werner
		These forms relates to user enterable info, comments, blogs and articles.
		These are called by othere script parts.

		clean() -> general_util.php
		category_select_list() -> general_html.php
		order_select_list() -> general_html.php
	*/

	//=== Articles ===//
	//--- New Article --------------------------------------------------------//
	function form_new_article() {
		$email = $_SESSION[ 'user' ];
		$form = '';

		$form .= '
							<div id="user_in">
								<form method="post" action="index.php">
									<fieldset class="border">
										<legend> Add A New Article </legend>
										<label> Set Category </label>
										<select name="wanted_category">
										';

		$form	.= category_select_list( 'top_disabled', '' );

		$form .=			 '</select>
										<label> Article Order (max:127)</label>
										<select name="wanted_order">
										';

		$form .= order_select_list( '64' );

		$form .= 				'</select> <br />
										<label> Title </label>
										<input type="text" maxlength="35" class="wanted_title" name="wanted_title" value="" /> <br />
										<label> Article </label>
										<textarea name="wanted_article"></textarea>
										<input class="right_edge" type="submit" name="submit_new_article" value="Submit New Article" />
									</fieldset>
								</form>
							</div> <!-- EoD: user_in -->
';

		return $form;
	}

	//--- Alter Article ------------------------------------------------------//
	function form_edit_article( $id ) {
		$g_art = "SELECT * FROM `GO-article` WHERE `id_article`='$id'";
		$r_art = query_db( $g_art );
		$art = mysql_fetch_assoc( $r_art );
		$title = $art[ 'title' ];
		$text = $art[ 'article' ];
		$order = $art[ 'order' ];
		$category = $art[ 'category' ];

		$form = '';

		$form .= '
							<div id="user_in">
								<form method="post" action="index.php">
									<fieldset class="border">
										<legend> Edit An Article </legend>
										<label> Set Category </label>
										<select name="wanted_category">
										';

		$form .= category_select_list( 'top_disabled', $category );

		$form .=			 '</select>
										<label> Article Order (max:127)</label>
										<select name="wanted_order">
										';

		$form .= order_select_list( $order );

		$form .= 				'</select> <br />
										<label> Title </label>
										<input type="text" maxlength="35" class="wanted_title" name="wanted_title" value="'.$title.'" /> <br />
										<label> Article </label>
										<textarea name="wanted_article">'.$text.'</textarea>
										<input class="right_edge" type="submit" name="submit_edited_article" value="Submit Edited Article" />
										<input type="hidden" name="wanted_id_article" value="'.$id.'" />
									</fieldset>
								</form>
							</div> <!-- EoD: user_in -->
';

		return $form;
	}

	//=== Blogs ===//
	//--- New Blog ----------------------------------------------------------//
	function form_new_blog() {
		$form = '';
		$form .=	'<div id="user_in">
								<form method="post" action="index.php">
									<fieldset class="border">
										<legend> Create a new blog </legend>
										<label name="wanted_title"> Title: </label>
										<input type="text" size="30" maxlength="35" name="wanted_title" />
										<br />
										<label name="wanted_blog"> Enter your blog </label>
										<textarea name="wanted_blog"></textarea>
										<input class="right_edge" type="submit" name="submit_new_blog" value="Submit Blog" />
									</fieldset>
								</form>
							</div> <!-- EoD: user_in -->
';
		return $form;
	}

	//--- Edit Blog ---------------------------------------------------------//
	function form_edit_blog( $id ) {
		$email = $_SESSION[ 'user' ];
		$sel_b = "SELECT * FROM `GO-blog` WHERE `id_blog`='$id' AND `email`='$email'";
		$res_b = query_db( $sel_b );
		$blog = mysql_fetch_assoc( $res_b );

		$title = $blog[ 'title' ];
		$text = $blog[ 'text' ];

		$form = '';
		$form .=	'<div id="user_in">
								<form method="post" action="index.php">
									<fieldset class="border">
										<legend> Edit blog </legend>
										<label name="wanted_title"> Title: </label>
										<input type="text" name="wanted_title" value="'.$title.'"/>
										<label name="wanted_blog"> Enter your blog </label>
										<textarea name="wanted_blog">'.$text.'</textarea>
										<input type="hidden" name="id_blog" value="'.$id.'" />
										<input class="right_edge" type="submit" name="submit_edited_blog" value="Submit Blog" />
									</fieldset>
								</form>
							</div> <!-- EoD: user_in -->
							';

		return $form;
	}

	//=== Categories ===//
	//--- New Category ------------------------------------------------------//
	function form_new_category() {
		$form = '';
		$form .= '
							<div id="user_in">
								<form action="index.php" method="post">
									<fieldset class="border">
										<legend> Create a new category </legend>
										<label name="wanted_parent"> Choose a Parent: </label>
										<select name="wanted_parent">
											<option value="none"> No Parent </option>
											';

		$form .= category_select_list( 'no_sub', '' );

		$form .= '</select>
										<label name="wanted_order"> Category Order(max:127): </label>
										<select name="wanted_order">
											';

		$form .= order_select_list( '64' );

		$form .= '</select> <br />
										<label name="wanted_name"> Wanted name: </label>
										<input type="text" name="wanted_name" /> <br />
										<label name="wanted_type"> Type of Category: </label>
										<select name="wanted_type">
											';

		$form .= category_type_select_list( '' );

		$form .= '			</select>
										<br />
										<label name="wanted_description"> Describe the category </label>
										<textarea name="wanted_description"></textarea>
										<input class="right_edge" type="submit" name="submit_new_category" value="Submit New Category" />
									</fieldset>
								</form>
							</div> <!-- EoD: user_in -->
							';

		return $form;
	}

	//--- Edit Category -----------------------------------------------------//
	function form_edit_category() {
		$form = '';
		$name = clean( $_POST[ 'wanted_category' ] );
		$sel_c = "SELECT * FROM `GO-category` WHERE `name`='$name'";
		$res_c = query_db( $sel_c );
		$cat = mysql_fetch_assoc( $res_c );

		$parent = $cat[ 'parent' ];
		$order = $cat[ 'order' ];
		$type = $cat[ 'type' ];
		$text = $cat[ 'description' ];

		$form .= '
							<div id="user_in">
								<form action="index.php" method="post">
									<fieldset class="border">
										<legend> Edit category </legend>
										<label name="wanted_name"> Wanted name: </label>
										<input type="text" name="wanted_category_new_name" value="'.$name.'"/>
										<label name="wanted_order"> Category Order(max:127): </label>
										<select name="wanted_order">
											';

		$form .= order_select_list( $order );

		$form .= '</select> <br />
										<label name="wanted_description"> Describe the category </label>
										<textarea name="wanted_description">'.$text.'</textarea>
										<input class="right_edge" type="submit" name="submit_edited_category" value="Submit New Category" />
										<input type="hidden" name="wanted_category_old_name" value="'.$name.'" />
									</fieldset>
								</form>
							</div> <!-- EoD: user_in -->
';

		return $form;
	}

	//=== Comments ==========================================================//
	//--- New Comment -------------------------------------------------------//
	function form_new_comment( $id_blog ) {
		$form = '';
		$form .= '<div id="user_in">
						<form action="index.php" method="post">
							<fieldset class="border">
								<legend> Submit your comment, 1000 char limit </legend>
								<textarea name="wanted_comment"></textarea>
								<input type="hidden" name="wanted_blog_id" value="'.$id_blog.'" />
								<input class="right_edge" type="submit" name="submit_new_comment" value="Submit comment" />
							</fieldset>
						</form>
					</div> <!-- EoD: user_in -->
';

		return $form;
	}
	//--- Edit Comment ------------------------------------------------------//
	function form_edit_comment( $id_comment ) {
		$sel_c = "SELECT `comment` FROM `GO-comment` WHERE `id_comment`='$id_comment'";
		$res_c = query_db( $sel_c );
		$comment = mysql_fetch_assoc( $res_c );
		$comment = $comment[ 'comment' ];
		$form = '';
		$form .= '<div id="user_in">
						<form action="index.php" method="post">
							<fieldset class="border">
								<legend> Submit your comment, 1000 char limit </legend>
								<textarea name="wanted_comment">'.$comment.'</textarea>
								<input type="hidden" name="wanted_comment_id" value="'.$id_comment.'" />
								<input class="right_edge" type="submit" name="submit_edited_comment" value="Submit comment" />
							</fieldset>
						</form>
					</div> <!-- EoD: user_in -->
';

		return $form;
	}
?>