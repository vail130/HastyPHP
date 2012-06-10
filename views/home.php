
<div class='container'>
	
	<?
	if(!empty($_SESSION['flash']['msg'])) {
		if($_SESSION['flash']['status'] === 'success') {
			$labelClass = 'success';
			$labelText = 'Success';
		}
		else {
			$labelClass = 'important';
			$labelText = 'Error';
		}
	?>
		<h3 class='offset2 span8 flashMessage'>
			<span class='label label-<? echo $labelClass; ?>'><? echo $labelText; ?></span>
			<? echo $_SESSION['flash']['msg']; ?>
		</h3>
		<?
		unset($_SESSION['flash']);
	}
	?>
	
	<header class='jumbotron subhead'>
		<h1><? echo $SITE['displayName']; ?></h1>
		<p class='lead'>A really, <i>REALLY</i> simple MVC framework in PHP and MySQL.</p>
		<ul class='lead-list'>
			<li>Responsive Twitter Bootstrap</li>
			<li>New User registration</li>
			<li>User email address confirmation</li>
			<li>Signing in & out</li>
			<li>Creating and emailing unique links for users to reset forgotten passwords</li>
			<li>A basic HTML email template and system for sending emails through SMTP</li>
			<li>Password hashing with bcrypt.</li>
		</ul>
	</header>
</div>
