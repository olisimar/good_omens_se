<?php
	/*
		Created: 09-09-06
		Authour: Werner
		This function provides a <div> that contains a menu of the blogs. It can be
		called in anywhere but should probably be a free floating entity. If none
		is choosen the last month will be exposed. If a choice to look into others
		are made that will work as the main menu.
	*/
	function generate_blogmenu() {
		$menu = '';

		if( isset( $_GET[ 'action' ] ) ) {
			if( $_SESSION[ 'action' ] == 'blogmenu' ) {
				$time  = clean( $_GET[ 'date' ] );
				$year  = substr( $time, 0, 4 ); // First 4 digits, year.
				$month = substr( $time, 4, 2 ); // Last 2 digits, month

				$menu = generate_specfic_blog_menu( $year, $month, '0' ); // No blog selected
			}
			else if( $_SESSION[ 'action' ] == 'blog' ) {
				$id = clean( $_GET[ 'id' ] );
				$sel_b = "SELECT `time` FROM `GO-blog` WHERE `id_blog`='$id'";
				$res_b = query_db( $sel_b );
				$date = mysql_fetch_assoc( $res_b );
				$year = substr( $date[ 'time' ], 0, 4 ); // First 4 digits, year
				$month = substr( $date[ 'time' ], 5, 2 ); // 6 and 7 digit, month

				$menu = generate_specfic_blog_menu( $year, $month, $id );
			}
			// Action called, but not to use, standard menu.
			else {
				$year = date( "Y" ); // 4 digit year
				$month = date( "m" ); // 2 digit month

				$menu = generate_specfic_blog_menu( $year, $month, '0' );
			}
		}
		// No action was called, standard menu.
		else {
			$year = date( "Y" ); // 4 digit year
			$month = date( "m" ); // 2 digit month

			$menu = generate_specfic_blog_menu( $year, $month, '0' );
		}


		return $menu;
	}

	//=== Util functions ====================================================//
	//--- blog menu ---//
	function generate_specfic_blog_menu( $target_year, $target_month, $target_id ) {
		$menu = '';
		$C_year = 0;
		$C_month = 0;
		// Due to how I chop the date this is the best way for now.
		$rep_mon = array( '01' => 'January',
											'02' => 'February',
											'03' => 'March',
											'04' => 'April',
											'05' => 'May',
											'06' => 'June',
											'07' => 'July',
											'08' => 'August',
											'09' => 'September',
											'10' => 'October',
											'11' => 'November',
											'12' => 'December'
										);

		$sel_b = "SELECT * FROM `GO-blog` ORDER BY `time` DESC";
		$res_b = query_db( $sel_b );

		$menu .= '<ul>
						<li id="top_blog_menu">
							&nbsp;
						</li>
					';
		while( $blog = mysql_fetch_assoc( $res_b ) ) {
			$blog_year  = substr( $blog[ 'time' ], 0, 4 ); // This blogs year.
			$blog_month = substr( $blog[ 'time' ], 5, 2 ); // This blogs month

			// Are in the same year as the one we are looking for.
			if( $blog_year == $target_year) {
				if( $C_year != $blog_year ) {
					$date = $blog_year.'00';
					$menu .= '	<li class="blog_selected_year">
							<a href="index.php?action=blogmenu&date='.$date.'"> '.$blog_year.'</a>
						</li>
					';
					$C_year = $blog_year;
				}
				// Same month
				if( $blog_month == $target_month ) {
					$blog_id = $blog[ 'id_blog' ];
					if( $C_month != $blog_month ) {
						$date = $blog_year.'00';
						$name_month = $rep_mon[ $blog_month ];

						$menu .= '	<li class=blog_selected_month>
							<a href="index.php?action=blogmenu&date='.$date.'"> '.$name_month.'</a>
						</li>
					';
						$C_month = $blog_month;
					}
					if( $blog_id == $target_id ) {
						$blog_title = $blog[ 'title' ];
						if( strlen( $blog_title ) > 25 ) {
							$blog_title = substr( $blog_title, 0, 22 );
							$blog_title = $blog_title.'...';
						}
						$menu .= '	<li class=blog_selected_blog>
							<a href="index.php?action=blog&id='.$target_id.'"> '.$blog_title.'</a>
						</li>
					';
					}
					else { // Not the selected blog.
						$blog_title = $blog[ 'title' ];
						if( strlen( $blog_title ) > 24 ) {
							$blog_title = substr( $blog_title, 0, 21 );
							$blog_title = $blog_title.'...';
						}
						$menu .= '	<li class=blog_unselected_blog>
							<a href="index.php?action=blog&id='.$blog_id.'"> '.$blog_title.'</a>
						</li>
					';
					}
				}
				else { // Not the selected month
					if( $C_month != $blog_month ) {
						$date = $blog_year.$blog_month;
						$name_month = $rep_mon[ $blog_month ];
						$menu .= '	<li class=blog_unselected_month>
								<a href="index.php?action=blogmenu&date='.$date.'"> '.$name_month.'</a>
							</li>
						';
						$C_month = $blog_month;
					}
				}
			}
			else { // Wasn't the selected year.
				if( $C_year != $blog_year ) {
					$date = $blog_year.'00';
					$menu .= '	<li class="blog_unselected_year">
								<a href="index.php?action=blogmenu&date='.$date.'"> '.$blog_year.'</a>
							</li>
						';
					$C_year = $blog_year;
				}
			}
		}
		$menu .= '</ul>
';

		return $menu;
	}


	//=======================================================================//
	// This runs every time the file is included or run.
	echo generate_blogmenu();
?>