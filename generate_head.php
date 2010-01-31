<?php
	function generate_head() {
		$theme  = $_SESSION[ 'theme' ];
		$head = '';
		
		if( $theme == 'default' ) {
			$head	= '<link rel="SHORTCUT ICON" href="logo2.ico" />
		<link rel="stylesheet" href="general.css" type="text/css" media="screen" />
		<title> Good Omens - Werners place on the web! </title>
';
		}
		else {
			$head	= '<link rel="SHORTCUT ICON" href="logo2.ico" />
		<link rel="stylesheet" href="general.css" type="text/css" media="screen" />
		<title> Good Omens - Werners place on the web! </title>
';
		}
		
		return $head;
	}
	echo generate_head();
?>