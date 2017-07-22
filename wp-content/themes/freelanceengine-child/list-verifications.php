<?php 
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get('verification');

?>
<div class="row">
	<ul class="list-item-verification">
		<?php
        $postdata = array();
        while (have_posts()) { the_post();
            $convert = $post_object->convert($post,'thumbnail');
            $postdata[] = $convert;
            get_template_part( 'template/verification', 'item' );
        }
        ?>
	</ul>

    
    <div class="col-md-4 col-sm-4 col-xs-4 list-item-verification-last add-verification-button">
        <a href="#" class="add-verification">
            <i class="fa fa-plus"></i>
            <?php _e('Add your Photo ID', ET_DOMAIN); ?>
        </a>
    </div>
</div>
	<?php
      
/**
 * render post data for js
*/
echo '<script type="data/json" class="postdata verifications-data" >'.json_encode($postdata).'</script>';
?>
<!-- pagination -->
<?php
	echo '<div class="paginations-wrapper">';
	ae_pagination($wp_query, get_query_var('paged'), 'load');
	echo '</div>';             
?>

