
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
	
	<form class="form-horizontal well offset2 span8" name='signinPageForm' method='post' action='<? echo $SITE['url']; ?>signin'>
		<fieldset>
			<legend>Sign In Form</legend>
			<div class="control-group">
				<label class="control-label" for="name">User Name</label>
				<div class="controls">
					<input type="text" class="input-xlarge" id="name" name='name' value='<? echo !empty($_SESSION['flash']['name']) ? $_SESSION['flash']['name'] : ''; ?>' />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="password">Password</label>
				<div class="controls">
					<input type="password" class="input-xlarge" name='password' id="password" />
				</div>
			</div>
			<div class='form-actions'>
				<button type='submit' class='btn btn-primary'>Sign In</button>
			</div>
		</fieldset>
	</form>
	
</div>
