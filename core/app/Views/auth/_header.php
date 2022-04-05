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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>/assets/uikit/js/uikit.min.js"></script>
<script src="<?php echo base_url(); ?>/assets/uikit/js/uikit-icons.min.js"></script>
</head><body> 