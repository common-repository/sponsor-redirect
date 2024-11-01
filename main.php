<?php
/*
Plugin Name: Sponsor Redirect
Plugin URI: http://microsolutionsbd.com/
Description: Sponsor Redirect plugin helps you to manage url/links of your affiliate partners. You can also show some of your sponsor info including image anywhere on your site using the shortcode provided by this plugin.
Version: 0.0.5
Author: Micro Solutions Bangladesh
Author URI: http://microsolutionsbd.com/
Text Domain: msbd-srp
License: GPL2
*/

define('MSBD_SRP_URL', trailingslashit(plugins_url(basename(dirname(__FILE__)))));

class MsbdSpanorRedirect {
    
    var $version = '0.0.5';
    var $plugin_name = 'Sponsor Redirect';

    var $srp_options_obj;
    var $srp_options_name;

    /**
     * The variable that stores all current options
     */
    var $srp_options;
    
    
    var $post_type_sponsor;
    var $post_type_sponsor_title;
    var $post_type_sponsor_title_single;
    

    function __construct() {
        global $wpdb;
        
        $this->post_type_sponsor = "sponsor";
        $this->post_type_sponsor_title = "Sponsors";
        $this->post_type_sponsor_title_single = "Sponsor";
        
        
        $this->srp_options_name = "_msbd_srp_options";
        $this->srp_options_obj = new MsbdSRPOptions($this);
        $this->admin = new MsbdSRPAdmin($this);
        
        add_action('init', array(&$this, 'init'));
        add_action('wp_enqueue_scripts', array(&$this, 'load_scripts_styles'), 100);

        add_action( 'add_meta_boxes', array(&$this, 'add_events_metaboxes') );
        add_action( 'save_post', array(&$this, 'srp_save_meta_box_data') );
        
        add_shortcode( 'msbd-srp' , array(&$this, 'msbd_sponsor_redirect_shortcode_func') );
        add_filter( 'single_template', array(&$this, 'get_sponsors_single_template') );
        
        
        add_filter('manage_sponsor_posts_columns', array(&$this, 'msbd_srp_columns_head'));
        add_action('manage_sponsor_posts_custom_column', array(&$this, 'msbd_srp_columns_content'), 10, 2);
    }


    function init() {
        $this->srp_options_obj->update_options();
        $this->srp_options = $this->srp_options_obj->get_option();
        
        // Register custom post for Sponsors
        $this->register_sponsors_post();
    }
    /* end of function : init() */


    function load_scripts_styles() {         
        wp_enqueue_style( "msbd-srp", MSBD_SRP_URL . 'css/msbd-srp.css', false, false );
        
        $use_masonary = $this->srp_options['srp_use_masonary'];
        if( $use_masonary=="yes" ) {
            wp_enqueue_script( "masonry-pkgd", MSBD_SRP_URL ."js/masonry.pkgd.min.js", "jquery", false, true);
            wp_enqueue_script( "srp-scripts", MSBD_SRP_URL ."js/scripts.js", "jquery", false, true);
        }
        
    }



    /***********************************************************
     *                    CUSTOM POST SECTION
     ***********************************************************/

    function register_sponsors_post() {
        
        $args = array(
            //'label' => 'reviews',
            'public' => true,
            'labels' => array(
                'name' => __($this->post_type_sponsor_title, 'srp'),
                'singular_name' => __($this->post_type_sponsor_title_single, 'srp'),
                'not_found_in_trash' => __('Sponsor not found in trash', 'srp')
            ),
            'show_ui' => true, 
            'query_var' => $this->post_type_sponsor,
            'publicly_queryable' => true,
            'rewrite' => array('slug' => 'go', 'with_front' => true),
            'capability_type' => 'post',
            'hierarchical' => false,
            'menu_position' => 5,
            //'taxonomies' => array('post_tag'),
            'menu_icon' => 'dashicons-star-filled',
            'supports' => array( 'title', 'editor', 'thumbnail' )
        );
        
        register_post_type($this->post_type_sponsor, $args);
        flush_rewrite_rules ();
    }





    /***********************************************************
     *                    META BOX SECTION
     ***********************************************************/
     
    function add_events_metaboxes() {

        add_meta_box(
            'srp-metabox',
            __( $this->post_type_sponsor_title_single.' Settings', 'srp' ),
            array(&$this, 'srp_meta_box_callback'),
            $this->post_type_sponsor
        );    
    }
    /* end of function : add_events_metaboxes() */


    function srp_meta_box_callback() {
        global $post;
        
        // Add a nonce field so we can check for it later.
        wp_nonce_field( 'srp_meta_box', 'srp_meta_box_nonce' );

        /*
         * Use get_post_meta() to retrieve an existing value
         * from the database and use the value for the form.
         */
        $srp_url = get_post_meta( $post->ID, 'msbd_srp_url', true );
        $srp_type = get_post_meta( $post->ID, 'msbd_srp_type', true );
        $srp_sorting = get_post_meta( $post->ID, 'msbd_srp_sorting', true );
        
        echo '<p><label for="srp_url">' . __( 'Refferel URL', 'srp' ) . '</label> ';
        echo '<input type="text" id="srp_url" name="srp_url" value="' . $srp_url . '" class="widefat" /></p>';

        echo '<p><label for="srp_type">' . __( 'Sponsor Type', 'srp' ) . '</label> ';
        echo $this->create_sponsor_types_select_box($srp_type);
        echo '</p>';

        echo '<p><label for="srp_sorting">' . __( 'Sponsor Sorting', 'srp' ) . '</label> ';
        echo '<input type="text" id="srp_sorting" name="srp_sorting" value="' . $srp_sorting . '" class="widefat" /></p>';
    }
    /* end of function : srp_meta_box_callback() */



    function srp_save_meta_box_data( $post_id ) {
        // Check if our nonce is set.
        if ( ! isset( $_POST['srp_meta_box_nonce'] ) ) {
            return;
        }

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['srp_meta_box_nonce'], 'srp_meta_box' ) ) {
            return;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check the user's permissions.
        if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;
            }

        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }
        
        // Make sure that it is set.
        if ( !isset($_POST['srp_url']) ) {
            return;
        }

        update_post_meta( $post_id, 'msbd_srp_url', esc_url( $_POST['srp_url'] ) );
        update_post_meta( $post_id, 'msbd_srp_type', sanitize_text_field( $_POST['srp_type'] ) );
        update_post_meta( $post_id, 'msbd_srp_sorting', intval($_POST['srp_sorting']) );        
    }
    /* end of function : srp_save_meta_box_data() */




    function create_sponsor_types_select_box($selected_val="") {
        $type_array = array("premium", "golden", "silver", "standard", "hide");
        
        $html = '<select id="srp_type" name="srp_type" class="widefat">';    
        foreach($type_array as $v) {        
            if($v==$selected_val)
                $html .= '<option value="'.$v.'" selected="selected">'.ucfirst($v).'</option>';
            else
                $html .= '<option value="'.$v.'">'.ucfirst($v).'</option>';
        }    
        $html .= '</select>';
        
        return $html;
    }
    /* end of function : create_sponsor_types_select_box() */
    
    
    
    
    
    
    /***********************************************************
     *                    SHORTCODES SECTION
     ***********************************************************/

    function msbd_sponsor_redirect_shortcode_func($atts, $content, $shortcode) {
        global $wp;
        $rm = '';
         
        $default_attr = array(
            'wrap_class' => '',
            'type' => 'default',
            'sorting' => 'DESC',
            'limit' => '0',
            'columns' => 3,
            'thumbnail' => 'thumbnail'
        );
            
        extract( shortcode_atts($default_attr, $atts) );
        
        //$use_masonary = $this->srp_options['srp_use_masonary'];
        $new_window = $this->srp_options['srp_share_new_window'];
        $rel_nofollow = $this->srp_options['srp_rel_nofollow'];

        $columns = intval($columns);
        $columns = ($columns==2) || ($columns==3) || ($columns==4) ? intval($columns) : 3;
        $masonry_item_width = (12/$columns);

        $args = array(
            'post_type'             => $this->post_type_sponsor,
            'meta_key'   => 'msbd_srp_sorting',
            'orderby'    => 'msbd_srp_sorting',
            'order'      => intval($sorting),
         );

        if( $type=='all' ) {
            //Just ommit filter for sponsor type to retireve all sponsors
        } else if( $type=='' || $type=='default' ) {
            $args['meta_query'] = array(
                'relation' => 'AND',
                array(
                    'key' => 'msbd_srp_type',
                    'value' => array('premium', 'golden', 'silver', 'standard'),
                    'compare' => 'IN',
                ),
            );
        } else {
            $args['meta_query'] = array(
                'relation' => 'AND',
                array(
                    'key' => 'msbd_srp_type',
                    'value' => $atts['type'],
                    'compare' => '=',
                ),
            );
        }
        
        if( intval($limit)>0 ) {
            $args['posts_per_page'] = $limit;
        }

        //print_r( $args );
        $the_query = new WP_Query( $args ); 
        
        if ( $the_query->have_posts() ) {
            while ( $the_query->have_posts() ) {
                $the_query->the_post();
                
                $post_id = get_the_ID();                
                $srp_type = get_post_meta( get_the_ID(), 'msbd_srp_type', true );
                
                //$new_window
                $link_html = '<a href="'.get_permalink().'" title="Redirect Link to '.get_the_title().'"';
                $link_html .= ($new_window=="yes") ? ' target="_blank"' : '';
                $link_html .= ($rel_nofollow=="yes") ? ' rel="nofollow"' : '';
                $link_html .= '>';
                
                $rm .= '
                    <div class="col-sm-'.$masonry_item_width.' '.$srp_type.' masonry-item">
                        <div class="inner-wrap">
                            <div class="blog-media">
                                '.$link_html.get_the_post_thumbnail( $post_id, $thumbnail ).'</a>
                            </div>
                            <div class="content-inner">
                                '.$link_html.'<h5 class="sponsor-title">'.get_the_title().'</h5></a>
                                '.get_the_content().'
                            </div>
                        </div>
                    </div>
                ';
            }
        }
        
         $rm = '<div class="row srp-masonry'.msbd_fspace($wrap_class).'">'.$rm.'</div>';
        return $rm;
    }
    
    

    /***********************************************************
     *             SPONSORS POST TEMPLATE SECTION
     ***********************************************************/
    
    function get_sponsors_single_template($single_template) {
         global $post;

         if ($post->post_type == $this->post_type_sponsor) {
             $url_counter = get_post_meta( $post->ID, 'msbd_srp_url_counter', true );
             update_post_meta( $post->ID, 'msbd_srp_url_counter', intval( $url_counter)+1 );
             
            $srp_url = get_post_meta( $post->ID, 'msbd_srp_url', true );
            msbd_redirect($srp_url);
         }
         return $single_template;
    }
    





    /***********************************************************
     *                          ADD CUSTOM COLUMNS
     ***********************************************************/

    function msbd_srp_columns_head($defaults) {
        $defaults['msbd-srp-url-counter'] = 'Clicked';
        return $defaults;
    }

    function msbd_srp_columns_content($column_name, $post_ID) {
        global $post;
        if ($column_name == 'msbd-srp-url-counter') {
            echo intval( get_post_meta( $post->ID, 'msbd_srp_url_counter', true ) );
        }
    }
    
} // End of Class MsbdSpanorRedirect



require_once('libs/msbd-helper-functions.php');

if (!class_exists('MsbdSRPAdminHelper')) {
    require_once('libs/views/admin-view-helper-functions.php');
}

if (!class_exists('MsbdSRPOptions')) {
    require_once('libs/msbd-srp-options.php');
}

if (!class_exists('MsbdSRPAdmin')) {
    require_once('libs/msbd-srp-admin.php');
}


global $srpObj;
$srpObj = new MsbdSpanorRedirect();

/* end of file main.php */
