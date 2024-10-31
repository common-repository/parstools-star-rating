<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://parstools.com/
 * @since      1.0.0
 *
 * @package    Parstools_Star_Rating
 * @subpackage Parstools_Star_Rating/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Parstools_Star_Rating
 * @subpackage Parstools_Star_Rating/includes
 * @author     Parstools <parsgateco@gmail.com>
 */
class Parstools_Star_Rating
{
	private $default_options = array(
		'theme' => '1',
		'position' => 'before_content',
		'align' => 'left',
		'in_archive' => true,
		'in_front_page' => true,
		'in_single' => true,
		'posttypes' => "post",
		'lang' => "fa",
		'tops' => true,
		'first_install_notic' => false
	);
	
	private $options = array();
	
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Parstools_Star_Rating_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		$this->plugin_name = 'parstools-star-rating';
		$this->version = '1.0.0';
		
		$lang = get_bloginfo("language");
		if($lang != "fa-IR")
			$this->default_options["lang"] = "en";
		
		$options = get_option($this->plugin_name);
		$this->options = wp_parse_args($options,$this->default_options);
		
		
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}
	
	
	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Parstools_Star_Rating_Loader. Orchestrates the hooks of the plugin.
	 * - Parstools_Star_Rating_i18n. Defines internationalization functionality.
	 * - Parstools_Star_Rating_Admin. Defines all hooks for the admin area.
	 * - Parstools_Star_Rating_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-parstools-star-rating-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-parstools-star-rating-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-parstools-star-rating-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-parstools-star-rating-public.php';

		$this->loader = new Parstools_Star_Rating_Loader();

	}
	
	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Parstools_Star_Rating_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{
		$plugin_i18n = new Parstools_Star_Rating_i18n();
		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{
		$plugin_admin = new Parstools_Star_Rating_Admin($this->get_plugin_name(), $this->get_version() , $this->options);

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		//$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action('admin_init', $plugin_admin, 'admin_init');
		$this->loader->add_action('admin_menu', $plugin_admin, 'add_setting_page');
		
		if(!$this->options["first_install_notic"])
		{
			$this->loader->add_action('admin_notices', $plugin_admin, 'admin_notices');
			$this->loader->add_action('admin_footer', $plugin_admin, 'admin_footer');
			$this->loader->add_action('wp_ajax_dismiss_notices', $plugin_admin, 'wp_ajax_dismiss_notices');
		}
		
		$this->loader->add_filter('plugin_action_links', $plugin_admin, 'plugin_page_settings_link',2,2);
	}
	
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{
		$plugin_public = new Parstools_Star_Rating_Public( $this->get_plugin_name(), $this->get_version() , $this->options);

		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		extract($this->options);
		if($position != "manual")
		{
			$this->loader->add_action('the_content', $plugin_public, 'add_rate_widget_to_content');
		}
		$this->loader->add_action('wp_footer', $plugin_public, 'add_script_to_footer');
	}
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Parstools_Star_Rating_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
