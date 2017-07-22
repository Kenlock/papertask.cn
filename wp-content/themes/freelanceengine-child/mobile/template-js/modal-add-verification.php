<?PHP
	$author_id 	= 	get_query_var( 'author' );
?>
<div class="modal fade" id="modal_add_verification">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<i class="fa fa-times"></i>
				</button>
				<h4 class="modal-title"><?php _e("Add your ID photo or Passport Photo", ET_DOMAIN) ?></h4>
			</div>
			<div class="modal-body">
				<form role="form" id="create_verification" class="auth-form create_verification">
                	<div id="verification_img_container">
                		<input type="hidden" name="post_thumbnail" id="post_thumbnail_verification" value="0" />
                		<span class="image" id="verification_img_thumbnail">
                			<!-- IMG UPLOAD GO HERE -->
                		</span>
                		<span class="et_ajaxnonce hidden" id="<?php echo wp_create_nonce( 'verification_img_et_uploader' ); ?>"></span>
                		<p class="add-file"><?php _e('ADD IMAGE', ET_DOMAIN) ?></p>
                		<p class="browser-image">
                			<input type="button" id="verification_img_browse_button" class="btn btn-default btn-submit" value="Browse" />
                		</p>
                	</div>
                	<div class="clearfix"></div>
                	<div class="form-group">
                		<label><?php _e('ID Type', ET_DOMAIN) ?></label>
                		<p><input type="text" class="verification-title" name="post_title" id="post_title" placeholder="<?php _e('Identification Card or Passport', ET_DOMAIN) ?>" /></p>
                	</div>
                	<div class="clearfix"></div>
					<div class="form-group">
                		<label><?php _e('ID Number', ET_DOMAIN) ?></label>
                		<p><input type="text" name="this_post_content" id="this_post_content" /></p>
                	</div>
                	<div class="clearfix"></div>
                	<p class="btn-warpper-bid btn-add-port">
						<input type="hidden" name="current_user" value="<?PHP echo $author_id; ?>">
						<button type="submit" class="btn-submit btn-sumary btn-bid">
							<?php _e('Add Photo', ET_DOMAIN) ?>
						</button>
					</p>
				</form>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->