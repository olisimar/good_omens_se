<?php
	/*
		Created: 09-08-10
		Authour: Werner
		This file generates the header <div>.

		generate_login()/userpanel()/adminpanel() are located in general_html.php
	*/
	function generate_header() {
		// presets.
		$action = 'userpanel';
		$hea = '';

		// If there is 'active' GET['action'] we set it here.
		if( ( isset( $_GET[ 'action' ] ) ) ){
			$action = $_SESSION[ 'action' ];
		}

		if( isset( $_SESSION[ 'failed_login' ] ) ) {
			$action = 'login';
			unset( $_SESSION[ 'failed_login' ] );
		}
		if( isset( $_SESSION[ 'loggedin' ] ) ) {
			$action = 'adminpanel';
		}

		if( $action == 'login' ) {
			$hea .= generate_login( 'login', 3 );
		}
		else if( $action == 'adminpanel' ) {
			$hea .= generate_adminpanel( 'admin' );
		}
		else {
			$hea .= generate_userpanel( 'login' );
		}


		return $hea;
	}

	echo generate_header();
?>