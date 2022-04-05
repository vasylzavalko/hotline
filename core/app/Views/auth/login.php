<main data-uk-height-viewport class="uk-flex uk-flex-middle uk-flex-center">
	<div class="login">
		<form method="post" action="<?php echo current_url(true); ?>" data-uk-grid class="uk-grid-small">
			<div class="uk-width-1-1 uk-text-center"><h1><?php echo lang('Auth.login') ?></h1></div>
			<div class="uk-width-1-1"><label><?php echo lang('Manager.email') ?></label><input class="uk-input" type="text" name="email" value="" required></div>
			<div class="uk-width-1-1"><label><?php echo lang('Manager.password') ?></label><input class="uk-input" type="password" name="password" value="" autocomplete="off" required></div>
			<div class="uk-width-1-1"><button class="uk-button uk-button-primary uk-width-1-1" type="submit" name="submit" value="Submit_login"><?php echo lang('Auth.login_button') ?></button></div>
            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
		</form>
	</div>
<main>