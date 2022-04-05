<main>
	<div data-uk-grid class="uk-grid-small">
		<div class="uk-width-expand uk-flex uk-flex-middle"><h1><?php echo $pageTitle ?></h1></div>
		<div class="uk-width-auto">
			<a data-uk-toggle href="#add" class="uk-button uk-button-primary"><?php echo lang('Manager.add') ?></a>
		</div>
		<div class="uk-width-1-1" id="add" hidden>
			<div class="alert">
				<form method="post" action="<?php echo current_url(true); ?>" data-uk-grid class="uk-grid-small">
					<div class="uk-width-expand@s"><label><?php echo lang('Manager.title') ?></label><input class="uk-input" type="text" name="title" value="" required></div>
					<div class="uk-width-auto uk-flex uk-flex-bottom"><button class="uk-button uk-button-primary" type="submit" name="submit" value="Submit_add"><?php echo lang('Manager.create') ?></button></div>
				</form>
			</div>
		</div>
		<div class="uk-width-1-1"><hr></div>
		<div class="uk-width-1-1">
			<table class="uk-table uk-table-divider uk-table-small uk-table-justify">
				<?php foreach($status as $value){ ?>
				<tr>
					<td class="uk-table-shrink"><?php echo $value['id'] ?></td>
					<td class="uk-text-bold"><?php echo $value['title'] ?></td>
					<td class="uk-table-shrink"><a data-uk-toggle href="#edit-<?php echo $value['id'] ?>" class="link-width"><span data-uk-icon="icon:file-edit"></span></a></td>
				</tr>
				<tr id="edit-<?php echo $value['id'] ?>" hidden>
					<td colspan="3">
						<div class="alert">
							<form method="post" action="<?php echo current_url(true); ?>" data-uk-grid class="uk-grid-small">
								<input hidden class="uk-input" value="<?php echo $value['id']; ?>" type="text" name="id" required>
								<div class="uk-width-1-1"><label><?php echo lang('Manager.title') ?></label><input class="uk-input" type="text" name="title" value="<?php echo $value['title'] ?>" required></div>
								<?php if(isset($value['comment'])){ ?><div class="uk-width-1-1"><label><?php echo lang('Manager.comment') ?></label><textarea class="uk-textarea" name="comment" rows="3"><?php echo $value['comment'] ?></textarea></div><?php } ?>
								<div class="uk-width-expand uk-flex uk-flex-middle"><a class="link-del" href="#del" data-id="<?= $value['id']; ?>" data-title="<?= $value['title']; ?>" data-uk-toggle><span uk-icon="icon:trash"></span></a></div>
								<div class="uk-width-auto uk-flex uk-flex-bottom"><button class="uk-button uk-button-primary" type="submit" name="submit" value="Submit_edit"><?php echo lang('Manager.save') ?></button></div>
								
							</form>
						</div>
					</td>
				</tr>
				<?php } ?>
			</table>
		</div>
	</div>
</main>
<div id="del" data-uk-modal="bg-close:false;">
    <div class="uk-modal-dialog uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close></button>
		<div data-uk-grid class="uk-grid-small">
			<div class="uk-width-1-1 uk-text-center"><h4><?php echo lang('Manager.delete_status') ?></h4></div>
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
	$('.del-title').text($(this).data('title'));
});
</script>