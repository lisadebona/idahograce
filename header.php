<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<link href="https://fonts.googleapis.com/css2?family=Heebo:wght@100;200;300;400;500;531;600;700;800;900&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<script defer src="<?php bloginfo( 'template_url' ); ?>/assets/svg-with-js/js/fontawesome-all.js"></script>
<script>
var params={};location.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){params[k]=v});
var currentURL = '<?php echo get_permalink(); ?>';
var d = new Date();
if(typeof params.sent!=='undefined') {
	history.replaceState('', document.title,currentURL);
}
</script>
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site cf">
	

	<a class="skip-link sr" href="#content"><?php esc_html_e( 'Skip to content', 'idahograce' ); ?></a>

	<header id="masthead" class="site-header" role="banner">
		<div class="wrapper">

	
		</div><!-- wrapper -->
	</header><!-- #masthead -->

	<div id="content" class="site-content">
