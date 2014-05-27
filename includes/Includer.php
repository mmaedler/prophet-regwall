<?php
/**
 * Created by mmaedler
 * Date: 19.05.14
 *
 *
 */

namespace includes;


class Includer {

	/**
	 * Returns the regwall HTML
	 * @return string
	 */
	public static function get_regwall_html () {
		return self::get_template_file("regwallhtml.html");
	}

	public static function get_settings_html () {
		return self::evaluate_template_file("settings.php");
	}


	/**
	 * Loads the file from template folder to disk
	 * @param $filename Filename of file to load
	 * @return string Files contents
	 */
	private static function get_template_file ($filename) {
		return file_get_contents(plugin_dir_path(__FILE__)."/templates/".$filename);
	}

	private static function evaluate_template_file ($filename) {
		ob_start();
		require plugin_dir_path(__FILE__)."/templates/".$filename;
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}