<?php
	/*
		All of these functions may be subject to be moved into the files named
		as the functions as they grow. They are all made with a return to that
		they can be used from other files.
		Currently they are all echoed from elsewhere.
	*/
	function generate_head() {
		$head	= '<link rel="SHORTCUT ICON" href="logo2.ico" />
						 <link rel="stylesheet" href="general.css" type="text/css" media="screen" />
						 <title> Good Omens - Werners place on the web! </title>
				 		';
				 
		return $head;
	}

	function generate_header() {
		$he = '<p>
					 Page generation has begun...
					 </p>
					';
					
		return $he;
	}
	
	function generate_menu() {
		$menu =  '<p>
							No menu yet...
							</p>
						 ';
		return $menu;
	}

	function generate_info() {
		$info = '<p>
						 Information is being generated...
						 </p>
				 		';

		return $info;
	}

	function generate_footer() {
		$foot = '<p>
						 Some information is being routed...
						 </p>
				 		';

		return $foot;
	}
?>