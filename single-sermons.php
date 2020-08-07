<?php 
$is_email_sent = false;
$currentURL = get_permalink();
$no_header = ( isset($_GET['plain']) && $_GET['plain'] ) ? true : false;
$pid = ( isset($_GET['pid']) && $_GET['pid'] ) ? $_GET['pid'] : '';
$dateNow = date('mdY');

//if( isset($_COOKIE['sermonsaveddate']) ) {
//}

if($no_header) { 
	if( $post = get_post($pid) ) {
		$content = $post->post_content;
		$content = apply_filters('the_content',$content);
		$inputField = '<span>{%userAnswer%}</span>';
		$inputFieldMultiple = '<span>{%userAnswerMultiple%}</span>';
		$content = str_replace('{%blank_field%}',$inputField,$content);
		//$content = str_replace('{%blank_field_multiple%}',$inputFieldMultiple,$content);
		$content = str_replace('{%additional_notes%}',$inputFieldMultiple,$content);
		echo $content;
	}


}

require_once get_template_directory() . "/dompdf/autoload.inc.php";
use Dompdf\Dompdf;
$dompdf = new DOMPDF();


if( isset($_POST['action_type']) && $_POST['action_type']=='download' )  {
	$post_id = $_POST['id'];
	$html = download_sermon_notes($_POST);
	//echo $html;
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
	$user_email = $_POST['user_email'];
	$sent = email_sermon_notes($_POST);
	if($sent) {
		$postTitle = get_the_title($post_id);
		$postTitle = urlencode($postTitle);
		wp_redirect($currentURL . '?sent=1&title='.$postTitle.'&id='.$post_id.'&email='.$user_email);
		// exit;
		//$is_email_sent = true;
	}
}


if( isset($_GET['action_type']) && $_GET['action_type']=='Download Notes' ) {
	$no_header = true;
    $post_id = (isset($_GET['id']) && $_GET['id']) ? $_GET['id'] : '';
    $html = ($post_id) ? download_sermon_notes($_GET) : '';
    if($html) {
        $post = get_post($post_id);
        $title = get_the_title($post_id);
        $fileName = sanitize_title($title) . '.pdf';
        $dompdf->load_html($html);
        $dompdf->render();
        $dompdf->stream($fileName);
    }
}

if( ( isset($_GET['getinfo']) && $_GET['getinfo']=='email' ) && isset($_GET['user_email']) && $_GET['user_email'] )  {
	$post_id = $_GET['id'];
	$user_email = $_GET['user_email'];
	$sent = email_sermon_notes($_GET);
	$emailSuccessMsg = '';
	if($sent) {
		$postTitle = get_the_title($post_id);
		$postTitle = urlencode($postTitle);
		//wp_redirect($currentURL . '?sent=1&title='.$postTitle.'&id='.$post_id.'&email='.$user_email);
		// exit;
		//$is_email_sent = true;
		$emailSuccessMsg = 'Notes successfully sent to <i><strong>' . $user_email . '</strong></i>';
	} else {
		$emailSuccessMsg = 'Email Notes faield. Plese try again.';
	}
	$out['sent'] = $sent;
	$out['message'] = $emailSuccessMsg;
	echo json_encode($out);
	$no_header = true;
}



$is_email_sent = ( isset($_GET['sent']) && $_GET['sent']==1 ) ? true : false;
$custom_logo_id = get_theme_mod( 'custom_logo' );
$logoImg = wp_get_attachment_image_src($custom_logo_id,'large');
$siteURL = get_site_url();
$logoURL = $logoImg[0];
$logo_url = str_replace($siteURL,'',$logoURL);
if( $is_email_sent ) {
	$show_page  = true;
	get_template_part("parts/alert-message");
} else {
	$show_page = ( isset($_POST['action_type']) && $_POST['action_type'] ) ? false : true;
}

if($no_header) {
	$show_page = false;
}
if($show_page) { 
get_header();  ?>
<div id="primary" class="sermon-content-area">
	<main id="main" class="site-main" role="main">

		<section class="sermon-posts">
			<div class="wrapper">
				<?php 
				$content = '';
				$actual_content = '';
				$i=1; while ( have_posts() ) : the_post();
					//$noteForm = note_form_markup(); 

					$v = get_field("scriptures");
					$verses = ( isset($v['verses']) && $v['verses'] ) ? $v['verses'] : '';
					$scripture = ( isset($v['text']) && $v['text'] ) ? $v['text'] : '';

					ob_start();
					the_content();
					$content = ob_get_contents();
					ob_end_clean();
					//$actual_content = $content;
					//$content = get_the_content();
					$inputField = '<span><input type="text" class="notes-input" name="answer[]"></span>';
					$textarea = '<div class="addNotesDiv"><a class="addtlNotesBtn"><i class="fas fa-edit"></i> <span>Add Notes</span></a><textarea class="notes-input" name="answer_multiple[]"></textarea></div>';
					$content = str_replace('{%blank_field%}',$inputField,$content);
					//$content = str_replace('{%blank_field_multiple%}',$textarea,$content);
					$content = str_replace('{%additional_notes%}',$textarea,$content);

					$sermon_id = get_the_ID();
					$sermon_date = get_field("sermon_date");
					?>
					<div class="pageheader">
						<h1 class="pageTitle"><?php the_title(); ?></h1>
						<?php if ($sermon_date) { ?>
						<div class="sermonDate"><?php echo $sermon_date ?></div>
						<?php } ?>
						<?php if ($verses) { ?>
						<div class="verses"><i>Text: <span id="verse-info" class="verselink <?php echo ($scripture) ? 'has-scripture':'' ?>"><?php echo $verses ?></span></i></div>
						<?php } ?>
					</div>
					<div class="entry-content"><?php echo $content ?></div>
					<div id="featuredPostId" data-id="<?php echo $sermon_id ?>"></div>
					<!-- <div class="sermon-item">
						<h3 class="sermonTitle"><?php //the_title(); ?></h2>
						<div class="sermonText"><?php //echo $content; ?></div>
					</div> -->
					<?php endwhile; ?>
			</div>
		</section>

		<div class="sermon-bottom-buttons">
			<div class="wrapper">
				<button type="button" id="resetBtn" class="btn btn-primary gray">Reset</button>

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
			<input type="hidden" id="dateToday" name="dateToday" value="<?php echo date('mdY'); ?>">
			<div class="notesContainer"></div>
		</form>
		</div>

		<?php if ($scripture) { ?>
		<div id="verseData" style="display:none;">
			<div class="scripture-content"><?php echo $scripture ?></div>
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

<div id="modalInputField"><div id="modalInputWrap"><div id="modalInputTxt" data-currenttext=""><input type="text" class="ansTxtbox"></div><div id="modalInputBtn"><a id="cancelInputBtn">Cancel</a><a id="saveInputBtn">Save</a></div></div></div>

<?php 
get_footer();
}
