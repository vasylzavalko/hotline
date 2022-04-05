<main data-uk-height-viewport="expand: true">
    <div class="uk-container uk-container-large uk-margin-medium-top">
        <div data-uk-grid class="uk-grid-small">
            <div class="uk-width-1-1"><h1><?php echo lang('Front.page_title_'.$page); ?></h1></div>

            <?php if (count($appeal)>0) { ?>
            <?php foreach ($appeal as $value) { ?>
            <div class="uk-width-1-1">
                <div class="appeal-item appeal-item-<?php echo $value['status'] ?> transition">
                    <div data-uk-grid class="uk-grid-small">
                        <div class="uk-width-auto@m"><b><?php echo lang('Front.appeal_number'); ?><?php echo $value['id'] ?></b> <?php echo lang('Front.from'); ?> <?php echo date("Y.m.d H:i",$value['date_add']); ?></div>
                        <div class="uk-width-expand@m">
                        
                            <?php if($value['rating']>0){ ?>
                            <span class="appeal-label uk-hidden@m"><?php echo lang('Front.appeal_rating'); ?></span><br class="uk-hidden@m">
                            <div class="appeal-item-rating">
                                    <div class="rating">
                                    <?php 
                                        for ($r=1;$r<=$value['rating'];$r++) { 
                                            echo '<span class="rating-1"></span>'; 
                                        }
                                        $rLast = 5-$value['rating'];
                                        if ($rLast>0){
                                            for ($r=1;$r<=$rLast;$r++) { echo '<span class="rating-0"></span>'; }
                                        }
                                        
                                    ?>
                                    </div>
                                    </div>

                            <?php } ?>                        
                        
                        </div>
                        <div class="uk-width-auto@m">
                            <span class="appeal-label"><?php echo lang('Front.appeal_status'); ?></span><br class="uk-hidden@m">
                            <span class="appeal-status appeal-status-<?php echo $value['status'] ?>"><?php echo $status[$value['status']] ?></span>
                        </div>
                        <div class="uk-width-1-1"><span class="appeal-label"><?php echo lang('Front.appeal_content'); ?>:</span><br><?php echo mb_strimwidth($value['content'], 0, 500, "..."); ?></div>
                        <div class="uk-width-1-1"><span class="appeal-label"><?php echo lang('Front.appeal_address'); ?>:</span><br><?php echo $value['address'] ?></div>
                        <div class="uk-width-1-1"><a href="/appeal/<?php echo $value['id'] ?>" class="button"><?php echo lang('Front.more'); ?></a></div>
                    </div>
                </div>
            </div>
            <?php } ?>
            <div class="uk-width-1-1">
                <?php echo lang('Front.all_appeal'); ?>
                <?php echo $appealTotal; ?>
            </div>
            <div class="uk-width-1-1">
                <?php echo $pagination; ?>
            </div>
            <?php } else { ?>
            <div class="uk-width-1-1">
                <div class="alert"><?php echo lang('Front.appeal_not_found'); ?></div>
            </div>
            <?php } ?>
        </div>
    </div>
</main>