<?php
/**
 * Plugin Name: Prophet Reg Wall
 * Plugin URI: http://github.com/mmaedler/prophet-regwall
 * Description: Adds a register form in front of selected posts
 * Author: Moritz Mädler
 * Version: 0.1
 * Author URI: http://prophet.com
*/

class PbsRegWall {
	private $metakey 		= "pbs_regwall";
	private $cookie_name	= "regflag";
	private $cookie_value	= "ok";


	public function __construct () {

		//
		// Load options from settings page
		//

		// cookie name/value
		if (trim(get_option("cookie_name")) != "" && trim(get_option("cookie_value")) != "") {
			$this->cookie_name = get_option("cookie_name");
			$this->cookie_value = get_option("cookie_value");
		}

		//
		// Filter Hooks
		//

		// add filter to post content
		add_filter("the_content", array($this, "filter_the_content"));

		//
		// Action Hooks
		//

		// add box to post edit page
		add_action("add_meta_boxes", array($this, "action_add_meta_boxes"));

		// make sure it's values get saved when post is saved
		add_action("save_post", array($this, "action_save_post"));

		// add page to admin>settings menue
		if (is_admin()) {
			add_action("admin_menu", array($this, "action_admin_menu"));
			add_action("admin_init", array($this, "action_admin_init"));
		}
	}

	/**
	 * Checks for existence of pbs_regwall meta flag and displays paywall html
	 * @param $content The post content ($post[content])
	 * @return string  The modified post content
	 */
	public function filter_the_content ($content) {
		global $post;
		$return = $content;

		//
		// Show Reg Wall if
		// 	- view is single post view
		//	- post is regwalled
		//	- user does not have regwall cookie
		//

		if (is_single($post) && get_post_meta($post->ID, $this->metakey, true) == 1) {
			if ((! is_user_logged_in() || get_option("force_regwall_for_loggedin_users"))) {
				// load static assets
				$this->load_static_assets();

				// load content
				$tmpl = (trim(get_option("regwall_html")) != "") ? get_option("regwall_html") : \includes\Includer::get_regwall_html();

				// replace markers
				$rendered_content = str_replace(
					array("###CONTENT###", "###TRACKING###", "###EXCERPT###"),
					array($content, $post->ID, ((empty($post->post_excerpt)) ? substr($content, 0, 80) : $post->post_excerpt)),
					$tmpl
				);
				$return = $rendered_content;
			}
		}

		return $return;
	}

	/**
	 * Adds the static assets (css/js) to DOM
	 * @param bool $version Version string of Asset
	 */
	private function load_static_assets ($version = false) {
		wp_enqueue_style("pbs_regwall_css", plugins_url("static/css/prophet-regwall.css", __FILE__), array(), $version);
		wp_enqueue_script("pbs_regwall_css", plugins_url("static/js/prophet-regwall.js", __FILE__), array(), $version);
	}

	//
	// ------------------------------------------------------------------------------------------------
	// Admin Post Page related functions
	// ------------------------------------------------------------------------------------------------
	//

	/**
	 * Adds Meta boxes (e.g. regwall box to post edit page)
	 */
	public function action_add_meta_boxes () {
		// add meta box regwallbox
		add_meta_box("pbs_metabox_regwall", "Regwall Post", array($this, "action_add_regwallbox"), "post", "advanced", "high");
	}

	/**
	 * Adds the regwall box to the post edit page
	 * @param $post The Post
	 */
	public function action_add_regwallbox ($post) {
		$checked = ((bool) get_post_meta($post->ID, $this->metakey, true)) ? ' checked="checked"' : '';

		$html = <<<HTML
			<div id="pbs_regwallbox">
				<input type="checkbox" name="{$this->metakey}" id="{$this->metakey}" value="1"{$checked}>
				<label for="{$this->metakey}">Mark this post as regwalled.</label>
			</div>
HTML;
		echo $html;
	}

	/**
	 * Handles post saves.
	 * @param $post_id The Post id
	 */
	public function action_save_post ($post_id) {
		// make sure we have the post id - not revision
		$revision = wp_is_post_revision($post_id);

		$post_id = ($revision !== false) ? $revision : $post_id;

		if ($post_id) {
			$this->action_save_meta($post_id);
		}
	}

	/**
	 * Saves/Updates the contents of the regwall metabox
	 * @param $post_id The Post id
	 */
	public function action_save_meta ($post_id) {
		// get all meta fields
		$post_meta = get_post_meta($post_id);

		// make sure we have a meta value even if not selected
		$meta_value = (array_key_exists($this->metakey, $_POST)) ? $_POST[$this->metakey] : 0;

		if (array_key_exists($this->metakey, $post_meta)) {
			update_post_meta($post_id, $this->metakey, $meta_value);
		} else {
			add_post_meta($post_id, $this->metakey, $meta_value);
		}
	}

	//
	// ------------------------------------------------------------------------------------------------
	// Admin Settings Page related functions
	// ------------------------------------------------------------------------------------------------
	//

	/**
	 * Setup regwall related admin variables
	 */
	public function  action_admin_init () {
		register_setting("pbs_regwall_option_group", "force_regwall_for_loggedin_users");
		register_setting("pbs_regwall_option_group", "regwall_html");
		register_setting("pbs_regwall_option_group", "cookie_name");
		register_setting("pbs_regwall_option_group", "cookie_value");
	}

	/**
	 * Include a link into the admin menue
	 */
	public function action_admin_menu () {
		add_options_page(
			"Prophet Regwall Settings",
			"Prophet Regwall",
			"manage_options",
			$this->metakey."_settings",
			array($this, "add_options_page")
		);
	}

	/**
	 * Add the options page
	 */
	public function add_options_page () {
		echo \includes\Includer::get_settings_html();
	}

}

require_once "includes/Includer.php";
$pbs_regwall = new PbsRegWall();