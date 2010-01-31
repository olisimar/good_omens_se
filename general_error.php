<?php
	/*
		Authour: Werner
		Edited:	09-08-15
		Deals with various error handling issues needed to tell the user what went
		wrong with their last action. It has one function only and as that then
		creates the needed info and displays it in the <div id="main_info">.
		This is called from generate_info.php in case $_SESSION[ 'last_action' ]
		was triggered to FALSE.
	*/

	function error_handling() {
		$emess = $_SESSION[ 'error_mess' ];
		$resp = '';

		// This is the session has the wrong IP as to what was logged in with.
		if( $emess == 'wrong_session') {
			$resp .= '
								<div id="error">
									<h4> Security warning </h4>
									<p>
										Message: Somehow we are getting the indication that your
										session is FALSE. This has been logged and you are thrown
										out. Also I might have fun with you now, noone mourns a
										cracker.
									</p>
								</div>
								';
								
			session_destroy();
		}
		// This is the user failed the security question.
		else if( $emess == 'failed_security' ) {
			$resp .= '
								<div id="error">
									<h4> Security Error </h4>
									<p>
										You failed to answer the security question correctly. Redo
										the action again and answer it correctly.
									</p>
								</div>
								';
		}
		// If I don't know what it is, this is the general error catcher.
		else {
			$resp .= '
								<div id="error">
									<h4> General Error </h4>
									<p>
										'.$emess.'
									</p>
								</div>
								';
		}

		// Resetting the error status to avoid mishaps.
		$_SESSION[ 'last_action' ] = TRUE;
		
		return $resp;
	}
?>