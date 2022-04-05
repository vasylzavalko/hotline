<main>
	<div data-uk-grid class="uk-grid-small">
		<div class="uk-width-1-1"><h1><?php echo $pageTitle ?></h1></div>
		<div class="uk-width-expand">
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
                <div class="uk-width-expand">
                    <label><?php echo lang('Manager.implementer') ?></label><br>
                    <select name="implementer" class="uk-select">
                        <option value="notselect" <?php if($implementerQuery=="notselect"){ ?>selected<?php } ?>><?php echo lang('Manager.stat_rating_notselect') ?></option>
                        <?php foreach($implementer as $implementerId => $implementerValue){ ?>
                        <option value="<?php echo $implementerId ?>" <?php if(is_numeric($implementerQuery) && $implementerId==$implementerQuery){ ?>selected<?php } ?>><?php echo $implementerValue['name'] ?>, <?php echo $implementerValue['title'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="uk-width-expand">
                    <label><?php echo lang('Manager.user_head') ?></label><br>
                    <select name="user_head" class="uk-select">
                        <option value="notselect" <?php if($userHeadQuery=="notselect"){ ?>selected<?php } ?>><?php echo lang('Manager.stat_rating_notselect') ?></option>
                        <?php foreach($userHead as $implementerId => $implementerValue){ ?>
                        <option value="<?php echo $implementerId ?>" <?php if(is_numeric($userHeadQuery) && $implementerId==$userHeadQuery){ ?>selected<?php } ?>><?php echo $implementerValue['name'] ?>, <?php echo $implementerValue['title'] ?></option>
                        <?php } ?>
                    </select>
                </div>                
                
                <div class="uk-width-auto uk-flex uk-flex-middle" hidden>
                    <div>
                        <input name="overdue" type="checkbox" class="uk-checkbox uk-margin-small-right" value="1" <?php if($overdueQuery==1){echo"checked";} ?>>Протерміновані
                    </div>
                </div>
				<div class="uk-width-auto uk-flex uk-flex-middle uk-flex-bottom"><button class="uk-button uk-button-primary" type="submit"><span uk-icon="icon:refresh"></span></button></div>
			</form>
		</div>
        <div class="uk-width-1-1 uk-margin-medium-top">
            <div data-uk-grid class="uk-flex">
                <div class="uk-width-xlarge@m">
                    <div data-uk-grid class="uk-grid-small">
                        <div class="uk-width-1-1">
                            <div class="box-border">
                                <div class="box-border-label"><?php echo lang('Manager.stat_period') ?></div>
                                <table class="uk-table uk-table-divider uk-table-small uk-table-justify">
                                    <tr>
                                        <td><?php echo lang('Manager.stat_date_start') ?></td>
                                        <td class="uk-table-shrink uk-text-nowrap uk-text-bold"><?php echo date("d-m-Y H:i",$date_start) ?></td>
                                    <tr>
                                    <tr>
                                        <td><?php echo lang('Manager.stat_date_end') ?></td>
                                        <td class="uk-table-shrink uk-text-nowrap uk-text-bold"><?php echo date("d-m-Y H:i",$date_end) ?></td>
                                    <tr>
                                    <tr>
                                        <td><?php echo lang('Manager.stat_all_appeal') ?></td>
                                        <td class="uk-table-shrink uk-text-nowrap uk-text-bold"><?php echo $artriesTotal ?></td>
                                    <tr>
                                    <tr>
                                        <td><?php echo lang('Manager.stat_all_rating') ?></td>
                                        <td class="uk-table-shrink uk-text-nowrap uk-text-bold"><?php echo $rating['total'] ?></td>
                                    <tr>
                                    <?php foreach($status as $statusId => $statusTitle){ ?>
                                    <tr>
                                        <td><?php echo lang('Manager.status') ?> <span class="uk-text-primary uk-text-bold"><?php echo $statusTitle ?></span></td>
                                        <td class="uk-table-shrink uk-text-nowrap uk-text-bold"><?php echo $statusStat[$statusId] ?></td>
                                    <tr>
                                    <?php } ?>
                                </table>
                                <div class="uk-text-right">
                                    <a class="uk-button uk-button-small uk-button-primary" href="/<?php echo $managerUrl ?>/statistic/export?<?= $uriQuery ?>" target="_blank"><?php echo lang('Manager.export_to_excel') ?></a>
                                </div>
                            </div>
                        </div>
                        <div class="uk-width-1-1">
                            <div class="alert-border">
                                <table class="uk-table uk-table-divider uk-table-small uk-table-justify">
                                    <tr>
                                        <td><?php echo lang('Manager.stat_total_all') ?></td>
                                        <td class="uk-table-shrink uk-text-nowrap uk-text-bold"><?php echo $artriesTotalAll ?></td>
                                    <tr>
                                    <tr>
                                        <td><?php echo lang('Manager.stat_inwork_all') ?></td>
                                        <td class="uk-table-shrink uk-text-nowrap uk-text-bold"><?php echo $artriesInWorkAll ?></td>
                                    <tr>
                                    <tr>
                                        <td> <?php echo lang('Manager.stat_done_all') ?></td>
                                        <td class="uk-table-shrink uk-text-nowrap uk-text-bold"><?php echo $artriesDoneAll ?></td>
                                    <tr>
                                </table>
                            </div>
                        </div>
                    </div>    
                </div>
                
                <div class="uk-width-expand uk-flex-first@m">
                    <div data-uk-grid>
                        <?php if( $artriesTotalFilter>0 ){ ?>
                        <div class="uk-width-1-1">
                            <div class="uk-overflow-auto">
                                <table class=" uk-table uk-table-divider uk-table-small uk-table-justify uk-table-middle">
                                    <thead>
                                        <tr>
                                            <th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.id'); ?></th>
                                            <th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.appeal_date'); ?></th>
                                            <th><?php echo lang('Manager.appeal'); ?></th>
                                            <th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.implementer'); ?></th>
                                            <th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.rating'); ?></th>
                                            <th class="uk-table-shrink uk-text-nowrap uk-text-right"><?php echo lang('Manager.status'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="filter-body">
                                    <?php foreach($entries as $value){ ?>
                                        <tr class="filter-status-<?php echo $value['status'] ?> <?php if ($value['rating']>0) { ?>filter-rating<?php } ?>">
                                            <td class="uk-table-shrink uk-text-nowrap"><?php echo $value['id'] ?></td>
                                            <td>
                                               <span class="uk-text-bold <?php echo ($value['appeal_date_approved']==1)? "uk-text-success" : "uk-text-danger" ; ?>">
                                                <?php echo ($value['appeal_date']===NULL)? lang('Manager.null') : date("Y.m.d",$value['appeal_date']) ;  ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if( $value['status'] > 0 ){ ?>
                                                <b><?php echo lang('Manager.appeal'); ?></b>: <?php echo $value['content'] ?>
                                                <div><b><?php echo lang('Manager.address'); ?></b>: <?php echo $value['address'] ?></div>
                                                <div class="uk-margin-small-top"><a href="<?php echo $managerUrl ?>/appeal/<?php echo $value['id'] ?>" class="uk-button uk-button-primary uk-button-small"><?php echo lang('Manager.go_to_appeal'); ?></a></div>
                                                <?php } else { ?>
                                                <span class="uk-text-bold uk-text-danger"><?php echo lang('Manager.appeal_status_0'); ?></span>
                                                <?php } ?>
                                            </td>
                                            <td class="uk-table-shrink uuk-text-nowrap">
                                                <?php foreach($value['implementer'] as $implementer){?>
                                                    <div class="td-implimenter">
                                                        <b><?php echo $implementer['first_name'] ?> <?php echo $implementer['last_name'] ?></b><?php if(!empty($implementer['comment_user'])){ echo ", ".$implementer['comment_user']; } ?>
                                                        <br><?php echo $implementer['title'] ?>
                                                    </div>
                                                <?php } ?>
                                                <?php if(count($value['implementer'])==0){ ?>
                                                <span class="uk-text-bold uk-text-danger"><?php echo lang('Manager.not_specified') ?></span>
                                                <?php } ?>
                                            </td>
                                            <td class="uk-table-shrink uk-text-nowrap">
                                                <div class="rating rating-small rating-<?= $value['rating'] ?>">
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
                                            <td class="uk-table-shrink uk-text-nowrap uk-text-right">
                                                <span class="status-label status-label-<?php echo $value['status'] ?>"><?php echo $status[$value['status']] ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="uk-width-1-1">
                            <?php echo lang('Manager.all_appeal'); ?>
                            <?php echo $artriesTotalFilter; ?>
                        </div>
                        <div class="uk-width-1-1 uk-margin-small-top">
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
                </div>
            </div>
        </div>
	</div>
</main>
<link rel="stylesheet" type="text/css" href="/assets/js/datepicker/jquery.datetimepicker.min.css"/ >
<script src="/assets/js/datepicker/jquery.datetimepicker.full.min.js"></script>
<script>
jQuery(document).ready(function () {
	jQuery.datetimepicker.setLocale('uk');
	jQuery('.datetimepicker').datetimepicker({
		dayOfWeekStart:1,
		format:'d-m-Y',
		timepicker:false,
	});
	$('.datetimepicker').change('click', function () {});
});
$(".table-filter").click(function () {
    
    $(".table-filter").removeClass('uk-button-primary');
    $(this).addClass('uk-button-primary');
    
    var filterData = $(this).data('filter');
    
    var rows = $("#filter-body").find("tr").hide();
    if (filterData.length) {
        console.log(filterData.length);
        var data = filterData.split(" ");
        $.each(data, function (i, v) {
            rows.filter("."+v).show();
        });
    } else rows.show();
    
});
</script>