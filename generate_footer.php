<?php
	function generate_footer() {
		$foot = '<p>
					Category: '.$_SESSION[ 'id_menu' ].' Article: '.$_SESSION[ 'id_art' ].' <br />
					Theme: '.$_SESSION[ 'theme' ].' IP: '.$_SERVER[ 'REMOTE_ADDR' ].' Loggedin: '. $_SESSION[ 'loggedin' ].' <br />
				</p>
';

		return $foot;
	}

	echo generate_footer();
?>