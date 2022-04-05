<main>
	<div data-uk-grid class="uk-grid-small">
		<div class="uk-width-expand uk-flex uk-flex-middle"><h1><?php echo $pageTitle; ?></h1></div>
        
		<div class="uk-width-1-1 uk-margin-medium-top">
			<div class="box-border">
				<div class="box-border-label"><?php echo lang('Manager.message_packages') ?></div>
				<div data-uk-grid class="uk-grid-small">
					<?php if( count($messagePackage)>0 ){ ?>
					<div class="uk-width-expand@s uk-flex uk-flex-middle">
						<b class="uk-margin-small-right"><?php echo lang('Manager.all_message_package') ?></b><?php echo $messageTotal ?>
					</div>
					<div class="uk-width-auto@s">
						<a data-uk-toggle href="#add-package" class="uk-button uk-button-primary uk-button-small uk-flex uk-flex-middle"><?php echo lang('Manager.message_package_add') ?><span class="uk-margin-small-left" data-uk-icon="icon:chevron-down;ratio:.8"></span></a>
					</div>				
					<div class="uk-width-1-1" id="add-package" hidden>
						<div class="alert">
							<form method="post" action="<?= current_url(true); ?>" data-uk-grid class="uk-grid-small">
								<input hidden class="uk-input" value="0" type="text" name="id" required>
								<div class="uk-width-1-1"><label><?php echo lang('Manager.message') ?></label><textarea class="uk-textarea" type="text" name="message" value="" rows="6" required></textarea></div>
								<div class="uk-width-auto uk-flex uk-flex-bottom"><button class="uk-button uk-button-primary" type="submit" name="submit" value="Submit_add_package"><?php echo lang('Manager.add') ?></button></div>
							</form>
						</div>
					</div>
					<div class="uk-width-1-1">
						<table class="uk-table uk-table-divider uk-table-small uk-table-justify">
							<tr>
								<th><?php echo lang('Manager.id') ?></th>
								<th><?php echo lang('Manager.package_add_date') ?></th>
                                <th><?php echo lang('Manager.package_done_date') ?></th>
                                <th><?php echo lang('Manager.package_users') ?></th>
								<th><?php echo lang('Manager.message') ?></th>
                                <th></th>
                                <th></th>
							</tr>
							<?php foreach($messagePackage as $value){ ?>
							<tr>
								<td class="uk-table-shrink"><?php echo $value['id'] ?></td>
								<td class="uk-table-shrink uk-text-nowrap uk-text-muted"><?php echo date("Y.m.d H:i",$value['date_add']) ?></td>
                                <td class="uk-table-shrink uk-text-nowrap uk-text-muted">
                                    <?php if(!empty($value['date_done'])){ ?>
                                        <span class="uk-text-bold uk-text-success"><?php echo date("Y.m.d H:i",$value['date_done']); ?></span>
                                    <?php }else{ if($value['status']==1){ ?>
                                        <span class="uk-text-bold uk-text-danger"><?php echo lang('Manager.message_package_work'); ?></span>
                                    <?php }else{ ?>
                                        <form method="post" action="<?= current_url(true); ?>">
                                            <input name="id" value="<?php echo $value['id'] ?>" type="text" hidden>
                                            <button class="uk-button uk-button-small uk-button-danger" type="submit" name="submit" value="Submit_start"><?php echo lang('Manager.message_package_start'); ?></button>
                                        </form>
                                    <?php } } ?>
                                </td>
                                <td class="uk-table-shrink"><?php echo $value['users'] ?></td>
								<td><div class="pre"><?php echo $value['message'] ?></div></td>
                                <td class="uk-table-shrink">
                                    <div class="button-icon"><a class="link-edit" href="#edit-<?php echo $value['id'] ?>" data-uk-toggle><span data-uk-icon="icon:file-edit"></span></a></div>
                                    <div id="edit-<?php echo $value['id'] ?>" data-uk-modal="bg-close:false;">
                                        <div class="uk-modal-dialog uk-modal-body">
                                            <button class="uk-modal-close-default" type="button" uk-close></button>
                                            <form method="post" action="<?= current_url(true); ?>" data-uk-grid class="uk-grid-small">
                                                <div class="uk-width-1-1 uk-text-center"><h4><?php echo lang('Manager.edit_message_package') ?></h4></div>
                                                <div class="uk-width-1-1 uk-text-center">
                                                    <textarea name="message" class="uk-textarea" rows="8"><?php echo $value['message'] ?></textarea>
                                                </div>
                                                <div class="uk-width-1-1 uk-text-center">
                                                    <input name="id" value="<?php echo $value['id'] ?>" type="text" hidden>
                                                    <button class="uk-button uk-button-primary" type="submit" name="submit" value="Submit_edit"><?php echo lang('Manager.save') ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                                <td class="uk-table-shrink"><div class="button-icon"><a class="link-del" href="#del" data-id="<?= $value['id']; ?>" data-uk-toggle><span data-uk-icon="icon:trash"></span></a></div></td>
							</tr>
							<?php } ?>
						</table>
					</div>
                    <div class="uk-width-1-1">
                        <?php echo $pagination; ?>
                    </div>
					<?php }else{ ?>
					<div class="uk-width-1-1">
						<div data-uk-grid data-uk-height-match="target: .message-height" class="uk-grid-small">
							<div class="uk-width-expand@s">
								<div class="message-height">
									<div class="alert">
										<?php echo lang('Manager.message_package_not_found') ?>
									</div>
								</div>
							</div>
							<div class="uk-width-auto@s">
								<a data-uk-toggle href="#add-package" class="uk-button uk-button-primary message-height uk-flex uk-flex-middle"><?php echo lang('Manager.message_package_add') ?><span class="uk-margin-small-left" data-uk-icon="icon:chevron-down;ratio:.8"></span></a>
							</div>
						</div>
					</div>
					<div class="uk-width-1-1" id="add-package" hidden>
						<div class="alert">
							<form method="post" action="<?= current_url(true); ?>" data-uk-grid class="uk-grid-small">
								<input hidden class="uk-input" value="0" type="text" name="id" required>
								<div class="uk-width-1-1"><label><?php echo lang('Manager.message') ?></label><textarea class="uk-textarea" type="text" name="message" value="" rows="6" required></textarea></div>
								<div class="uk-width-auto uk-flex uk-flex-bottom"><button class="uk-button uk-button-primary" type="submit" name="submit" value="Submit_add_package"><?php echo lang('Manager.add') ?></button></div>
							</form>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</main>
<div id="del" data-uk-modal="bg-close:false;">
    <div class="uk-modal-dialog uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close></button>
		<div data-uk-grid class="uk-grid-small">
			<div class="uk-width-1-1 uk-text-center"><h4><?php echo lang('Manager.delete_message_package') ?></h4></div>
			<div class="uk-width-1-1 uk-text-center uk-text-large"><span class="del-title"></span></div>
			<div class="uk-width-1-1 uk-text-center">
				<form method="post" action="<?= current_url(true); ?>">
					<input class="input_item_id" name="id" value="" type="text" hidden>
					<button class="uk-button uk-button-danger" type="submit" name="submit" value="Submit_del"><?php echo lang('Manager.confirm') ?></button>
				</form>
			</div>
		</div>
    </div>
</div>
<script>
$(".link-del").click(function(){
	$('.input_item_id').val($(this).data('id'));
});
$(".link-run").click(function(){
    event.preventDefault();
	var id = $(this).data('id');
    console.log(id);
    window.open(this.href, "unicorn", "width=300, height=300, menubar=no, toolbar=no, scrollbars=no, location=no, status=no, titlebar=no, resizable=no");
});

</script>