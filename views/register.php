
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
	
	<form class="form-horizontal well offset2 span8" name='registerForm' method='post' action='<? echo $SITE['url']; ?>register'>
		<fieldset>
			<legend>Registration Form</legend>
			<div class="control-group">
				<label class="control-label" for="name">User Name</label>
				<div class="controls">
					<input type="text" class="input-xlarge" name='name' id="name" value='<? echo !empty($_SESSION['flash']['name']) ? $_SESSION['flash']['name'] : ''; ?>' />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="email">Email Address</label>
				<div class="controls">
					<input type="text" class="input-xlarge" name='email' id="email" value='<? echo !empty($_SESSION['flash']['email']) ? $_SESSION['flash']['email'] : ''; ?>' />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="p1">Password</label>
				<div class="controls">
					<input type="password" class="input-xlarge" name='p1' id="p1" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="p2">Verify Password</label>
				<div class="controls">
					<input type="password" class="input-xlarge" name='p2' id="p2" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="ref">Referral Code</label>
				<div class="controls">
					<input type="text" class="input-xlarge" name='ref' id="ref" value='<? echo !empty($PARAMS['ref']) ? $PARAMS['ref'] : (!empty($_SESSION['flash']['ref']) ? $_SESSION['flash']['ref'] : ''); ?>' />
					<p class="help-block">Optional code provided by existing user.</p>
				</div>
			</div>
			<div class='form-actions'>
				<button type='submit' class='btn btn-primary'>Register</button>
			</div>
		</fieldset>
	</form>
	
</div>