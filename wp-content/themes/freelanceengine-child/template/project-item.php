<?php

global $wp_query, $ae_post_factory, $post;
$post_object    = $ae_post_factory->get( PROJECT );
$current        = $post_object->current_post;

$category = "";
$sourcelanguage = "";
$targetlanguage = "";
if(isset($current->project_category) && count($current->project_category) > 0) {
	$category = $current->tax_input['project_category'][0]->name;
}
if(isset($current->sourcelanguage) && count($current->sourcelanguage) > 0) {
	$sourcelanguage = $current->tax_input['sourcelanguage'][0]->name;
}
if(isset($current->targetlanguage) && count($current->targetlanguage) > 0) {
	$targetlanguage = $current->tax_input['targetlanguage'][0]->name;
}
?>
<li <?php post_class( 'project-item' ); ?>>
	<div class="row">
    	<div class="col-md-4 col-sm-4 col-xs-7 text-ellipsis pd-r-30">
        	<a href="<?php echo get_author_posts_url( $current->post_author ); ?>"  class="title-project">
                <?php echo get_avatar( $post->post_author, 35 ); ?>
            </a>
            <a href="<?php the_permalink();?>" title="<?php the_title(); ?>" class="project-item-title">
                <?php the_title(); ?>
            </a>

        </div>
        <div class="col-md-2 col-sm-3 hidden-xs">
            <?php
            if($current->et_featured) { ?>
                <span class="ribbon"><i class="fa fa-star"></i></span>
            <?php } ?>
            <span>
                <?php  //the_author_posts_link(); ?>
				<?php echo $sourcelanguage;?> => <?php echo $targetlanguage;?>
            </span>
        </div>
        <div class="col-md-2 col-sm-2 hidden-sm hidden-xs">
             <span>
                <?php echo get_the_date() ?>
            </span>
        </div>

        <div class="col-md-2 col-sm-3 col-xs-4 hidden-xs">
            <span class="budget-project">
                <!-- <?php echo fre_price_format($current->et_budget);?> -->
				<?php echo $category;?>
            </span>
        </div>
        <div class="col-md-2 col-sm-2 col-xs-5">
            <?php
            if( $current->current_user_bid ){ ?>
            <span class="wrapper-btn">
                <a href="<?php the_permalink();?>" class="bid-label" >
                    <i class="fa fa-check"></i><?php _e(' Bid',ET_DOMAIN);?>
                </a>
            </span>
            <?php }
            else{
            ?>
            <p class="wrapper-btn">
                <a href="<?php the_permalink();?>" class="btn-sumary btn-apply-project">
                    <?php _e('Apply',ET_DOMAIN);?>
                </a>
            </p>
            <?php  } ?>
        </div>
    </div>
</li>
