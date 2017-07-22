<?php
    global $user_ID;
    $step = 3;

    $disable_plan = ae_get_option('disable_plan', false);
    if($disable_plan) $step--;
    if($user_ID) $step--;
    $post = '';
    if(isset($_REQUEST['id'])) {
        $post = get_post($_REQUEST['id']);
        if($post) {
            global $ae_post_factory;
            $post_object = $ae_post_factory->get($post->post_type);
            echo '<script type="data/json"  id="edit_postdata">'. json_encode($post_object->convert($post)) .'</script>';
        }

    }
    //$current_skills = get_the_terms( $profile, 'skill' );
?>
<div class="step-wrapper step-post" id="step-post">
    <a href="#" class="step-heading active">
        <span class="number-step"><?php if($step > 1 ) echo $step; else echo '<i class="fa fa-rocket"></i>'; ?></span>
        <span class="text-heading-step"><?php _e("Enter your project details", ET_DOMAIN); ?></span>
        <i class="fa fa-caret-right"></i>
    </a>
    <div class="step-content-wrapper content" style="<?php if($step != 1) echo "display:none;" ?>" >
        <form class="post" role="form" class="validateNumVal">
            <!-- project title -->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label for="post_title" class="control-label title-plan">
                            <?php _e("Project Title", ET_DOMAIN); ?>
                            <br/>
                            <span><?php _e("Enter a short title for your project", ET_DOMAIN); ?></span>
                        </label>
                    </div>
                    <div class="col-md-8 col-sm-12">
                        <input type="text" class="input-item form-control text-field" id="post_title" placeholder="<?php _e("Project Title", ET_DOMAIN); ?>" name="post_title">
                    </div>
                </div>
            </div>
            <!--// project title -->

            <!-- Source file or Source text -->
            <div class="form-group">

                <div class="row">
                    <div class="col-md-12">
                        <div class="nav-tabs-profile">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs responsive" role="tablist" id="myTab">
                                <li class="active">
                                    <a href="#tab_source_files" role="tab" data-toggle="tab">
                                        <?php _e('Source Files', ET_DOMAIN) ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="#tab_source_text" role="tab" data-toggle="tab">
                                        <?php _e('Source Text', ET_DOMAIN) ?>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>

                <br><br>

                <div class="tab-content">
                    
                    <div class="tab-pane fade in active" id="tab_source_files">
                        <div class="row" id="gallery_place">
                            <div class="col-md-12">
                                <label for="carousel_browse_button" class="control-label title-plan">
                                    
                                    <span>
                                    <?php _e("File extension: Gif, Jpeg, Jpg, Png, Doc, Docx, Ppt, Pptx, Chm, Xls, Xlsx, Txt, Zip, Rar etc.", ET_DOMAIN); ?>
                                    </span>
                                </label>
                            </div>
                            <br><br>
                            <div class="edit-gallery-image col-md-12" id="gallery_container">
                               <ul class="gallery-image carousel-list" id="image-list">
                                    <li>
                                        <div class="plupload_buttons" id="carousel_container">
                                            <span class="img-gallery" id="carousel_browse_button">
                                                <a href="#" class="add-img"><?php _e("Attach file", ET_DOMAIN); ?> <i class="fa fa-plus"></i></a>
                                            </span>
                                        </div>
                                    </li>
                                </ul>
                                <span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'ad_carousels_et_uploader' ); ?>"></span>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab_source_text">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="post_content" class="control-label title-plan">
                                    
                                    <span><?php _e("Copy and Past the text you want to translate.", ET_DOMAIN); ?></span>
                                </label>
                            </div>
                            <br><br>
                            <div class="col-sm-12">
                                <?php wp_editor( '', 'source_text', ae_editor_settings()  );  ?>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <!--// Source file or Source text -->

            <!-- project category -->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label for="project_category" class="control-label title-plan"><?php _e("Category", ET_DOMAIN); ?><br/><span><?php _e(" Select the most appropriate one(s)", ET_DOMAIN); ?></span></label>
                    </div>

                    <div class="col-md-8 col-sm-12">
                       <?php ae_tax_dropdown( 'project_category' ,
                              array(  'attr' => 'data-chosen-width="100%" data-chosen-disable-search="" multiple data-placeholder="'.__("Choose categories", ET_DOMAIN).'"',
                                      'class' => 'chosen multi-tax-item tax-item required',
                                      'hide_empty' => false,
                                      'hierarchical' => true ,
                                      'id' => 'project_category' ,
                                      'show_option_all' => false,
                                  )
                        ) ;?>
                    </div>
                </div>
            </div>
            <!--// project category -->            

            <!-- project skills -->
            <div class="form-group skill-control">
                <div class="row">
                    <div class="col-md-4">
                        <label for="skill" class="control-label title-plan"><?php _e("Fields", ET_DOMAIN); ?>
                            <br/>
                            <span><?php _e("Press Select Document(s) Fields", ET_DOMAIN); ?></span>
                        </label>
                    </div>
                    <div class="col-md-8 col-sm-12">

                        <?php
                        $switch_skill = ae_get_option('switch_skill');
                        if(!$switch_skill){
                            ?>
                            <input type="text" class="form-control text-field skill" id="skill" placeholder="<?php _e("Skills", ET_DOMAIN); ?>" name=""  autocomplete="off" spellcheck="false" >
                            <ul class="skills-list" id="skills_list"></ul>
                            <?php
                        }else{
                            ae_tax_dropdown( 'skill' , array(  'attr' => 'data-chosen-width="100%" data-chosen-disable-search="" multiple data-placeholder="'.sprintf(__(" Fields (max is %s)", ET_DOMAIN), ae_get_option('fre_max_skill', 5)).'"',
                                                'class' => 'sw_skill chosen multi-tax-item tax-item required',
                                                'hide_empty' => false,
                                                'hierarchical' => true ,
                                                'id' => 'skill' ,
                                                'show_option_all' => false
                                        )
                            );
                        }

                        ?>
                    </div>
                </div>
            </div>
            <!--// project skills -->

            <?php do_action( 'ae_submit_post_form', PROJECT, $post ); ?>
            <script>
		jQuery(document).ready(function($) {
			$('select#targetlanguage').prepend('<option value="" selected><?php _e("Select Target Language", ET_DOMAIN); ?></option>');
			$('select#sourcelanguage').prepend('<option value="" selected><?php _e("Select Source Language", ET_DOMAIN); ?></option>');
			$('select#sourcelanguage').trigger("chosen:updated");
			$('select#targetlanguage').trigger("chosen:updated");
			
			$('select#targetlanguage').chosen().change( function() {
				var selectedValue = $(this).find('option:selected').text();
				$('select#sourcelanguage option').prop('disabled', false);
				
				$('select#sourcelanguage option').each(function(index, object) {
					if($(this).find('option:selected').val() != "" && $(this).text() == selectedValue) {
						$(this).prop('disabled', true);
					}
				});
				
				$('select#sourcelanguage').trigger("chosen:updated");
			});
			$('select#sourcelanguage').chosen().change( function() {
				var selectedValue = $(this).find('option:selected').text();
				$('select#targetlanguage option').prop('disabled', false);
				
				$('select#targetlanguage option').each(function(index, object) {
					if($(this).find('option:selected').val() != "" && $(this).text() == selectedValue) {
						$(this).prop('disabled', true);
					}
				});
				
				$('select#targetlanguage').trigger("chosen:updated");
			});
		});
	    </script>

            <!-- project translation graphic or not -->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label class="control-label title-plan" for="translategraphics">
                            <?php _e("Translate Graphics", ET_DOMAIN); ?><br>
                            <span><p><?php _e("Translate Graphics", ET_DOMAIN); ?></p></span>
                        </label>
                    </div>
                    <div class="col-sm-8">
                        <div class="col-sm-8">
                            <span class="user-type">
                                <!--<input type="hidden" name="translategraphics" id="role" value="Yes" />-->
                                <input type="checkbox" class="sign-up-switch" name="translategraphics" data-switchery="true" style="display: none;">
                                <span class="user-role text hire">
                                    <?php _e("No", ET_DOMAIN); ?>
                                </span>
                            </span>
                            <input class="work-text" type="hidden" name="worktext" value="<?php _e("Yes", ET_DOMAIN); ?>">
                            <input class="hire-text" type="hidden" name="hiretext" value="<?php _e("No", ET_DOMAIN); ?>">
                        </div>
                    </div>
                </div>           
            </div>
            <!--// project translation graphic or not -->

            <!-- project description -->
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4">
                        <label for="post_content" class="control-label title-plan">
                            <?php _e("Description", ET_DOMAIN); ?>
                            <br />
                            <span><?php _e("Describe your project in a few paragraphs", ET_DOMAIN); ?></span>
                        </label>
                    </div>

                    <div class="col-md-8 col-sm-12">
                        <?php wp_editor( '', 'post_content', ae_editor_settings()  );  ?>
                    </div>
                </div>
            </div>
            <!--// project description -->
            

            <!-- Captcha -->
            <?php if(ae_get_option('gg_captcha')){ ?>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-sm-8">
                            <div class="gg-captcha">
                                <?php ae_gg_recaptcha(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4"></div>
                    <div class="col-sm-8">
                        <button type="submit" class="btn btn-submit-login-form"><?php _e("Submit", ET_DOMAIN); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Step 3 / End -->