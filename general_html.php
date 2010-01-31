<?php
	/*
		Created: 09-08-08
		Authour: Werner
		This file contains general usuage <div>s which are generic and can
		be used as such. They will all return an appropiate container <tag>.
		As a rule this is generally a <div>. They will all take in parameters
		which the first one will always be the id= of the container.
		This is for styling purposes. Not sending anything will make it empty.
	*/


	//=====================================================================//
	/*
		This will generate a general login <div> split over 1 line or 3 lines.
		It does not contain rememberence tech,
		$idname is the wanted id="" for the div.
		$lines are 1,2 or 3, anything else will make it come out on one 1 line.
			If you choose 1 it will all come out on one line, <label><input>
			If you choose 2 it will all come out on two lines, <labels> above <input>
			If you choose 3 it will all come out on three lines, <label><input><br />
	*/
	function generate_login( $idname, $lines ) {
		$login_panel = '';

		if( $lines == 2 ) {
			$login_panel .='<div id="'.$idname.'">
				<form method="post" action="index.php">
													<label name="user"> Login: </label>
													<label name="passwd"> Password: </label>
													<label name="login"> </label>
													<br />
													<input type="text" name="user" />
													<input type="password" name="passwd" />
													<input type="submit" name="login" value="Login" />
												</form>
											</div>
';
		}

		else if( $lines == 3 ) {
			$login_panel .='<div id="'.$idname.'">
				<form method="post" action="index.php">
					<label name="user"> Login: </label>
					<input type="text" name="user" />
					<label name="passwd"> Password: </label>
					<input type="password" name="passwd" />
					<label name="login"> </label>
					<input type="submit" name="login" value="Login" />
					</form>
				</div>
';
		}
		else {
			$login_panel .='<div id="'.$idname.'">
												<form method="post" action="index.php">
													<label name="user"> Login: </label>
													<input type="text" name="user" />
													<label name="passwd"> Password: </label>
													<input type="password" name="passwd" />
													<label name="login"> </label>
													<input type="submit" name="login" value="Login" />
												</form>
											</div>
';
		}

		return $login_panel;
	} // EndOf: generate_login()

	//=======================================================================//

	/*
		Gives a few options, outer div can be named for CSS purposes.
	*/
	function generate_userpanel( $idname ) {
		$panel = '<div id="'.$idname.'">
								<div>
									<a href="index.php?action=login"> Login </a>
								</div>
								<div>
									<a href="index.php?action=newuser"> Register New User </a>
								</div>
							</div>
';
		return $panel;
	} // EndOf: generate_userpanel()

	//=======================================================================//

	/*
		If logged in this will give some options.
	*/
	function generate_adminpanel( $idname ) {
		$panel = '';
		$panel .=  '<div id="'.$idname.'">
					<form method="post" action="index.php">
						<input type="submit" name="change_profile" value="Change Profile" />
					</form>
					<form method="post" action="index.php">
						<input type="submit" name="logout" value="Logout" />
					</form>
				</div>
			';
		return $panel;
	} // EndOf: generate_adminpanel()

	//=======================================================================//

	/*
		This function will produce the options list for a use within a <select>
		tag. This one produces as the same notes, categories from `GO-category`
		Take note that the top_disabled will disable the parents and sub_disabled
		will disable will disable the children
		'no_sub' as selection will only show the top level categories and let them
		selectedable.
		$category is either '' if none is to be selected or the name of the
		category wanted to be selected.
	*/
	function category_select_list( $selection, $category ) {

		$form = '';
		$top_s = '';
		$sub_s = '';
		$show_sub = 'yes';
		if( $selection == 'all_disabled' ) {
			$top_s = 'disabled="disabled"';
			$sub_s = 'disabled="disabled"';
		}
		else if( $selection == 'top_disabled' ) {
			$top_s = 'disabled="disabled"';
		}
		else if( $selection == 'sub_disabled' ) {
			$sub_s = 'disabled="disabled"';
		}
		else if( $selection == 'no_sub' ) {
			$show_sub = 'no';
		}
		else {
			$top_s = '';
			$sub_s = '';
		}

		$g_cat = "SELECT `name` FROM `GO-category` WHERE `parent`='none' ORDER BY `name` ASC";
		$r_cat = query_db( $g_cat );
		while( $top_cat = mysql_fetch_assoc( $r_cat ) ) {
			$name = $top_cat[ 'name' ];
			if( $name == $category ) {
				$form .= '	<option value="'.$name.'" '.$top_s.' selected="selected"> '.$name.' </option>
								';
			}
			else {
				$form .= '	<option value="'.$name.'" '.$top_s.'> '.$name.' </option>
								';
			}

			if( $show_sub == 'yes' ) {
				$g_scat = "SELECT `name` FROM `GO-category` WHERE `parent`='$name' ORDER BY `name` ASC";
				$r_scat = query_db( $g_scat );
				while( $sub_cat = mysql_fetch_assoc( $r_scat ) ) {
					$name = $sub_cat[ 'name' ];
					if( $category == $name ) {
						$form .= '	<option value="'.$name.'" selected="selected"> +'.$name.' </option>
							';
					}
					else {
						$form .= '	<option value="'.$name.'"> +'.$name.' </option>
								';
					}
				}
			} // end of $if( $show_sub ){}
		}

		return $form;
	} // EndOf: category_select_list()

	//========================================================================//

	/*
		This gives a list of <option> tags for a <select> tag . $count shows a
		preferred
	*/
	function order_select_list( $count ) {
		$form = '';
		for( $q = 1; $q <= 127; $q++ ) {
			if( $q == $count ) {
				$form .= '<option value="'.$q.'" selected="selected"> '.$q.' </option>
										';
			}
			else {
				$form .= '<option value="'.$q.'"> '.$q.' </option>
										';
			}
		}

		return $form;
	} // EndOf: order_select_list()

	//========================================================================//

	/*
		This will produce a list of <option>s for a <select> tag. This will contain
		different types of categories.
	*/
	function category_type_select_list( $wanted ) {
		$form = '';
		$types = array( 'top'=> 'Top Category, No Parent',
							 			'Articles' => 'Will contain Articles',
							 			'Galleries' => 'Will contain Galleries'
						); //Types of categories

		foreach( $types as $type => $desc ) {
			if( $type == $wanted ) {
			$form .= '	<option value="'.$type.'" selected="selected"> '.$desc.' </option>
						';
			}
			else if( $type == 'Galleries' ) {
			$form .= '	<option value="'.$type.'" disabled="disabled"> '.$desc.' </option>
						';
			}
			else {
			$form .= '	<option value="'.$type.'"> '.$desc.' </option>
						';
			}
		}

		return $form;
	}
?>