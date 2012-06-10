
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
	
	<form class="form-horizontal well offset2 span8" name='forgotpasswordPageForm' method='post' action='<? echo $SITE['url']; ?>forgotpassword'>
		<fieldset>
			<legend>Forgot Password Form</legend>
			<div class="control-group">
				<label class="control-label" for="email">Email Address</label>
				<div class="controls">
					<input type="text" class="input-xlarge" id="email" name='email' value='<? echo !empty($_SESSION['flash']['email']) ? $_SESSION['flash']['email'] : ''; ?>' />
				</div>
			</div>
			<div class='form-actions'>
				<button type='submit' class='btn btn-primary'>Reset Password</button>
			</div>
		</fieldset>
	</form>
	
</div>