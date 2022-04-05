<main>
	<div data-uk-grid class="uk-grid-small">
		<div class="uk-width-expand uk-flex uk-flex-middle"><h1><?php echo $pageTitle ?></h1></div>
		<div class="uk-width-auto">
			<a data-uk-toggle href="#add" class="uk-button uk-button-primary"><?php echo lang('Manager.add') ?></a>
		</div>
		<div class="uk-width-1-1" id="add" hidden>
			<div class="alert">
				<form method="post" action="<?php echo current_url(true); ?>" data-uk-grid class="uk-grid-small">
					<div class="uk-width-expand@s"><label><?php echo lang('Manager.email') ?></label><input class="uk-input" type="text" name="email" value="" required></div>
					<div class="uk-width-expand@s"><label><?php echo lang('Manager.password') ?></label><input class="uk-input" type="text" name="password" value="" required></div>
					<div class="uk-width-expand@s"><label><?php echo lang('Manager.password_repeat') ?></label><input class="uk-input" type="text" name="password_repeat" value="" required></div>
					<div class="uk-width-auto uk-flex uk-flex-bottom"><button class="uk-button uk-button-primary" type="submit" name="submit" value="Submit_add"><?php echo lang('Manager.create') ?></button></div>
				</form>
			</div>
		</div>
		<div class="uk-width-1-1"><hr></div>
		<div class="uk-width-1-1">
			<ul class="link-category">
				<?php foreach($userGroup as $value){ ?>
				<li><a class="<?php if($activeGroup==$value['id']){ echo "active"; } ?>" href="<?php echo $managerUrl; ?>/users<?php echo ($value['id']==0)?"":"?g=".$value['id']; ?>"><?php echo ($value['id']==0)? lang('Manager.all_category_group') : $value['title'] ; ?></a></li>
				<?php } ?>
			</ul>
		</div>
		<div class="uk-width-1-1">
			<table class="uk-table uk-table-divider uk-table-small uk-table-justify">
				<tr>
					<th class="uk-table-shrink"><?php echo lang('Manager.id') ?></th>
					<th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.date_add') ?></th>
					<th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.email') ?></th>
					<th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.name') ?> / <?php echo lang('Manager.posada') ?></th>
					<th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.phone') ?></th>
					<th class=""><?php echo lang('Manager.user_group') ?></th>
					<th></th>
				</tr>
				<?php foreach($user as $value){ ?>
				<tr>
					<td class="uk-table-shrink"><?php echo $value['id'] ?></td>
					<td class="uk-table-shrink uk-text-nowrap"><?php echo date("Y.m.d",$value['date_add']) ?></td>
					<td class="uk-text-bold uk-table-shrink uk-text-nowrap"><a href="<?php echo $managerUrl; ?>/users/<?php echo $value['id'] ?>"><?php echo $value['email'] ?></a></td>
					<td class="">
                        <div class="td-implimenter">
                        <?php if(!empty($value['first_name']) && !empty($value['last_name'])){
                            echo $value['first_name']." ".$value['last_name'];
                        }else{
                            echo $value['title'];
                        } ?>
                        </div>
                    </td>
					<td class="uk-table-shrink uk-text-nowrap"><?php echo $value['phone'] ?></td>
					<td class=""><span class="status-label status-label-user-<?php echo $value['group_id'] ?>"><?php echo $userGroup[$value['group_id']]['title'] ?></span></td>
					<td class="uk-table-shrink uk-text-nowrap"><a href="<?php echo $managerUrl; ?>/logs/<?php echo $value['id'] ?>"><?php echo lang('Manager.user_log') ?></a></td>
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