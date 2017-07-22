<?php
/**
 * the template for displaying the freelancer work (bid success a project)
 # this template is loaded in template/bid-history-list.php
 * @since 1.0
 * @package FreelanceEngine
 */
$author_id = get_query_var('author');
if(is_page_template('page-profile.php')) {
    global $user_ID;
    $author_id = $user_ID;
}

global $wp_query, $ae_post_factory, $post, $HIDE_ACTUAL_PAID_AMOUNT;

// $post_object = $ae_post_factory->get( BID );
// if(ae_user_role($author_id) == FREELANCER) {
    $post_object = $ae_post_factory->get(BID);
// }else {
//     $post_object = $ae_post_factory->get(PROJECT);
// }

$current     = $post_object->current_post;
// get commission settings
$commission = ae_get_option('commission', 0);
$commission_fee = $commission;


// caculate commission fee by percent
$commission_type = ae_get_option('commission_type');
if ($commission_type != 'currency') {
    $commission_fee = ((float)($current->bid_budget * (float)$commission)) / 100;
}
$total = $current->bid_budget - $commission_fee;

$current->bid_budget_text = fre_price_format($total);

/*if ( ! add_post_meta($result->ID, 'translategraphics', 'No',true) ) {
   update_post_meta($result->ID, 'translategraphics', 'No');
}*/
$payment_status = get_post_meta($current->ID, 'payment_status', true);

//var_dump("dsadadasda");

//var_dump($payment_status);exit;

if(!$current || !isset( $current->project_title )){
    return;
}

?>

<li class="bid-item">
    <div class="name-history">
        <a href="<?php echo get_author_posts_url( $current->post_author ); ?>">
            <span class="avatar-bid-item"><?php echo $current->project_author_avatar;?></span>
        </a>
        <div class="content-bid-item-history">
            <?php if($current->project_status == 'complete'){ ?>
                <h5><a href = "<?php echo $current->project_link; ?>"><?php echo $current->project_title; ?></a>
                    <div class="rate-it" data-score="<?php echo $current->rating_score; ?>"></div>
                </h5>
                <?php if(isset($current->project_comment)){ ?>
                <span class="comment-author-history">
                    <?php echo $current->project_comment; ?>
                </span>
                <?php } else { ?>
                <span class="stt-in-process"><?php _e('Job is closed', ET_DOMAIN);?></span>
                <?php } ?>
            <?php } else if($current->project_status == 'publish'){ ?>
                <h5>
                    <a href = "<?php echo $current->project_link; ?>"><?php echo $current->project_title; ?></a>
                </h5>
                <span class="stt-in-process"><?php _e('Job in process', ET_DOMAIN);?></span>
            <?php } else { ?>
                <h5>
                    <a href = "<?php echo $current->project_link; ?>"><?php echo $current->project_title; ?></a>
                </h5>
                <span class="stt-in-process"><?php _e('Job is closed', ET_DOMAIN);?></span>
            <?php } ?>
        </div>
    </div>
    <ul class="info-history">
        <li><?php echo $current->project_post_date; ?></li>
        <li>
            <?php _e("Project Fee", ET_DOMAIN); ?> : <span class="number-price-project-info"><?php if($HIDE_ACTUAL_PAID_AMOUNT) echo fre_price_format($current->bid_budget); else echo fre_price_format($current->bid_budget_text); ?> </span>
            <?PHP if(ae_user_role($user_ID) == "administrator" || ae_user_role($user_ID) == 'editor' || $user_ID == $author_id) { ?>
				<?php if($payment_status==0) { ?>
				<code><?php _e('UNPAID', ET_DOMAIN);?></code>
				<?php }  else { ?>
				<code style="color: green"><?php _e('PAID', ET_DOMAIN);?></code>
				<?php } ?>
            <?php } ?>
        </li>
        <!-- <li><?php _e('Earned :', ET_DOMAIN) ;  echo $current->et_budget; ?></li> -->
    </ul>
    <div class="clearfix"></div>
</li>
