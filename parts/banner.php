<?php
$home_id = 2;
$header_image = get_field("header_image",$home_id);
$title1 = get_field("title1",$home_id);
$title2 = get_field("title2",$home_id);
$custom_logo_id = get_theme_mod( 'custom_logo' );
$logoImg = wp_get_attachment_image_src($custom_logo_id,'large');
if ($header_image) { ?>
<div class="header-image" style="background-image:url('<?php echo $header_image['url']?>');min-height:200px;">
	<div class="header-inner">
		<div class="wrap">
			<?php if ($logoImg) { ?>
				<a href="https://www.idahograce.com/" title="Grace Bible Church - Homepage" class="logo animated zoomIn"><img src="<?php echo $logoImg[0] ?>" alt="<?php bloginfo('name') ?>"></a>
			<?php } ?>

			<?php if ($title1) { ?>
			<h2 class="title1"><?php echo $title1 ?></h2>	
			<?php } ?>
			<?php if ($title2) { ?>
			<div class="title2"><?php echo $title2 ?></div>	
			<?php } ?>
		</div>
	</div>
</div>	
<?php } ?>