<!DOCTYPE html><html lang="uk" dir="ltr"><head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="<?= csrf_header() ?>" content="<?= csrf_hash() ?>">
<title><?php echo  $pageTitleSeo ?></title>
<link rel="icon" href="<?php echo base_url(); ?>/assets/template/k-icon-64.svg">
<link rel="apple-touch-icon" href="<?php echo base_url(); ?>/assets/template/k-icon-64.svg">
<base href="<?= base_url() ?>">
<link rel="stylesheet" href="<?php echo base_url(); ?>/assets/uikit/css/uikit.min.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>/assets/style/style-manager.css?<?= $time ?>" />
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>/assets/uikit/js/uikit.min.js"></script>
<script src="<?php echo base_url(); ?>/assets/uikit/js/uikit-icons.min.js"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/assets/js/datepicker/jquery.datetimepicker.min.css"/ >
<script src="<?php echo base_url(); ?>/assets/js/datepicker/jquery.datetimepicker.full.min.js"></script>
</head><body id="top">
<?php if( isset($headerView) AND $headerView == 'view' ){ ?>
<header data-uk-sticky>
	<div>
		<div data-uk-grid class="uk-grid-small">
			<div class="uk-width-auto uk-flex uk-flex-middle"><a href="<?php echo $menu['bashboard']['url'] ?>" class="logo"></a></div>
			<div class="uk-width-expand uk-flex uk-flex-middle">
				<ul>
					<?php foreach ($menu as $value){ ?>
					<li>
						<a href="<?php echo ( isset($value['childs']) AND count($value['childs'])>0 ) ? "#" : $value['url'] ; ?>" class="<?php echo $value['class'] ?>"><?php echo $value['title'] ?></a>
						<?php if( isset($value['childs']) AND count($value['childs'])>0 ){ ?>
						<div data-uk-dropdown="mode: click;">
							<ul class="">
								<?php foreach ($value['childs'] as $valueSecond){ ?>
								<li><a href="<?php echo $valueSecond['url'] ?>" class="<?php echo $valueSecond['class'] ?>"><?php echo $valueSecond['title'] ?></a></li>
								<?php } ?>
							</ul>
						</div>
						<span data-uk-icon="icon:triangle-down"></span>
						<?php } ?>
					</li>
					<?php } ?>
				</ul>
			</div>
			<div class="uk-width-auto uk-flex uk-flex-middle uk-text-bold"><?php echo $loginUser['email'] ?></div>
			<div class="uk-width-auto uk-flex uk-flex-middle"><a href="/logout"><span uk-icon="icon:sign-out;ratio:1.2"></span></a></div>
		</div>
	</div>
</header>
<?php } ?>