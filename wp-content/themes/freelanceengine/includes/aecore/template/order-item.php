<?php

global $post;
$order_object =	new AE_Order($post->ID);
$order_data = $order_object->get_order_data();

$products = $order_data['products'];

//$package = array_pop($products);

$post_parent = '';
$project ="";
if($post->post_parent) {
	$post_parent = get_post($post->post_parent);
	$project = get_post($post_parent->post_parent);
}
$key=$_POST['search'];
$title = get_the_title( $project->ID );
$author = get_the_author_meta('display_name',$post->post_author);
if($key){
	//var_dump($key);
	//var_dump($title);
	//var_dump($author);
	$check = strpos($author, $key);
	//var_dump($check);
	if(strpos($title, $key) === false && strpos($author, $key) === false)
	return;
}
//var_dump($post);exit;
//$authordata = get_userdata( $post->post_author );
//var_dump($authordata);exit;
$support_gateway = apply_filters('ae_support_gateway', array(
    			'cash' => __("Cash", ET_DOMAIN),
    			'alipay' => __("Alipay", ET_DOMAIN) ,
    			'paypal' => __("Paypal", ET_DOMAIN),
    			'2checkout' => __("2Checkout", ET_DOMAIN),
    		));
?>
<li>
	<div class="method">
		<?php echo isset($support_gateway[$order_data['payment']]) ? $support_gateway[$order_data['payment']] : $order_data['payment'];
			if($post->post_title == 'cash') : ?>
				<a title="<?php _e("Approve", ET_DOMAIN); ?>" class="color-green action publish" data-id="<?php echo $post->ID; ?>" href="#">
					<span class="icon" data-icon="3"></span>
				</a>
				<a title="<?php _e("Decline", ET_DOMAIN); ?>" class="color-red action decline" data-id="<?php echo $post->ID; ?>" href="#">
					<span class="icon" data-icon="*"></span>
				</a>
		<?php
			endif;
		?>
	</div>
	<div class="content">
		<?php
		if( $post ) {
			switch ($post->post_title) {
			case 'cash':
				echo '<a title="' . __("Pending", ET_DOMAIN) . '" class="color-red error" href="#"><span class="icon" data-icon="!"></span></a>';
				break;
			case 'alipay':
				echo '<a title="'. __("Confirmed", ET_DOMAIN) . '" class="color-green" href="#"><span class="icon" data-icon="2"></span></a>';
				break;
			default:
				echo '<a title="' .__("Failed", ET_DOMAIN) .'" class="color" style="color :grey;" href="#"><span class="icon" data-icon="*"></span></a>';
				break;
			}
		?>
			<span class="price font-quicksand">
				<?php echo ae_currency_sign(false) . $order_data['total']; ?>
			</span>
		<?php
			if($post_parent) { ?>
				<a target="_blank" href="<?php echo get_permalink( $post_parent->ID ) ?>" class="ad ad-name">
					<?php
						echo $title;

					?>
				</a>
			<?php }else { ?>

			<?php
			}
			 _e(' by ', ET_DOMAIN);
			?>
			<a target="_blank" href="<?php echo get_author_posts_url($post->post_author, $author_nicename = '') ?>" class="company">
				<?php echo $author ?> <?php if($post->post_title=="cash") echo("- VIP");?>
			</a>
		<?php
		} else {
			$author	=	'<a target="_blank" href="'.get_author_posts_url($post->post_author).'" class="company">' .
							$author . ($post->post_title=="")? "- VIP":"".
						'</a>';
		?>
			<span>
				<?php printf (__("This post has been deleted by %s", ET_DOMAIN) , $author ); ?>
			</span>
		<?php
			}
		?>

	</div>
</li>

<!-- this is only HTML format for VIP user cash payment or other payment -->
<!--<li>
	<div class="method">
		Cash
				<a title="Approve" class="color-green action publish" data-id="386" href="#">
					<span class="icon" data-icon="3"></span>
				</a>
				<a title="Decline" class="color-red action decline" data-id="386" href="#">
					<span class="icon" data-icon="*"></span>
				</a>
			</div>
	<div class="content">
		<a title="Pending" class="color-red error" href="#"><span class="icon" data-icon="!"></span></a>
			<span class="price font-quicksand">￥878.68</span>
			<a href="#" class="ad ad-name">"project title put here"</a>
			 by
			<a target="_blank" href="http://papertask.cn/author/admin/" class="company">"VIP employer name" - VIP</a>

	</div>
</li>
<li>
	<div class="method">
		Alipay

			</div>
	<div class="content">
		<a title="Pending" class="color-green" href="#"><span class="icon" data-icon="2"></span></a>
			<span class="price font-quicksand">￥1000.00</span>
			<a href="#" class="ad ad-name">"project title put here"</a>
			 by
			<a target="_blank" href="" class="company">"Employer's account name"</a>

	</div>
</li>-->
