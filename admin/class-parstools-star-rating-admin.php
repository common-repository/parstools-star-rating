<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://parstools.com/
 * @since      1.0.0
 *
 * @package    Parstools_Star_Rating
 * @subpackage Parstools_Star_Rating/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Parstools_Star_Rating
 * @subpackage Parstools_Star_Rating/admin
 * @author     Parstools <parsgateco@gmail.com>
 */
class Parstools_Star_Rating_Admin
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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $options)
	{
		$this->options = $options;
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Parstools_Star_Rating_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Parstools_Star_Rating_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/parstools-star-rating-admin.css', array(), $this->version, 'all' );

	}
	public function admin_init()
	{
		register_setting('parstools_star_rating_options', $this->plugin_name, array($this, 'validate'));
	}
	
	public function admin_notices()
	{
		if(!current_user_can('manage_options'))
			return;
		
		global $pagenow;
		if(!in_array($pagenow,array("index.php","plugins.php","options-general.php")))
			return;
		?>
        <div class="prw_notice notice notice-success is-dismissible updated">
        <p><?php _e('Thank you for using <a target="_blank" href="http://parstools.com/?p=4705">Parstools star rating</a>.<br />Please go to <a href="options-general.php?page=parstools_star_rating_options">settings</a> page and choose theme and position of rate.', 'parstools-star-rating');?>
        </p>
        </div>
		<?php
	}
	public function admin_footer()
	{
		?>
		<script type="text/javascript" >
        jQuery(document).ready(function($) {
			jQuery(document).on( 'click', '.prw_notice .notice-dismiss', function() {
				var data = {
                	'action': 'dismiss_notices'
				};
				jQuery.post(ajaxurl, data);
			})
        });
        </script>
		<?php
    }
	public function wp_ajax_dismiss_notices()
	{
		$options = $this->options;
		$options["first_install_notic"] = true;
		update_option($this->plugin_name,$options);
		wp_die(); // this is required to terminate immediately and return a proper response
	}
	public function validate($input)
	{
		if(isset($input['theme']))
			$input['theme'] = sanitize_text_field($input['theme']);
		else
			$input['theme'] = 1;
		
		if(isset($input['position']))
			$input['position'] = sanitize_text_field($input['position']);
		else
			$input['position'] = "before_content";
		
		$input['align'] = (isset($input['align']) ? $input['align'] : 'left');
		
		if(isset($input['lang']))
			$input['lang'] = sanitize_text_field($input['lang']);
		else
			$input['lang'] = "fa";
		
		$input['tops'] = (isset($input['tops']) && $input['tops'] == 1 ? true : false);
		
		$input['in_archive'] = (isset($input['in_archive']) && $input['in_archive'] == 1 ? true : false);
		$input['in_front_page'] = (isset($input['in_front_page']) && $input['in_front_page'] == 1 ? true : false);
		$input['in_single'] = (isset($input['in_single']) && $input['in_single'] == 1 ? true : false);
		
		if(!is_null($input['posttypes']) && is_array($input['posttypes']))
		{
			$input['posttypes'] = implode(",",$input['posttypes']);
		}
		
		if(isset($input['dismiss_first_install_notic']) && $input['dismiss_first_install_notic'] == 1)
			$input['first_install_notic'] = true;
		
		return $input;
	}
	public function plugin_page_settings_link($actions, $file)
	{
		if(false !== strpos($file, 'parstools-star-rating'))
			$actions['settings'] = '<a href="options-general.php?page=parstools_star_rating_options">'.__("Settings","parstools-star-rating").'</a>';
		return $actions; 
	}
	public function add_setting_page()
	{
		add_options_page(__("Parstools Star Rating","parstools-star-rating"), __("Parstools Star Rating","parstools-star-rating"), 'manage_options', 'parstools_star_rating_options', array($this, 'setting_page'));
	}
	public function setting_page()
	{
		$options = $this->options;
		?>
		<div class="wrap">
			<h2><?php _e("Parstools Star Rating","parstools-star-rating"); ?></h2>
            
            <h2 class="nav-tab-wrapper">
            <a class='nav-tab nav-tab-active' href='options-general.php?page=parstools_star_rating_options&tab=settings'><?php _e("Settings","parstools-star-rating"); ?></a>
            <a class='nav-tab' href='options-general.php?page=parstools_star_rating_options&tab=other'><?php _e("Other Plugins","parstools-star-rating"); ?></a>
            </h2>
            <div id="sections">
            	<section>
                	<form method="post" action="options.php">
				<?php settings_fields('parstools_star_rating_options'); ?>
                <input type="hidden" name="<?php echo $this->plugin_name?>[dismiss_first_install_notic]" value="1" />
				<table class="form-table">
					<tr valign="top"><th scope="row"><?php _e("Theme: ","parstools-star-rating"); ?></th>
						<td>
                        <div>
                        <?php
						$theme_dir = plugin_dir_path( __FILE__ ) . 'themes/';
						$theme_url = plugin_dir_url( __FILE__ ) . 'themes/';
						
						$i = 0;
						$files = array();
						foreach (glob($theme_dir."*f.png") as $file)
						{
							//$name = explode("/",$file);
							$name = basename($file,"f.png");
							//var_dump($name);
							//$name = explode("u",$name[1]);
							$files[] = $name;
								
						}
						natsort($files);
						
						
						foreach($files as $file)
						{
							/*
							if ($i==0)
								$check="checked";
							else
								$check="";
							$main_td = '<td><input type="radio" id="'.$file.'" name="skin" value="'.$file.'" '.$check.' /><label for="'.$file.'"><img src="skins/'.$file.'u.png" /><img src="skins/'.$file.'d.png" /></label></td>';
							
							if (($i+1)%2==0)
							{
								echo $main_td.'</tr>';
							}
							else
							{
								echo '<tr>'.$main_td;
							};
							*/
							
							if ($file == $options['theme'])
								$check="checked";
							else
								$check="";
							
							echo '<div class="theme"><input type="radio" id="'.$file.'" name="'.$this->plugin_name.'[theme]" value="'.$file.'" '.$check.' /><label for="'.$file.'"><img src="'.$theme_url.$file.'f.png" /><img src="'.$theme_url.$file.'e.png" /></label></div>';
							$i++;
							
							
						}
						
						/*
						while (file_exists($theme_dir.$i.'u.png'))
						{
							$r[$i-1][0] = $theme_url.$i.'u.png';
							$r[$i-1][1] = $theme_url.$i.'d.png';
							$i++;
						}
						
						for ($i = 0; $i < count($r); $i++)
						{
							if (($i+1) == $options['theme'])
								$check="checked";
							else
								$check="";
							
							echo '<div class="theme"><input type="radio" id="'.($i+1).'" name="'.$this->plugin_name.'[theme]" value="'.($i+1).'" '.$check.' /><label for="'.($i+1).'"><img src="'.$r[$i][0].'" /><img src="'.$r[$i][1].'" /></label></div>';
							
						}
						*/
						?>
                        </div>
                        </td>
					</tr>
					<tr valign="top"><th scope="row"><?php _e("Position: ","parstools-star-rating"); ?></th>
						<td>
                        
                        <select name="<?php echo $this->plugin_name?>[position]">
                        	<option value="before_content" <?php if($options['position'] == "before_content") echo 'selected="selected"'; ?> ><?php _e("Before Content","parstools-star-rating"); ?></option>
                            <option value="after_content" <?php if($options['position'] == "after_content") echo 'selected="selected"'; ?> ><?php _e("After Content","parstools-star-rating"); ?></option>
                            <option value="manual" <?php if($options['position'] == "manual") echo 'selected="selected"'; ?> ><?php _e("Manual","parstools-star-rating"); ?></option>
                        </select>
                        
                        <select name="<?php echo $this->plugin_name?>[align]">
                        	<option value="left" <?php if($options['align'] == "left") echo 'selected="selected"'; ?> ><?php _e("Left","parstools-star-rating"); ?></option>
                            <option value="right" <?php if($options['align'] == "right") echo 'selected="selected"'; ?> ><?php _e("Right","parstools-star-rating"); ?></option>
                            <option value="center" <?php if($options['align'] == "center") echo 'selected="selected"'; ?> ><?php _e("Center","parstools-star-rating"); ?></option>
                        </select>
                        <small style="font-size:12px"><?php _e("Select where would you like to display the rate. Use [prw] shortcode for manual display.","parstools-star-rating"); ?></small>
                        </td>
					</tr>
                    <tr valign="top"><th scope="row"><?php _e("Language: ","parstools-star-rating"); ?></th>
						<td>
                        <select name="<?php echo $this->plugin_name?>[lang]">
                        	<option value="fa" <?php if($options['lang'] == "fa") echo 'selected="selected"'; ?> ><?php _e("Persian","parstools-star-rating"); ?></option>
                            <option value="en" <?php if($options['lang'] == "en") echo 'selected="selected"'; ?> ><?php _e("English","parstools-star-rating"); ?></option>
                        </select>
                        </td>
					</tr>
                    <tr valign="top"><th scope="row"><?php _e("Show Tops: ","parstools-star-rating"); ?></th>
						<td>
                        <div class="theme"><input type="checkbox" id="tops" name="<?php echo $this->plugin_name?>[tops]" value="1" <?php if($options['tops']) echo 'checked="checked"'; ?> /></div>
                        <small style="font-size:12px"><?php _e("By enabling this option a link for view all rates will be added to submit rate page.","parstools-star-rating"); ?></small>
                        </td>
					</tr>
                    
                    <tr valign="top"><th scope="row"><?php _e("Show in: ","parstools-star-rating"); ?></th>
						<td>
                        
                        <div class="theme"><label for="in_archive"><?php _e("Archive ","parstools-star-rating"); ?></label><input type="checkbox" id="in_archive" name="<?php echo $this->plugin_name?>[in_archive]" value="1" <?php if($options['in_archive']) echo 'checked="checked"'; ?> /></div>
                        
                        <div class="theme"><label for="in_front_page"><?php _e("Front Page ","parstools-star-rating"); ?></label><input type="checkbox" id="in_front_page" name="<?php echo $this->plugin_name?>[in_front_page]" value="1" <?php if($options['in_front_page']) echo 'checked="checked"'; ?> /></div>
                        
                        <div class="theme"><label for="in_single"><?php _e("Single Post","parstools-star-rating"); ?></label><input type="checkbox" id="in_single" name="<?php echo $this->plugin_name?>[in_single]" value="1" <?php if($options['in_single']) echo 'checked="checked"'; ?> /></div>
                        
                        </td>
					</tr>
                    
                    <tr valign="top"><th scope="row"><?php _e("Show in post types: ","parstools-star-rating"); ?></th>
						<td>
                        <?php
						$types = get_post_types(array('public' => true));
						
						$posttypes = explode(",",$options['posttypes']);
						$posttypes = array_flip($posttypes);
						
						foreach($types as $type)
						{
						?>
                        <div class="theme"><label for="<?php echo $type; ?>"><?php echo $type; ?></label><input type="checkbox" id="<?php echo $type; ?>" name="<?php echo $this->plugin_name?>[posttypes][]" value="<?php echo $type; ?>" <?php if(isset($posttypes[$type])) echo 'checked="checked"'; ?> /></div>
                        <?php
						}
						
						?>
                        </td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e("Save Settings","parstools-star-rating"); ?>" />
				</p>
			</form>
                </section>
            	<section>
                        <table class="form-table" width="100%" height="100%">
                        <?php
						$lang = get_bloginfo("language");
						if($lang)
							$lang = "?lang=".$lang."&pn=".$this->plugin_name."&pv=".$this->version;
						else
							$lang = "?pn=".$this->plugin_name."&pv=".$this->version;
						?>
                        	<tr valign="top"><td><iframe width="100%" height="768" src="http://parstools.com/parstools_wp_plugins/all_plugins.php<?php echo $lang ?>"></iframe></td></tr>
                        </table>
                    </section>
            </div>
            <p><?php _e("More tools and widget in <a href=\"http://parstools.com\">Parstools.com</a>","parstools-star-rating"); ?></p>
		</div>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
			$(document).on( 'click', '.nav-tab-wrapper a', function() {
				
				$(".nav-tab-wrapper a").removeClass('nav-tab-active');
				$(this).addClass('nav-tab-active');
				
				$('section').hide();
				$('section').eq($(this).index()).show();
				
				return false;
			})
        });
        </script>
        <?php
	}
}
