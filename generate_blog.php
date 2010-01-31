<?php
	/*
		Created: 09-09-06
		Authour: Werner
		This file is for the sole purpose of showing off blogs and it's comments
		if there are any. The comments are added with the file
		'generate_comment.php'. It's added at the bottom of the blog show and is
		a seperate entity from the blogs but should blend in seamlessly hopefully.
	*/

	//--- General call function ---//
	function show_blog( $id_blog ) {
		$show = '';
		$sel_b = "SELECT * FROM `GO-blog` WHERE `id_blog`='$id_blog'";
		$res_b = query_db( $sel_b );
		$blog = mysql_fetch_assoc( $res_b );

		$date = substr( $blog[ 'time' ], 0,10 );
		$title = $blog[ 'title' ];
		$text = $blog[ 'text' ];
		$authour = $blog[ 'authour' ];

		$show .= '
					<div id="show_blog">
						<div id="blog_head">
							<h3> '.$date.' - '.$title.' </h3>
						</div>
						<div id="blog_body">
							'.$text.'
						</div>
						<div id="blog_authour">
							<p> '.$authour.' </p>
						</div>
					</div>
					';

		$sel_c = "SELECT `id_comment` FROM `GO-comment` WHERE `id_blog`='$id_blog'";
		$res_c = query_db( $sel_c );
		$num_com = mysql_num_rows( $res_c );

		// Are comments to be shown?
		$show_com = $_SESSION[ 'show_comments' ];

		if( isset( $_GET[ 'show_comments' ] ) ) {
			$show_com = 'Y';
		}
		else if( isset( $_GET[ 'hide_comments' ] ) ) {
			$show_com = 'N';
		}

		// Actual production of the div begins here.
		$show .= '<div id="blog_comments">
						<p>
							There are '.$num_com.' comments for this blog.
							<span>
							';

		if( isset( $_SESSION[ 'loggedin' ] ) ) {
			$show .= '	<a href="index.php?action=comment&id='.$id_blog.'"> Comment </a>
								';
		}
		else {
			$show .= '	<a href="index.php?action=login"> Login to comment </a>
								';
		}

		if( ( $show_com == 'Y' ) && ( $num_com != 0 ) ) {
			$show .= '	<a href="index.php?action=blog&id='.$id_blog.'&hide_comments"> Hide Comments </a>
							</span>
						</p> <!-- if -->
				</div> <!-- EoD: blog_comments (gen_blog) -->
				<div id="comments">
				';

			include( 'generate_comment.php' );
			$show .= show_comments( $id_blog );

			$show .= '	</div> <!-- EoD: comments (gen_blog)-->
					';
		}
		else if( $num_com != 0 ) {
			$show .= '<a href="index.php?action=blog&id='.$id_blog.'&show_comments"> Show Comments </a>
							</span>
						</p> <!-- else if -->
					</div> <!-- EoD: (gen_blog) -->
					';
		}
		else {
			$show .= '	</span>
						</p>
					</div>
				';
		}

		$show .= '
';


		return $show;
	}
?>