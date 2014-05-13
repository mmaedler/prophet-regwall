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
	private $metakey = "pbs_regwall";


	public function __construct() {
		// add filter to post content
		add_filter("the_content", array($this, "filter_the_content"));

		// add box to post edit page
		add_action("add_meta_boxes", array($this, "action_add_meta_boxes"));

		// make sure it's values get saved when post is saved
		add_action("save_post", array($this, "action_save_post"));
	}

	/**
	 * Checks for existence of pbs_regwall meta flag and displays paywall html
	 *
	 * @TODO: implement final html
	 *
	 * @param $content The post content ($post[content])
	 * @return string  The modified post content
	 */
	public function filter_the_content ($content) {
		global $post;
		$return = "";
		if (get_post_meta($post->ID, $this->metakey, true) == 1) {
			$return .= "<div style='position: absolute; width: 100%; height: 100%; background-color: red'> </div>";
		}
		$return .= $content;

		return $return;
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
	 *
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

}

$pbs_regwall = new PbsRegWall();