<?php
/**
 * Plugin Name:       WooCommerce Product Sales Countdown Timer
 * Description:       WooCommerce Product Sales Countdown Timer plugin helps you display for single product page.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Ariful Islam
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       woo-countdown-timer
 */

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
 
class Product_Countdown {
    
    /**
     * Initializes the Product_Countdown class
     *
     * Checks for an existing Product_Countdown instance
     * and if it cant't find one, then creates it.
     */

    public static function init() {
        static $instance = false;
        if( ! $instance ) {
            $instance = new Product_Countdown();
        }

        return;
    }

    /**
     * Sets up our plugin
     */
    public function __construct() {

        // define constants
        $this->define_constants();

        // includes
        $this->includes();
        
    
    }

    function define_constants() {
        define( 'WCPC_DIR_FILE', plugin_dir_url( __FILE__ ) );
        define( 'WCPC_ASSETS', WCPC_DIR_FILE . '/assets' );
    }

    /**
    * Includes function
    */

    function includes() {
        add_filter( 'woocommerce_product_data_tabs', [ $this, 'woo_countdown_timer_tab' ] );
        add_action( 'woocommerce_product_data_panels', [ $this, 'woo_countdown_timer_product_data_panels' ] );
        add_action( 'woocommerce_process_product_meta', [ $this, 'woo_countdown_timer_save_fields' ] );
        add_action( 'woocommerce_single_product_summary', [ $this, 'display_woo_countdown_timer' ], 30);
        add_action( 'wp_enqueue_scripts', array( $this, 'load_enqueue' ) );
    }

    /**
    * Countdown Tab Function
    */

    function woo_countdown_timer_tab( $product_data_tabs ) {
        $product_data_tabs['my-custom-tab'] = array(
            'label'     =>  __( 'Product Countdown', 'woo-countdown-timer' ),
            'target'    => 'my_custom_product_data',
        );
        return $product_data_tabs;
    }

    /**
    * Product Data Panels Function
    */

    function woo_countdown_timer_product_data_panels() {
        ?>
            <div id='my_custom_product_data' class='panel woocommerce_options_panel'>
                <div class='options_group'>
                    <?php

                        woocommerce_wp_checkbox( array(
                            'id' 		=> '_enable_timer',
                            'label' 	=> __( 'Enable Timer', 'woo-countdown-timer' ),
                        ) );

                        woocommerce_wp_text_input( array(
                            'id'         => '_valid_for_timer_text',
                            'label'      => __( 'Timer Heading Text', 'woo-countdown-timer' ),
                            'desc_tip'   => 'true',
                            'description'=> __( 'Enter timer header text', 'woo-countdown-timer' ),
                            'type'       => 'text',
                        ) );

                        woocommerce_wp_text_input( array(
                            'id'				=> '_valid_for_date',
                            'label'				=> __( 'End Date', 'woo-countdown-timer' ),
                            'desc_tip'			=> 'true',
                            'description'		=> __( 'Enter the end date here countdown for product sales', 'woo-countdown-timer' ),
                            'type' 				=> 'date',
                        ) );
                        woocommerce_wp_text_input( array(
                            'id'				=> '_valid_for_date_time',
                            'label'				=> __( 'Time', 'woo-countdown-timer' ),
                            'desc_tip'			=> 'true',
                            'description'		=> __( 'Enter the end date here countdown for product sales', 'woo-countdown-timer' ),
                            'type' 				=> 'time',
                        ) );

                    ?>
                </div>
            </div>
        <?php
    }

    /**
    * Field Save Function
    */

    function woo_countdown_timer_save_fields( $post_id ) {
        $valid_for_date         = isset( $_POST[ '_valid_for_date' ] ) ? sanitize_text_field( wp_unslash( $_POST[ '_valid_for_date' ] ) ) : '';
        $valid_for_date_time    = isset( $_POST[ '_valid_for_date_time' ] ) ? sanitize_text_field( wp_unslash( $_POST[ '_valid_for_date_time' ] )  ) : '';
        $valid_for_head_text    = isset( $_POST[ '_valid_for_timer_text' ] ) ? sanitize_text_field( wp_unslash( $_POST[ '_valid_for_timer_text' ] )  ) : '';
        $enable_timer           = isset( $_POST[ '_enable_timer' ] ) ? 'yes' : 'no';

        update_post_meta( $post_id, '_valid_for_date', esc_attr( $valid_for_date ) );
        update_post_meta( $post_id, '_valid_for_date_time',  esc_attr( $valid_for_date_time ) );
        update_post_meta( $post_id, '_valid_for_timer_text',  esc_attr( $valid_for_head_text ) );
        update_post_meta( $post_id, '_enable_timer',  esc_attr( $enable_timer ) );
        
    }

    /**
    * Display Data Showing Function
    */

    function display_woo_countdown_timer() {
        $date           = get_post_meta( get_the_ID(), '_valid_for_date', true );
        $time           = get_post_meta( get_the_ID(), '_valid_for_date_time', true );
        $text           = get_post_meta( get_the_ID(), '_valid_for_timer_text', true );
        $enable_timer   = get_post_meta( get_the_ID(), '_enable_timer', true );

        ?>
           <?php if( 'yes' === $enable_timer && ! empty( $date ) ) {
               ?> <p id="demo"></p><?php
           } ?>
            
            <script>
                var countDownDate = new Date("<?php echo $date .' '. $time;?>").getTime();
                var x = setInterval(function() {
                var now = new Date().getTime();
                var distance = countDownDate - now;
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Display the result in the element with id="demo"
                document.getElementById("demo").innerHTML = 
                    `<div class="countdown_wrap">
                        <p><?php echo $text; ?></p>
                    
                        <div class="countdown_timer">
                            <span class="countdown-single-item day">
                                <span class="date">${days}</span>Days
                            </span>
                            <span class="countdown-single-item hours">
                                <span class="date">${hours}</span>Hours
                            </span>
                            <span class="countdown-single-item mins">
                                <span class="date">${minutes}</span>Mins
                            </span>
                            <span class="countdown-single-item secs">
                                <span class="date">${seconds}</span>Secs
                            </span>
                        </div>
                    </div>
                    `

                // If the count down is finished, write some text
                if ( distance < 0 ) {
                    clearInterval(x);
                    document.getElementById("demo").innerHTML = "<span class='expire-texxt'>EXPIRED</span>";
                }
                }, 1000);
            </script>

        <?php
    }


    /**
     * Add all the enqueue required by the plugin
     *
     * @since 1.0
     *
     * @return void
     */
    function load_enqueue() {
        wp_enqueue_style( 'woo-countdown-timer-style', WCPC_ASSETS . '/css/style.css' );
        
    }

}

function product_countdown() {
    return Product_Countdown::init();
}
product_countdown();
