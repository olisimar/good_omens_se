<?php
	/* * * * * Util Functions * * * * *
	 *	These functions are for general use on the page, used by all sections of
	 *	the php scripts. It has no script to run it self, it's all for the
	 *	benefit of the other files/scripts.
	 *	Also a nice central point for util functions which is what these are for.
	 * * * * * * * * * * * * * * * * * * */

	/*
		Public
		Connect to a database and make a SQL query retuns the result as is.
	*/
	function query_db( $indata ) {
	  /*
		$resurs = mysql_connect( 'mysql05.cliche.se', 'good-omens.se', 'zka72kbh' );
		$resurs = mysql_connect( 'mysql10.cliche.se', 'good-omens.se', 'zka72kbh' );
		$db = mysql_select_db( 'good_omens_se', $resurs );
		*/
		$resurs = mysql_connect( 'localhost', 'good-omens.se', 'zka72kbh' );
		$db = mysql_select_db( 'good_omens_se', $resurs );

		$result = mysql_query( $indata ) or die( mysql_error() );
		mysql_close( $resurs );
		return $result;
	}
/*
		$resurs = mysql_connect( 'mysql05.cliche.se', 'good-omens.se', 'zka72kbh' );
		$database = mysql_select_db( 'good_omens_se', $resurs );

		$result = mysql_query( $query_login );
*/

	/*
		Public
		Cleans any indata and strips it of any extra chars. If it fails it returns
		nothing which in it self is FALSE.
	*/
	function clean( $inData ) {
 		$resurs = mysql_connect( 'localhost', 'good-omens.se', 'zka72kbh' );

		if( get_magic_quotes_gpc() == 1 ) {
			$inData = stripslashes( $inData );
		}

		$inData = mysql_real_escape_string( $inData, $resurs );
		if( $inData ) {
			return $inData; // returns the string if it was ok.
		}
		else {
			return "";
		}
		return $inData;
	}

	/*
		This will provide a standardized security question to be placed within
		a <form>. It has no own form tags or classes. Standard CSS will apply.
	*/
	function get_security() {
		$ran = 1;
		$que = '';
		$q_sec = "SELECT * FROM `GO-security` ORDER BY `id_security`";
		$r_sec = query_db( $q_sec );
		$num_s = mysql_num_rows( $r_sec );

		$ran = rand( 1,$num_s );

		//Might have to change to account for removed questions. While or foreach.
		$q_sel = "SELECT * FROM `GO-security` WHERE `id_security`=$ran";
		$r_sel = query_db( $q_sel );
		$item = mysql_fetch_assoc( $r_sel );
		$question = $item[ 'question' ];
		$image = $item[ 'image' ];
		$alter = $item[ 'description' ];
		$id = $item[ 'id_security' ];

		$que .= '
						<fieldset id="question">
							<label name="answer">
								<img src="security/'.$image.'" alt="'.$alter.'" height="100" />
							</label>
							<span> '.$question.' </span> <br />
							<input type="hidden" value="'.$id.'" name="quest" />
							<input type="text" name="answer" /> <br />
							<span id="instruct"> ( use lowercase letters only ) </span>
						</fieldset>
						';

		return $que;
	}
?>