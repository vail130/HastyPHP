<?PHP
	
	$url = $SITE['url'];
	$name = $SITE['displayName'];
	$title = $SITE['displayName'];
	$description = $SITE['description'];
	
	$keywords = $SITE['keywords'];
	
	$favicon = $SITE['favicon'];
	$appleIcon = $SITE['appleIcon'];
	$appleIcon72 = $SITE['appleIcon72'];
	$appleIcon114 = $SITE['appleIcon114'];
	$thumbnail = $SITE['thumbnail'];
		
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
	
	<!-- Basic Page Needs
  ================================================== -->
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><? echo $title; ?></title>
	<meta name="author" content="<? echo $name; ?>">
	<meta name='description' content='<? echo $description; ?>' />
	<meta name='keywords' content='<? echo $keywords; ?>' />
	
	<meta property="og:site_name" content="<? echo $name; ?>" />
	<meta property="og:title" content="<? echo $title; ?>" />
	<meta property="og:url" content="<? echo $url; ?>" />
	<meta property="og:image" content="<? echo $thumbnail; ?>" />
	<meta property="og:description" content="<? echo $description; ?>" />
	
	<meta itemprop="name" content="<? echo $name; ?>">
	<meta itemprop="description" content="<? echo $description; ?>">
	<meta itemprop="image" content="<? echo $thumbnail; ?>">
	

	<!-- Mobile Specific Metas
  ================================================== -->
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	<meta name="HandheldFriendly" content="true" />

	<!-- CSS
  ================================================== -->
	<style type='text/css'>
	
	<?
	ob_start();
	
	try {
		lessc::ccompile($SITE['path'].'css/style.less', $SITE['path'].'css/style.css');
	} catch (exception $ex) {
		exit('lessc fatal error:<br />'.$ex->getMessage());
	}
	
	require($SITE['path'].'css/bootstrap.min.css');
	require($SITE['path'].'css/prettify.css');
	require($SITE['path'].'css/docs.css');
	require($SITE['path'].'css/style.css');
	require($SITE['path'].'css/bootstrap-responsive.min.css');
	
	$css = ob_get_contents();
	ob_end_clean();
	#echo CssMin::minify($css);
	echo $css;
	
	?>
	</style>
	
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Favicons
	================================================== -->
	<link rel='shortcut icon' href='<? echo $favicon; ?>' />
	<link rel='icon' href='<? echo $favicon; ?>' />
	<link rel="apple-touch-icon" href="<? echo $appleIcon; ?>">
	<link rel="apple-touch-icon" sizes="72x72" href="<? echo $appleIcon72; ?>">
	<link rel="apple-touch-icon" sizes="114x114" href="<? echo $appleIcon114; ?>">

</head>
<body data-offset='40'>
	<div id='debug'></div>
	<div id='whiteShadow' class='shadow'></div>
	<div id='blackShadow' class='shadow'></div>
	
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner" style='padding:0'>
			<div class="container">
					<ul class="nav">
						<li class="<? echo $PAGE === 'home' ? 'active' : ''; ?>">
							<a href='<? echo $SITE['url']; ?>'>Home</a>
						</li>
					</ul>
					<ul class='nav pull-right'>
						<?
						if($SESSION === true) {
						?>
						<li class='dropdown'>
							<a id='accountLink' href='<? echo $SITE['url']; ?>settings' class='dropdown-toggle' data-toggle='account'>Account<b class='caret'></b></a>
							<ul class='dropdown-menu'>
								<li><a href='<? echo $SITE['url']; ?>settings'>Settings</a></li>
								<?
								if(SessionController::getSessionType() === 'administrator') {
								?>
								<li><a href='<? echo $SITE['url']; ?>admin'>Admin</a></li>
								<?
								}
								?>
								<li><a href='<? echo $SITE['url']; ?>signout'>Sign Out</a></li>
							</ul>
						</li>
						<? } else { ?>
						<li class='<? echo $PAGE === 'signin' ? 'active' : ''; ?>'>
							<a id='signinLink' href='<? echo $SITE['url']; ?>signin'>Sign In</a>
						</li>
						<li class='<? echo $PAGE === 'register' ? 'active' : ''; ?>'>
							<a id='registerLink' href='<? echo $SITE['url']; ?>register'>Register</a>
						</li>
						<!--
						<div id='signinMenu' class='dropdownMenu'>
							<form id='signinForm' method='post' action='<? echo $SITE['url']; ?>signin'>
								<label class='dropdownMenuLabel' for='name'>User Name</label>
								<input class='dropdownMenuInput name' type='text' name='name' />
								<label class='dropdownMenuLabel' for='password'>Password</label>
								<input class='dropdownMenuInput password' type='password' name='password' />
								<button class='signinSubmit dropdownMenuButton'>Sign In</button>
							</form>
						</div>
						-->
						<?
						}
						?>
					</ul>
			</div>
		</div>
	</div>
	
	
	<?
	
	if(file_exists($SITE['views'].$PAGE.'.php')) {
		require($SITE['views'].$PAGE.'.php');
	}
	
	?>
	
	
	
	<div id='footer'>
		<div class='big-container'>
			
		</div>
	</div>
	
</body>

<script type='text/javascript' src='<? echo $SITE['jsUtil']; ?>jquery-1.7.min.js'></script>
<script type='text/javascript' src='<? echo $SITE['jsUtil']; ?>jquery-ui-1.8.16.min.js'></script>

<!--
<script type='text/javascript' src='<? echo $SITE['jsUtil']; ?>underscore.min.js'></script>
<script type='text/javascript' src='<? echo $SITE['jsUtil']; ?>backbone.min.js'></script>
<script type='text/javascript' src='<? echo $SITE['jsUtil']; ?>modernizr.min.js'></script>
-->

<script type='text/javascript'>
	<?
	
	$min = '';
	ob_start();
	
	if(file_exists($SITE['path'].'js/site.js')) {
		require($SITE['path'].'js/site.js');
	}
	if(file_exists($SITE['path'].'js/'.$PAGE.'.js')) {
		require($SITE['path'].'js/'.$PAGE.'.js');
	}
	
	$min = ob_get_contents();
	ob_end_clean();
	
	/*
		Un-comment this for production
	*/
	#echo JSMin::minify($min);
	
	/*
		This is better for development & debugging
	*/
	echo $min;
	
	?>
</script>

</html>