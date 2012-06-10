
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
	
	<form class="form-horizontal well offset2 span8" name='resetpasswordForm' method='post' action='<? echo $SITE['url']; ?>resetpassword'>
		<fieldset>
			<legend>Reset Password Form</legend>
			<input type='hidden' name='code' value='<? echo !emtpy($PARAMS['code']) ? $PARAMS['code'] : ''; ?>' />
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
			<div class='form-actions'>
				<button type='submit' class='btn btn-primary'>Reset Password</button>
			</div>
		</fieldset>
	</form>
	
</div>
