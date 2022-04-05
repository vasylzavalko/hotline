<main>
	<div data-uk-grid class="uk-grid-small">
		<div class="uk-width-1-1"><span data-uk-icon="icon:arrow-left" class="uk-margin-small-right"></span><a href="<?php echo $managerUrl ?>/appeal"><?php echo lang('Manager.back_appeal') ?></a></div>
		<div class="uk-width-expand uk-flex uk-flex-middle"><h1><?php echo $pageTitle ?></h1></div>
		<div class="uk-width-auto uk-flex uk-flex-middle"><?php echo lang('Manager.add_date') ?>: <?php echo date("Y.m.d H:i",$entries['date_add']) ?></div>
		<div class="uk-width-1-1">
			<div data-uk-grid>
							
				<!-- Звернення -->
                <div class="uk-width-1-1">
                    <div data-uk-grid class="uk-grid-small">
                        <div class="uk-width-auto">
                            <a href="#appeal-content" class="uk-button uk-button-primary uk-button-small" data-uk-toggle><?php echo lang('Manager.appeal_content') ?><span class="uk-margin-small-left" data-uk-icon="icon:chevron-down;ratio:.8"></span></a>
                        </div>
                        <div class="uk-width-expand">
                            <?php if ($entries['rating']>0) { ?>
                            <div class="rating">
                                <?php 
                                    for ($r=1;$r<=$entries['rating'];$r++) { 
                                        echo '<span class="rating-1"></span>'; 
                                    }
                                    $rLast = 5-$entries['rating'];
                                    if ($rLast>0){
                                        for ($r=1;$r<=$rLast;$r++) { echo '<span class="rating-0"></span>'; }
                                    }
                                    
                                ?>
                                <i>(<?php echo $entries['rating'] ?>)</i>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
				<div class="uk-width-1-1" id="appeal-content" hidden>
					<div data-uk-grid data-uk-height-match="target: .appeal-height">
						<!-- Заявка -->
						<div class="uk-width-expand">
							<div class="appeal-height">
								<div data-uk-grid>
									<div class="uk-width-1-1">
										<div class="box-border">
											<div class="box-border-label"><?php echo lang('Manager.personal_data') ?></div>
											<table class="uk-table uk-table-margin-remove uk-table-small uk-table-justify">
												<tr>
													<td class="uk-table-shrink uk-text-bold"><?php echo lang('Manager.name') ?></td>
													<td><?php echo $user['first_name'] ?> <?php echo $user['last_name'] ?></td>
												</tr>
												<tr>
													<td class="uk-table-shrink uk-text-bold"><?php echo lang('Manager.phone') ?></td>
													<td><?php echo $user['phone'] ?></td>
												</tr>
												<tr>
													<td class="uk-table-shrink uk-text-bold"><?php echo lang('Manager.email') ?></td>
													<td><?php echo $user['email'] ?></td>
												</tr>
												<tr>
													<td class="uk-table-shrink uk-text-bold"><?php echo lang('Manager.city') ?></td>
													<td><?php echo $user['city'] ?></td>
												</tr>
												<tr>
													<td class="uk-table-shrink uk-text-bold"><?php echo lang('Manager.address') ?></td>
													<td><?php echo $user['address'] ?></td>
												</tr>
												<tr>
													<td></td>
													<td><a href="<?php echo $managerUrl ?>/applicant/<?php echo $user['id'] ?>"><?php echo lang('Manager.applicant_page') ?></a><span data-uk-icon="icon:arrow-right" class="uk-margin-small-left"></span></td>
												</tr>
											</table>
										</div>
									</div>
									<div class="uk-width-1-1">						
										<div class="box-border">
											<div class="box-border-label"><?php echo lang('Manager.appeal') ?></div>
											<table class="uk-table uk-table-margin-remove uk-table-small uk-table-justify">
												<tr>
													<td class="uk-table-shrink uk-text-bold"><?php echo lang('Manager.description') ?></td>
													<td><?php echo $entries['content'] ?></td>
												</tr>
												<tr>
													<td class="uk-table-shrink uk-text-bold"><?php echo lang('Manager.address') ?></td>
													<td><?php echo $entries['address'] ?></td>
												</tr>
												<?php if( !empty($entries['location_lat']) AND !empty($entries['location_lat'])){ ?>
												<tr>
													<td class="uk-table-shrink uk-text-bold"><?php echo lang('Manager.location') ?></td>
													<td><a href="https://maps.google.com/maps?q=loc:<?php echo $entries['location_lat'] ?>,<?php echo $entries['location_lng'] ?>" target="_blank"><?php echo $entries['location_lat'] ?>, <?php echo $entries['location_lng'] ?></a></td>
												</tr>
												<?php } ?>
												<?php if( count($gallery)>0 ){ ?>
												<tr>
													<td class="uk-table-shrink uk-text-bold"><?php echo lang('Manager.gallery') ?></td>
													<td>
														<div data-uk-grid class="uk-grid-small" data-uk-lightbox>
														<?php foreach($gallery as $value){ ?>
															<div class="uk-width-1-2 uk-width-1-4@m uk-width-1-5@l uk-width-1-6@xl">
																<a href="<?php echo $value['path'] ?><?php echo $value['image'] ?>">
																<div class="uk-cover-container">
																	<canvas width="600" height="600"></canvas>
																	<img src="<?php echo $value['path'] ?><?php echo $value['image'] ?>" alt="" data-uk-cover>
																</div>
																</a>
															</div>
														<?php } ?>
														</div>
													</td>
												</tr>
												<?php } ?>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<!-- Мапа -->
						<?php if( !empty($entries['location_lat']) AND !empty($entries['location_lat'])){ ?>
						<div class="uk-width-1-3@l">
							<div class="appeal-height map-container">
							<div id="map" class="map"></div>
							</div>
						</div>
						<?php } ?>
						
					</div>
				</div>
				
                <div class="uk-width-expand">
                    <div data-uk-grid>
                    
                        <!-- Дата Виконання -->
                        <div class="uk-width-1-1">
                            <div class="box-border">
                                <div class="box-border-label"><?php echo lang('Manager.appeal_date') ?></div>
                                <div data-uk-grid class="uk-grid-small">
                                    <?php if(count($appealDate)>0 ){ ?>
                                    <div class="uk-width-auto uk-flex uk-flex-middle">
                                        <div class="table-alert <?php if($appealDate[array_key_first($appealDate)]['approved']==0){ ?>table-alert-active<?php } ?>">
                                            <span class="appeal-date appeal-date-<?php echo $appealDate[array_key_first($appealDate)]['approved'] ?>">
                                                <?php echo date("Y.m.d",$appealDate[array_key_first($appealDate)]['date']) ?>
                                            </span> 
                                        </div>
                                    </div>
                                    <?php if(!empty($appealDate[array_key_first($appealDate)]['comment'])){ ?>
                                    <div class="uk-width-auto uk-flex uk-flex-middle">
                                        <a href="#date-comment" data-uk-toggle><?php echo lang('Manager.comment') ?></a>
                                    </div>
                                    <?php } ?>
                                    <div class="uk-width-expand">
                                        <?php if($appealDate[array_key_first($appealDate)]['approved']==0 AND $loginUser['group_id']==2 ){ ?>
                                        <form method="post" action="<?php echo current_url(true); ?>">
                                            <input hidden class="uk-input" value="<?php echo $appealDate[array_key_first($appealDate)]['id']; ?>" type="text" name="id" required>
                                            <button class="uk-button uk-button-small uk-button-primary" type="submit" name="submit" value="Submit_approved_date"><?php echo lang('Manager.appeal_date_approved') ?></button>
                                        </form>
                                        <?php } ?>
                                    </div>
                                    <div class="uk-width-auto uk-flex uk-flex-middle">
                                        <a href="#stat-date" data-uk-toggle class="uk-button uk-button-small uk-button-primary"><?php echo lang('Manager.appeal_date_stat') ?><span class="uk-margin-small-left" data-uk-icon="icon:chevron-down;ratio:.8"></span></a>
                                    </div>
                                    <?php if ($loginUser['group_id']<3) { ?>
                                    <div class="uk-width-auto uk-flex uk-flex-middle">
                                        <a href="#add-date" data-uk-toggle class="uk-button uk-button-small uk-button-primary"><?php echo lang('Manager.date_update') ?><span class="uk-margin-small-left" data-uk-icon="icon:chevron-down;ratio:.8"></span></a>
                                    </div>
                                    <?php } ?>
                                    <div id="date-comment" class="uk-width-1-1" hidden>
                                        <div class="alert">
                                            <?php echo $appealDate[array_key_first($appealDate)]['comment']; ?>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <?php if ($loginUser['group_id']<3) { ?>
                                    <div id="add-date" class="uk-width-1-1" <?php if(count($appealDate)>0 ){ echo "hidden"; } ?> >
                                        <form method="post" action="<?php echo current_url(true); ?>" data-uk-grid class="uk-grid-small">
                                            <?php if(count($appealDate)>0 ){ ?>
                                            <div class="uk-width-1-1"><hr></div>
                                            <?php } ?>
                                            <input hidden class="uk-input" value="<?php echo $entries['id']; ?>" type="text" name="id" required>
                                            <div class="uk-width-1-1 uk-text-bold uk-text-danger uk-flex uk-flex-middle">
                                                <?php echo ( count($appealDate)==0 )? lang('Manager.appeal_date_add') : lang('Manager.appeal_date_update') ; ?>
                                            </div>
                                            <div class="uk-width-expand">
                                                <label><?php echo lang('Manager.appeal_date') ?></label>
                                                <input class="uk-input datetimepicker" value="<?php echo date("Y/m/d H:i",time()) ?>" type="text" name="date" required>
                                            </div>
                                            <div class="uk-width-auto uk-flex uk-flex-bottom"><button class="uk-button uk-button-primary" type="submit" name="submit" value="Submit_add_date"><?php echo ( count($appealDate)==0 )? lang('Manager.add') : lang('Manager.update') ; ?></button></div>
                                            <div class="uk-width-1-1">
                                                <label><?php echo lang('Manager.comment') ?></label>
                                                <textarea class="uk-textarea" name="comment" rows="6"></textarea>
                                            </div>									
                                        </form>
                                    </div>
                                    <?php } ?>
                                    <?php if(count($appealDate)>0 ){ ?>
                                    <div id="stat-date" hidden class="uk-width-1-1">
                                        <div data-uk-grid class="uk-grid-small">
                                            <div class="uk-width-1-1"><hr></div>
                                            <div class="uk-width-1-1">
                                                <table class="uk-table uk-table-margin-remove uk-table-small uk-table-justify uk-table-divider">
                                                    <tr>
                                                        <th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.id') ?></th>
                                                        <th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.date') ?></th>
                                                        <th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.user') ?></th>
                                                        <th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.user_group') ?></th>
                                                        <th><?php echo lang('Manager.comment') ?></th>
                                                        <th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.appeal_approved') ?></th>
                                                    </tr>
                                                    <?php foreach($appealDate as $value ){ ?>
                                                    <tr>
                                                        <td class="uk-table-shrink uk-text-nowrap"><?php echo $value['id'] ?></td>
                                                        <td class="uk-table-shrink uk-text-nowrap"><?php echo date("Y.m.d",$value['date']) ?></td>
                                                        <td class="uk-table-shrink uk-text-nowrap"><?php echo $userManager[$userGroup[$value['user_id']]][$value['user_id']]['first_name'] ?> <?php echo $userManager[$userGroup[$value['user_id']]][$value['user_id']]['last_name'] ?></td>
                                                        <td class="uk-table-shrink uk-text-nowrap"><span class="status-label status-label-user-<?php echo $value['group_id'] ?>"><?php echo $userGroupName[$value['group_id']]['title'] ?></span></td>
                                                        <td class=""><?php echo $value['comment'] ?></td>
                                                        <td class="uk-table-shrink uk-text-nowrap"><?php if($value['approved']==1){ ?><span class="status-label status-label-user-1"><?php echo lang('Manager.yes') ?></span><?php } ?></td>
                                                    </tr>
                                                    <?php } ?>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                        <!-- Особи які приймають рішення -->
                        <?php if(isset($userManager[2])){ ?>
                        <div class="uk-width-1-1">
                            <div class="box-border">
                                <div class="box-border-label"><?php echo lang('Manager.appeal_user_head') ?></div>
                                <div data-uk-grid class="uk-grid-small">
                                
                                    <?php if(isset($appealUser[2])){ ?>
                                    <div class="uk-width-expand">
                                        <ul class="tags">
                                            <?php foreach($appealUser[2] as $value){ ?>
                                            <li><?php echo $userManager[2][$value['user_id']]['first_name'] ?> <?php echo $userManager[2][$value['user_id']]['last_name'] ?></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                    <?php if ($loginUser['group_id']<3) { ?>
                                    <div class="uk-width-auto uk-flex uk-flex-middle">
                                        <a href="#add-head" data-uk-toggle class="uk-button uk-button-primary  uk-button-small"><?php echo lang('Manager.update') ?><span class="uk-margin-small-left" data-uk-icon="icon:chevron-down;ratio:.8"></span></a>
                                    </div>
                                    <?php } ?>
                                    <?php } ?>
                                    <?php if ($loginUser['group_id']<3) { ?>
                                    <div id="add-head" class="uk-width-1-1" <?php if( isset($appealUser[2]) ){ echo "hidden"; } ?> >
                                        <form method="post" action="<?php echo current_url(true); ?>" data-uk-grid class="uk-grid-small">
                                            <?php if( isset($appealUser[2]) ){ ?>
                                            <div class="uk-width-1-1"><hr></div>
                                            <?php } ?>
                                            <input hidden class="uk-input" value="<?php echo $entries['id']; ?>" type="text" name="id" required>
                                            <div class="uk-width-1-1 uk-text-bold uk-text-danger uk-flex uk-flex-middle">
                                                <?php echo ( isset($appealUser[2]) && count($appealUser[2])>0 )? lang('Manager.appeal_head_update') : lang('Manager.appeal_head_add') ; ?>
                                            </div>
                                            <div class="uk-width-expand">
                                                <?php foreach($userManager[2] as $value){ ?>
                                                <label class="label-input label-input-full cursor"><input class="uk-checkbox" value="<?php echo $value['id'] ?>" type="checkbox" name="user[]" <?php if( isset($appealUser[2]) && in_array($value['id'],array_keys($appealUser[2]))){ ?>checked<?php } ?>> <?php echo $value['first_name'] ?> <?php echo $value['last_name'] ?></label>
                                                <?php } ?>
                                            </div>
                                            <div class="uk-width-auto uk-flex uk-flex-bottom"><button class="uk-button uk-button-primary" type="submit" name="submit" value="Submit_add_head"><?php echo ( isset($appealUser[2]) && count($appealUser[2])>0 )? lang('Manager.update') : lang('Manager.add') ; ?></button></div>
                                        </form>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        
                        <!-- Виконавець -->
                        <?php if ($loginUser['group_id']<4) { ?>
                        <?php if(isset($userManager[3])){ ?>
                        <div class="uk-width-1-1">
                            <div class="box-border">
                                <div class="box-border-label"><?php echo lang('Manager.appeal_user_implementer') ?></div>
                                <div data-uk-grid class="uk-grid-small">
                                    
                                    <?php if(isset($appealUser[3])){ ?>
                                    <div class="uk-width-expand">
                                        <ul class="tags">
                                            <?php foreach($appealUser[3] as $value){ ?>
                                            <li><?php if(!empty($value['comment_user'])){ echo $value['comment_user']; }else{ echo $userManager[3][$value['user_id']]['first_name']." ".$userManager[3][$value['user_id']]['last_name']; } ?> / <?php echo $userManager[3][$value['user_id']]['title'] ?></li>
                                            <?php if(!empty($value['comment'])){ ?><li class="comment"><?php echo lang('Manager.comment') ?>: <?php echo $value['comment'] ?></li><?php } ?>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                    <?php if ($loginUser['group_id']==3) { ?>
                                    <div class="uk-width-auto uk-flex uk-flex-middle">
                                        <a href="#add-implementer" data-uk-toggle class="uk-button uk-button-primary  uk-button-small"><?php echo lang('Manager.update') ?><span class="uk-margin-small-left" data-uk-icon="icon:chevron-down;ratio:.8"></span></a>
                                    </div>
                                    <?php } ?>
                                    <?php if ($loginUser['group_id']==2) { ?>
                                    <div class="uk-width-auto uk-flex uk-flex-middle">
                                        <a href="#add-implementer" data-uk-toggle class="uk-button uk-button-primary  uk-button-small"><?php echo lang('Manager.update') ?><span class="uk-margin-small-left" data-uk-icon="icon:chevron-down;ratio:.8"></span></a>
                                    </div>
                                    <?php } ?>
                                    <?php } else { ?>
                                    <div class="uk-width-expand">
                                        <span class="uk-text-bold uk-text-danger"><?php echo lang('Manager.appeal_user_implementer_notyet'); ?></span>
                                    </div>
                                    <?php } ?>
                                    <?php if ($loginUser['group_id']==3) { ?>
                                    <div id="add-implementer" class="uk-width-1-1" <?php if( isset($appealUser[3]) ){ echo "hidden"; } ?> >
                                        <form method="post" action="<?php echo current_url(true); ?>" data-uk-grid class="uk-grid-small">
                                            <?php if( isset($appealUser[3]) ){ ?>
                                            <div class="uk-width-1-1"><hr></div>
                                            <?php } ?>
                                            <input hidden class="uk-input" value="<?php echo $entries['id']; ?>" type="text" name="id" required>
                                            <div class="uk-width-1-1 uk-text-bold uk-text-danger uk-flex uk-flex-middle">
                                                <?php echo ( isset($appealUser[3]) && count($appealUser[3])>0 )? lang('Manager.appeal_implementer_update') : lang('Manager.appeal_implementer_add') ; ?>
                                            </div>
                                            <div class="uk-width-1-1">
                                                <label><?php echo lang('Manager.comment') ?></label>
                                                <textarea class="uk-textarea" name="comment" rows="6"></textarea>
                                            </div>
                                            <div class="uk-width-expand">
                                                <label><?php echo lang('Manager.appeal_user_implementer') ?></label>
                                                <input class="uk-input uk-width-1-1" name="comment_user">
                                            </div>
                                            <div class="uk-width-auto uk-flex uk-flex-bottom"><button class="uk-button uk-button-primary" type="submit" name="submit" value="Submit_add_implementer_user"><?php echo ( isset($appealUser[3]) && count($appealUser[3])>0 )? lang('Manager.update') : lang('Manager.add') ; ?></button></div>
                                        </form>
                                    </div>
                                    <?php } ?>
                                    <?php if ($loginUser['group_id']==2) { ?>
                                    <div id="add-implementer" class="uk-width-1-1" <?php if( isset($appealUser[3]) ){ echo "hidden"; } ?> >
                                        <form method="post" action="<?php echo current_url(true); ?>" data-uk-grid class="uk-grid-small">
                                            <?php if( isset($appealUser[3]) ){ ?>
                                            <div class="uk-width-1-1"><hr></div>
                                            <?php } ?>
                                            <input hidden class="uk-input" value="<?php echo $entries['id']; ?>" type="text" name="id" required>
                                            <div class="uk-width-1-1 uk-text-bold uk-text-danger uk-flex uk-flex-middle">
                                                <?php echo ( isset($appealUser[3]) && count($appealUser[3])>0 )? lang('Manager.appeal_implementer_update') : lang('Manager.appeal_implementer_add') ; ?>
                                            </div>
                                            <div class="uk-width-expand">
                                                <?php foreach($userManager[3] as $value){ ?>
                                                <label class="label-input label-input-full cursor"><input class="uk-radio" value="<?php echo $value['id'] ?>" type="radio" name="user[]" <?php if( isset($appealUser[3]) && in_array($value['id'],array_keys($appealUser[3]))){ ?>checked<?php } ?>><?php echo $value['first_name'] ?> <?php echo $value['last_name'] ?> <span><?php echo $value['title'] ?></span></label>
                                                <?php } ?>
                                            </div>
                                            <div class="uk-width-auto uk-flex uk-flex-bottom"><button class="uk-button uk-button-primary" type="submit" name="submit" value="Submit_add_implementer"><?php echo ( isset($appealUser[3]) && count($appealUser[3])>0 )? lang('Manager.update') : lang('Manager.add') ; ?></button></div>
                                            <div class="uk-width-1-1">
                                                <label><?php echo lang('Manager.comment') ?></label>
                                                <textarea class="uk-textarea" name="comment" rows="6"></textarea>
                                            </div>
                                        </form>
                                    </div>
                                    <?php } ?>
                                </div>
                                
                            </div>
                        </div>
                        <?php } ?>
                        <?php } ?>
                        
                        <!-- Запит виконавця уповноваженим особам -->
                        <div class="uk-width-1-1" <?php if( count($appealRequest)==0 && $loginUser['group_id']!=3 ){ ?>hidden<?php } ?>>
                            <div class="box-border">
                                <div class="box-border-label"><?php echo lang('Manager.appeal_request') ?></div>
                                <div data-uk-grid class="uk-grid-small">
                                    <div class="uk-width-1-1 uk-text-right">
                                        <a href="#appael-request" data-uk-toggle class="uk-button uk-button-primary uk-button-small"><?php echo lang('Manager.appeal_request_new') ?><span class="uk-margin-small-left" data-uk-icon="icon:chevron-down;ratio:.8"></span></a>
                                    </div>
                                    <div id="appael-request" class="uk-width-1-1" hidden>
                                        <form method="post" action="<?php echo current_url(true); ?>" data-uk-grid class="uk-grid-small">
                                            <input hidden class="uk-input" value="<?php echo $entries['id']; ?>" type="text" name="id" required>
                                            <div class="uk-width-1-1"><hr></div>
                                            <div class="uk-width-1-1 uk-text-bold uk-text-danger"><?php echo lang('Manager.appeal_request_new') ?></div>
                                            <div class="uk-width-1-1">
                                                <label><?php echo lang('Manager.appeal_request_type') ?></label>
                                                <?php if(count($appealRequestType)>0){ foreach ($appealRequestType as $value) { ?>
                                                <label class="label-input label-input-full cursor"><input class="uk-radio" value="<?php echo $value['id'] ?>" type="radio" name="type" <?php if($value['id']==1){ ?>checked<?php } ?> ><?php echo $value['title'] ?> <span class="comment"><?php echo $value['comment'] ?></span></label>
                                                <?php } } ?> 
                                            </div>
                                            <div class="uk-width-1-1">
                                                <label><?php echo lang('Manager.comment') ?></label>
                                                <textarea class="uk-textarea" name="comment" rows="12"></textarea>
                                            </div>
                                            <div class="uk-width-1-1">
                                                <button class="uk-button uk-button-primary" type="submit" name="submit" value="Submit_appeal_request"><?php echo lang('Manager.appeal_request_add') ?></button>
                                            </div>
                                        </form>
                                    </div>
                                    <?php if(count($appealRequest)>0){ ?>
                                    <div class="uk-width-1-1"><hr></div>
                                    <div class="uk-width-1-1">
                                        <table class="uk-table uk-table-divider uk-table-small uk-table-justify">
                                            <tr>
                                                <th class="uk-table-shrink"><?php echo lang('Manager.id') ?></th>
                                                <th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.date_add') ?></th>
                                                <th class=""><?php echo lang('Manager.request') ?></th>
                                                <?php if ($loginUser['group_id']<3) { ?>
                                                <th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.action') ?></th>
                                                <?php } ?>
                                                <th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.status') ?></th>
                                            </tr>
                                            <?php foreach($appealRequest as $value){ ?>
                                            <tr>
                                                <td class="uk-table-shrink"><div class="table-alert <?php if($value['approved']==0 AND $loginUser['group_id']<3){ ?>table-alert-active<?php } ?>"><?php echo $value['id'] ?></div></td>
                                                <td class="uk-table-shrink uk-text-nowrap"><?php echo date("Y.m.d",$value['date_add']) ?></td>
                                                <td class="">
                                                    <b><?php echo $appealRequestType[$value['type']]['title'] ?></b><br>
                                                    <a href="#" data-uk-toggle="target:.request-comment-<?php echo $value['id'] ?>" ><?php echo lang('Manager.request_content') ?></a>
                                                    <div class="alert request-comment-<?php echo $value['id'] ?>" style="margin-top:10px;" hidden>
                                                        <?php echo $value['comment'] ?>
                                                    </div>
                                                </td>
                                                <?php if ($loginUser['group_id']<3) { ?>
                                                <td class="uk-table-shrink uk-text-nowrap">
                                                    <?php if ($value['approved']==0) { ?>
                                                    <form method="post" action="<?php echo current_url(true); ?>">
                                                        <input hidden class="uk-input" value="<?php echo $value['id']; ?>" type="text" name="id" required>
                                                        <button class="button-icon" type="submit" name="submit" value="Submit_appeal_request_approve"><span data-uk-icon="icon:plus-circle"></span></button>
                                                        <button class="button-icon" type="submit" name="submit" value="Submit_appeal_request_reject"><span data-uk-icon="icon:minus-circle"></span></button>
                                                    </form>
                                                    <?php } ?>
                                                </td>
                                                <?php } ?>
                                                <td class="uk-table-shrink uk-text-nowrap"><span class="status-label status-label-<?php echo $value['approved'] ?>"><?php echo lang('Manager.appeal_request_status_'.$value['approved']) ?></span></td>
                                            </tr> 
                                            <?php } ?>
                                        </table>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Процес виконавця роботи -->
                        <?php if( $entries['status']>2 ){ ?>
                        <div class="uk-width-1-1" id="appaelimplementation">
                            <div class="box-border">
                                <div class="box-border-label"><?php echo lang('Manager.appeal_implementation') ?></div>
                                <div data-uk-grid class="uk-grid-small">
                                    <?php if ($loginUser['group_id']!=2) { ?>
                                    <div class="uk-width-1-1 uk-text-right">
                                        <a href="#appael-implementation-add" data-uk-toggle class="uk-button uk-button-primary uk-button-small"><?php echo lang('Manager.appeal_implementation_new') ?><span class="uk-margin-small-left" data-uk-icon="icon:chevron-down;ratio:.8"></span></a>
                                    </div> 
                                    <div id="appael-implementation-add" class="uk-width-1-1" hidden>
                                        <form method="post" action="<?php echo current_url(true); ?>" data-uk-grid class="uk-grid-small">
                                            <input hidden class="uk-input" value="<?php echo $entries['id']; ?>" type="text" name="id" required>
                                            <div class="uk-width-1-1"><hr></div>
                                            <div class="uk-width-1-1 uk-text-bold uk-text-danger"><?php echo lang('Manager.appeal_implementation_new') ?></div>
                                            <div class="uk-width-1-1">
                                                <label><?php echo lang('Manager.title') ?></label>
                                                <input class="uk-input" name="title" value="" required> 
                                            </div>
                                            <div class="uk-width-1-1">
                                                <label><?php echo lang('Manager.comment') ?></label>
                                                <textarea class="uk-textarea" name="comment" rows="6"></textarea>
                                            </div>
                                            <div class="uk-width-1-1">
                                                <button class="uk-button uk-button-primary" type="submit" name="submit" value="Submit_appeal_implementation"><?php echo lang('Manager.add') ?></button>
                                            </div>
                                        </form>
                                    </div>
                                    <?php } ?>
                                    <?php if(count($appealWork)>0){ ?>
                                    <?php if ($loginUser['group_id']!=2) { ?>
                                    <div class="uk-width-1-1"><hr></div>
                                    <?php } ?>
                                    <div class="uk-width-1-1">
                                        <table class="uk-table uk-table-divider uk-table-small uk-table-justify">
                                            <tr>
                                                <th class="uk-table-shrink"><?php echo lang('Manager.id') ?></th>
                                                <th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.date_add') ?></th>
                                                <th class=""><?php echo lang('Manager.work') ?></th>
                                                <?php if ($loginUser['group_id']!=2) { ?>
                                                <th class="uk-table-shrink uk-text-nowrap uk-text-center"><?php echo lang('Manager.action') ?></th>
                                                <?php } ?>
                                            </tr>
                                            <?php foreach($appealWork as $value){ ?>
                                            <tr>
                                                <td class="uk-table-shrink"><?php echo $value['id'] ?></td>
                                                <td class="uk-table-shrink uk-text-nowrap"><?php echo date("Y.m.d",$value['date_add']) ?></td>
                                                <td class="">
                                                    <b><?php echo $value['title'] ?></b><br>
                                                    <?php echo $value['comment'] ?>
                                                    
                                                    <div class="work-file work-photo-<?php echo $value['id'] ?>"></div>
                                                    <div class="work-file work-doc-<?php echo $value['id'] ?>"></div>
                                                    
                                                    
                                                    <div class="alert" id="work-edit-<?php echo $value['id'] ?>" style="margin-top:10px;" hidden>
                                                        <form method="post" action="<?php echo current_url(true); ?>" data-uk-grid class="uk-grid-small">
                                                            <input hidden class="uk-input" value="<?php echo $value['id']; ?>" type="text" name="id" required>
                                                            <div class="uk-width-1-1">
                                                                <label><?php echo lang('Manager.title') ?></label>
                                                                <input class="uk-input" name="title" value="<?php echo htmlspecialchars($value['title']); ?>" required> 
                                                            </div>
                                                            <div class="uk-width-1-1">
                                                                <label><?php echo lang('Manager.comment') ?></label>
                                                                <textarea class="uk-textarea" name="comment" rows="6"><?php echo $value['comment']; ?></textarea>
                                                            </div>
                                                            <div class="uk-width-1-1">
                                                                <button class="uk-button uk-button-primary  uk-button-small" type="submit" name="submit" value="Submit_appeal_implementation_edit"><?php echo lang('Manager.update') ?></button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </td>
                                                <?php if ($loginUser['group_id']!=2) { ?>
                                                <td class="uk-table-shrink uk-text-nowrap">
                                                    <a href="#work-edit-<?php echo $value['id'] ?>" class="link-width" data-uk-toggle><span data-uk-icon="icon:file-edit"></span></a>
                                                    <a href="#" class="link-width work-upload" data-id="<?php echo $entries['id'] ?>" data-work="<?php echo $value['id'] ?>" data-user="<?php echo $loginUser['id'] ?>"><span data-uk-icon="icon:cloud-upload"></span></a>
                                                </td>
                                                <?php } ?>
                                            </tr>
                                            <?php } ?>
                                        </table>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div> 
                        <?php } ?>
                        
                        <!-- Чат із заявником -->
                        <div class="uk-width-1-1" <?php if ( $loginUser['group_id']==2 AND count($appealChat)==0 ) { echo "hidden"; } ?> >
                            <div class="box-border">
                                <div class="box-border-label"><?php echo lang('Manager.chat') ?></div>
                                <div data-uk-grid class="uk-grid-small">
                                    <?php if ($loginUser['group_id']!=2) { ?>
                                    <div class="uk-width-1-1 uk-text-right">
                                        <a href="#appael-chat-add" data-uk-toggle class="uk-button uk-button-primary uk-button-small"><?php echo lang('Manager.appeal_chat_add') ?><span class="uk-margin-small-left" data-uk-icon="icon:chevron-down;ratio:.8"></span></a>
                                    </div> 
                                    <div id="appael-chat-add" class="uk-width-1-1" hidden>
                                        <div class="alert">
                                            <form method="post" action="<?php echo current_url(true); ?>" data-uk-grid class="uk-grid-small">
                                                <input hidden class="uk-input" value="<?php echo $entries['id']; ?>" type="text" name="id" required>
                                                <div class="uk-width-1-1 uk-text-bold"><?php echo lang('Manager.appeal_chat_add') ?></div>
                                                <div class="uk-width-1-1">
                                                    <label><?php echo lang('Manager.message') ?></label>
                                                    <textarea class="uk-textarea" name="message" rows="6"></textarea>
                                                </div>
                                                <div class="uk-width-1-1">
                                                    <button class="uk-button uk-button-primary  uk-button-small" type="submit" name="submit" value="Submit_appeal_chat"><?php echo lang('Manager.send') ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <?php if(count($appealChat)>0){ ?>
                                    <?php if ($loginUser['group_id']!=2) { ?>
                                    <div class="uk-width-1-1"><hr></div>
                                    <?php } ?>
                                    <div class="uk-width-1-1">
                                        <table class="uk-table uk-table-divider uk-table-small uk-table-justify">
                                            <tr>
                                                <th class="uk-table-shrink"><?php echo lang('Manager.id') ?></th>
                                                <th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.date_add') ?></th>
                                                <th class="uk-table-shrink uk-text-nowrap"><?php echo lang('Manager.sender') ?></th>
                                                <th class=""><?php echo lang('Manager.message') ?></th>
                                            </tr>
                                            <?php foreach($appealChat as $value){ ?>
                                            <tr>
                                                <td class="uk-table-shrink"><?php echo $value['id'] ?></td>
                                                <td class="uk-table-shrink uk-text-nowrap"><?php echo date("Y.m.d H:i",$value['date_add']) ?></td>
                                                <td class="uk-table-shrink uk-text-nowrap uk-text-bold">
                                                    <?php 
                                                        if ($value['who']==0) {
                                                            echo $userManager[$userGroup[$value['user_id']]][$value['user_id']]['first_name']." ".$userManager[$userGroup[$value['user_id']]][$value['user_id']]['last_name'];
                                                        }else{
                                                            echo '<span class="uk-text-danger">'.lang('Manager.appeal_chat_who_'.$value['who']).'</span>';
                                                        }
                                                    ?>
                                                </td>
                                                <td class=""><?php echo $value['message'] ?></td>
                                            </tr>
                                            <?php } ?>
                                        </table>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                </div>
                <div class="uk-width-1-3@l">
                    <div data-uk-grid>
                        <!-- Статуси -->
                        <div class="uk-width-1-1">
                            <div class="box-border">
                                <div class="box-border-label"><?php echo lang('Manager.status') ?></div>
                                <div data-uk-grid class="uk-grid-small">
                                    <div class="uk-width-1-1">
                                        <table class="uk-table uk-table-margin-remove uk-table-small uk-table-justify uk-table-divider">
                                            <?php foreach($statuses as $value){ ?>
                                            <tr>
                                                <td class="uk-table-shrink uk-text-nowrap uk-text-muted"><?php echo date("Y.m.d H:i",$value['date_add']) ?></td>
                                                <td>
                                                    <b><?php echo $status[$value['status_id']] ?></b>
                                                    <div><?php echo $value['comment'] ?></div>
                                                </td>
                                                <td class="uk-table-shrink"><?php if($value['notify']==1 AND $value['status_id']!=7){ ?><span class="status-label status-label-1"><?php echo lang('Manager.notify'); ?></span><?php } ?></td>
                                            </tr>
                                            <?php } ?>
                                        </table>
                                    </div>
                                    <?php if ($entries['status']!=5) { ?>
                                    <div class="uk-width-1-1">
                                        <a data-uk-toggle href="#add-status" class="uk-button uk-button-primary uk-button-small"><?php echo lang('Manager.add_status') ?><span class="uk-margin-small-left" data-uk-icon="icon:chevron-down;ratio:.8"></span></a>
                                    </div>
                                    <div class="uk-width-1-1" id="add-status" hidden>
                                        <div class="alert">
                                            <form method="post" action="<?php echo current_url(true); ?>" data-uk-grid class="uk-grid-small">
                                                <input hidden class="uk-input" value="<?php echo $entries['id']; ?>" type="text" name="id" required>
                                                <div class="uk-width-1-1">
                                                    <label><?php echo lang('Manager.status') ?></label>
                                                    <select class="uk-select" name="status">
                                                        <?php foreach($statusSelect as $key=>$value){ ?>
                                                        <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="uk-width-1-1">
                                                    <label><?php echo lang('Manager.comment') ?></label>
                                                    <textarea class="uk-textarea" name="comment" rows="6"></textarea>
                                                </div>
                                                <div class="uk-width-1-1">
                                                    <input class="uk-checkbox uk-margin-small-right" type="checkbox" name="notify" value="1" checked><?php echo lang('Manager.send_status_message') ?>
                                                </div>
                                                <div class="uk-width-auto uk-flex uk-flex-bottom"><button class="uk-button uk-button-primary" type="submit" name="submit" value="Submit_add_status"><?php echo lang('Manager.add') ?></button></div>
                                            </form>
                                        </div>
                                    </div> 
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <?php if ($loginUser['group_id']==2) { ?>
                        <div class="uk-width-1-1 uk-text-center">
                            <?php if ($appealActive==0) { ?>
                            <a href="#status-open" class="uk-button uk-button-primary" data-uk-toggle><?php echo lang('Manager.add_status_open') ?></a>
                            <div id="status-open" class="uk-flex-top" data-uk-modal="bg-close:false">
                                <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical">
                                    <button class="uk-modal-close-default" type="button" data-uk-close></button>
                                    <form method="post" action="<?php echo current_url(true); ?>" data-uk-grid class="uk-grid-small">
                                        <input hidden class="uk-input" value="<?php echo $entries['id']; ?>" type="text" name="id" required>
                                        <input hidden class="uk-input" value="3" type="text" name="status" required>
                                        <div class="uk-width-1-1"><h2><?php echo lang('Manager.comment_open_title') ?></h2></div>
                                        <div class="uk-width-1-1"><?php echo lang('Manager.comment_open_desc') ?></div>
                                        <div class="uk-width-1-1">
                                            <label><?php echo lang('Manager.comment') ?></label>
                                            <textarea class="uk-textarea" name="comment" rows="6"></textarea>
                                        </div>
                                        <div class="uk-width-1-1">
                                            <input class="uk-checkbox uk-margin-small-right" type="checkbox" name="notify" value="1" checked><?php echo lang('Manager.send_status_message') ?>
                                        </div>
                                        <div class="uk-width-auto uk-flex uk-flex-bottom"><button class="uk-button uk-button-primary" type="submit" name="submit" value="Submit_add_status"><?php echo lang('Manager.confirm') ?></button></div>
                                    </form>
                                </div>
                            </div>
                            <?php } ?>
                            <?php if ($appealActive==1 AND $appealDone==0) { ?>
                            <a href="#status-close" class="uk-button uk-button-primary" data-uk-toggle><?php echo lang('Manager.add_status_close') ?></a>
                            <div id="status-close" class="uk-flex-top" data-uk-modal="bg-close:false">
                                <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical">
                                    <button class="uk-modal-close-default" type="button" data-uk-close></button>
                                    <form method="post" action="<?php echo current_url(true); ?>" data-uk-grid class="uk-grid-small">
                                        <input hidden class="uk-input" value="<?php echo $entries['id']; ?>" type="text" name="id" required>
                                        <input hidden class="uk-input" value="5" type="text" name="status" required>
                                        <div class="uk-width-1-1"><h2><?php echo lang('Manager.comment_close_title') ?></h2></div>
                                        <div class="uk-width-1-1"><?php echo lang('Manager.comment_close_desc') ?></div>
                                        <div class="uk-width-1-1">
                                            <label><?php echo lang('Manager.comment') ?></label>
                                            <textarea class="uk-textarea" name="comment" rows="6"></textarea>
                                        </div>
                                        <div class="uk-width-1-1">
                                            <input class="uk-checkbox uk-margin-small-right" type="checkbox" name="notify" value="1" checked><?php echo lang('Manager.send_status_message') ?>
                                        </div>
                                        <div class="uk-width-auto uk-flex uk-flex-bottom"><button class="uk-button uk-button-primary" type="submit" name="submit" value="Submit_add_status"><?php echo lang('Manager.confirm') ?></button></div>
                                    </form>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <?php } ?>
                        
                    </div>
                </div>			
			</div>
		</div>
		<div class="uk-width-1-1" hidden>
			<pre>
				<?php var_dump($appealChat) ?> 
			<pre>
		</div>
	</div>
</main>

<div class="link-width js-upload" data-uk-form-custom hidden>
	<input name="file" type="file" multiple>
	<span class="js-upload-link"><span data-uk-icon="icon:cloud-upload"></span></span>
</div>

<script>
<?php if( !empty($entries['location_lat']) AND !empty($entries['location_lat'])){ ?>
var map = L.map("map").setView([<?php echo $entries['location_lat'] ?>, <?php echo $entries['location_lng'] ?>], 18);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'}).addTo(map);
map.scrollWheelZoom.disable();
map.invalidateSize(true);
L.marker([<?php echo $entries['location_lat'] ?>, <?php echo $entries['location_lng'] ?>]).addTo(map);

var appealContent = $('#appeal-content');
appealContent.on("beforeshow", function () {
	setTimeout(function(){ map.invalidateSize()}, 200);
}); 

<?php } ?>
jQuery(document).ready(function () {
	jQuery.datetimepicker.setLocale('uk');
	jQuery('.datetimepicker').datetimepicker({
		dayOfWeekStart:1,
	});
	$('.datetimepicker').change('click', function () {});
});

$(".work-upload").click(function() {
	event.preventDefault();
	var id = $(this).data('id');
	var work = $(this).data('work');
	var user = $(this).data('user');
	var settings = {
		url: 'https://hotline.kalushcity.gov.ua/ajax/upload',
		multiple: true,
		params: {
			id: id,
			work: work,
			user: user,
		},
		completeAll: function () {
			loadGallery(work);
			loadDoc(work);
		}
	};
	$('.js-upload input').click();
	UIkit.upload('.js-upload', settings);
}); 

$('.work-file').on('click','.work-delete',function(){
	event.preventDefault();
	var id = $(this).data('id');
	var work = $(this).data('work');
	$.ajax({
		url: '/ajax/filedel',
		type: "POST",
		data: {id:id},
		success: function(data){
			loadGallery(work);
			loadDoc(work);
		}
	});	
});
$('.work-file').on('click','.work-completed',function(){
	event.preventDefault();
	var id = $(this).data('id');
	var work = $(this).data('work');
	$.ajax({
		url: '/ajax/filecompleted',
		type: "POST",
		data: {id:id},
		success: function(data){
			loadGallery(work);
			loadDoc(work);
		}
	});	
});


<?php foreach($appealWork as $value){ ?>
loadGallery(<?php echo $value['id'] ?>);
loadDoc(<?php echo $value['id'] ?>);
<?php } ?>

function loadGallery(work){
	var dirGallery = "assets/upload/work/<?php echo $entries['id'] ?>/";
	var divGallery = ".work-photo-"+work;
	var jsonUrl = "/ajax/workphoto/"+work; 
    $.getJSON(jsonUrl, function(data) {
		$(divGallery).html("");
		if(data.length!=0){
			$(divGallery).append('<div class="work-photo"><ul data-uk-grid class="uk-grid-small" data-uk-lightbox>');
			$.each(data, function(key, val) {
				$(divGallery+" div ul").append('<li class="uk-width-small"><a href="'+dirGallery+val.image+'"><div class="uk-cover-container"><canvas width="300" height="300"></canvas><img src="'+dirGallery+val.image+'" data-uk-cover></div></a><span class="work-completed work-completed-'+val.completed+'" data-id="'+key+'" data-work="'+work+'"></span><span class="work-delete" data-id="'+key+'" data-work="'+work+'"></span></li>');
			});
			$(divGallery).append('</ul></div>'); 
		}
    });
}; 

function loadDoc(work){
	var dirDoc = "assets/upload/work/<?php echo $entries['id'] ?>/";
	var divDoc = ".work-doc-"+work;
	var jsonUrl = "/ajax/workdoc/"+work;
    $.getJSON(jsonUrl, function(data) {
		$(divDoc).html("");
		if(data.length!=0){
			$.each(data, function(key, val) {
			$(divDoc).append('<div class="work-doc"><ul>');
				$(divDoc+' div ul').append('<li><a href="'+dirDoc+val+'">'+val+'</a><span class="work-delete" data-id="'+key+'" data-work="'+work+'"></span></li>');
			});
			$(divDoc).append('</ul></div>');
		}
    });
};
</script>