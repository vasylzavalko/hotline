<main>
	<div data-uk-grid class="uk-grid-small">
		<div class="uk-width-expand uk-flex uk-flex-middle"><h1><?php echo $pageTitle ?></h1></div>
		<div class="uk-width-1-1"><hr></div>
		<div class="uk-width-1-1">
			<table class="uk-table uk-table-divider uk-table-small uk-table-justify">
				<tr>
					<th class=""><?php echo lang('Manager.id') ?></th>
					<th class=""><?php echo lang('Manager.email') ?></th>
					<th class=""><?php echo lang('Manager.user') ?></th>
					<th class=""><?php echo lang('Manager.last_log') ?></th>
					<th class=""><?php echo lang('Manager.date_add') ?></th>
				</tr>
				<?php foreach($logs as $value){ ?>
				<tr>
					<td class="uk-table-shrink"><?php echo $value['id'] ?></td>
					<td class="uk-table-shrink uk-text-nowrap uk-text-bold"><a href="<?php echo $managerUrl; ?>/logs/<?php echo $value['id'] ?>"><?php echo $value['email'] ?></a></td>
					<td class="uk-table-shrink uk-text-nowrap"><?php echo $value['first_name'] ?> <?php echo $value['last_name'] ?></td>
					<td class=""><?php echo $value['log'] ?></td>
					<td class="uk-table-shrink uk-text-nowrap"><?php echo date("Y.m.d H:i:s",$value['date_log']) ?></td>
				</tr>
				<?php } ?>
			</table>
		</div>
	</div>
</main>