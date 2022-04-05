<main>
	<div data-uk-grid class="uk-grid-small">
		<div class="uk-width-expand uk-flex uk-flex-middle"><h1><?php echo $pageTitle ?></h1></div>
        <div class="uk-width-auto uk-flex uk-flex-middle">
            <form action="<?php echo current_url(true); ?>" data-uk-grid class="uk-grid-small">
                <div class="uk-width-expand"><input class="uk-input uk-form-small uk-width-1-1" name="s" value="<?php echo $search; ?>" autocomplete="off"></div>
                <div class="uk-width-auto"><input class="uk-button uk-button-small uk-button-primary" type="submit" value="<?php echo lang('Manager.button_search'); ?>"></div>
            </form>
        </div>
        <?php if($search!=""){ ?>
        <div class="uk-width-1-1">
            <div class="alert">
                <b><?php echo lang('Manager.search_string'); ?> <span class="uk-text-danger"><?php echo $search; ?></span></b>
                <p><a href="<?php echo $managerUrl ?>/applicant"><?php echo lang('Manager.view_all_aplicant'); ?></a></p>
            </div>
        </div>
        <?php } ?>
		<div class="uk-width-1-1"><hr></div>
		<?php if( $artries_total>0 ){ ?>
		<div class="uk-width-1-1">
			<table class="uk-table uk-table-divider uk-table-small uk-table-justify">
				<tr>
					<th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.id'); ?></th>
					<th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.name'); ?></th>
					<th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.phone'); ?></th>
					<th><?php echo lang('Manager.email'); ?></th>
					<th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.appeal_qty'); ?></th>
				</tr>
				<?php foreach($entries as $value){ ?>
				<tr>
					<td><?php echo $value['id'] ?></td>
					<td class="uk-text-bold">
                        <div class="td-implimenter">
                            <a href="<?php echo $managerUrl ?>/applicant/<?php echo $value['id'] ?>"><?php echo $value['first_name'] ?> <?php echo $value['last_name'] ?></a>
                        </div>
                    </td>
					<td class="uk-text-nowrap"><?php echo $value['phone'] ?></td>
					<td class="uk-text-nowrap"><?php echo $value['email'] ?></td>
					<td><?php echo $value['appeals'] ?></td>
				</tr>
				<?php } ?>
			</table>
		</div>
		<div class="uk-width-1-1">
			<?php echo lang('Manager.all_entries'); ?>
			<?php echo $artries_total; ?>
		</div>
		<div class="uk-width-1-1">
			<?php echo $pagination; ?>
		</div>
		<?php }else{ ?>
		<div class="uk-width-1-1">
			<div class="alert">
				<?php echo lang('Manager.etries_not_found'); ?>
			</div>
		</div>
		<?php } ?>
	</div>
</main>