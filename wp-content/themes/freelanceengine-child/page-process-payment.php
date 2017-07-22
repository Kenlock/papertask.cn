<?php
/**
 *	Template Name: Process Payment
 */
 //error_reporting(E_ALL);
 //ini_set('display_errors', 1);
$payment_type			= get_query_var( 'paymentType' );
//$payment_type =
$session	=	et_read_session ();

//process payment for alipay
$bid_id = $_GET['bid_id'];
//$bid_id = $_GET['bid_id'];
$payment_type_alipay = $_GET['payment_type'];
$is_success =  $_GET['is_success'];
//var_dump($payment_type_alipay);exit;
if($payment_type_alipay == 1 && $bid_id>0 && $is_success == "T"){
get_header();
	global $user_ID;
	$project_id = get_post_field('post_parent', $bid_id);
	$project = get_post($project_id);

	$request = $_REQUEST;
	$request['post_parent']=$bid_id;
	$request['post_title']="alipay";
	$orders = AE_Order::get_orders($request);
	//var_dump($orders);exit;
	//if($order);return;
	//$bid->post_parent
	//var_dump($project_id);
//var_dump($project);exit;
	//$result = new WP_Error($code = '200', $message = __('You don\'t have perminsion to accept this project.', ET_DOMAIN) , array());

	// check authenticate
	//if (!$user_ID) return new WP_Error($code = '200', $message == __(' You must login to accept bid.', ET_DOMAIN));

	//if ($project->post_status != 'publish') {

			// a project have to published when bidding
			//return new WP_Error($code = '200', $message = __('Your project was not pubished. You can not accept a bid!', ET_DOMAIN));
	//}

	if ((int)$project->post_author == $user_ID && !$orders->have_posts()) {

			// add accepted bid id to project meta

			update_post_meta($project->ID, 'accepted', $bid_id);

			// change project status to close so mark it to on working
			wp_update_post(array(
					'ID' => $project->ID,
					'post_status' => 'close'
			));

			// change a bid to be accepted
			wp_update_post(array(
					'ID' => $bid_id,
					'post_status' => 'accept'
			));


			$order_post = array(
					'post_type' => 'order',
					'post_status' => 'pending',
					'payment' => 'alipay',
					'post_parent' => $bid_id,
					'post_author' => $user_ID,
					'payer' => $user_ID,
					'total' => (float)get_post_meta($bid_id, 'bid_budget', true),
					'post_title' => 'Pay for accept bid',
					'post_content' => 'Pay for accept bid ' . $bid_id
			);

			$order_object =	new AE_Order($order_post);
			//var_dump($order_object);exit;

			$mail = Fre_Mailing::get_instance();
			$freelancer_id = get_post_field('post_author', $bid_id);
			$mail->bid_accepted($freelancer_id, $project->ID);


			do_action('fre_accept_bid', $bid_id);
			//return true;
	}

	//return $result;



	?>

	<section class="blog-header-container">
		<div class="container">
			<!-- blog header -->
			<div class="row">
					<div class="col-md-12 blog-classic-top">
							<h2><?php the_title(); ?></h2>
					</div>
			</div>
			<!--// blog header  -->
		</div>
	</section>
	<section id="blog-page">
	    <div class="container page-container">
			<!-- block control  -->
			<div class="row block-posts block-page">
				<div class="col-md-8 col-sm-12 col-xs-12 posts-container" id="left_content">
		            <div class="blog-content">
<?php
//	var_dump($project_id);exit;
									if( ( isset($project_id) && $project_id ) ) {

											$permalink	=	get_permalink( $project_id );
//var_dump($permalink);exit;
										/**
										 * template payment success
										*/
										get_template_part( 'template/payment' , 'success' );

											/* _e("You are now redirected to your listing page ... ",ET_DOMAIN);?>
											<br/>
											<?php _e("You are now redirected to your listing page ... ",ET_DOMAIN);?>
											<br/>
											<?php printf(__('Time left: %s', ET_DOMAIN ), '<span class="count_down">10</span>');

											echo '<a href="'.$permalink.'" >'.get_the_title( $project_id ).'</a>';*/
									} else {

											$permalink	=	home_url();

										/**
										 * template payment fail
										*/
										//get_template_part( 'template/payment' , 'fail' );
									}
?>

								</div>
									</div>
								<!-- Column left / End -->
								<div class="col-md-4 col-sm-12 col-xs-12 page-sidebar blog-sidebar" id="right_content">
								<?php get_sidebar('page'); ?>
							</div><!-- RIGHT CONTENT -->
						</div>
						</div>
				</section>
				<!-- Page Blog / End -->
				<script type="text/javascript">
						jQuery(document).ready (function () {
							var $count_down	=	jQuery('.count_down');
						setTimeout (function () {
							window.location = '<?php echo $permalink ?>';
						}, 10000 );
						setInterval (function () {
							if($count_down.length >  0) {
								var i	=	 $count_down.html();
								$count_down.html(parseInt(i) -1 );
							}
						}, 1000 );
						});
				</script>
<?php

} else {

	if ($payment_type == 'paypaladaptive' || $payment_type == 'frecredit') {
		$payment_return = fre_process_escrow($payment_type , $session );
	}else{
		$payment_return = ae_process_payment($payment_type , $session );
	}

	$ad_id		=	$session['ad_id'];

	get_header();

	global $ad , $payment_return;

	$payment_return	=	wp_parse_args( $payment_return, array('ACK' => false, 'payment_status' => '' ));
	extract( $payment_return );
	if($session['ad_id'])
		$ad	=	get_post( $session['ad_id'] );
	else
		$ad	=	false;
	?>

	<section class="blog-header-container">
		<div class="container">
			<!-- blog header -->
			<div class="row">
			    <div class="col-md-12 blog-classic-top">
			        <h2><?php the_title(); ?></h2>
			    </div>
			</div>
			<!--// blog header  -->
		</div>
	</section>

	<!-- Page Blog -->
	<section id="blog-page">
	    <div class="container page-container">
			<!-- block control  -->
			<div class="row block-posts block-page">
				<div class="col-md-8 col-sm-12 col-xs-12 posts-container" id="left_content">
		            <div class="blog-content">
						<?php
							if ( $payment_type == 'paypaladaptive' ) {
								// Process Accept Bid
								$permalink	=	get_permalink( $ad->ID );
								if( isset($ACK) && $ACK  ) {
									$permalink = add_query_arg(array('workspace' => 1), $permalink );
									$workspace = '<a href="'.$permalink.'">'.get_the_title($ad->ID).'</a>';
									printf(__( 'You have successfully sent the money. Now you can start working on project %s .' , ET_DOMAIN ), $workspace);
									/**
									 * template payment success
									*/
									// redirect to workspace

								} else {
								 	// redirect to project place
									_e( 'Accept bid fail !' , ET_DOMAIN );
									// echo $payment_return['msg'];
								}
							}else{
								if( ( isset($ACK) && $ACK ) ) {
									if($ad) :
										$permalink	=	get_permalink( $ad->ID );
									else:
										$permalink = home_url();
									endif;
									/**
									 * template payment success
									*/
									get_template_part( 'template/payment' , 'success' );
								} else {
									if($ad):
										$permalink	=	et_get_page_link('submit-project', array( 'id' => $ad->ID ));
									else :
										$permalink	=	home_url();
									endif;
									/**
									 * template payment fail
									*/
									get_template_part( 'template/payment' , 'fail' );
								}
							}

							// clear session
							et_destroy_session();
							?>
					</div>
		        </div>
			    <!-- Column left / End -->
			    <div class="col-md-4 col-sm-12 col-xs-12 page-sidebar blog-sidebar" id="right_content">
					<?php get_sidebar('page'); ?>
				</div><!-- RIGHT CONTENT -->
			</div>
	    </div>
	</section>
	<!-- Page Blog / End -->
	<script type="text/javascript">
	  	jQuery(document).ready (function () {
	  		var $count_down	=	jQuery('.count_down');
			setTimeout (function () {
				window.location = '<?php echo $permalink ?>';
			}, 10000 );
			setInterval (function () {
				if($count_down.length >  0) {
					var i	=	 $count_down.html();
					$count_down.html(parseInt(i) -1 );
				}
			}, 1000 );
	  	});
	</script>

<?php } ?>

<?php get_footer(); ?>
