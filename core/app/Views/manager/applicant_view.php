<main>
	<div data-uk-grid class="uk-grid-small">
		<div class="uk-width-1-1"><span data-uk-icon="icon:arrow-left" class="uk-margin-small-right"></span><a href="<?php echo $managerUrl ?>/applicant"><?php echo lang('Manager.back_applicant') ?></a></div>
		<div class="uk-width-expand uk-flex uk-flex-middle"><h1><?php echo $pageTitle." / ".$entries['first_name']." ".$entries['last_name']; ?></h1></div>
		<div class="uk-width-auto uk-flex uk-flex-middle"><?php echo lang('Manager.registration_date') ?>: <?php echo date("Y.m.d H:i",$entries['date_add']) ?></div>
		<div class="uk-width-1-1 uk-margin-medium-top">
			<div class="box-border">
				<div class="box-border-label"><?php echo lang('Manager.personal_data') ?></div>
				<div data-uk-grid class="uk-grid-small">
					<div class="uk-width-expand@s">
						<p><b><?php echo lang('Manager.first_name') ?></b>: <?php echo $entries['first_name'] ?></p>
						<p><b><?php echo lang('Manager.last_name') ?></b>: <?php echo $entries['last_name'] ?></p>
					</div>
					<div class="uk-width-expand@s">
						<p><b><?php echo lang('Manager.phone') ?></b>: <?php echo $entries['phone'] ?></p>
						<p><b><?php echo lang('Manager.email') ?></b>: <?php echo $entries['email'] ?></p>
					</div>
					<div class="uk-width-expand@l">
						<p><b><?php echo lang('Manager.city') ?></b>: <?php echo $entries['city'] ?></p>
						<p><b><?php echo lang('Manager.address') ?></b>: <?php echo $entries['address'] ?></p>
					</div>
				</div>
			</div>
		</div>
		<div class="uk-width-1-1 uk-margin-medium-top">
			<div class="box-border">
				<div class="box-border-label"><?php echo lang('Manager.appeal') ?></div>
				<b class="uk-margin-small-right"><?php echo lang('Manager.all_appeal') ?></b><?php echo $appealTotal ?>
				<?php if( count($appeal)>0 ){ ?>
				<table class="uk-table uk-table-divider uk-table-small uk-table-justify">
					<tr>
						<th><?php echo lang('Manager.id') ?></th>
						<th class="uk-text-nowrap"><?php echo lang('Manager.appeal_date') ?></th>
						<th></th>
						<th><?php echo lang('Manager.appeal') ?></th>
						<th><?php echo lang('Manager.status') ?></th>
					</tr>
					<?php foreach($appeal as $value){ ?>
					<tr>
						<td class="uk-table-shrink"><?php echo $value['id'] ?></td>
						<td class="uk-table-shrink uk-text-nowrap uk-text-muted">
                            <span class="uk-text-bold <?php echo ($value['appeal_date_approved']==1)? "uk-text-success" : "uk-text-danger" ; ?>">
                            <?php echo ($value['appeal_date']===NULL)? lang('Manager.null') : date("Y.m.d",$value['appeal_date']) ;  ?>
                            </span>
                        </td>
						<td class="uk-table-shrink"><a href="<?php echo $managerUrl ?>/appeal/<?php echo $value['id'] ?>" class="link-width"><span data-uk-icon="icon:file-edit"></a></td>
						<td>
                            <b><?php echo lang('Manager.appeal'); ?></b>: <?php echo $value['content'] ?>
                            <div><b><?php echo lang('Manager.address'); ?></b>: <?php echo $value['address'] ?></div>
                        </td>
						<td class="uk-table-shrink uk-text-nowrap"><span class="status-label status-label-<?php echo $value['status'] ?>"><?php echo $status[$value['status']] ?></td>
					</tr>
					<?php } ?>
				</table>
				<center class="uk-text-bold" hidden><a href="<?php echo $managerUrl ?>/appeal?u=<?php echo $value['id'] ?>" class="uk-margin-small-right"><?php echo lang('Manager.more_appeal_user') ?></a></center>
				<?php }else{ ?>
				<div class="alert">
					<?php echo lang('Manager.etries_not_found') ?>
				</div>
				<?php } ?>
			</div>
		</div>
		<div class="uk-width-1-1 uk-margin-medium-top">
			<div class="box-border">
				<div class="box-border-label"><?php echo lang('Manager.message') ?></div>
				<div data-uk-grid class="uk-grid-small">
					<?php if( count($message)>0 ){ ?>
					<div class="uk-width-expand@s uk-flex uk-flex-middle">
						<b class="uk-margin-small-right"><?php echo lang('Manager.all_message') ?></b><?php echo $messageTotal ?>
					</div>
					<div class="uk-width-auto@s">
						<a data-uk-toggle href="#add-message" class="uk-button uk-button-primary uk-button-small uk-flex uk-flex-middle"><?php echo lang('Manager.write_user') ?><span class="uk-margin-small-left" data-uk-icon="icon:chevron-down;ratio:.8"></span></a>
					</div>				
					<div class="uk-width-1-1" id="add-message" hidden>
						<div class="alert">
							<form method="post" action="<?= current_url(true); ?>" data-uk-grid class="uk-grid-small">
								<input hidden class="uk-input" value="<?php echo $entries['id'] ?>" type="text" name="id" required>
								<div class="uk-width-1-1"><label><?php echo lang('Manager.message') ?></label><textarea class="uk-textarea" type="text" name="message" value="" rows="6" required></textarea></div>
								<div class="uk-width-auto uk-flex uk-flex-bottom"><button class="uk-button uk-button-primary" type="submit" name="submit" value="Submit_add_message"><?php echo lang('Manager.send') ?></button></div>
							</form>
						</div>
					</div>
					<div class="uk-width-1-1">
						<table class="uk-table uk-table-divider uk-table-small uk-table-justify">
							<tr>
								<th><?php echo lang('Manager.id') ?></th>
								<th><?php echo lang('Manager.date') ?></th>
								<th><?php echo lang('Manager.message') ?></th>
							</tr>
							<?php foreach($message as $value){ ?>
							<tr>
								<td class="uk-table-shrink"><?php echo $value['id'] ?></td>
								<td class="uk-table-shrink uk-text-nowrap uk-text-muted"><?php echo date("Y.m.d H:i",$value['date_add']) ?></td>
								<td><?php echo $value['message'] ?></td>
							</tr>
							<?php } ?>
						</table>
					</div>
					<div class="uk-width-1-1 uk-text-center uk-text-bold">
						<a href="<?php echo $managerUrl ?>/message/<?php echo $entries['id'] ?>" class="uk-margin-small-right"><?php echo lang('Manager.more_message_user') ?></a>
					</div>
					<?php }else{ ?>
					<div class="uk-width-1-1">
						<div data-uk-grid data-uk-height-match="target: .message-height" class="uk-grid-small">
							<div class="uk-width-expand@s">
								<div class="message-height">
									<div class="alert">
										<?php echo lang('Manager.message_not_found') ?>
									</div>
								</div>
							</div>
							<div class="uk-width-auto@s">
								<a data-uk-toggle href="#add-message" class="uk-button uk-button-primary message-height uk-flex uk-flex-middle"><?php echo lang('Manager.write_user') ?><span class="uk-margin-small-left" data-uk-icon="icon:chevron-down;ratio:.8"></span></a>
							</div>
						</div>
					</div>
					<div class="uk-width-1-1" id="add-message" hidden>
						<div class="alert">
							<form method="post" action="<?= current_url(true); ?>" data-uk-grid class="uk-grid-small">
								<input hidden class="uk-input" value="<?php echo $entries['id'] ?>" type="text" name="id" required>
								<div class="uk-width-1-1"><label><?php echo lang('Manager.message') ?></label><textarea class="uk-textarea" type="text" name="message" value="" rows="6" required></textarea></div>
								<div class="uk-width-auto uk-flex uk-flex-bottom"><button class="uk-button uk-button-primary" type="submit" name="submit" value="Submit_add_message"><?php echo lang('Manager.send') ?></button></div>
							</form>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>

		<div class="uk-width-1-1" hidden>
			<pre>
				<?php var_dump($appeal) ?> 
			<pre>
		</div>
	</div>
</main>