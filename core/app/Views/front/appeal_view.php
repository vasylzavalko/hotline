<main data-uk-height-viewport="expand: true">
    <div class="uk-container uk-container-large uk-margin-small-top">
        <div data-uk-grid class="uk-grid-small">
            <div class="uk-width-1-1"><a href="/appeal" class="underline"><span data-uk-icon="arrow-left" class="uk-margin-small-right"></span><?php echo lang('Front.back_to_appeal') ?></a></div>
            <div class="uk-width-1-1"><h1><?php echo lang('Front.appeal_number').$appeal['id']; ?></h1></div>
            
            <div class="uk-width-1-1">
                <div data-uk-grid class="uk-grid-small" data-uk-height-match="target: .card-height">
                    <div class="uk-width-expand">
                        <div class="card-height">
                            <div data-uk-grid class="uk-grid-small">
                                <div class="uk-width-1-1"><span class="appeal-label"><?php echo lang('Front.appeal_date_add'); ?>:</span><br><?php echo date("Y.m.d H:i",$appeal['date_add']) ?></div>
                                <div class="uk-width-1-1"><span class="appeal-label"><?php echo lang('Front.appeal_status'); ?>:</span><br><span class="appeal-status appeal-status-<?php echo $appeal['status'] ?>"><?php echo $status[$appeal['status']] ?></span></div>
                                <?php if($appeal['rating']>0){ ?>
                                <div class="uk-width-1-1">
                                    <span class="appeal-label"><?php echo lang('Front.appeal_rating'); ?>:</span><br>
                                    <div class="rating">
                                    <?php 
                                        for ($r=1;$r<=$appeal['rating'];$r++) { 
                                            echo '<span class="rating-1"></span>'; 
                                        }
                                        $rLast = 5-$appeal['rating'];
                                        if ($rLast>0){
                                            for ($r=1;$r<=$rLast;$r++) { echo '<span class="rating-0"></span>'; }
                                        }
                                        
                                    ?>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="uk-width-1-1"><span class="appeal-label"><?php echo lang('Front.appeal_content'); ?>:</span><br><?php echo $appeal['content'] ?></div>
                                <div class="uk-width-1-1"><span class="appeal-label"><?php echo lang('Front.appeal_address'); ?>:</span><br><?php echo $appeal['address'] ?></div>
                                <?php if (!empty($appeal['location_lat']) && !empty($appeal['location_lng'])){ ?>
                                <div class="uk-width-1-1"><span class="appeal-label"><?php echo lang('Front.appeal_location'); ?>:</span><br><?php echo $appeal['location_lat'] ?>, <?php echo $appeal['location_lng'] ?></div>
                                <?php } ?>
                                <?php if (count($appeal['gallery'])>0){ ?>
                                <div class="uk-width-1-1">
                                    <span class="appeal-label"><?php echo lang('Front.appeal_gallery'); ?>:</span><br>
                                    <div data-uk-grid class="uk-grid-small">
                                    <?php foreach ($appeal['gallery'] as $value){ ?>
                                        <div class="uk-width-small" data-uk-lightbox>
                                            <a href="<?php echo $value ?>">
                                                <div class="uk-cover-container">
                                                    <canvas width="300" height="300"></canvas>
                                                    <img src="<?php echo $value ?>" alt="" data-uk-cover>
                                                </div>
                                            </a>
                                        </div>
                                    <?php } ?>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($appeal['location_lat']) && !empty($appeal['location_lng'])){ ?>
                    <div class="uk-width-1-2@m">
                        <div class="map card-height" id="map"></div>
                    </div>
                    <?php } ?>
                    
                    <div class="uk-width-1-1 uk-margin-medium-top">
                        <div class="appeal-implementer">
                            <div data-uk-grid class="uk-grid-small">
                                <?php if (!empty($appeal['date'])) { ?>
                                <div class="uk-width-1-1"><span class="appeal-label"><?php echo lang('Front.appeal_date_work'); ?>:</span><br><?php echo date("Y.m.d",$appeal['date']) ?></div>
                                <?php } ?>
                                <div class="uk-width-1-1"><span class="appeal-label"><?php echo lang('Front.implementer'); ?>:</span><br><?php echo $appeal['implementer'] ?></div>
                                <?php if (count($appeal['work'])>0){ ?>
                                <div class="uk-width-1-1">
                                    <span class="appeal-label"><?php echo lang('Front.implementer_comment'); ?>:</span><br>
                                    <b><?php echo $appeal['work']['title'] ?></b><br>
                                    <?php echo $appeal['work']['comment'] ?>
                                </div>                    
                                <?php if (count($appeal['work']['gallery'])>0){ ?>
                                <div class="uk-width-1-1">
                                    <span class="appeal-label"><?php echo lang('Front.appeal_gallery'); ?>:</span><br>
                                    <div data-uk-grid class="uk-grid-small">
                                    <?php foreach ($appeal['work']['gallery'] as $value){ ?>
                                        <div class="uk-width-small" data-uk-lightbox>
                                            <a href="<?php echo $appeal['work']['gallery_dir'] ?><?php echo $value ?>">
                                                <div class="uk-cover-container">
                                                    <canvas width="300" height="300"></canvas>
                                                    <img src="<?php echo $appeal['work']['gallery_dir'] ?><?php echo $value ?>" alt="" data-uk-cover>
                                                </div>
                                            </a>
                                        </div>
                                    <?php } ?>
                                    </div>
                                </div>
                                <?php } ?>                    
                                <?php } else { ?>
                                <div class="uk-width-1-1">
                                    <div class="appeal-alert"><?php echo lang('Front.implementer_not_answered'); ?></div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>            
            
            <div class="uk-width-1-1" hidden><pre><?php var_dump($appeal); ?></pre></div>
        </div>
    </div>
</main>
<?php if (!empty($appeal['location_lat']) && !empty($appeal['location_lng'])){ ?>
<script>
var map = L.map("map").setView([<?php echo $appeal['location_lat'] ?>, <?php echo $appeal['location_lng'] ?>], 18);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'}).addTo(map);
map.scrollWheelZoom.disable();
map.invalidateSize(true);
L.marker([<?php echo $appeal['location_lat'] ?>, <?php echo $appeal['location_lng'] ?>]).addTo(map);
</script>
<?php } ?>