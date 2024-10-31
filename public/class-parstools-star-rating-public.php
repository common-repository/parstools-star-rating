<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://parstools.com/
 * @since      1.0.0
 *
 * @package    Parstools_Star_Rating
 * @subpackage Parstools_Star_Rating/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Parstools_Star_Rating
 * @subpackage Parstools_Star_Rating/public
 * @author     Parstools <parsgateco@gmail.com>
 */
class Parstools_Star_Rating_Public
{
	private $options = array();
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $options)
	{
		$this->options = $options;
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_shortcode('prw',array($this, 'shortcode'));
	}
	
	public function shortcode($args)
	{
		$options = $this->options;
		extract($options);
		
		if($position != "manual")
			return;
		/*
		if(!in_the_loop())
			return;
		*/
		$defaults = array(
			'id' => get_the_ID()
		);
		
		$args = shortcode_atts($defaults, $args);
		
		$id = (int) $args["id"];
		
		$code = $this->prepare_code($id);
		return $code;
	}
	private function clean_title($title)
	{
		$title = str_ireplace("'","",$title);
		$title = str_ireplace('"','',$title);
		$title = str_ireplace("<",'',$title);
		$title = str_ireplace(">",'',$title);
		$title = str_ireplace("=",'',$title);
		
		
		$title = str_ireplace("/",'',$title);
		$title = str_ireplace("\\",'',$title);
		return $title;
	}
	
	public function add_script_to_footer()
	{
		?>
		<!--Parstools Voting Widget-->
		<script type="text/javascript">
		;!(function(e, d) {
			d.write('<scr'+'ipt type="text/javascript" src="http://code.parstools.com/async/rate/rate.js" async></scri' + 'pt>');})(window, document);</script>
		<!--Parstools Voting Widget-->
	<?php
	}
	
	public function add_rate_widget_to_content($content)
	{
		if(is_preview() || is_admin())
			return $content;
		
		$code = $this->prepare_code(get_the_ID());
		
		$options = $this->options;
		extract($options);
		
		
		if($position == "before_content")
		{
			$content = $code . $content;
		}
		else if($position == "after_content")
		{
			$content = $content . $code;
		}
		
		return $content;
	}
	private function prepare_code($post_id = 0)
	{
		$options = $this->options;
		extract($options);
		
		if(!$in_archive && is_archive())
			return;
		
		if(!$in_single && is_singular())
			return;
		
		if(!$in_front_page && is_front_page())
			return;
		
		//global $post;
		
		$posttype = get_post_type();
		$posttypes = explode(",",$posttypes);
		
		if(isset($posttypes) && !in_array($posttype,$posttypes))
			return;
		
		if($posttype == 'page' && is_front_page())
			return;
		
		if($align == "left")
			$align = "text-align:left;";
		else if($align == "right")
			$align = "text-align:right;";
		else if($align == "center")
			$align = "text-align:center;";
		
		$tops = (isset($tops) && $tops == 1)? 1 : 0;
		
		return "<!--Parstools Voting Widget--><script type=\"text/javascript\">
;!(function(e, d) {'use strict';var rating = {id: 'prw-' + ~~(Math.random() * 999999),theme: '$theme',tops: $tops,lang: '$lang',url: '".esc_url(wp_get_shortlink($post_id))."',title: '".$this->clean_title(get_the_title($post_id))."'};if (typeof e.prwAttrs != 'object') e.prwAttrs = {};e.prwAttrs[rating.id] = rating;d.write('<d'+'iv id=\"' + rating.id + '\" style=\"$align\"></d'+'iv>');})(window, document);</script><!--Parstools Voting Widget-->";
	}
}
