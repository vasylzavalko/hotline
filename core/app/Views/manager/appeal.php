<main>
	<div data-uk-grid class="uk-grid-small">
		<div class="uk-width-expand uk-flex uk-flex-middle"><h1><?php echo $pageTitle ?></h1></div>
        <div class="uk-width-1-1">
			<form method="get" action="<?= current_url(true); ?>" data-uk-grid class="uk-grid-small">
				<div class="uk-width-auto">
                    <label><?php echo lang('Manager.stat_date_start') ?></label><br>
                    <input id="datetimepicker" class="uk-input uk-width-small datetimepicker" name="start" value="<?= date("d-m-Y",$date_start) ?>">
                </div>
				<div class="uk-width-auto">
                    <label><?php echo lang('Manager.stat_date_end') ?></label><br>
                    <input id="datetimepicker" class="uk-input uk-width-small datetimepicker" name="end" value="<?= date("d-m-Y",$date_end) ?>">
                </div>
                <div class="uk-width-auto">
                    <label><?php echo lang('Manager.status') ?></label><br>
                    <select name="status" class="uk-select">
                        <option value="notselect" <?php if($statusQuery=="notselect"){ ?>selected<?php } ?>><?php echo lang('Manager.stat_status_notselect') ?></option>
                        <?php foreach($status as $statusId => $statusTitle){ ?>
                        <option value="<?php echo $statusId ?>" <?php if(is_numeric($statusQuery) && $statusId==$statusQuery){ ?>selected<?php } ?>><?php echo $statusTitle ?></option>
                        <?php } ?>
                    <select>
                </div>
                <div class="uk-width-auto">
                    <label><?php echo lang('Manager.rating') ?></label><br>
                    <select name="rating" class="uk-select">
                        <option value="notselect" <?php if($ratingQuery=="notselect"){ ?>selected<?php } ?>><?php echo lang('Manager.stat_rating_notselect') ?></option>
                        <?php for($i=1;$i<7;$i++){ ?>
                        <option value="<?php echo $i ?>" <?php if(is_numeric($ratingQuery) && $ratingQuery==$i){ ?>selected<?php } ?>><?php echo lang('Manager.stat_rating_'.$i) ?></option>
                        <?php } ?>
                    </select>
                </div>
                <?php if($loginUser['group_id']!=3){ ?>
                <div class="uk-width-expand">
                    <label><?php echo lang('Manager.implementer') ?></label><br>
                    <select name="implementer" class="uk-select">
                        <option value="notselect" <?php if($implementerQuery=="notselect"){ ?>selected<?php } ?>><?php echo lang('Manager.stat_rating_notselect') ?></option>
                        <?php foreach($implementer as $implementerId => $implementerValue){ ?>
                        <option value="<?php echo $implementerId ?>" <?php if(is_numeric($implementerQuery) && $implementerId==$implementerQuery){ ?>selected<?php } ?>><?php echo $implementerValue['name'] ?>, <?php echo $implementerValue['title'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <?php } ?>
                <?php if($loginUser['group_id']==1){ ?>
                <div class="uk-width-expand">
                    <label><?php echo lang('Manager.user_head') ?></label><br>
                    <select name="user_head" class="uk-select">
                        <option value="notselect" <?php if($userHeadQuery=="notselect"){ ?>selected<?php } ?>><?php echo lang('Manager.stat_rating_notselect') ?></option>
                        <?php foreach($userHead as $implementerId => $implementerValue){ ?>
                        <option value="<?php echo $implementerId ?>" <?php if(is_numeric($userHeadQuery) && $implementerId==$userHeadQuery){ ?>selected<?php } ?>><?php echo $implementerValue['name'] ?>, <?php echo $implementerValue['title'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="uk-width-auto uk-flex uk-flex-middle" hhidden>
                    <div>
                        <input name="overdue" type="checkbox" class="uk-checkbox uk-margin-small-right" value="1" <?php if($overdueQuery==1){echo"checked";} ?>>Протерміновані
                    </div>
                </div>
                <?php } ?>
				<div class="uk-width-auto uk-flex uk-flex-middle uk-flex-bottom"><button class="uk-button uk-button-primary" type="submit"><span uk-icon="icon:refresh"></span></button></div>
			</form>
		</div>        
        
        <div class="uk-width-auto" hidden>
            <div data-uk-grid class="uk-grid-small">
                <div class="uk-width-auto"><a href="/<?php echo $managerUrl ?>/appeal?filter=all" class="uk-button uk-button-small uk-button-default <?php if($filterQuery=='all'){echo"uk-button-primary";} ?>"><?php echo lang('Manager.button_all'); ?></a></div>
                <div class="uk-width-auto"><a href="/<?php echo $managerUrl ?>/appeal" class="uk-button uk-button-small uk-button-default <?php if($filterQuery=='inwork'){echo"uk-button-primary";} ?>"><?php echo lang('Manager.button_inwork'); ?></a></div>
                <div class="uk-width-auto"><a href="/<?php echo $managerUrl ?>/appeal?filter=done" class="uk-button uk-button-small uk-button-default <?php if($filterQuery=='done'){echo"uk-button-primary";} ?>"><?php echo lang('Manager.button_done'); ?></a></div>
            </div>
        </div>
		<div class="uk-width-1-1"><hr></div>
		<?php if( $artriesTotal>0 ){ ?>
		<div class="uk-width-1-1">
			<table class="uk-table uk-table-divider uk-table-small uk-table-justify uk-table-middle">
				<tr> 
					<th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.id'); ?></th>
                    <th hidden></th>
                    <th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.appeal_date'); ?></th>
					<th hidden class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.applicant'); ?></th>
					<th class=""><?php echo lang('Manager.appeal'); ?></th>
                    <th class=""><?php echo lang('Manager.implementer'); ?></th>
                    <th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.rating'); ?></th>
					<th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.status'); ?></th>
				</tr>
				<?php foreach($entries as $value){ ?>
				<tr> 
					<td class="uk-table-shrink"><?php echo $value['id'] ?></td>
                    <td hidden class="uk-table-shrink"><a href="<?php echo $managerUrl ?>/appeal/<?php echo $value['id'] ?>" class="link-width"><span data-uk-icon="icon:file-edit"></a></td>
                    <td class="uk-table-shrink uuk-text-nowrap">
                        <span class="uk-text-bold <?php echo ($value['appeal_date_approved']==1)? "uk-text-success" : "uk-text-danger" ; ?>">
                        <?php echo ($value['appeal_date']===NULL)? lang('Manager.null') : date("Y.m.d",$value['appeal_date']) ;  ?>
                        </span>
                    </td>
					<td hidden class="uk-table-shrink uuk-text-nowrap"><a class="block" href="<?php echo $managerUrl ?>/applicant/<?php echo $value['user_id'] ?>"><?php echo $users[$value['user_id']]['first_name'] ?><br><?php echo $users[$value['user_id']]['last_name'] ?></a></td>
					<td>
                        <div><b><?php echo lang('Manager.applicant'); ?></b>: <a class="uk-text-bold uk-text-danger" href="<?php echo $managerUrl ?>/applicant/<?php echo $value['user_id'] ?>"><?php echo $users[$value['user_id']]['first_name'] ?> <?php echo $users[$value['user_id']]['last_name'] ?></a></div>
						<div class="uk-margin-small-top"><b><?php echo lang('Manager.appeal'); ?></b>: <?php echo $value['content'] ?></div>
						<div><b><?php echo lang('Manager.address'); ?></b>: <?php echo $value['address'] ?></div>
                        <div class="uk-margin-small-top"><a href="<?php echo $managerUrl ?>/appeal/<?php echo $value['id'] ?>" class="uk-button uk-button-primary uk-button-small"><?php echo lang('Manager.go_to_appeal'); ?></a></div>
					</td>
                    <td class="uuk-table-shrink">
                        <?php foreach($value['implementer'] as $implementer){?>
                            <div class="td-implimenter">
                                <b><?php echo $implementer['first_name'] ?> <?php echo $implementer['last_name'] ?></b><?php if(!empty($implementer['comment_user'])){ echo ", ".$implementer['comment_user']; } ?>
                                <br><?php echo $implementer['title'] ?>
                                <?php if($value['appeal_request']==1){ ?>
                                <br><span class="uk-text-danger uk-text-bold">Подано запит уповноваженій особі!</span>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if(count($value['implementer'])==0){ ?>
                        <span class="uk-text-bold uk-text-danger"><?php echo lang('Manager.not_specified') ?></span>
                        <?php } ?>
                    </td>
                    <td>
                        <div class="rating rating-small">
                        <?php 
                            if ($value['rating']>0) {
                                for ($r=1;$r<=$value['rating'];$r++) { 
                                    echo '<span class="rating-1"></span>'; 
                                }
                                $rLast = 5-$value['rating'];
                                if ($rLast>0){
                                    for ($r=1;$r<=$rLast;$r++) { echo '<span class="rating-0"></span>'; }
                                }
                            } else {
                                for ($r=1;$r<=5;$r++) { 
                                    echo '<span class="rating-0"></span>'; 
                                }
                            }                                        
                        ?>                    
                        </div>
                    </td>
					<td class="uk-table-shrink uk-text-nowrap"><span class="status-label status-label-<?php echo $value['status'] ?>"><?php echo $status[$value['status']] ?></td>
				</tr>
				<?php } ?>
			</table>
		</div>
		<div class="uk-width-1-1">
			<?php echo lang('Manager.all_appeal'); ?>
			<?php echo $artriesTotal; ?>
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
<script>
window.setTimeout(function () {
  window.location.reload();
}, 900000);
jQuery(document).ready(function () {
	jQuery.datetimepicker.setLocale('uk');
	jQuery('.datetimepicker').datetimepicker({
		dayOfWeekStart:1,
		format:'d-m-Y',
		timepicker:false,
	});
	$('.datetimepicker').change('click', function () {});
});
</script>