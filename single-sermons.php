<?php while ( have_posts() ) : the_post(); 
	$sermon_id = get_the_ID();
	$sermon_date = get_field("sermon_date");
	$is_type = (isset($_GET['gettype']) && $_GET['gettype']) ? $_GET['gettype'] : '';
	$logo = get_site_url() . "/idaho-grace.jpg";
	$siteName = get_bloginfo("name");
	$tdStyles = '';
	if($is_type=='email') {
		$tdStyles = ' style="background-color:#FBAE6D;padding:20px;"';
	}
?>
<table style="border:none;border-collapse:collapse;width:100%;">
	<tbody>
		<tr>
			<td<?php echo $tdStyles ?>>
				<table style="border:none;border-collapse: collapse;background-color:#FFFFFF;font-family:Arial,Helvetica;font-size:16px;line-height:1.3;max-width:800px;width:100%;margin:20px auto">
					<tbody>
						<tr>
							<td style="padding:20px;background:#fff;">
								<p style="text-align:center;margin:0 0 10px">
									<a href="https://www.idahograce.com/" target="_blank"><img src="<?php echo $logo ?>" style="width:100px;height:auto"></a>
								</p>
								<h1 align="center" style="font-size:20px;line-height: 1.2;margin:10px 0 20px">
									<?php echo $siteName ?><br>Sermon Guide
								</h1><hr>
								
								
								<h2 style="font-size:25px;color:#f79e54;margin:15px 0 0"><?php the_title(); ?></h2>
								<p style="font-size:16px;margin:0 0 35px">
								<?php if ($sermon_date) { ?>
								<strong><?php echo $sermon_date ?></strong>
								<?php } ?>
								</p>
		
								<?php the_content(); ?>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>	

<?php endwhile; ?>