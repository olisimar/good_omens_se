<?php
	session_start();
	include( 'general_util.php' );
	include( 'check_incoming.php' );
	
	include( 'general_html.php' );
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta name="author" content="Werner Johansson" />
		<meta name="generator" content="Kate/KDE4" />
		<meta name="description" content="Good Omens" />
		<meta name="keywords" content="werner,tux,good-omens" />
		<meta http-equiv="Content-Script-Type" content="text/javascript" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
		<?php
			include( 'generate_head.php' );
		?>
	</head>

	<body>
		<div id="wrapper">
		
			<!-- Keeps the logo and such in place -->
			<div id="header">
				<?php
					include( 'generate_header.php' );
				?>
			</div> <!-- EoD: header -->
			
			<!-- Menu, info and possible submenu -->
			<div id="main">
			
				<!-- Menu, generated from PHP -->
				<div id="main_menu">
					<?php
						include( 'generate_menu.php' );
					?>
				</div><!-- EoD: main_menu -->
				
				<!-- Information generated from PHP, possible db entry -->
				<div id="main_info">
					<?php
						include( 'generate_info.php' );
					?>
				</div> <!-- end of main_info -->
				
				<div id="blog_menu">
					<?php
						include( 'generate_blogmenu.php' );
					?>
				</div> <!-- End of Blog Menu -->
				
			</div> <!-- End of Main -->
			<!-- News items and possible last entry links -->
			<div id="footer">
				<?php
					include( 'generate_footer.php' );
				?>
			</div> <!-- EoD: footer -->
		</div> <!-- EoD: wrapper -->
	</body>
</html>