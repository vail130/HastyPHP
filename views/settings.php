
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
	
	<form class="form-horizontal well offset2 span8" name='changepasswordForm' method='post' action='<? echo $SITE['url']; ?>changepassword'>
		<fieldset>
			<legend>Change Password Form</legend>
			<div class="control-group">
				<label class="control-label" for="op">Old password</label>
				<div class="controls">
					<input type="password" class="input-xlarge" name='op' id="op" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="p1">New Password</label>
				<div class="controls">
					<input type="password" class="input-xlarge" name='p1' id="p1" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="p2">Verify New Password</label>
				<div class="controls">
					<input type="password" class="input-xlarge" name='p2' id="p2" />
				</div>
			</div>
			<div class='form-actions'>
				<button type='submit' class='btn btn-primary'>Change Password</button>
			</div>
		</fieldset>
	</form>
	
</div>