<main>
	<div data-uk-grid class="uk-grid-small">
		<div class="uk-width-1-1"><span data-uk-icon="icon:arrow-left" class="uk-margin-small-right"></span><a href="<?php echo $managerUrl; ?>/users"><?php echo lang('Manager.back_user') ?></a></div>
		<div class="uk-width-expand uk-flex uk-flex-middle"><h1><?php echo lang('Manager.user_edit') ?> <span class="uk-text-danger"><?php echo $user[$id]['first_name'] ?> <?php echo $user[$id]['last_name'] ?></span></h1></div>
		<div class="uk-width-1-1"><hr></div>
		<?php if ( !empty($alert) ) { ?>
		<div class="uk-width-1-1">
			<div class="alert-border">
				<?php echo lang('Manager.alert_'.$alert) ?>
			</div>
		</div>
		<?php } ?>
		<div class="uk-width-1-1">
			<form method="post" action="<?php echo current_url(true); ?>" data-uk-grid class="uk-grid-medium">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
				<input hidden class="uk-input" value="<?php echo $id; ?>" type="text" name="id" required>
				<div class="uk-width-1-3">
					<div data-uk-grid class="uk-grid-small">
						<div class="uk-width-1-1"><label><?php echo lang('Manager.first_name') ?></label><input class="uk-input" type="text" name="first_name" value="<?php echo htmlspecialchars($user[$id]['first_name']) ?>"></div>
						<div class="uk-width-1-1"><label><?php echo lang('Manager.last_name') ?></label><input class="uk-input" type="text" name="last_name" value="<?php echo htmlspecialchars($user[$id]['last_name']) ?>"></div>
						<div class="uk-width-1-1"><label><?php echo lang('Manager.phone') ?></label><input class="uk-input" type="text" name="phone" value="<?php echo $user[$id]['phone'] ?>"></div>
						<div class="uk-width-1-1"><label><?php echo lang('Manager.email') ?></label><input class="uk-input" type="text" name="email" value="<?php echo $user[$id]['email'] ?>"></div>					
						<div class="uk-width-1-1">
							<label><?php echo lang('Manager.user_group') ?></label>
							<select name="group" class="uk-select">
								<?php foreach ($userGroup as $value ) { ?>
								<option value="<?php echo $value['id'] ?>" <?php echo ( $value['id']==$user[$id]['group_id'] ) ? "selected" : "" ; ?>><?php echo $value['title'] ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="uk-width-1-1">
							<label><?php echo lang('Manager.user_email_send') ?></label>
							<select name="email_send" class="uk-select">
								<option value="0" <?php echo ( $user[$id]['email_send']==0 ) ? "selected" : "" ; ?>><?php echo lang('Manager.no') ?></option>
                                <option value="1" <?php echo ( $user[$id]['email_send']==1 ) ? "selected" : "" ; ?>><?php echo lang('Manager.yes') ?></option>
							</select>
						</div>
					</div>
				</div>
				<div class="uk-width-2-3">
					<div data-uk-grid class="uk-grid-small">
						<div class="uk-width-1-1"><label><?php echo lang('Manager.user_title') ?></label><input class="uk-input" type="text" name="title" value="<?php echo htmlspecialchars($user[$id]['title']) ?>"></div>
						<div class="uk-width-1-1"><label><?php echo lang('Manager.user_description') ?></label><textarea class="uk-textarea" name="description" rows="6"><?php echo $user[$id]['description'] ?></textarea></div>
						<div class="uk-width-1-1"><label><?php echo lang('Manager.user_comment') ?></label><textarea class="uk-textarea" name="comment"  rows="7"><?php echo $user[$id]['comment'] ?></textarea></div>
					</div>
				</div>
				<div class="uk-width-1-1">
					<a href="#" data-uk-toggle="target:.edit-pass" class="edit-pass"><?php echo lang('Manager.user_edit_pass') ?></a>
					<div class="alert edit-pass" hidden>
						<div data-uk-grid class="uk-grid-small">
							<div class="uk-width-1-2@s"><label><?php echo lang('Manager.password') ?></label><input class="uk-input" type="text" name="password" value=""></div>
							<div class="uk-width-1-2@s"><label><?php echo lang('Manager.password_repeat') ?></label><input class="uk-input" type="text" name="password_repeat" value=""></div>
						</div>
					</div>
				</div>
				<div class="uk-width-expand"><a data-uk-toggle href="#del" class=""><span data-uk-icon="icon:trash" class="uk-margin-small-right"></span></a></div>
				<div class="uk-width-auto uk-flex uk-flex-bottom"><button class="uk-button uk-button-primary" type="submit" name="submit" value="Submit_edit"><?php echo lang('Manager.save') ?></button></div>
			</form>
		</div>
	</div>
</main>
<div id="del" data-uk-modal="bg-close:false;">
    <div class="uk-modal-dialog uk-modal-body">
        <button class="uk-modal-close-default" type="button" data-uk-close></button>
		<div data-uk-grid class="uk-grid-small">
			<div class="uk-width-1-1 uk-text-center"><h4><?php echo lang('Manager.user_delete') ?></h4></div>
			<div class="uk-width-1-1 uk-text-center uk-text-large"><span class="del-title"><?php echo $user[$id]['email'] ?></span></div>
			<div class="uk-width-1-1 uk-text-center">
				<form method="post" action="<?= current_url(true); ?>">
					<input class="input_item_id" name="id" value="<?php echo $id ?>" type="text" hidden>
					<button class="uk-button uk-button-danger" type="submit" name="submit" value="Submit_del"><?php echo lang('Manager.confirm') ?></button>
				</form>
			</div>
		</div>
    </div>
</div>