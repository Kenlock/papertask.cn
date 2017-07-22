<?php
    global $wp_query, $ae_post_factory, $post, $user_ID;

    $post_object      = $ae_post_factory->get(PROFILE);
    $author_id        = get_query_var( 'author' );
    $author_name      = get_the_author_meta('display_name', $author_id);
    $author_available = get_user_meta($author_id, 'user_available', true);
    // get user profile id
    $profile_id       = get_user_meta($author_id, 'user_profile_id', true);
    // get post profile
    $profile          = get_post($profile_id);
    $convert          = '';

    if( $profile && !is_wp_error($profile) ){
        $convert = $post_object->convert( $profile );
    }

    $user       = get_userdata( $author_id );
    $ae_users   = AE_Users::get_instance();
    $user_data  = $ae_users->convert($user);
    $user_role  = ae_user_role( $author_id );

    // try to check and add profile up current user dont have profile
    if(!$convert && ($user_role == FREELANCER || $user_role == 'FREELANCER_VERIFIED')) {

        $profile_post = get_posts(array('post_type' => PROFILE,'author' => $author_id));
        if(!empty($profile_post)) {

            $profile_post = $profile_post[0];
            $convert      = $post_object->convert( $profile_post );
            $profile_id   = $convert->ID;
            update_user_meta($author_id, 'user_profile_id', $profile_id);

        } else {

            $convert = $post_object->insert( array(
                'post_status'  => 'publish' ,
                'post_author'  => $author_id ,
                'post_title'   => $author_name ,
                'post_content' => ''
                )
            );

            $convert    = $post_object->convert( get_post($convert->ID) );
            $profile_id = $convert->ID;
        }

    }

    //  count author review number
    $count_review  = fre_count_reviews($author_id);
    $count_project = fre_count_user_posts_by_type($user_ID, PROJECT, 'publish');
	
	et_get_mobile_header();
	
	
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


$company_name    = get_user_meta($author_id, 'company_name', true);
$invoice_title   = get_user_meta($author_id, 'invoice_title', true);
$mailing_address = get_user_meta($author_id, 'mailing_address', true);


// Handle email change requests
$user_meta = get_user_meta($author_id, 'adminhash', true);
	
	$PAGE_PORTFOLIO = true;
?>
<section class="section section-single-profile">
	<div class="single-profiles-top">
        <div class="avatar-proflie">
            <?php echo get_avatar( $author_id, 48 ); ?>
        </div><!-- / avatar-proflie -->
        <div class="user-proflie">
            <div class="profile-infor">
                <span class="name">
                    <?php echo $author_name ?>
                </span>
                <span class="position">
                <?php if($user_role == FREELANCER || $user_role == FREELANCER_VERIFIED) {
                    echo $profile->et_professional_title;
                }else{
                    echo $user_data->location;
                } ?>
                </span>
            </div>
             <?php
				if ( (ae_user_role($user_ID) == "administrator" || ae_user_role($user_ID) == 'editor') ) {
					if(isset($_GET['edit']) && $_GET['edit'] === 'true') {
				?>
						<a href="?view=true" class="invite-freelancer btn btn-success pull-right" style="margin-left: 20px;">
							<?php _e("View Profile", ET_DOMAIN) ?>
						</a> 
				<?PHP
					}
					else {
				?>
						<a href="?edit=true" class="invite-freelancer btn btn-success pull-right" style="margin-left: 20px;">
							<?php _e("Edit Profile", ET_DOMAIN) ?>
						</a> 
				<?PHP
					}
				}
			 ?>
			 <?php if($author_available == 'on' || $author_available == '' ){ ?>
                <div class="contact-link btn-warpper-bid">
                    <a href="#" data-toggle="modal" class="btn-bid invite-freelancer btn-sumary <?php if ( is_user_logged_in() ) { echo 'invite-open';}else{ echo 'login-btn';} ?>"  data-user="<?php echo $convert->post_author ?>">
                        <?php _e("Invite me to join", ET_DOMAIN) ?>
                    </a>
                    <?php /*
                    <span><?php _e("Or", ET_DOMAIN); ?></span>
                    <a href="#" class="<?php if ( is_user_logged_in() ) {echo 'contact-me';} else{ echo 'login-btn';} ?> fre-contact"  data-user="<?php echo $convert->post_author ?>" data-user="<?php echo $convert->post_author ?>">
                        <?php _e("Contact me", ET_DOMAIN) ?>
                    </a>
                    */
                    ?>
                </div>
            <?php } ?>
        </div><!-- / user-proflie -->
        <div class="clearfix"></div>
        <?php if(fre_share_role() || $user_role == FREELANCER || $user_role == FREELANCER_VERIFIED) { ?>
            <ul class="list-skill">
                <?php
                    if(isset($convert->tax_input['skill']) && $convert->tax_input['skill']){
                        foreach ($convert->tax_input['skill'] as $tax){
                ?>
            	<li>
                    <a href="#">
                        <span class="skill-name"><?php echo $tax->name; ?></span>
                    </a>
                </li>
                <?php
                        }
                    }
                ?>
            </ul>
        <?php } ?>
    </div>
	<?PHP if(isset($_GET['edit']) && (ae_user_role($user_ID) == "administrator" || ae_user_role($user_ID) == 'editor') ) : ?>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	<section class="section-wrapper section-user-profile list-profile-wrapper">
		<div class="tabs-acc-details tab-profile mobile-tab-profile" id="tab_account" style="display:block">
			<div class="user-profile-avatar" id="user_avatar_container">
				<span class="image" id="user_avatar_thumbnail">
					<?php echo get_avatar( $User_data->ID, 90 ); ?>
				</span>
				<a href="#" class="icon-edit-profile-user edit-avatar-user" id="user_avatar_browse_button">
					<i class="fa fa-pencil"></i>
				</a>
				<span class="current_user_id hidden" id="<?php echo $author_id; ?>">
				<span class="et_ajaxnonce hidden" id="<?php echo de_create_nonce( 'user_avatar_et_uploader' ); ?>"></span>
			</div>
			<form class="form-mobile-wrapper form-user-profile" id="account_form">
				<div class="form-group-mobile">
					<label><?php _e("Your Fullname", ET_DOMAIN) ?></label>
					<!-- <a href="#" class="icon-edit-profile-user edit-info-user"><i class="fa fa-pencil"></i></a> -->
					<input type="text" id="display_name" name="display_name" value="<?php echo $User_data->display_name ?>" placeholder="<?php _e("Full name", ET_DOMAIN); ?>">
				</div>
				<div class="form-group-mobile">
					<label><?php _e("Location", ET_DOMAIN) ?></label>
					<input type="text" id="location" name="location" value="<?php echo $User_data->location ?>" placeholder="<?php _e("Location", ET_DOMAIN); ?>">
				</div>
				<div class="form-group-mobile">
					<label><?php _e("Mobile Phone", ET_DOMAIN) ?></label>
					<input type="text" id="phone" name="phone" value="<?php echo $User_data->phone?>" placeholder="<?php _e('Mobile Phone', ET_DOMAIN) ?>">
				</div>
				<div class="form-group-mobile">
					<label><?php _e("Email Address", ET_DOMAIN) ?></label>
					<input type="text" id="user_email" value="<?php echo $Current_user_email ?>" name="user_email" placeholder="<?php _e("Email", ET_DOMAIN); ?>">
				</div>
				<?PHP if(ae_user_role($author_id) == EMPLOYER || ae_user_role($author_id) == EMPLOYER_VIP) : ?>
				<div class="form-group-mobile">
					<label><?php _e('Company Name', ET_DOMAIN) ?></label>
					<input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo $company_name ?>" placeholder="<?php _e('Company Name', ET_DOMAIN) ?>">
				</div>
				<div class="clearfix"></div>
				<div class="form-group-mobile">
					<label><?php _e('Invoice Title', ET_DOMAIN) ?></label>
					<input type="text" class="form-control" id="invoice_title" name="invoice_title" value="<?php echo $invoice_title ?>" placeholder="<?php _e('Invoice Title', ET_DOMAIN) ?>">
				</div>
				<div class="clearfix"></div>
				<div class="form-group-mobile">
					<label><?php _e('Mailing Address', ET_DOMAIN) ?></label>
					<input type="text" class="form-control" id="mailing_address" name="mailing_address" value="<?php echo $mailing_address ?>" placeholder="<?php _e('Mailing Address', ET_DOMAIN) ?>">
				</div>
				<div class="clearfix"></div>
				<?PHP endif; ?>
				<?php if(ae_get_option('use_escrow', false)) {
					do_action( 'ae_escrow_recipient_field');
				} ?>
				<?php if( ae_get_option('pay_to_bid', false) ){ ?>
				 <div class="form-group-mobile">
				   <label>
						<?php _e('Credit number: ', ET_DOMAIN);  ?>
						<?php echo get_user_credit_number( $Current_user->ID ) ; ?>
					</label>
				</div>
				<?php } ?>
				<p class="btn-warpper-bid">
					<input type="hidden" name="current_user" value="<?PHP echo $author_id; ?>">
					<input type="submit" class="btn-submit btn-sumary btn-bid" value="<?php _e("Update", ET_DOMAIN) ?>" />
				</p>
			</form>
		</div>
		<!-- Tab profile details -->
		<?php if(fre_share_role() || ae_user_role($Current_user->ID) == FREELANCER || ae_user_role($Current_user->ID) == FREELANCER_VERIFIED) { ?>
		<div class="tabs-profile-details tab-profile mobile-tab-profile collapse" id="tab_profile" style="display:block">
			<?php if(isset($_GET['loginfirst']) && $_GET['loginfirst'] == 'true'){ ?>
				<div class="notice-first-login">
					<p><?php _e('<i class="fa fa-warning"></i> You must complete your profile to do any activities on site', ET_DOMAIN);?></p>
				</div>
			<?php } ?>
			<form class="form-mobile-wrapper form-user-profile" id="profile_form">
				<div class="form-group-mobile edit-profile-title">
					<label><?php _e("Your Professional Title", ET_DOMAIN) ?></label>
					<!-- <a href="#" class="icon-edit-profile-user edit-info-user"><i class="fa fa-pencil"></i></a> -->
					<input type="text" id="et_professional_title" value="<?php echo $job_title; ?>" name="et_professional_title" placeholder="<?php _e("Title", ET_DOMAIN); ?>">
				</div>
				<div class="form-group-mobile">
					<div class="hourly-rate-form">
						<label><?php _e("Your Hourly Rate", ET_DOMAIN) ?></label>
						<!-- <a href="#" class="icon-edit-profile-user edit-info-user"><i class="fa fa-pencil"></i></a> -->

						<div class="group_profile_tan">
							<input type="text" id="hour_rate" name="hour_rate" value="<?php echo $hour_rate ?>" placeholder="<?php _e("e.g:30", ET_DOMAIN); ?>">
							<?php
							$currency = ae_get_option('content_currency');
							if($currency){
								?>
								<span class="currency-tan"><?php echo $currency['code']; ?></span>
							<?php } else { ?>
								<span class="currency-tan"><?php _e('USD', ET_DOMAIN); ?></span>
							<?php } ?>
						</div>
					</div>

					<div class="clearfix"></div>
				</div>
				<div class="form-group-mobile skill-profile-control">

					<?php
					$switch_skill = ae_get_option('switch_skill');
					if(!$switch_skill){
						?>
						<div class="wrapper-skill">
							<label><?php _e("Skills", ET_DOMAIN) ?></label>
							<a href="#" class="btn-sumary btn-add-skill add-skill"><?php _e("Add", ET_DOMAIN) ?></a>
							<input type="text" id="skill" class="skill" placeholder="<?php _e("Skills", ET_DOMAIN); ?>">
						</div>
						<div class="clearfix"></div>
						<ul class="list-skill skills-list" id="skills_list"></ul>
						<?php
					}else{
						?>
						<div class="wrapper-skill">
							<label><?php _e("Skills", ET_DOMAIN) ?></label>
						</div>
						<?php
						$c_skills = array();
						if(!empty($current_skills)){
							foreach ($current_skills as $key => $value) {
								$c_skills[] = $value->term_id;
							};
						}
						ae_tax_dropdown( 'skill' , array(  'attr' => 'data-chosen-width="95%" data-chosen-disable-search="" multiple data-placeholder="'.sprintf(__("Skills (max is %s)", ET_DOMAIN), ae_get_option('fre_max_skill', 5)).'"',
											'class' => 'experience-form chosen multi-tax-item tax-item required',
											'hide_empty' => false,
											'hierarchical' => true ,
											'id' => 'skill' ,
											'show_option_all' => false,
											'selected' =>$c_skills
											)
						);
					}
					?>
				</div>
				<div class="form-group-mobile">
					<label><?php _e("Category", ET_DOMAIN) ?></label>
					<?php
						$cate_arr = array();
						if(!empty($Profile->tax_input['project_category'])){
							foreach ($Profile->tax_input['project_category'] as $key => $value) {
								$cate_arr[] = $value->term_id;
							};
						}
						ae_tax_dropdown( 'project_category' ,
							  array(
									'attr'            => 'data-chosen-width="95%" multiple data-chosen-disable-search="" data-placeholder="'.__("Choose categories", ET_DOMAIN).'"',
									'class'           => 'experience-form chosen multi-tax-item tax-item required',
									'hide_empty'      => false,
									'hierarchical'    => true ,
									'id'              => 'project_category' ,
									'selected'        => $cate_arr,
									'show_option_all' => false
								  )
						);
					?>
				</div>
				<?php if(fre_share_role() || $user_role == FREELANCER || ae_user_role($current_user->ID) == FREELANCER_VERIFIED){ ?>
				<div class="form-group-mobile">
					<label class="et-receive-mail" for="et_receive_mail"><input type="checkbox" id="et_receive_mail" name="et_receive_mail_check" <?php echo (isset($Profile->et_receive_mail) &&$Profile->et_receive_mail == '1') ? 'checked': '' ;?>/><?php _e("Receive emails about projects that match your categories", ET_DOMAIN) ?></label>
					<input type="hidden" value="<?php echo (isset($Profile->et_receive_mail)) ? $Profile->et_receive_mail : '';?>" id="et_receive_mail_value" name="et_receive_mail" />
				</div>
				<?php } ?>
				<div class="form-group-mobile">
					<label><?php _e("Country", ET_DOMAIN) ?></label>
					<!-- <a href="#" class="icon-edit-profile-user edit-info-user"><i class="fa fa-pencil"></i></a> -->
					<?php if(!ae_get_option('switch_country')){ ?>
						<input class="" type="text" id="country" placeholder="<?php _e("Country", ET_DOMAIN); ?>" name="country" value="<?php if($country){echo $country;} ?>" autocomplete="off" class="country" spellcheck="false" >
					<?php }else{ 
							$country_arr = array();
							if(!empty($Profile->tax_input['country'])){
								foreach ($Profile->tax_input['country'] as $key => $value) {
									$country_arr[] = $value->term_id;
								};
							}
							ae_tax_dropdown( 'country' ,
								array(
									'attr'            => 'data-chosen-width="100%" multiple data-chosen-disable-search="" data-placeholder="'.__("Choose country", ET_DOMAIN).'"',
									'class'           => 'experience-form chosen multi-tax-item tax-item required country_profile',
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
				<div class="form-group-mobile about-form">
					<label><?php _e("About You", ET_DOMAIN) ?></label>
					<!-- <a href="#" class="icon-edit-profile-user edit-info-user"><i class="fa fa-pencil"></i></a> -->
					<textarea name="post_content" id="post_content" placeholder="<?php _e("About", ET_DOMAIN); ?>" rows="7"><?php echo trim(strip_tags($about)) ?></textarea>
				</div>
				<div class="form-group-mobile">
					<label><?php _e("Experience", ET_DOMAIN) ?></label>
					<!-- <a href="#" class="icon-edit-profile-user edit-info-user"><i class="fa fa-pencil"></i></a> -->
					<input type="text" name="et_experience" value="<?php echo $experience; ?>" />
				</div>
				<div class="form-group-mobile">
					<?php do_action( 'ae_edit_post_form', PROFILE, $Profile ); ?>
				</div>
				<p class="btn-warpper-bid tantan">
					<input type="hidden" name="current_profile_id" value="<?PHP echo $profile_id; ?>">
					<input type="hidden" name="current_user" value="<?PHP echo $author_id; ?>">
					<input type="submit" class="btn-submit btn-sumary btn-bid" value="<?php _e("Update", ET_DOMAIN) ?>" />
				</p>
			</form>
			<div class="form-group-mobile tantan">
				<label><?php _e("Portfolio", ET_DOMAIN) ?></label>
				<div class="edit-portfolio-container">
					<?php
						// list portfolio
						query_posts( array(
							'post_status' => 'publish',
							'post_type'   => 'portfolio',
							'author'      => $Current_user->ID
						));
						get_template_part( 'mobile/list', 'portfolios' );
						wp_reset_query();
					?>
				</div>
			</div>
			<!-- Your ID information  -->
			<div class="form-group-mobile tantan">
				<label><?php _e("Photo ID", ET_DOMAIN) ?></label>
				<div class="edit-verification-container">
					<?php
						// list verification
						query_posts( array(
							'post_status' => 'publish',
							'post_type'   => 'verification',
							'author'      => $Current_user->ID
						));
						get_template_part( 'mobile/list', 'verifications' );
						wp_reset_query();
					?>
				</div>
			</div>
			<!-- //Your ID information  -->
		</div>
		<?php } ?>
	</section>
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	<?PHP else : ?>
    <?php if( fre_share_role() || $user_role == FREELANCER || $user_role == FREELANCER_VERIFIED) {
        $bid_posts = fre_count_user_posts($author_id, BID);
     ?>
    <!-- freelancer info -->
    <div class="info-bid-wrapper">
        <ul class="bid-top">
            <li>
                <span class="number">
                    <?php echo $convert->hourly_rate_price; ?>
                </span>
                <?php _e('Hourly Rate', ET_DOMAIN) ?>
            </li>
            <li>
                <span class="number">
                    <div class="rate-it" data-score="<?php echo $convert->rating_score ; ?>"></div>
                </span>
                <?php _e('Rating', ET_DOMAIN) ?>
            </li>
            <li>
                <span class="number">
                    <?php echo $convert->experience; ?>
                </span>
                <?php _e('Experience', ET_DOMAIN) ?>
            </li>
        </ul>

        <div class="clearfix"></div>
        <div class="line-mid"></div>
        <div class="clearfix"></div>

        <ul class="bid-top">
            <li>
                <span class="number"><?php echo $bid_posts; ?></span>
                <?php _e('Projects Worked', ET_DOMAIN) ?>
            </li>
            <li>
                <span class="number"><?php echo fre_price_format(get_user_meta($author_id, 'total_earned', true)); ?></span>
                <?php _e('Total earned', ET_DOMAIN) ?>
            </li>
            <li>
                <span class="number"><?php if($convert->tax_input['country']){ echo $convert->tax_input['country']['0']->name;} ?></span>
                <?php _e('Location', ET_DOMAIN) ?>
            </li>
        </ul>

        <div class="clearfix"></div>
    </div>
    <!--// freelancer info -->
    <?php } else { ?>
        <!-- employer info  !-->
        <div class="info-bid-wrapper">
            <ul class="bid-top">
                <li>
                    <span class="number"><?php echo fre_count_user_posts_by_type($author_id,'project','"publish","complete","close" ', true); ?></span>
                    <?php _e("Project posted", ET_DOMAIN); ?>
                </li>
                <li>
                    <span class="number"><?php echo   fre_price_format(fre_count_total_user_spent($author_id));;?></span>
                    <?php _e('Total spent ', ET_DOMAIN) ?>
                </li>
                <li>
                    <span class="number">
                        <?php echo fre_count_user_posts_by_type($author_id,'project', 'complete');?>
                    </span>
                    <?php _e('Hired', ET_DOMAIN) ?>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>

        <!-- end employer info !-->
    <?php } ?>
    <!-- Author overview -->
    <div class="content-project-wrapper">
   		<h2 class="title-content">
            <?php _e('Overview:', ET_DOMAIN) ?>
        </h2>
        <?php
            echo $convert->post_content;
            if(function_exists('et_render_custom_field')) {
                et_render_custom_field($convert);
            }
        ?>
    </div>

    <div class="history-cmt-wrapper">
        <?php
        if(fre_share_role() || $user_role == FREELANCER || $user_role == FREELANCER_VERIFIED) {
            get_template_part('mobile/template/bid', 'history');
        }
        if(fre_share_role() || $user_role != FREELANCER || $user_role != FREELANCER_VERIFIED ) {
            get_template_part('mobile/template/work', 'history');
        }
         ?>
    </div>
	<?PHP endif; ?>
</section>
<?php
	et_get_mobile_footer();
?>