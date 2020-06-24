<?php if ( isset($_GET['sent']) && $_GET['sent'] && isset($_GET['title']) && isset($_GET['id']) && isset($_GET['email']) ) { ?>
	<div class="modal fade show" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display:block;margin-top:8%">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="exampleModalLiveLabel">Sermon Guide Emailed!</h5>
	        <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">×</span>
	        </button> -->
	      </div>
	      <?php  
	      	$id = ( isset($_GET['id']) && $_GET['id'] ) ? $_GET['id'] : '';
	      	$user_email =( isset($_GET['email']) && $_GET['email'] ) ? $_GET['email'] : '';
	      	$post = ($id) ? get_post($id) : '';
	      	$post_title = ( isset($post->post_title) ) ? $post->post_title : "";
	      ?>
	      <div class="modal-body">
	      	<h2 class="topic">Topic: <?php echo $post_title ?></h2>
	        <p>Notes successfully sent to <strong><i><?php echo $user_email; ?></i></strong></p>
	      </div>
	      	<div class="modal-footer">
		        <button type="button" class="closeModal btn btn-secondary" data-dismiss="modal">Close</button>
		    </div>
	    </div>
	  </div>
	</div>
	<div id="messageBg" class="modal-backdrop fade show"></div>
<?php } ?>