<?php
	function generate_menu() {
		$menu =  '';

		if( $_SESSION[ 'id_menu' ] != 'None' ) {
			$sel_cat = $_SESSION[ 'id_menu' ];

			$sel_c = "SELECT `parent` FROM `GO-category` WHERE `name` = '$sel_cat'";
			$par = query_db( $sel_c );
			$parent = mysql_fetch_assoc( $par );
			$parent = $parent[ 'parent' ];

			$menu .= '<ul>
						<li class="top_of_menu">
							&nbsp;
						</li>
						';

			$sel_q = "SELECT name FROM `GO-category` WHERE `parent` = 'none' ORDER BY `order` ASC";
			$cats = query_db( $sel_q );

			while( $row = mysql_fetch_assoc( $cats ) ) {
				$name = $row[ 'name' ];

				/*
					If a top level menu was selected this will take care of this. It will also
					display lower level categories to the selected item.
				*/
				if( $name == $sel_cat ) {
					$menu .='<li>
							<a href="index.php?id_menu='.$name.'" class="menu_top_select"> '.$name.' </a>
						</li>
						';

					$sel_s = "SELECT name FROM `GO-category` WHERE `parent` = '$name' ORDER BY `order` ASC";
					$subc = query_db( $sel_s );

					while( $sub = mysql_fetch_assoc( $subc ) ) {
						$name = $sub[ 'name' ];
						$menu .='<li>
							<a href="index.php?id_menu='.$name.'" class="menu_sub_unselect"> '.$name.' </a>
						</li>
					';
					}
				}

				else if( $name == $parent ) {
					$menu .='<li>
							<a href="index.php?id_menu='.$parent.'" class="menu_top_select"> '.$name.' </a>
						</li>
						';

					/*
						This will add the sub-categories to a top-category as one	was
						selected. It will also list any articles or image galleries under
						this sub category.
					*/
					$sel_s = "SELECT name,type FROM `GO-category` WHERE `parent` = '$name' ORDER BY `order` ASC";
					$subc = query_db( $sel_s );

					while( $sub = mysql_fetch_assoc( $subc ) ) {
						$name = $sub[ 'name' ];
						if( $name != $sel_cat ) {
							$menu .='<li>
							<a href="index.php?id_menu='.$name.'" class="menu_sub_unselect"> '.$name.' </a>
						</li>
						';
						}
						else {
							$menu .='<li>
							<a href="index.php?id_menu='.$parent.'" class="menu_sub_select"> '.$name.' </a>
						</li>
						';
							/*
								Getting the articles or image links under this sub-category.
							*/
							$sel_a = "SELECT id_article,title FROM `GO-article` WHERE `category` = '$name' ORDER BY `order` ASC";
							$arts = query_db( $sel_a );

							while( $article = mysql_fetch_assoc( $arts ) ) {
								$title = $article[ 'title' ];
								$title = substr( $title, 0, 25 );
								$id = $article[ 'id_article' ];
								if( $id == $_SESSION[ 'id_art' ] ) {
									$menu .= '<li>
							<a href="index.php?id_menu='.$name.'&id_art='.$id.'" class="menu_art_select"> '.$title.' </a>
						</li>
						';
								}
								else {
									$menu .= '<li>
							<a href="index.php?id_menu='.$name.'&id_art='.$id.'" class="menu_art_unselect"> '.$title.' </a>
						</li>
					';
								}
							}
						}
					}
				}
				else {
					$menu .='<li>
							<a href="index.php?id_menu='.$name.'" class="menu_top_unselect"> '.$name.' </a>
						</li>
						';
				}
			}

			$menu .= '</ul>
';
		}

		/* In case no menu item was actually selected */
		else if( $_SESSION[ 'id_menu' ] == 'None' ) {
			$menu .= '<ul>
						<li class="top_of_menu">
							&nbsp;
						</li>
						';
			$sel_q = "SELECT `name` FROM `GO-category` WHERE `parent` = 'none' ORDER BY `order` ASC";
			$cats = query_db( $sel_q );

			while( $row = mysql_fetch_assoc( $cats ) ) {
				$name = $row[ 'name' ];
				$menu .='	<li>
							<a href="index.php?id_menu='.$name.'" class="menu_top_unselect"> '.$name.' </a>
						</li>
					';
			}

			$menu .= '</ul>
';
		}

		/* Complete breakdown, nothing was found...*/
		else {
			$menu =  '<p>
									No menu yet...
								</p>
';
		}




		return $menu;
	}

	//=======================================================================//
	// This bit generates the actual output. This is run every time this file
	// is loaded.
	echo generate_menu();
?>