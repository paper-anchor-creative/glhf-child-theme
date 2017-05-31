<?php

// Defines
define( 'FL_CHILD_THEME_DIR', get_stylesheet_directory() );
define( 'FL_CHILD_THEME_URL', get_stylesheet_directory_uri() );

// Classes
require_once 'classes/class-fl-child-theme.php';

// Actions
add_action( 'wp_enqueue_scripts', 'FLChildTheme::enqueue_scripts', 1000 );

// Add New Stylesheet
function glhf_enqueues() {

	// enqueue style
	wp_enqueue_style('glhf-style', get_stylesheet_directory_uri() .'/css/style.css', array(), '1.0.0', false);

	// enqueue script
	wp_enqueue_script('glhf-script', get_stylesheet_directory_uri() .'/js/scripts.js', array('jquery'), '1.0.0', true);

}
add_action('wp_enqueue_scripts', 'glhf_enqueues');

/**
 * Show a single product page.
 *
 * @param array $atts
 * @return string
 */
function glhf_product_page( $atts ) {
    if ( empty( $atts ) ) {
        return '';
    }

    if ( ! isset( $atts['id'] ) && ! isset( $atts['sku'] ) ) {
        return '';
    }

    $args = array(
        'posts_per_page'      => 1,
        'post_type'           => 'product',
        'post_status'         => 'publish',
        'ignore_sticky_posts' => 1,
        'no_found_rows'       => 1,
    );

    if ( isset( $atts['sku'] ) ) {
        $args['meta_query'][] = array(
            'key'     => '_sku',
            'value'   => sanitize_text_field( $atts['sku'] ),
            'compare' => '=',
        );

        $args['post_type'] = array( 'product', 'product_variation' );
    }

    if ( isset( $atts['id'] ) ) {
        $args['p'] = absint( $atts['id'] );
    }

    $single_product = new WP_Query( $args );

    $preselected_id = '0';

    // check if sku is a variation
    if ( isset( $atts['sku'] ) && $single_product->have_posts() && 'product_variation' === $single_product->post->post_type ) {

        $variation = new WC_Product_Variation( $single_product->post->ID );
        $attributes = $variation->get_attributes();

        // set preselected id to be used by JS to provide context
        $preselected_id = $single_product->post->ID;

        // get the parent product object
        $args = array(
            'posts_per_page'      => 1,
            'post_type'           => 'product',
            'post_status'         => 'publish',
            'ignore_sticky_posts' => 1,
            'no_found_rows'       => 1,
            'p'                   => $single_product->post->post_parent,
        );

        $single_product = new WP_Query( $args );
    ?>
        <script type="text/javascript">
            jQuery( document ).ready( function( $ ) {
                var $variations_form = $( '[data-product-page-preselected-id="<?php echo esc_attr( $preselected_id ); ?>"]' ).find( 'form.variations_form' );

                <?php foreach ( $attributes as $attr => $value ) { ?>
                    $variations_form.find( 'select[name="<?php echo esc_attr( $attr ); ?>"]' ).val( '<?php echo esc_js( $value ); ?>' );
                <?php } ?>
            });
        </script>
    <?php
    }

    ob_start();

    while ( $single_product->have_posts() ) :
        $single_product->the_post();
        wp_enqueue_script( 'wc-single-product' );
        ?>

        <div class="glhf-single-product" data-product-page-preselected-id="<?php echo esc_attr( $preselected_id ); ?>">

            <?php wc_get_template_part( 'content', 'glhf-single-product' ); ?>

        </div>

    <?php endwhile; // end of the loop.

    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode('glhf_single_product','glhf_product_page');

 /*
 *** Single Product Shortcode ***
 */

function glhf_product_item($atts) {
    $atts = shortcode_atts(
        array(
            'id' => 'value'
        ) , $atts);

    ob_start();

    $args = array(
        'post_type' => 'product',
        'p'         => $atts['id']
    );

    $wp_query = new WP_Query($args);

    global $product;
    ?>

    <?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>

        <?php global $product; ?>

        <h1><?php echo the_title(); ?></h1>

        <?php

        $available_variations = $product->get_available_variations();
        $attributes = $product->get_attributes();

        ?>

        <?php
            echo "<pre>";
            var_dump($attributes);
            echo "</pre>";

            echo "<pre>";
            var_dump($available_variations[1]);
            echo "</pre>";
        ?>


    <?php endwhile; wp_reset_query();

    return ob_get_clean();

}
