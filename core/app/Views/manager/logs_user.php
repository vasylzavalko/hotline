<main>
	<div data-uk-grid class="uk-grid-small">
		<div class="uk-width-1-1"><span data-uk-icon="icon:arrow-left" class="uk-margin-small-right"></span><a href="<?php echo $managerUrl; ?>/logs"><?php echo lang('Manager.back_logs') ?></a></div>
		<div class="uk-width-expand uk-flex uk-flex-middle"><h1><?php echo $pageTitle ?></h1></div>
		<div class="uk-width-1-1"><hr></div>
		<?php if (count($user)>0){ ?>
		<div class="uk-width-1-1">
			<div data-uk-grid class="uk-grid-small">
				<div class="uk-width-expand">
					<b><?php echo lang('Manager.email') ?></b>: <?php echo $user['email'] ?><br>
					<b><?php echo lang('Manager.name') ?></b>: <?php echo $user['first_name'] ?> <?php echo $user['last_name'] ?>
				</div>
				<div class="uk-width-auto uk-flex uk-flex-middle">
					<a href="<?php echo $managerUrl; ?>/users/<?php echo $user['id'] ?>"><?php echo lang('Manager.user_data') ?></a>
				</div>
			</div>
		</div>
		<div class="uk-width-1-1"><hr></div>
		<?php } ?>
		<?php if (count($logs)>0){ ?>
		<div class="uk-width-1-1">
			<table class="uk-table uk-table-divider uk-table-small uk-table-justify">
				<tr>
					<th class=""><?php echo lang('Manager.id') ?></th>
					<th class=""><?php echo lang('Manager.date_add') ?></th>
					<th class=""><?php echo lang('Manager.logs') ?></th>
				</tr>
				<?php foreach($logs as $value){ ?>
				<tr>
					<td class="uk-table-shrink"><?php echo $value['id'] ?></td>
					<td class="uk-table-shrink uk-text-nowrap"><?php echo date("Y.m.d H:i:s",$value['date_add']) ?></td>
					<td class=""><?php echo $value['log'] ?></td>
				</tr>
				<?php } ?>
			</table>
		</div>
		<div class="uk-width-1-1">
			<?php echo lang('Manager.all_entries'); ?>
			<?php echo $logsTotal; ?>
		</div>
		<div class="uk-width-1-1">
			<?php echo $pagination; ?>
		</div>
		<?php } else { ?>
		<div class="uk-width-1-1"><div class="alert">
			<?php echo lang('Manager.etries_not_found'); ?>
		</div></div>
		<?php } ?>
	</div>
</main>