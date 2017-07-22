<?php
/**
 * The template for displaying project description, comment, taxonomy and custom fields
 * @since 1.0
 * @package FreelanceEngine
 * @category Template
 */
global $wp_query, $ae_post_factory, $post, $user_ID;
$post_object    = $ae_post_factory->get(PROJECT);
$convert = $project = $post_object->current_post;
$role = ae_user_role();
//var_dump($role);
//var_dump((int)$project->post_author);
//var_dump($user_ID);
//exit;

?>

<style type="text/css">
    .single-projects .content-require-skill-project .workspace-link {
        margin-bottom: 10px;
    }    
</style>

<div class="info-project-item-details">
    <div class="row">
        <div class="col-md-8">
            <div class="content-require-project">
                <h4><?php _e('Project Description:',ET_DOMAIN);?></h4>
                <?php the_content(); ?>

			<?php
			 $sourcetext=get_post_meta ( $post->ID, $key = 'source_text', $single = false );

      // $role = ae_user_role();
       //var_dump($role);exit;
       //if( $user_ID == $post && ($role=='employer' || $role=='employer-vip')

       if( $role ==  "administrator" ||  ( $user_ID > 0 && $role == "FREELANCER_VERIFIED" ) || ( $user_ID > 0  && ( (int)$project->post_author == $user_ID)  && ($role == "employer" || $role == "EMPLOYER_VIP")))
			 if($sourcetext && $sourcetext[0]!='')
			 {
				echo "<h4>需要翻译的文字：</h4>".$sourcetext[0];
			 }
			 else
			 {
			 // list project attachment
                $attachment = get_children( array(
                        'numberposts' => -1,
                        'order' => 'ASC',
                        'post_parent' => $post->ID,
                        'post_type' => 'attachment'
                      ), OBJECT );
                if(!empty($attachment)) {
                    echo '<h4>'. __("Source Attachments:", ET_DOMAIN) .'</h3>';
                    echo '<ul class="list-file-attack-report">';
                    foreach ($attachment as $key => $att) {
                        $file_type = wp_check_filetype($att->post_title, array('jpg' => 'image/jpeg',
                                                                                'jpeg' => 'image/jpeg',
                                                                                'gif' => 'image/gif',
                                                                                'png' => 'image/png',
                                                                                'bmp' => 'image/bmp'
                                                                            )
                                                    );
                        $class="text-ellipsis";

                        if(isset($file_type['ext']) && $file_type['ext']) $class="image-gallery text-ellipsis";
                        echo '<li>
                                <a class="'.$class.'" target="_blank" href="'.$att->guid.'"><i class="fa fa-paperclip"></i>'.$att->post_title.'</a>
                            </li>';
                    }
                    echo '</ul>';
                }
				}
			?>
			</div>
            <?php if(!ae_get_option('disable_project_comment')) { ?>
            <div class="comments" id="project_comment">
                <?php comments_template('/comments.php', true)?>


			</div>
            <?php } ?>


        </div>
        <div class="col-md-4">
            <div class="content-require-skill-project" style="padding-top: 10px;">
                <?PHP
                    do_action('after_sidebar_single_project', $project);
                ?>
                <hr />
                <?php

                    do_action('before_sidebar_single_project', $project);

    				if(function_exists('et_render_custom_field')) {
                        et_render_custom_field($project);
                    }

                    list_tax_of_project( get_the_ID(), __('Skills required:',ET_DOMAIN), 'skill' );
                    list_tax_of_project( get_the_ID(), __('Category:',ET_DOMAIN)  );
    	        ?>


                <div class="custom-field-wrapper" style="margin-bottom:4px !important;">
                    <span class="ae-field-title delivertime-title"><?php _e('Deliver Time:',ET_DOMAIN);?></span>
                    <div class="list-require-skill-project list-taxonomires list-skill">
                        <ul>
                            <li>
                                <span class="delivertime-title"><?php $delivertime=get_post_meta ( $post->ID, $key = 'delivertime', $single = false ); echo $delivertime[0];?></span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="custom-field-wrapper" style="margin-bottom:4px !important;">
                    <span class="ae-field-title delivertime-title"><?php _e('Translate Graphics:',ET_DOMAIN);?></span>
                    <div class="list-require-skill-project list-taxonomires list-skill">
                        <ul>
                            <li>
                                <span class="delivertime-title"><?php $trnslatgr=get_post_meta ( $post->ID, $key = 'translategraphics', $single = false );
                                    if(@$trnslatgr[0]=='Yes')
                                    {
                                    	echo '是';
                                    }
                                    else
                                    {
                                    echo '否';
                                    }
                                    ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
                <br>
            </div>
        </div>
    </div>
</div>
<style>

.delivertime-wrapper {
    display: none !important;
}
.list-require-skill-project{
	margin-bottom: 0px !important;
}

</style>
