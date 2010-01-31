<?php
	/*
		Created: 09-08-30
		Authour: Werner Johansson
		This file deals with showing comments to blogs and possibly articles in
		the future. This goes through the $_GET[] array and such will be checked
		here as well.

		clean() is located in : general_util.php
	*/
	function show_comments( $id ) {
		$coms = ''; // The <div> we'll return.
		$email = '';
		$edit_all = 'N';
		if( isset( $_SESSION[ 'loggedin' ] ) ) {
			$email = $_SESSION[ 'user' ];
			$edit_all = $_SESSION[ 'rem_com' ]; // If I can remove I can edit.
		}
		$sel_c = "SELECT * FROM `GO-comment` WHERE `id_blog`='$id' ORDER BY `time` ASC";
		$res_c = query_db( $sel_c );

		while( $curc = mysql_fetch_assoc( $res_c ) ) {
			$authour = $curc[ 'authour' ];
			$writer = $curc[ 'email' ];
			$id_com = $curc[ 'id_comment' ];
			$text = $curc[ 'comment' ];
			$time = $curc[ 'time' ];

			$coms .= '	<div class="blog_comment">
							<div class="blog_comment_head">
								Comment by: '.$authour;
			if( $edit_all == 'Y' ) {
				$coms .= '
								<span>
									<a href="index.php?action=remove_comment&id='.$id_com.'"> Remove Comment </a>
								</span>
								<br />
							';
			}
			else {
				$coms .= '<br />
							';
			}
			$coms .='Date:'.$time.'
							';
			if( ( $email == $writer ) || ( $edit_all == 'Y' ) ) {
				$coms .= '	<span>
									<a href="index.php?action=edit_comment&id='.$id_com.'"> Edit Comment </a>
								</span>
							</div>
							';
			}
			else {
				$coms .= '</div>
							';
			}
			$coms .= '<div class="blog_comment_text">
								'.$text.'
							</div>
							<div class="blog_comment_end">
							</div>
						</div> <!-- EoD: class=>blog_comment (gen_comment)-->
				';
		} // EndOf while( $curc = ...)


		return $coms;
	}
?>