<?php
///**
// * Plugin Name: My Plugin
// * Plugin URI: http://myplugin.dev/
// * Description: My Plugin Show Related Posts
// * Version: 1.0
// * Author: Stefan Djindjic
// * Author URI: http://myplugin.dev/
// *
// */


if (!function_exists('my_plugin_set_ajax_url')) {
	/**
	 * load myplugin ajax functionality
	 *
	 */
	function my_plugin_set_ajax_url()
	{
		echo '<script type="application/javascript">var myPluginAjaxUrl = "' . admin_url('admin-ajax.php') . '"</script>';
	}

	add_action('wp_enqueue_scripts', 'my_plugin_set_ajax_url');

}

if (!function_exists('my_plugin_enqueue_scripts')) {

	function my_plugin_enqueue_scripts()
	{
		wp_enqueue_script('my_plugin_script', plugins_url('myplugin/js/myplugin.js'), array('jquery'), false, true);
	}

	add_action('wp_enqueue_scripts', 'my_plugin_enqueue_scripts');

}


if (!function_exists('my_plugin_enqueue_styles')) {

	function my_plugin_enqueue_styles()
	{

		wp_enqueue_style('my_plugin_style', plugins_url('myplugin/myplugin.css'));

	}

	add_action('wp_enqueue_scripts', 'my_plugin_enqueue_styles');

}


if (!function_exists('add_button_on_single_post')) {

	function add_button_on_single_post($content)
	{
		if (is_single()) {
			$html = '<div class="load-more-button">';
			$html .= '<a class="my-button">';
			$html .= 'See Related Articles';
			$html .= '</a>';
			$html .= '</div>';
			$html .= $content;

			return $html;

		} else {
			return $content;
		}
	}

	add_filter('the_content', 'add_button_on_single_post');

}


if (!function_exists('my_plugin_ajax_function')) {
	function my_plugin_ajax_function()
	{
		$post_id = '';
		$html = '';

		if (!empty($_POST['PostID'])) {
			$post_id = $_POST['PostID'];
		}
		$posts = '';
		if ($post_id !== '') {

			$cats = get_the_category($post_id);


			if (is_array($cats) && count($cats)) {
				$cat_id = $cats[0]->term_id;
			} else {
				$cat_id = $cats->term_id;
			}

			$args = array(
				'posts_per_page' => 3,
				'exclude' => $post_id,
				'category' => $cat_id
			);

			$posts = get_posts($args);

		}
		if (is_array($posts) && count($posts)) {
			$html = '<div class="related-posts-holder">';

			foreach ($posts as $post) {
				$html .= '<div class="related-post">';

				if (has_post_thumbnail($post->ID)) {
					$html .= '<div class="related-post-image">';
					$html .= get_the_post_thumbnail($post->ID, "medium");
					$html .= '</div>';
				}

				$html .= '<div class = "related-post-title">';
				$html .= '<h4>';
				$html .= '<a href="' . get_permalink($post->ID) . '">';
				$html .= get_the_title($post->ID);
				$html .= '</a>';
				$html .= '</h4>';
				$html .= '</div>'; //closed  related-post-title

				$curent_content = substr($post->post_content, 0, 149);
				$html .= '<div class = "related-post-content">';
				$html .= $curent_content;

				// if it is more than 150 characters add this '...' on the end of content
				if (strlen($post->post_content) > 150) {
					$html .= '...';
				}
				$html .= '</div>';
				$html .= '</div>'; // closed related-post
			}

			$html .= '</div>'; // closed related-posts-holder
		}

		$return_obj = array(
			'html' => $html
		);


		echo json_encode($return_obj);
		exit;
	}

	add_action('wp_ajax_nopriv_my_plugin_ajax_function', 'my_plugin_ajax_function');
	add_action('wp_ajax_my_plugin_ajax_function', 'my_plugin_ajax_function');
}

