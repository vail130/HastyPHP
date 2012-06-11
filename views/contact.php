
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
	
	<form class="form-horizontal well offset2 span8" name='contactForm' method='post' action='<? echo $SITE['url']; ?>contact'>
		<fieldset>
			<legend>Contact Form</legend>
			<div class="control-group">
				<label class="control-label" for="name">Full Name</label>
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
				<label class="control-label" for="subject">Subject</label>
				<div class="controls">
					<input type="text" class="input-xlarge" name='subject' id="subject" value='<? echo !empty($_SESSION['flash']['subject']) ? $_SESSION['flash']['subject'] : ''; ?>' />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="message">Message</label>
				<div class="controls">
					<textarea class="input-xlarge" rows='5' name='message' id="message"><? echo !empty($_SESSION['flash']['message']) ? $_SESSION['flash']['message'] : ''; ?></textarea>
				</div>
			</div>
			<div class='form-actions'>
				<button type='submit' class='btn btn-primary'>Submit</button>
			</div>
		</fieldset>
	</form>
	
</div>