<main data-uk-height-viewport class="uk-flex uk-flex-middle uk-flex-center">
	<div class="login">
		<form method="post" action="<?php echo current_url(true); ?>" data-uk-grid class="uk-grid-small">
			<div class="uk-width-1-1 uk-text-center"><h1><?php echo lang('Auth.login_confirm') ?></h1></div>
			<div class="uk-width-1-1 uk-text-center"><?php echo lang('Auth.login_confirm_desc') ?></div>
			<div class="uk-width-1-1 uk-text-center"><?php echo lang('Auth.login_confirm_left') ?> <span class="timer"></span> <?php echo lang('Auth.seconds') ?></div>
			
			<div class="uk-width-1-1"><label><?php echo lang('Auth.login_confirm_code') ?></label><input class="uk-input" type="text" name="code" value="" autocomplete="off" required></div>
			<div class="uk-width-1-1"><button class="uk-button uk-button-primary uk-width-1-1" type="submit" name="submit" value="Submit_code"><?php echo lang('Auth.login_confirm_button') ?></button></div>
		</form>
	</div>
<main>
<script>
var counter = <?php echo $leftTime ?>;
$('.timer').html(counter);
var x = setInterval(function () {
	counter = counter-1;
	$('.timer').html(counter);
	if(counter<1){ document.location.reload(); }
},1000);
</script>