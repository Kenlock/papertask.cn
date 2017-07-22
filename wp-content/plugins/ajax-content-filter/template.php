<?php 
global $post;
$args = array( 
	'post_type' => 'ajax_filter', 
	'posts_per_page' => '-1', 
	'post_status' => 'publish',
	'orderby' => 'date',
	'order'	=> 'asc'
);
$all_acf_posts = get_posts( $args );

$html = '<select class="chosen chosen-single chosen-select tax-item" data-chosen-disable-search data-chosen-width="50%" style="display: none;" onchange="change_content(this.value)">';

$html .= '<option value="0">选择翻译语言查看建议价格</option>';
if ($all_acf_posts){
	foreach($all_acf_posts as $single_acf_post){
		$html .= '<option value="'.$single_acf_post->ID.'">'.$single_acf_post->post_title.'</option>';
	}
}
$html .= '<select>';
echo $html;
?>
<div id="loadcontent_here"></div>
