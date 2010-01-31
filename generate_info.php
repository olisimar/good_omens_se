<?php
	/*
		Authour: Werner
		Edited:	09-08-15
		2part file, first part controls what to show to the user. Second part
		contains the general purpose functions, these are the ones mainly used.
	*/

	//=======================================================================//
	// First part - Flow/Show control.

	//--- This single file deals with all error messages shown to the user ---//
	if( ( $_SESSION[ 'last_action' ] == FALSE ) && ( isset( $_SESSION[ 'error_mess' ] ) ) ) {
		include( 'general_error.php' );
		echo error_handling();
	}
	//--- Anything that comes through an <a> link and is an "action" ---//
	else if( isset( $_GET[ 'action' ] ) ) {
		if( $_SESSION[ 'action' ] == 'newuser' ) {
			include( 'generate_user.php' );
			echo form_new_user();
		}
		else if( $_SESSION[ 'action' ] == 'blog' ) {
			require( 'generate_blog.php' );
			$id_blog = clean( $_GET[ 'id' ] );
			echo show_blog( $id_blog );
		}
		else if( $_SESSION[ 'action' ] == 'blogmenu' ) {
			$id_time = clean( $_GET[ 'date' ] );
			$get_b = "SELECT * FROM `GO-blog` ORDER BY `time` DESC";
			$res_b = query_db( $get_b );

			if( substr( $id_time, 4, 2 ) != '00' ) { // There is a month selection.
				while( $blog = mysql_fetch_assoc( $res_b ) ) {
					$year  = substr( $blog[ 'time' ], 0, 4 ); // This blogs year.
					$month = substr( $blog[ 'time' ], 5, 2 ); // This blogs month.
					$time = $year.$month;
					if( $id_time == $time ) {
						require( 'generate_blog.php' );
						$id = $blog[ 'id_blog' ];
						echo show_blog( $id );
						break;
					}
				}
			}
			else { // This selection was for a year, not a month.
				while( $blog = mysql_fetch_assoc( $res_b ) ) {
					$blog_year  = substr( $blog[ 'time' ], 0, 4 ); // This blogs year.
					$time = $blog_year.'00';

					if( $id_time == $time ) {
						require( 'generate_blog.php' );
						$id = $blog[ 'id_blog' ];
						echo show_blog( $id );
						break;
					}
				}
			}
		}
		else if( $_SESSION[ 'action' ] == 'comment' ) {
			$id_blog = clean( $_GET[ 'id' ] );
			include( 'generate_form.php' );
			echo form_new_comment( $id_blog );
		}
		else if( $_SESSION[ 'action' ] == 'edit_comment' ) {
			$id_comment = clean( $_GET[ 'id' ] );
			include( 'generate_form.php' );
			echo form_edit_comment( $id_comment );
		}
	}

	// Produced internally.
	else if( isset( $_SESSION[ 'requested_action' ] ) ) {
 		if( $_SESSION[ 'requested_action' ] == 'blog' ) {
 			$id_blog = $_SESSION[ 'requested_id' ];
			session_unset( 'requested_id' );

			require( 'generate_blog.php' );
			echo show_blog( $id_blog );
		}

		session_unset( 'requested_action' );
	}
	//--- Specific form requests. Mainly for comments, blogs and articles ---//
	else if( isset( $_POST[ 'change_profile' ] ) ) {
		include( 'generate_user.php' );
		echo form_edit_user();
	}

	else if( isset( $_POST[ 'admin_chore' ] ) ) {
		include( 'generate_user.php' );
		echo form_admin();
	}

	else if( $_POST[ 'new_article' ] ) {
		include( 'generate_form.php' );
		echo form_new_article();
	}
	else if( $_POST[ 'edit_article' ] ) {
		$id = clean( $_POST[ 'wanted_article' ] );
		include( 'generate_form.php' );
		echo form_edit_article( $id );
	}
	else if( $_POST[ 'new_blog' ] ) {
		include( 'generate_form.php' );
		echo form_new_blog();
	}
	else if( $_POST[ 'edit_blog' ] ) {
		$id = clean( $_POST[ 'wanted_blog' ] );
		include( 'generate_form.php' );
		echo form_edit_blog( $id );
	}
	else if( $_POST[ 'new_category' ] ) {
		include( 'generate_form.php' );
		echo form_new_category();
	}
	else if( $_POST[ 'edit_category' ] ) {
		include( 'generate_form.php' );
		echo form_edit_category();
	}
	//--- Else we show an article or category description ---//
	else {
		echo generate_info();
	}

	//=======================================================================//
	// Second part - general functions.
	function generate_info() {
		$info = '';
		$type = $_SESSION[ 'cat_type' ];

		if( isset( $_GET[ 'id_art' ] ) ) {
			$art_id = $_SESSION[ 'id_art' ];
			$sel_q = "SELECT * FROM `GO-article` WHERE `id_article` ='$art_id'";
			$in = query_db( $sel_q );
			$in = mysql_fetch_assoc( $in );
			$name = $in['title'];
			$authour = $in['authour'];
			$info = '<div id="article">
						<div id="head">
							<h3> <!-- else if -->
								'.$name.'
							</h3>
						</div>
						<div id="text">
							';
			$info .= $in[ 'article' ];
		  $info .= '
						</div>
						<div id="authour">
							<p> '.$authour.' </p>
						</div>
					</div>
';
		}

		else if( isset( $_GET[ 'id_menu' ] ) ) {
			$name = $_SESSION[ 'id_menu' ];
			$sel_c = "SELECT * FROM `GO-category` WHERE `name`='$name'";
			$res_c = query_db( $sel_c );
			$menu = mysql_fetch_assoc( $res_c );
			$authour = $in['authour'];
			$info .= '<div id="article">
					<div id="head">
						<h3> <!-- else if -->
							'.$name.'
						</h3>
					</div>
					<div id="text">
						';
			$info .= $menu[ 'description' ];
		  $info .= '</div>
				<div id="authour">
					<p> '.$authour.' </p>
				</div>
			</div>
			';
}

		else {
			$art_id = 1;
			$sel_q = "SELECT * FROM `GO-article` WHERE `id_article` ='$art_id'";
			$in = query_db( $sel_q );
			$in = mysql_fetch_assoc( $in );
			$name = $in['title'];
			$authour = $in['authour'];

			$info = '<div id="article">
					<div id="head">
						<h3> <!-- else if -->
							'.$name.'
						</h3>
					</div>
					<div id="text">
							';
			$info .= $in[ 'article' ];
			$info .= '
					</div>
					<div id="authour">
						<p> '.$authour.' </p>
					</div>
				</div>
';
}

		/*
			Exit to whatever called it, returning something to be content of
			div:main_info. Can be just about anything as this is really the anything
			div of the page.
		*/
		return $info;
	}

?>