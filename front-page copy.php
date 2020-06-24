<?php 
$is_email_sent = false;
$currentURL = get_permalink();
//require_once get_template_directory() . "/html2pdf/src/Html2Pdf.php";

require_once get_template_directory() . "/dompdf/autoload.inc.php";
use Dompdf\Dompdf;

if( isset($_POST['action_type']) && $_POST['action_type']=='download' )  {
	$dompdf = new DOMPDF();

	$post_id = $_POST['id'];
	$notes = $_POST['note'];
	$html = download_sermon_notes($post_id,$notes);
	if($html) {
		$post = get_post($post_id);
		if($post) {
			$title = $post->post_title;
			$fileName = sanitize_title($title) . '.pdf';
			$dompdf->load_html($html);
			$dompdf->render();
			$dompdf->stream($fileName);
		}
	}
}

if( ( isset($_POST['action_type']) && $_POST['action_type']=='email' ) && isset($_POST['user_email']) && $_POST['user_email'] )  {
	$post_id = $_POST['id'];
	$notes = $_POST['note'];
	$user_email = $_POST['user_email'];
	$sent = email_sermon_notes($_POST);
	if($sent) {
		$postTitle = get_the_title($post_id);
		$postTitle = urlencode($postTitle);
		wp_redirect($currentURL . '?sent=1&title='.$postTitle.'&email='.$user_email);
		exit;
	}
}


$custom_logo_id = get_theme_mod( 'custom_logo' );
$logoImg = wp_get_attachment_image_src($custom_logo_id,'large');
$siteURL = get_site_url();
$logoURL = $logoImg[0];
$logo_url = str_replace($siteURL,'',$logoURL);
if( $is_email_sent ) {
	$show_page  = true;
} else {
	$show_page = ( isset($_POST['action_type']) && $_POST['action_type'] ) ? false : true;
}
if($show_page) { 
get_header();  ?>
<div id="primary" class="sermon-content-area">
	<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php
			$header_image = get_field("header_image");
			$title1 = get_field("title1");
			$title2 = get_field("title2");
			?>
			
			<?php if ($header_image) { ?>
			<div class="header-image" style="background-image:url('<?php echo $header_image['url']?>')">
				<div class="header-inner">
					<div class="wrap">
						<?php if ($logoImg) { ?>
							<a href="https://www.idahograce.com/" class="logo animated zoomIn"><img src="<?php echo $logoImg[0] ?>" alt="<?php bloginfo('name') ?>"></a>
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
			
			<h1 style="display:none"><?php the_title(); ?></h1>

			<div class="entry-content">
				<?php the_content(); ?>
			</div>

		<?php endwhile;  ?>


		<?php  
			$args = array(
				'posts_per_page'=> 1,
				'post_type'		=> 'sermons',
				'post_status'	=> 'publish',
				'meta_query' => array(
			       array(
			           'key' => 'sermon_visibility',
			           'value' => 'on',
			           'compare' => '=',
			       )
			   	)
			);
			$sermons = new WP_Query($args);
		$content = '';
		$actual_content = '';
		if ( $sermons->have_posts() ) {  ?>
		<section class="sermon-posts">
			<div class="wrapper">
				<?php $i=1; while ( $sermons->have_posts() ) : $sermons->the_post();
				$noteForm = note_form_markup(); 
				ob_start();
				the_content();
				$content = ob_get_contents();
				ob_end_clean();
				$actual_content = $content;
				$content = str_replace('{%AddNoteButton%}',$noteForm,$content);
				$sermon_id = get_the_ID();
				?>
				<div class="sermon-item">
					<h3 class="sermonTitle"><?php the_title(); ?></h2>
					<div class="sermonText"><?php echo $content; ?></div>
				</div>
				<?php $i++; endwhile; wp_reset_postdata(); ?>
			</div>
		</section>

		<div class="sermon-bottom-buttons">
			<div class="wrapper">
				<a id="downloadNotes" class="gbcBtn btn btn-primary" data-id="<?php echo $sermon_id ?>">Download Notes</a>
				<!-- <a id="emailBtn" class="gbcBtn">Email Notes</a> -->
				<button type="button" id="emailBtn" class="btn btn-primary" data-toggle="modal" data-target="#emailNotesFrm">Email Notes</button>
			</div>
		</div>
		
		<div style="display:none">
		<form id="notesForm" action="<?php echo get_permalink() ?>" method="post">
			<input type="hidden" name="action_type" id="action_type" value="">
			<input type="hidden" name="id" value="<?php echo $sermon_id ?>">
			<input type="hidden" name="user_email" id="userEmail" value="">
			<div class="notesContainer"></div>
		</form>
		</div>

		<?php } ?>
		
		
		<!-- Modal -->
		<div class="modal fade" id="emailNotesFrm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <h5 class="modal-title" id="exampleModalLabel">Email Notes</h5>
		        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		          <span aria-hidden="true">&times;</span>
		        </button> -->
		      </div>
		      <div class="modal-body">
		       	<div class="emailNoteField">
					<label for="email_note">Your Email Address:</label>
					<input type="email" id="emailTo" class="form-control emailTo" name="email" value="">
					<div id="respond"></div>
				</div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		        <button id="emailNotes" class="btn btn-primary">Submit</button>
		      </div>
		    </div>
		  </div>
		</div>


	</main><!-- #main -->
</div><!-- #primary -->
<?php
get_footer();
}
