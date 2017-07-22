
<?php
//global $current;
 ?>

<div class="modal fade" id="modal_edit_project">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                </button>
                <h4 class="modal-title"><?php _e('Edit Project',ET_DOMAIN);?></h4>
            </div>
            <div class="modal-body">
                <div class="content">
                    <form class="post edit-project" role="form" id="frm_edit_project">
                        <!-- project title -->
                        <div class="form-group">
                            <label for="post_title" class="control-label title-plan"><?php _e("Project Title", ET_DOMAIN); ?></label>
                            <input type="text" class="input-item form-control text-field" id="post_title" placeholder="<?php _e("Project Title", ET_DOMAIN); ?>" name="post_title">
                        </div>
                        <!--// project title -->
						<div class="clearfix"></div>



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
                      <div class="edit-gallery-image" id="gallery_container">
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


                        <!-- file attachment -->
                        <!--<div class="form-group" id="gallery_place">
                            <label for="carousel_browse_button" class="control-label title-plan">
                                <?php _e("Source Attachments:", ET_DOMAIN); ?><br/>
                            </label>
                            <div class="edit-gallery-image" id="gallery_container">
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
                        </div>-->
                        <!--//file attachment -->

                        <!-- project skills -->
                        <div class="form-group skill-control">
                            <label for="skill" class="control-label title-plan">
                                <?php _e("Skills required:", ET_DOMAIN); ?>
                            </label>
                            <?php
                            $switch_skill = ae_get_option('switch_skill');
                            if(!$switch_skill){
                                ?>
                                <input class="form-control skill" type="text" id="skill" placeholder="<?php _e("Skills (max is 5)", ET_DOMAIN); ?>" name=""  autocomplete="off" class="skill" spellcheck="false" >
                                <ul class="skills-list" id="skills_list"></ul>
                                <?php
                            }else{

                                ae_tax_dropdown(    'skill' , array(  'attr' => 'data-chosen-width="100%" data-chosen-disable-search="" multiple data-placeholder="'.sprintf(__(" Skills (max is %s)", ET_DOMAIN), ae_get_option('fre_max_skill', 5)).'"',
                                                    'class' => 'sw_skill required',
                                                    'hide_empty' => false,
                                                    'hierarchical' => true ,
                                                    'id' => 'skill' ,
                                                    'show_option_all' => false
                                                )
                                );
                            }
                            ?>
                        </div>
                        <!--// project skills -->
                        <div class="clearfix"></div>

                        <!-- project category -->
                        <div class="form-group project_category">
                            <label for="project_category" class="control-label title-plan">
                                <?php _e("Category", ET_DOMAIN); ?>
                            </label>
                            <?php ae_tax_dropdown( 'project_category' ,
                                  array(  'attr' => 'data-chosen-width="500px" data-chosen-disable-search="" multiple data-placeholder="'.__("Choose categories", ET_DOMAIN).'"',
                                          'class' => 'chosen multi-tax-item tax-item required',
                                          'hide_empty' => false,
                                          'hierarchical' => true ,
                                          'id' => 'project_category' ,
                                          'show_option_all' => false
                                      )
                            ) ;?>

                        </div>
                        <!--// project category -->

                        <div class="clearfix"></div>

                        <!-- project translate Graphics -->
                        <div class="form-group">

                            <label for="post_content" class="control-label title-plan"><?php _e("Translate Graphics", ET_DOMAIN); ?></label>

                            <span class="user-type">
                                <input type="hidden" name="translategraphics" id="role" value="Yes" />
                                <input type="checkbox" class="sign-up-switch" name="translategraphics" data-switchery="true" style="display: none;">
                                <span class="user-role text hire">
                                    <?php _e("No", ET_DOMAIN); ?>
                                </span>
                            </span>
                            <input class="work-text" type="hidden" name="worktext" value="<?php _e("Yes", ET_DOMAIN); ?>">
                            <input class="hire-text" type="hidden" name="hiretext" value="<?php _e("No", ET_DOMAIN); ?>">
                            <!--<input class="translategraphics_old" type="hidden" name="translategraphics_old" >-->


                        </div>


                        <!--// project translate Graphics -->

                        <!-- project description -->
                        <div class="form-group">

                            <label for="post_content" class="control-label title-plan"><?php _e("Description", ET_DOMAIN); ?></label>

                            <?php wp_editor( '', 'post_content', ae_editor_settings()  );  ?>

                        </div>
                        <div class="clearfix"></div>

                        <?php $post = ''; do_action( 'ae_edit_post_form', PROJECT, $post ); ?>
                        <?php if(ae_get_option('gg_captcha') && !current_user_can( 'manage_options' )){ ?>
                            <div class="form-group">
                                <div class="gg-captcha">
                                    <?php ae_gg_recaptcha(); ?>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        <?php } ?>
                        <div class="form-group">
                             <button type="submit" class="btn btn-submit-login-form"><?php _e("Submit", ET_DOMAIN); ?></button>
                        </div>
                    </form>
                </div>

            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
jQuery(function($) {

var clickCheckbox = document.querySelector('.sign-up-switch'),
    //roleInput = $("input#role"),
    hire_text = $('.hire-text').val();
    work_text = $('.work-text').val();
    translategraphics = $('.translategraphics_old').val();
    view = this;
//var _this = $(event.currentTarget);
//var _switch = _this.parents('.user-type');


if ($('.sign-up-switch').length > 0) {
  if($('#signup_form, .signup_form').find('span.user-role').hasClass('hire'))
  {
      $('.sign-up-switch').parents('.user-type').find('small').css({
          "left" :  -5 + "px"
      })
  }
  clickCheckbox.onchange = function(event) {
      //console.log(view.$('.user-type span.text').text());
      var _this = $(event.currentTarget);
      var _switch = _this.parents('.user-type');
      if (clickCheckbox.checked) {
          //roleInput.val("freelancer");
          $('.user-type span.text').text(work_text).removeClass('hire').addClass('work');
          _switch.find('small').css({
              "left" :  (_switch.find('.switchery').width() - _switch.find('small').width() + 5) + "px"
          });
      } else {
          //roleInput.val("employer");
          $('.user-type span.text').text(hire_text).removeClass('work').addClass('hire');
          _switch.find('small').css({
              "left" :  -5 + "px"
          })
      }
  };
}});

</script>
