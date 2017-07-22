<?php
/**
 * The Template for displaying a user profile
 *
 * @package WordPress
 * @subpackage FreelanceEngine
 * @since FreelanceEngine 1.0
 */
global $wp_query, $ae_post_factory, $post, $user_ID;
$post_object = $ae_post_factory->get(PROFILE);
$author_id 	= 	get_query_var( 'author' );

$author_name = get_the_author_meta('display_name', $author_id);
$author_available = get_user_meta($author_id, 'user_available', true);
// get user profile id
$profile_id = get_user_meta($author_id, 'user_profile_id', true);
// get post profile
$profile = get_post($profile_id);
$convert = '';

if( $profile && !is_wp_error($profile) ){    
    $convert = $post_object->convert( $profile );
}

// try to check and add profile up current user dont have profile
if(!$convert && ( fre_share_role() || ae_user_role($author_id) == FREELANCER || ae_user_role($author_id) == 'FREELANCER_VERIFIED') ) {
    $profile_post = get_posts(array('post_type' => PROFILE,'author' => $author_id));
    if(!empty($profile_post)) {
        $profile_post = $profile_post[0];
        $convert = $post_object->convert( $profile_post );
        $profile_id = $convert->ID;
        update_user_meta($author_id, 'user_profile_id', $profile_id);
    }else {
        $convert = $post_object->insert( array( 'post_status' => 'publish' , 
                                                'post_author' => $author_id , 
                                                'post_title' => $author_name , 
                                                'post_content' => '')
                                        );
        
        $convert = $post_object->convert( get_post($convert->ID) );
        $profile_id = $convert->ID;
    }

    
}
//  count author review number
$count_review = fre_count_reviews($author_id);
// $count_project = fre_count_user_posts_by_type($user_ID, PROJECT, 'publish');


$Current_user = new WP_User( $author_id );
$Current_user_email = $Current_user->data->user_email;
$ae_users     = AE_Users::get_instance();
$User_data    = $ae_users->convert($Current_user->data);
$User_role    = ae_user_role($Current_user->ID);

$Profile = array('id' => 0, 'ID' => 0);
if($profile_id) {
	$Profile_post = get_post( $profile_id );
	if($Profile_post && !is_wp_error( $Profile_post )){
		$Profile = $post_object->convert($Profile_post);
	}
}

//get profile skills
$current_skills = get_the_terms( $Profile, 'skill' );
//define variables:
$skills         = isset($Profile->tax_input['skill']) ? $Profile->tax_input['skill'] : array() ;
$job_title      = isset($Profile->et_professional_title) ? $Profile->et_professional_title : '';
$hour_rate      = isset($Profile->hour_rate) ? $Profile->hour_rate : '';
$currency       = isset($Profile->currency) ? $Profile->currency : '';
$experience     = isset($Profile->et_experience) ? $Profile->et_experience : '';
$about          = isset($Profile->post_content) ? $Profile->post_content : '';
$display_name   = $User_data->display_name;
$user_available = isset($User_data->user_available) && $User_data->user_available == "on" ? 'checked' : '';
$country        = isset($Profile->tax_input['country'][0]) ? $Profile->tax_input['country'][0]->name : '' ;
$category       = isset($Profile->tax_input['project_category'][0]) ? $Profile->tax_input['project_category'][0]->slug : '' ;

$AuthorProfile = $Profile;

$company_name    = get_user_meta($author_id, 'company_name', true);
$invoice_title   = get_user_meta($author_id, 'invoice_title', true);
$mailing_address = get_user_meta($author_id, 'mailing_address', true);


// Handle email change requests
$user_meta = get_user_meta($author_id, 'adminhash', true);



$next_post = false;
if($convert) {
    $next_post = ae_get_adjacent_post($convert->ID, false, '', true, 'skill');    
}

$PAGE_PORTFOLIO = true;
$HIDE_ACTUAL_PAID_AMOUNT = true;
get_header();
?>
	<section class="breadcrumb-wrapper">
		<div class="breadcrumb-single-site">
        	<div class="container">
    			<div class="row">
                	<div class="col-md-6 col-xs-8">
                    	<ol class="breadcrumb">
                            <li><a href="<?php echo home_url(); ?>"><?php _e("Home", ET_DOMAIN); ?></a></li>
                            <li class="active"><?php printf(__("Profile of %s", ET_DOMAIN), $author_name); ?></li>
                        </ol>
                    </div>

                    <?php /* if($next_post) { ?>
                        <div class="col-md-6 col-xs-4">
                        	<a title="<?php the_author_meta('display_name', $next_post->post_author) ?>" href="<?php echo get_author_posts_url($next_post->post_author);  ?>" class="prj-next-link"><?php _e('Next Profile', ET_DOMAIN);?> <i class="fa fa-angle-double-right"></i></a>
                        </div>
                    <?php }*/ ?>
                </div>
            </div>
        </div>
	</section>
	<?PHP if(isset($_GET['edit']) && (ae_user_role($user_ID) == "administrator" || ae_user_role($user_ID) == 'editor') ) : ?>
	<div class="number-profile-wrapper">
    	<div class="container">
        	<div class="row">
            	<div class="col-md-12">
                    <div class="nav-tabs-profile">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs responsive" role="tablist" id="myTab">
                            <li class="active">
                                <a href="#tab_account_details" role="tab" data-toggle="tab">
                                    <?php _e('Account Details', ET_DOMAIN) ?>
                                </a>
                            </li>
                            <?php if(fre_share_role() || ae_user_role($author_id) == FREELANCER || ae_user_role($author_id) == 'FREELANCER_VERIFIED'){ ?>
                            <li>
                                <a href="#tab_profile_details" role="tab" data-toggle="tab">
                                    <?php _e('Profile Details', ET_DOMAIN) ?>
                                </a>
                            </li>
                            <?php } ?>
                            <?php do_action('fre_profile_tabs'); ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<?PHP endif; ?>
    <div class="single-profile-wrapper list-profile-wrapper">
    	<div class="container">
        	<div class="row">
            	<div class="col-md-8">
					<?PHP if(isset($_GET['edit']) && (ae_user_role($user_ID) == "administrator" || ae_user_role($user_ID) == 'editor') ) : ?>
					<div class="tab-content-profile">
						<!-- Tab panes -->
                        <div class="tab-content block-profiles responsive">
                            <!-- Tab account details -->
                            <div class="tab-pane fade in active" id="tab_account_details">
                                <div class="row">
                                    <div class="avatar-profile-page col-md-3 col-xs-12" id="user_avatar_container">
                                        <span class="img-avatar image" id="user_avatar_thumbnail">
                                            <?php echo get_avatar($User_data->ID, 125) ?>
                                        </span>
                                        <a href="#" id="user_avatar_browse_button">
                                            <?php _e('Change', ET_DOMAIN) ?>
                                        </a>
                                        <span class="current_user_id hidden" id="<?php echo $author_id; ?>">
                                        <span class="et_ajaxnonce hidden" id="<?php echo de_create_nonce( 'user_avatar_et_uploader' ); ?>">
                                        </span>
                                    </div>
                                    <div class="info-profile-page col-md-9 col-xs-12">
                                        <form class="form-info-basic" id="account_form">
                                            <div class="form-group">
                                            	<div class="form-group-control">
                                                    <label><?php _e('Full Name', ET_DOMAIN) ?></label>
                                                    <input type="text" class="form-control" id="display_name" name="display_name" value="<?php echo $User_data->display_name ?>" placeholder="<?php _e('Enter Full Name', ET_DOMAIN) ?>">
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="form-group">
                                                <div class="form-group-control">
                                                    <label><?php _e('Email Address', ET_DOMAIN) ?></label>
                                                    <input type="email" class="form-control" id="user_email" name="user_email" value="<?php echo $Current_user_email; ?>" placeholder="<?php _e('Enter email', ET_DOMAIN) ?>">
                                                </div>
                                                <?php /*
												    if(!empty($user_meta['newemail'])){
                                                        printf( __( '<p>There is a pending change of the email to %1$s. <a href="%2$s">Cancel</a></p>', ET_DOMAIN ),
                                                                    '<code>' . esc_html( $user_meta['newemail'] ) . '</code>',
                                                                        esc_url( et_get_page_link("profile").'?dismiss=new_email' )
                                                                );
                                                    }*/
                                                ?>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="form-group">
                                                <div class="form-group-control">
                                                    <div class="form-group-control">
                                                        <label><?php _e('Mobile Phone', ET_DOMAIN) ?></label>
                                                        <input type="number" class="form-control" id="phone" name="phone" value="<?php echo $User_data->phone?>" placeholder="<?php _e('Enter phone', ET_DOMAIN) ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="form-group">
                                                <div class="form-group-control">
                                                    <div class="form-group-control">
                                                        <label><?php _e('Address', ET_DOMAIN) ?></label>
                                                        <input type="text" class="form-control" id="location" name="location" value="<?php echo $User_data->location ?>" placeholder="<?php _e('Enter address', ET_DOMAIN) ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                            <?PHP if(ae_user_role($author_id) == EMPLOYER || ae_user_role($author_id) == EMPLOYER_VIP) : ?>
											<div class="form-group">
                                                <div class="form-group-control">
                                                    <div class="form-group-control">
                                                        <label><?php _e('Company Name', ET_DOMAIN) ?></label>
                                                        <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo $company_name ?>" placeholder="<?php _e('Company Name', ET_DOMAIN) ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
											<div class="form-group">
                                                <div class="form-group-control">
                                                    <div class="form-group-control">
                                                        <label><?php _e('Invoice Title', ET_DOMAIN) ?></label>
                                                        <input type="text" class="form-control" id="invoice_title" name="invoice_title" value="<?php echo $invoice_title ?>" placeholder="<?php _e('Invoice Title', ET_DOMAIN) ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
											<div class="form-group">
                                                <div class="form-group-control">
                                                    <div class="form-group-control">
                                                        <label><?php _e('Mailing Address', ET_DOMAIN) ?></label>
                                                        <input type="text" class="form-control" id="mailing_address" name="mailing_address" value="<?php echo $mailing_address ?>" placeholder="<?php _e('Mailing Address', ET_DOMAIN) ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
											<?PHP endif; ?>
                                            <?php if(ae_get_option('use_escrow', false)) {
                                                    do_action( 'ae_escrow_recipient_field');
                                                } ?>
                                            <?php if( ae_get_option('pay_to_bid', false) ){ ?>
                                            <div class="form-group">
                                                <div class="form-group-control">
                                                    <label>
                                                        <?php _e('Bid Credit: ', ET_DOMAIN);  ?>
                                                        <?php echo get_user_credit_number( $author_id ) ; ?>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                            <?php } ?>
                                            <div class="form-group">
                                                <input type="hidden" name="current_user" value="<?PHP echo $author_id; ?>">
												<input type="submit" class="btn-submit btn-sumary" name="" value="<?php _e('Save Details', ET_DOMAIN) ?>">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!--// END ACCOUNT DETAILS -->
                            <!-- Tab profile details -->
                            <?php if(fre_share_role() || $User_role == FREELANCER || $User_role == 'FREELANCER_VERIFIED') { ?>
                            <div class="tab-pane fade" id="tab_profile_details">
                            	<div class="detail-profile-page">
                                    <?php if(isset($_GET['loginfirst']) && $_GET['loginfirst'] == 'true'){ ?>
                                        <div class="notice-first-login">
                                            <p><?php _e('<i class="fa fa-warning"></i>You must complete your profile to do any activities on site', ET_DOMAIN);?></p>
                                        </div>
                                    <?php } ?>
                                	<form class="form-detail-profile-page validateNumVal" id="profile_form">
                                        <div class="form-group">
                                        	<div class="form-group-control">
                                                <label><?php _e('Professional Title', ET_DOMAIN) ?></label>
                                                <input required class="input-item form-control text-field"  type="text" name="et_professional_title"
                                                <?php   if($job_title){
                                                            echo "value= $job_title ";
                                                        } ?>
                                                       placeholder="<?php _e("e.g: Wordpress Developer", ET_DOMAIN) ?>">
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <?php do_action( 'ae_edit_post_form', PROFILE, $Profile ); ?>
										<script>
										jQuery(document).ready(function($) {
											<?PHP
											if(get_the_terms( $profile, 'sourcelanguage' )) {
												echo "$('select#sourcelanguage').prepend('<option>选择源语言</option>');";
                                                echo "$('select#targetlanguage').prepend('<option>选择目标语言</option>');";
											}
											else {
												echo "$('select#sourcelanguage').prepend('<option selected>选择源语言</option>');";
                                                echo "$('select#targetlanguage').prepend('<option selected>选择目标语言</option>');";
											}
											?>
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
                                        <div class="clearfix"></div>

                                    	<div class="form-group">
                                        	<div class="form-group-control">
                                                <label><?php _e('Translation rate (per source word)', ET_DOMAIN) ?></label>
                                                <div class="row">
                                                    <div class="col-xs-8">
                                                        <input required class="input-item form-control text-field"  type="text" name="hour_rate"
                                                               <?php   if($hour_rate){
                                                            echo "value= $hour_rate ";
                                                        }?>  placeholder="<?php _e('e.g:30.22', ET_DOMAIN) ?>">
                                                    </div>
                                                    <div class="col-xs-4">
                                                        <span class="profile-exp-year">
                                                        <?php $currency = ae_get_option('content_currency');
                                                        if($currency){
                                                            echo $currency['code'];
                                                        }else{
                                                            _e('USD', ET_DOMAIN);
                                                        } ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="clearfix"></div>
                                        <div class="form-group">
                                            <div class="profile-category">
                                                <label><?php _e('Category', ET_DOMAIN) ?></label>
                                                <?php
                                                    $cate_arr = array();
                                                    if(!empty($Profile->tax_input['project_category'])){
                                                        foreach ($Profile->tax_input['project_category'] as $key => $value) {
                                                            $cate_arr[] = $value->term_id;
                                                        };
                                                    }
                                                    ae_tax_dropdown( 'project_category' ,
                                                          array(
                                                                'attr'            => 'data-chosen-width="100%" multiple="multiple" data-chosen-disable-search="" data-placeholder="'.__("Choose categories", ET_DOMAIN).'"',
                                                                'class'           => 'chosen-select multi-tax-item tax-item required cat_profile',
                                                                'hide_empty'      => false,
                                                                'hierarchical'    => true ,
                                                                'id'              => 'project_category' ,
                                                                'selected'        => $cate_arr,
                                                                'show_option_all' => false
                                                              )
                                                    );
                                                ?>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
										
										<?php if(fre_share_role() || $User_role == FREELANCER || $User_role == FREELANCER_VERIFIED){ ?>
										<div class="form-group-mobile">
											<label class="et-receive-mail" for="et_receive_mail"><input type="checkbox" id="et_receive_mail" name="et_receive_mail_check" <?php echo (isset($Profile->et_receive_mail) &&$Profile->et_receive_mail == '1') ? 'checked': '' ;?>/><?php _e("Receive emails about projects that match your categories", ET_DOMAIN) ?></label>
											<input type="hidden" value="<?php echo (isset($Profile->et_receive_mail)) ? $Profile->et_receive_mail : '';?>" id="et_receive_mail_value" name="et_receive_mail" />
										</div>
										<?php } ?>
										
                                        <div class="clearfix"></div>
                                        <div class="form-group">
                                            <div class="skill-profile-control form-group-control">
                                            	<label><?php _e('Your Skills', ET_DOMAIN) ?></label>
                                                <?php
                                                $switch_skill = ae_get_option('switch_skill');
                                                if(!$switch_skill){
                                                    ?>
                                                    <input class="form-control skill" type="text" id="skill" placeholder="Your Skills" name="skill"  autocomplete="off" spellcheck="false" >
                                                    <ul class="skills-list" id="skills_list"></ul>
                                                    <?php
                                                }else{
                                                    $c_skills = array();
                                                    if(!empty($current_skills)){
                                                        foreach ($current_skills as $key => $value) {
                                                            $c_skills[] = $value->term_id;
                                                        };
                                                    }
                                                    ae_tax_dropdown( 'skill' , array(  'attr' => 'data-chosen-width="100%" required data-chosen-disable-search="" multiple data-placeholder="'.sprintf(__(" Skills (max is %s)", ET_DOMAIN), ae_get_option('fre_max_skill', 5)).'"',
                                                                        'class' => 'sw_skill required',
                                                                        'hide_empty' => false,
                                                                        'hierarchical' => true ,
                                                                        'id' => 'skill' ,
                                                                        'show_option_all' => false,
                                                                        'selected' =>$c_skills
                                                                        )
                                                    );
                                                }

                                                ?>
												<?php /*
												$c_skills = array();
												if(!empty($current_skills)){
													foreach ($current_skills as $key => $value) {
														$c_skills[] = $value->term_id;
													};
												}
												ae_tax_dropdown( 'skill' , array(  'attr' => 'data-chosen-width="100%" required data-chosen-disable-search="" multiple data-placeholder="'.sprintf(__(" Skills (max is %s)", ET_DOMAIN), ae_get_option('fre_max_skill', 5)).'"',
																	'class' => 'chosen-select sw_skill required',
																	'hide_empty' => false,
																	'hierarchical' => true ,
																	'id' => 'skill' ,
																	'show_option_all' => false,
																	'selected' =>$c_skills
																	)
												);*/
                                                ?>
                                            </div>
                                        </div>

                                        <div class="clearfix"></div>
                                        <div class="form-group">
                                            <div class="form-group-control">
                                                <label><?php _e('Your Country', ET_DOMAIN) ?></label>
                                                <?php if(!ae_get_option('switch_country')){ ?>
                                                        <input required class="input-item form-control text-field" type="text" id="country" placeholder="<?php _e("Country", ET_DOMAIN); ?>" name="country"
                                                               <?php if($country){ echo "value=$country"; } ?> autocomplete="off" class="country" spellcheck="false" >
                                                <?php }else{
                                                        $country_arr = array();
                                                        if(!empty($Profile->tax_input['country'])){
                                                            foreach ($Profile->tax_input['country'] as $key => $value) {
                                                                $country_arr[] = $value->term_id;
                                                            };
                                                        }
                                                        ae_tax_dropdown( 'country' ,
                                                            array(
                                                                'attr'            => 'data-chosen-width="100%" data-chosen-disable-search="" data-placeholder="'.__("Choose country", ET_DOMAIN).'"',
                                                                'class'           => 'chosen multi-tax-item tax-item required country_profile',
                                                                'hide_empty'      => false,
                                                                'hierarchical'    => true ,
                                                                'value'           => 'slug',
                                                                'id'              => 'country' ,
                                                                'selected'        => $country_arr,
                                                                'show_option_all' => false
                                                            )
                                                        );
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="form-group">
                                        	<div class="experience">
                                                <label><?php _e('Your Experience', ET_DOMAIN) ?></label>
                                                <div class="row">
                                                    <div class="col-md-12 fix-width">
                                                        <input class="form-control col-xs-3 number is_number numberVal" step="5" min="1" type="number" name="et_experience" value="<?php echo $experience; ?>" />
                                                        <span class="col-xs-3 profile-exp-year"><?php _e("year(s)", ET_DOMAIN); ?></span>
                                                    </div>
                                                    <div class="col-xs-3">

                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                        <div class="clearfix"></div>
                                        <div class="form-group about-you">
                                        	<div class="form-group-control row-about-you">
                                                <label><?php _e('About you', ET_DOMAIN) ?></label>
                                                <div class="clearfix"></div>
												<div class="hidden"><?php wp_editor( '', '_content', ae_editor_settings()  );  ?></div>
                                                <textarea class="form-control" name="post_content" id="about_content" cols="30" rows="5"><?php echo esc_textarea(trim($about)) ?></textarea>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <!--// project description -->

                                        <!-- Your Portfolio  -->
                                        <div class="form-group portfolios-wrapper">
                                            <div class="form-group-control">
                                                <label><?php _e('Portfolio', ET_DOMAIN) ?></label>
                                                <style>
													.author .add-porfolio-button {
														display: inline-block !important;
													}
												</style>
												<div class="edit-portfolio-container">
                                                    <?php
                                                    // list portfolio
                                                    query_posts( array(
                                                        'post_status' => 'publish',
                                                        'post_type'   => 'portfolio',
                                                        'author'      => $Current_user->ID,
                                                        'posts_per_page' => -1
                                                    ));
                                                    get_template_part( 'list', 'portfolios' );
                                                    wp_reset_query();
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                       <!-- //Your Portfolio  -->

                                        <!-- Your ID information  -->
                                        <div class="form-group verifications-wrapper" style="width: 100%;">
                                            <div class="form-group-control">
                                                <label><?php _e('ID photo', ET_DOMAIN) ?></label>
												<style>
													.author .add-verification-button {
														display: inline-block !important;
													}
												</style>
                                                <div class="edit-verification-container">
                                                    <?php
                                                    // list verification
                                                    query_posts( array(
                                                        'post_status' => 'publish',
                                                        'post_type'   => 'verification',
                                                        'author'      => $Current_user->ID,
                                                        'posts_per_page' => -1
                                                    ));
                                                    get_template_part( 'list', 'verifications' );
                                                    wp_reset_query();
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                       <!-- //Your ID information  -->

                                        <div class="clearfix"></div>
                                        <div class="form-group">
											<input type="hidden" name="current_profile_id" value="<?PHP echo $profile_id; ?>">
                                        	<input type="hidden" name="current_user" value="<?PHP echo $author_id; ?>">
											<input type="submit" class="btn-submit btn-sumary" name="" value="<?php _e('Save Details', ET_DOMAIN) ?>">
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <?php } ?>
                            <!--// END PROFILE DETAILS -->
                            <?php do_action('fre_profile_tab_content');?>
                            <!--// END PROJECT DETAILS -->
                            <!--End show model-->
                            <!--// END TABS CREDITS-->
                        </div>
					</div>
					<?PHP else : ?>
                	<div class="tab-content-single-profile">
                    	<!-- Title -->
                    	<div class="row title-tab-profile">
                            <div class="col-md-12">
                                <h2><?php printf(__('ABOUT %s', ET_DOMAIN), strtoupper($author_name) ); ?></h2>
                            </div>
                        </div>
                        <!-- Title / End -->
                        <!-- Content project -->
                        <div class="single-profile-content">
                        	<div class="single-profile-top">
                                <ul class="single-profile">
                                    <li class="img-avatar"><span class="avatar-profile"><?php echo get_avatar($author_id, 70); ?></span></li>
                                    
                                    <li class="info-profile">
                                        <span class="name-profile"><?php echo $author_name; ?></span>
                                    <?php if($convert) { ?>
                                        <span class="position-profile"><?php echo $convert->et_professional_title; ?></span>
                                    <?php } ?> 

                                        <span class="number-review-profile"><?php if($count_review < 2) printf(__('%d review', ET_DOMAIN), $count_review ); else printf(__('%d reviews', ET_DOMAIN), $count_review );?></span>
                                    </li>
                                    
                                </ul>  
                                <?php if($convert && (fre_share_role() || ae_user_role($author_id) == FREELANCER || ae_user_role($author_id) == 'FREELANCER_VERIFIED' ) ){ ?>
                                <div class="list-skill-profile">
                                    <ul>
                                    <?php if(isset($convert->tax_input['skill']) && $convert->tax_input['skill']){
                                            foreach ($convert->tax_input['skill'] as $tax){ ?>	
                                                <li><span class="skill-name-profile"><?php echo $tax->name; ?></span></li>
                                    <?php 	}
                                          } ?>        	
                                    </ul>
                                </div>
                                <?php } ?>
                                <div class="clearfix"></div>
                            </div>
                            <div class="single-profile-bottom">
                                <?php if($convert) { ?>
                                <!-- overview -->
                                <div class="profile-overview">
                                	<h4 class="title-single-profile"><?php _e('Overview', ET_DOMAIN);?></h4>
                                    <p><?php echo $convert->post_content; ?></p>
                                    <?php 
                                    if(function_exists('et_the_field')) {
                                        et_render_custom_meta($convert);
                                        et_render_custom_taxonomy($convert);
                                    }
                                    ?>
                                </div>
                                <!--// overview -->
                                <?php }  
                                
                                if(ae_user_role($author_id) != FREELANCER && ae_user_role($author_id) != 'FREELANCER_VERIFIED' ){
                                    get_template_part('template/work', 'history');
                                } 
                                if( fre_share_role() || ae_user_role($author_id) == FREELANCER || ae_user_role($author_id) == 'FREELANCER_VERIFIED' ){ 
                                    get_template_part('template/bid', 'history');
                                    $bid_posts   = $wp_query->found_posts;
                                ?>
                                    <div class="portfolio-container">
                                    <?php 
                                        query_posts( array( 
                                                        // 'post_parent' => $convert->ID, 
                                                        'post_status' => 'publish', 
                                                        'post_type' => PORTFOLIO, 
                                                        'author' => $author_id ) 
                                                    );
                                        if(have_posts()):
                                            get_template_part('template/portfolios', 'filter' );   
                                            // list portfolio
                                            get_template_part( 'list', 'portfolios' );     
                                        else :
                                        endif;
                                        //wp_reset_postdata();
                                        wp_reset_query();
                                    ?>
                                    </div>
                                <?php } else { ?>
									<div class="portfolio-container">
                                    <?php 
                                        query_posts( array( 
                                                        // 'post_parent' => $convert->ID, 
                                                        'post_status' => 'publish', 
                                                        'post_type' => PORTFOLIO, 
                                                        'author' => $author_id ) 
                                                    );
                                        if(have_posts()):
                                            get_template_part('template/portfolios', 'filter' );   
                                            // list portfolio
                                            get_template_part( 'list', 'portfolios' );
                                        else :
                                        endif;
                                        //wp_reset_postdata();
                                        wp_reset_query();
                                    ?>
                                    </div>
                                <?php } ?>
								
								<?PHP if(ae_user_role($user_ID) == "administrator" || ae_user_role($user_ID) == 'editor' ||
										 $user_ID == $author_id) : ?>
								<!-- Your ID information  -->
								<div class="portfolio-container">
									<h4 class="title-count-portfolio">Photo ID</h4>
									<?php
									// list verification
									query_posts( array(
										'post_status' => 'publish',
										'post_type'   => 'verification',
										'author'      => $Current_user->ID,
										'posts_per_page' => -1
									));
									get_template_part( 'list', 'verifications' );
									wp_reset_query();
									?>
								</div>
								<!-- //Your ID information  -->
								<?PHP endif; ?>
								
							</div>
                        </div>
                        <!-- Content project / End -->
                        <div class="clearfix"></div>
                    </div>
					<?PHP endif; ?>
                </div>
                <div class="col-md-4">
                    <!-- Title -->
                    <div class="row title-tab-profile">
                        <div class="col-md-12">
                            <h2><?php _e('INFO', ET_DOMAIN);?></h2>
                        </div>
                    </div>
                    <div class="single-profile-content">
						<?PHP if ( ae_user_role($user_ID) == "administrator" || ae_user_role($user_ID) == 'editor' || 
								   ae_user_role($author_id) == FREELANCER || ae_user_role($author_id) == 'FREELANCER_VERIFIED') : ?>
						<div class="contact-link">
                        <?php
                        if ( (ae_user_role($user_ID) == "administrator" || ae_user_role($user_ID) == 'editor') ) {
							if(isset($_GET['edit']) && $_GET['edit'] === 'true') {
						?>
								<a href="?view=true" class="invite-freelancer btn btn-success pull-right">
									<?php _e("View Profile", ET_DOMAIN) ?>
								</a> 
						<?PHP
							}
							else {
						?>
								<a href="?edit=true" class="invite-freelancer btn btn-success pull-right">
									<?php _e("Edit Profile", ET_DOMAIN) ?>
								</a> 
						<?PHP
							}
						}
						
						if ( ae_user_role($author_id) == FREELANCER || ae_user_role($author_id) == 'FREELANCER_VERIFIED' ){
                         if($author_available == 'on' || $author_available == '' ){ ?>
                                <a href="#" data-toggle="modal" class="invite-freelancer btn-sumary <?php if ( is_user_logged_in() ) { echo 'invite-open';}else{ echo 'login-btn';} ?>"  data-user="<?php echo $convert->post_author ?>">
                                    <?php _e("Invite me to join", ET_DOMAIN) ?>
                                </a> 
                                <?php /*
                                <span><?php _e("Or", ET_DOMAIN); ?></span>
                                <a href="#" class="<?php if ( is_user_logged_in() ) {echo 'contact-me';} else{ echo 'login-btn';} ?> fre-contact"  data-user="<?php echo $convert->post_author ?>" data-user="<?php echo $convert->post_author ?>">
                                    <?php _e("Contact me", ET_DOMAIN) ?>
                                </a>
                                */
                                ?>
                            
                        <?php } else {
                                echo '<h3 style="padding: 20px 25px;margin:0;">'.$author_name .'</h3>';
                            }
                            $rating = Fre_Review::freelancer_rating_score($author_id);
                        }
                        ?> 
						</div>
						<?PHP endif; ?>
                    </div>
                    <?php if( ae_user_role($author_id) == FREELANCER || ae_user_role($author_id) == 'FREELANCER_VERIFIED' ){?>
                        <!-- Title / End -->
                        <!-- Content project -->
                        <div class="single-profile-content">
                            <ul class="list-detail-info">
                            	<li>
                                    <i class="fa fa-dollar"></i>
                                    <span class="text"><?php _e('Rate (per word):',ET_DOMAIN);?></span>
                                    <span class="text-right"><?php echo $convert->hourly_rate_price;  ?></span>
                                </li>
                                <li>
                                	<i class="fa fa-star"></i>
                                    <span class="text"><?php _e('Rating:',ET_DOMAIN);?></span>
                                	<div class="rate-it" data-score="<?php echo $rating['rating_score']; ?>"></div>
                                </li>
                                <li>
                                    <i class="fa fa-pagelines"></i>
                                    <span class="text"><?php _e('Experience:',ET_DOMAIN);?></span>
                                    <span class="text-right"><?php echo $convert->experience; ?></span>
                                </li>
                                <li>
                                    <i class="fa fa-briefcase"></i>
                                    <span class="text"><?php _e('Projects worked:',ET_DOMAIN);?></span>
                                    <span class="text-right"><?php echo $bid_posts; ?></span>
                                </li>
                                <?PHP if(ae_user_role($user_ID) == "administrator" || ae_user_role($user_ID) == 'editor' || $user_ID == $author_id) { ?>
                                <li>
                                    <i class="fa fa-money"></i>
                                    <span class="text"><?php _e('Total earned:',ET_DOMAIN);?></span>
                                    <span class="text-right"><?php echo fre_price_format(get_user_meta($author_id, 'total_earned', true)); ?></span>
                                </li>
								<?PHP } ?>
                                <li>
                                    <i class="fa fa-map-marker"></i>
                                    <span class="text"><?php _e('Country:',ET_DOMAIN);?></span>
                                    <span class="text-right">
                                        <?php 
                                        if($convert->tax_input['country']){ 
                                            echo $convert->tax_input['country']['0']->name;
                                        } ?>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    <?php }else{ ?>
                        <div class="info-company-wrapper">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php fre_display_user_info( $author_id ); ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <!-- Content project / End -->
                </div>
            </div>
        </div>
    </div>

<!-- CURRENT PROFILE -->
<?php if($profile_id && $Profile_post && !is_wp_error( $Profile_post )){ ?>
<script type="data/json" id="current_profile">
    <?php echo json_encode($AuthorProfile) ?>
</script>
<?php } ?>
<!-- END / CURRENT PROFILE -->

<!-- CURRENT SKILLS -->
<?php if( !empty($current_skills) ){ ?>
<script type="data/json" id="current_skills">
    <?php echo json_encode($current_skills) ?>
</script>
<?php } ?>
<!-- END / CURRENT SKILLS -->
	
<?php
get_footer();