<?php

/**
 * Helper class for child theme functions.
 *
 * @class FLChildTheme
 */
final class FLChildTheme {

    /**
	 * Enqueues scripts and styles.
	 *
     * @return void
     */
    static public function enqueue_scripts()
    {
	    wp_enqueue_style( 'fl-child-theme', FL_CHILD_THEME_URL . '/style.css' );
	    wp_enqueue_style( 'glhf-style', get_stylesheet_directory_uri() .'/css/style.css', array(), '1.0.0', false);
	    wp_enqueue_script( 'glhf-script', get_stylesheet_directory_uri() .'/js/scripts.js', array('jquery'), '1.0.0', true);
    }
}
