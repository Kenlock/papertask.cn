<?php
/**
 * The template for displaying project message list, mesage form in single project
 */
global $post, $user_ID;
$date_format = get_option('date_format');
$time_format = get_option('time_format');

// Load milestone change log if ae-milestone plugin is active
if( defined( 'MILESTONE_DIR_URL' ) ) {
    $total_args_chat_box = array(
        'type' => 'message',
        'post_id' => $post->ID ,
        'paginate' => 'load',
        'order' => 'DESC',
        'orderby' => 'date',
    );
} else {
    $total_args_chat_box = array(
        'type' => 'message',
        'post_id' => $post->ID ,
        'paginate' => 'load',
        'order' => 'DESC',
        'orderby' => 'date',
        'meta_query' => array(
            array(
                'key' => 'changelog',
                'value' => '',
                'compare' => 'NOT EXISTS'
            ),
			array(
                'key' => 'fre_comment_box_type',
                'value' => 'chat_box',
                'compare' => '='
            )
        )
    );
}
$total_args_chat_box['text'] = __("Load older message", ET_DOMAIN);
echo '<script type="data/json"  id="workspace_query_args_chat_box">'. json_encode($total_args_chat_box) .'</script>';
/**
 * count all reivews
*/
$total_args = $total_args_chat_box;
$all_cmts   = get_comments( $total_args );

/**
 * get page 1 reviews
*/
$total_args_chat_box['number'] = get_option('posts_per_page');
$comments_chat_box = get_comments( $total_args_chat_box );

$total_messages = count($all_cmts);
$comment_pages_chat_box  =   ceil( $total_messages/$total_args_chat_box['number'] );
$total_args_chat_box['total'] = $comment_pages_chat_box;

$messagedata_chat_box = array();
$message_object_chat_box = Fre_Message::get_instance();


/********************** translator_box ****************************/

// Load milestone change log if ae-milestone plugin is active
if( defined( 'MILESTONE_DIR_URL' ) ) {
    $total_args_translator_box = array(
        'type' => 'message',
        'post_id' => $post->ID ,
        'paginate' => 'load',
        'order' => 'DESC',
        'orderby' => 'date',
    );
} else {
    $total_args_translator_box = array(
        'type' => 'message',
        'post_id' => $post->ID ,
        'paginate' => 'load',
        'order' => 'DESC',
        'orderby' => 'date',
        'meta_query' => array(
            array(
                'key' => 'changelog',
                'value' => '',
                'compare' => 'NOT EXISTS'
            ),
			array(
                'key' => 'fre_comment_box_type',
                'value' => 'translator_box',
                'compare' => '='
            )
        )
    );
}
$total_args_translator_box['text'] = __("Load older message", ET_DOMAIN);
echo '<script type="data/json"  id="workspace_query_args_translator_box">'. json_encode($total_args_translator_box) .'</script>';
/**
 * count all reivews
*/
$total_args = $total_args_translator_box;
$all_cmts   = get_comments( $total_args );

/**
 * get page 1 reviews
*/
$total_args_translator_box['number'] = get_option('posts_per_page');
$comments_translator_box = get_comments( $total_args_translator_box );

$total_messages = count($all_cmts);
$comment_pages_translator_box  =   ceil( $total_messages/$total_args_translator_box['number'] );
$total_args_translator_box['total'] = $comment_pages_translator_box;

$messagedata_translator_box = array();
$message_object_translator_box = Fre_Message::get_instance();
?>
<div class="project-workplace-details">
    <div class="row">
		<div class="col-md-8">
			<div class="workplace-details-chat-box">
				<div class="message-container-chat-box">
					<?php
						if( et_load_mobile() ) {
							do_action('after_mobile_project_workspace', $post);
						}
					?>

					<div class="work-place-wrapper pd-r-15">
						<?php if($post->post_status != 'complete') { ?>
						<form class="form-group-work-place-wrapper form-message">
							<div class="form-group-work-place file-container"  id="file-container-chat-box" style="width: 90%;">
								<span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'file_et_uploader' ) ?>"></span>

								<div class="content-chat-wrapper form-content-chat-wrapper">
									<textarea name="comment_content" class="content-chat" placeholder="<?php _e('Type here to reply',ET_DOMAIN);?>"></textarea>
									<div class="submit-btn-msg" style="right: -15%;">
										<span><input type="submit" name="submit" value="<?php _e('Submit',ET_DOMAIN);?>" class="btn btn-primary"></span>
									</div>
									<input type="hidden" name="comment_post_ID" value="<?php echo $post->ID; ?>" />
								</div>
								<ul class="file-attack apply_docs_file_list" id="apply_docs_chat_box_file_list">
								</ul>
								<div id="apply_docs_chat_box_container">
									<a href="#" class="btn btn-primary attack attach-file" id="apply_docs_chat_box_browse_button"><?php _e('Add File ',ET_DOMAIN);?><i class="fa fa-plus-circle"></i></a>
								</div>
							</div>
						</form>
						<?php } ?>
						<ul class="list-chat-work-place  new-list-message-item">
							<?php
							foreach ($comments_chat_box as $key => $message) {
								$convert = $message_object_chat_box->convert($message);
								$messagedata_chat_box[] = $convert;
								$author_name = get_the_author_meta( 'display_name', $message->user_id );

								if( !empty( $convert->changed_milestone_id ) && !empty( $convert->action ) && !empty( $convert->changelog ) ) {
									// Render html for changelog
									?>
									<li class="message-item changelog-item" id="comment-<?php echo $message->comment_ID; ?>">
									   <?php
											$changelog_time = human_time_diff( strtotime($convert->comment_date), current_time( 'timestamp' ) ). ' ago';
											printf( __( '<span class="author-name">%s</span> marked <span class="">"%s"</span> as <span class="status">%s</span><span class="changelog-time" >%s</span>', ET_DOMAIN ), $convert->author_name, $convert->milestone_title, $convert->action, $changelog_time ); ?>
									</li>
									<?php
								} else {
									// Render html for message
									?>
									<li class="message-item <?php echo $message->user_id == $user_ID ? '' : 'partner-message-item' ?>" id="comment-<?php echo $message->comment_ID; ?>">
										<div class="form-group-work-place">
											<?php
												if($message->user_id != $user_ID){ ?>
													<div class="avatar-chat-wrapper">
														<a href="#" class="avatar-employer">
															<?php echo $message->avatar; ?>
														</a>
													</div>
											<?php    }  ?>
											<div class="content-chat-wrapper">
												<div class="content-chat fixed-chat">
													<div class="param-content"><?php echo $convert->comment_content; ?></div>
												<?php echo $convert->file_list; ?>
												</div>
												<div class="date-chat">
												<?php
													//echo $message->message_time;
													echo human_time_diff( strtotime($convert->comment_date), current_time( 'timestamp' ) ). __(' ago', ET_DOMAIN);
												?>
												</div>
											</div>
										</div>
									</li>
									<?php
								}
							}
							?>
						</ul>
						<div class="paginations-wrapper" >
							<?php
							if($comment_pages_chat_box > 1) {
								ae_comments_pagination( $comment_pages_chat_box, $paged ,$total_args_chat_box );
							}
							 ?>
						</div>
						<?php echo '<script type="json/data" class="postdata" > ' . json_encode($messagedata_chat_box) . '</script>'; ?>
					</div>
				</div>
			</div>
			<div class="workplace-details-translator-box">
				<div class="message-container-translator-box">
					<div class="work-place-wrapper pd-r-15">
						
						<h2><?php _e('Translation Delivery Box',ET_DOMAIN);?></h2>
						<?PHP if($user_ID != $post->post_author) : ?>
						<form class="form-group-work-place-wrapper form-message">
							<div class="form-group-work-place file-container"  id="file-container-translator-box" style="width: 90%;">
								<span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'file_et_uploader' ) ?>"></span>

								<div class="content-chat-wrapper form-content-chat-wrapper">
									<textarea name="comment_content" class="content-chat" placeholder="<?php _e('Paste your translation text, Here is only for translation delivery',ET_DOMAIN);?>"></textarea>
									<div class="submit-btn-msg" style="right: -15%;">
										<span>
											<input type="button" id="modal-confirm-href" value="<?php _e('Submit',ET_DOMAIN);?>" class="btn btn-primary">
											<input type="submit" id="modal-confirm-submit-button" name="submit" style="display: none;" value="<?php _e('Submit',ET_DOMAIN);?>" class="btn btn-primary">
										</span>
									</div>
									<input type="hidden" name="comment_post_ID" value="<?php echo $post->ID; ?>" />
								</div>
								<ul class="file-attack apply_docs_file_list" id="apply_docs_file_list">
								</ul>
								<div id="apply_docs_container">
									<a href="#" class="btn btn-primary attack attach-file" id="apply_docs_browse_button"><?php _e('Upload translated file(s) ',ET_DOMAIN);?><i class="fa fa-plus-circle"></i></a>
								</div>
							</div>
						</form>
						
						
						<div class="modal fade" id="modal-confirm" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal">
											<i class="fa fa-times"></i>
										</button>
										<h3><?php _e('Do you want to submit your file for Employer review?',ET_DOMAIN);?></h3>
									</div>
									<div class="modal-body">
										<div class="clearfix"></div>
										<button type="button" id="modal-confirm-do-submit" class="btn-sumary btn-success"><?php _e('Yes',ET_DOMAIN);?></button>
									</div>
								</div><!-- /.modal-content -->
							</div><!-- /.modal-dialog login -->
						</div>
						<script>
							jQuery(document).ready(function($) {
								$('#modal-confirm-href').click(function(e) {
									e.preventDefault();
									$('#modal-confirm').modal('show');
								});
								$('#modal-confirm-do-submit').click(function(e) {
									$('#modal-confirm-submit-button').click();
									$('#modal-confirm').modal('hide');
								});
							});
						</script>
						
						<?PHP endif; ?>
						
						<ul class="list-chat-work-place  new-list-message-item">
							<?php
							foreach ($comments_translator_box as $key => $message) {
								$convert = $message_object_translator_box->convert($message);
								$messagedata_translator_box[] = $convert;
								$author_name = get_the_author_meta( 'display_name', $message->user_id );

								if( !empty( $convert->changed_milestone_id ) && !empty( $convert->action ) && !empty( $convert->changelog ) ) {
									// Render html for changelog
									?>
									<li class="message-item changelog-item" id="comment-<?php echo $message->comment_ID; ?>">
									   <?php
											$changelog_time = human_time_diff( strtotime($convert->comment_date), current_time( 'timestamp' ) ). ' ago';
											printf( __( '<span class="author-name">%s</span> marked <span class="">"%s"</span> as <span class="status">%s</span><span class="changelog-time" >%s</span>', ET_DOMAIN ), $convert->author_name, $convert->milestone_title, $convert->action, $changelog_time ); ?>
									</li>
									<?php
								} else {
									// Render html for message
									?>
									<li class="message-item <?php echo $message->user_id == $user_ID ? '' : 'partner-message-item' ?>" id="comment-<?php echo $message->comment_ID; ?>">
										<div class="form-group-work-place">
											<?php
												if($message->user_id != $user_ID){ ?>
													<div class="avatar-chat-wrapper">
														<a href="#" class="avatar-employer">
															<?php echo $message->avatar; ?>
														</a>
													</div>
											<?php    }  ?>
											<div class="content-chat-wrapper">
												<div class="content-chat fixed-chat">
													<div class="param-content"><?php echo $convert->comment_content; ?></div>
												<?php echo $convert->file_list; ?>
												</div>
												<div class="date-chat">
												<?php
													//echo $message->message_time;
													echo human_time_diff( strtotime($convert->comment_date), current_time( 'timestamp' ) ). __(' ago', ET_DOMAIN);
												?>
												</div>
											</div>
										</div>
									</li>
									<?php
								}
							}
							?>
						</ul>
						<div class="paginations-wrapper" >
							<?php
							if($comment_pages_translator_box > 1) {
								ae_comments_pagination( $comment_pages_translator_box, $paged ,$total_args_translator_box );
							}
							 ?>
						</div>
						<?php echo '<script type="json/data" class="postdata" > ' . json_encode($messagedata_translator_box) . '</script>'; ?>
					</div>
				</div>
			</div>
		</div>
        <?php if(!et_load_mobile()) { ?>
        <div class="col-md-4 workplace-project-details">
        	<div class="content-require-project">
                <?php
                if(fre_access_workspace($post)) {
                    echo '<a style="font-weight:600;" href="'.get_permalink( $post->ID ).'">
                            <i class="fa fa-arrow-left"></i> '.__("Back To Project Page", ET_DOMAIN).
                        '</a>';
                }
                ?>
                <?php do_action('after_sidebar_single_project_workspace', $post); ?>

                <h4><?php _e('Project description:',ET_DOMAIN);?></h4>
                <?php the_content(); ?>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
