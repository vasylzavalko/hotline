<!DOCTYPE html><html lang="uk" dir="ltr"><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="<?= csrf_header() ?>" content="<?= csrf_hash() ?>">
<title><?php echo  $pageTitleSeo ?></title>
<link rel="icon" href="<?php echo base_url(); ?>/assets/upload/template/k-icon-64.svg">
<link rel="apple-touch-icon" href="<?php echo base_url(); ?>/assets/upload/template/k-icon-64.svg">
<base href="<?= base_url() ?>">
<link rel="stylesheet" href="<?php echo base_url(); ?>/assets/uikit/css/uikit.min.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>/assets/style/style.css?<?= $time ?>" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>/assets/uikit/js/uikit.min.js"></script>
<script src="<?php echo base_url(); ?>/assets/uikit/js/uikit-icons.min.js"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/assets/js/datepicker/jquery.datetimepicker.min.css"/ >
<script src="<?php echo base_url(); ?>/assets/js/datepicker/jquery.datetimepicker.full.min.js"></script>
</head><body> 
<header>
	<div class="header-top uk-visible@m">
		<div class="uk-container uk-container-large">
			<div data-uk-grid class="uk-grid-small">
				<div class="uk-width-auto">
					<ul class="header-top-padding">
						<li><span class="icon" data-uk-icon="icon: homenew"></span><a href="https://kalushcity.gov.ua">Портал міста</a></li>
						<li><span class="icon" data-uk-icon="icon: documents"></span><a href="https://kalushcity.gov.ua/docs">Нормативна база</a></li>
						<li><span class="icon" data-uk-icon="icon: opendata"></span><a target="_blank" href="https://data.gov.ua/organization/ae0dd46e-a8b1-457d-8d9e-a12e235c30ad">Відкриті дані</a></li>
						<li><span class="icon" data-uk-icon="icon: publicinfo"></span><a href="https://kalushcity.gov.ua/publicinfo">Публічна інформація</a></li>
					</ul>
				</div>
				<div class="uk-width-expand uk-flex uk-flex-center">
					<ul>
						<li><span class="icon" data-uk-icon="icon: video-camera"></span><a href="https://kalushcity.gov.ua/live">Трансляції</a></li>
					</ul>
				</div>
				<div class="uk-width-auto">
					<ul>
						<li class="uk-visible@l"><span class="icon" data-uk-icon="icon: mail"></span><a href="https://kalushcity.gov.ua/kmr/contacts">Контакти</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="header">
		<div class="style-bw logo-bw"><a href="<?php echo base_url(); ?>"><?= lang('Front.site_name'); ?><span><?= lang('Front.site_sub_name'); ?></span></a></div>
		<div class="uk-container uk-container-large">
			<div class="boundary-align">
				<div data-uk-grid class="uk-grid-small">
					<div class="uk-width-auto"><a href="<?php echo base_url(); ?>" class="logo"><?= lang('Front.site_name'); ?><span><?= lang('Front.site_sub_name'); ?></span></a></div>
					<div class="uk-width-expand uk-flex uk-flex-middle">
						<ul class="main-menu uk-visible@m">
                            <li><a href="<?php echo base_url(); ?>/appeal">Звернення мешканців</a></li>
                            <li hidden><a href="<?php echo base_url(); ?>/before-after">Результати роботи</a></li>
                            <li><a href="<?php echo base_url(); ?>/help">Допомога</a></li>
						</ul>
					</div>
                    
					<div class="uk-width-auto uk-flex uk-flex-middle uk-visible@s" hidden><a class="social" href="https://www.facebook.com/kalushcity" target="_blank"><span data-uk-icon="icon:facebook"></span></a></div>
                    
					<div class="uk-width-auto uk-flex uk-flex-middle uk-hidden@m">
						<a href="#mobile-menu" data-uk-toggle><span data-uk-icon="icon:menu;ratio:1.6;"></span></a>					
					</div>
				</div>
			</div>
		</div>
	</div>
</header>
<div id="mobile-menu" uk-offcanvas="flip: true; overlay: true; mode: push">
    <div class="uk-offcanvas-bar">
        <button class="uk-offcanvas-close" type="button" uk-close></button>
		<div class="uk-grid-small" data-uk-grid>
			<div class="uk-width-1-1">
				<ul class="mobile-menu-icon">
					<li><span class="icon" data-uk-icon="icon: homenew"></span><a href="https://kalushcity.gov.ua">Портал міста</a></li>
					<li><span class="icon" data-uk-icon="icon: documents"></span><a href="https://kalushcity.gov.ua/docs">Нормативна база</a></li>
					<li><span class="icon" data-uk-icon="icon: opendata"></span><a href="https://data.gov.ua/organization/ae0dd46e-a8b1-457d-8d9e-a12e235c30ad">Відкриті дані</a></li>
					<li><span class="icon" data-uk-icon="icon: publicinfo"></span><a href="https://kalushcity.gov.ua/publicinfo">Публічна інформація</a></li>
				</ul>
			</div>
			<div class="uk-width-1-1">
				<ul class="mobile-menu-ul uk-nav-parent-icon" data-uk-nav>
                    <li><a class="first" href="<?php echo base_url(); ?>/appeal">Звернення мешканців</a></li>
                    <li><a class="first" href="<?php echo base_url(); ?>/before-after">Результати роботи</a></li>
                    <li><a class="first" href="<?php echo base_url(); ?>/help">Допомога</a></li>
				</ul>
			</div>
		</div>
    </div>
</div>