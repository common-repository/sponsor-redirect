<?php
class MsbdSRPAdmin {

    var $parent;

    function __construct($parent) {
        $this->parent = $parent;
        
        add_action('admin_menu', array(&$this, 'init_admin_menu'));
        
        //Loading Styles and Scripts for admin
        add_action( 'admin_enqueue_scripts', array(&$this, 'load_admin_scripts_styles'), 100);
        
    }


    function init_admin_menu() {
        global $wpdb;

        $var_manage_authority = 'manage_options';
        
        add_submenu_page(
            'edit.php?post_type='.$this->parent->post_type_sponsor, 
            'Sponsor Redirect Settings', 
            'Settings', 
            $var_manage_authority, //$capability 
            'msbd-srp-settings', 
            array(&$this, 'msbd_srp_settings_page_render')
        );
        
        add_submenu_page(
            'edit.php?post_type='.$this->parent->post_type_sponsor, 
            'Sponsor Redirection Plugin Documentation', 
            'Documentation', 
            $var_manage_authority, //$capability 
            'msbd-srp-documentation', 
            array(&$this, 'msbd_srp_documentation_page_render')
        );
    }
    
    

    function msbd_srp_documentation_page_render($wrapped = false) {
        $options = $this->parent->srp_options_obj->get_option();

        if (!$wrapped) {
            $this->wrap_admin_page('documentation');
            return;
        }

        //Check User Permission
        $var_manage_authority = $this->parent->srp_options['msbd_srp_manage_authority'];
        if (!current_user_can($var_manage_authority)) {
            wp_die( __('You do not have sufficient permissions to access this page.') );
        }
        
        ?>
        <div class="srp-documentation">
        
            <p><strong>Sponsor Redirect plugin</strong> helps you to manage your sponsors or affiliate link on your own site easily. Most of the time we get an affiliate link which is almost impossible to memorise and for which we have to share the affiliate links by copy and paste everytime. In that case we can create an url on your site that will redirect to the specific url.</p>
        
            <p>We have use Masonary library to looks good the grid of sponsor. If the masonary library already exist on your theme then please use the plugin settings page to unload the library from this plugin. If you are not interested to show your sponsors in any page then it will batter to disable loading masonary library using plugin settings section.</p>
        
            <p><strong>Features of Sponsor Redirect plugin:</strong></p>
            <ul>
                <li>This plugin enables you to manage all your affiliate link centrally from your own site.</li>
                <li>You can update link of any sponsor anytime without getting headache about where you have shared the url previously.</li>
                <li>This plugin offer create post including featured image which enable you to create a page showing your special affiliate partners.</li>
                <li>Number of clicks is printed on the manage page.</li>
            </ul>
        
            <p class="title"><strong>How to use:</strong></p>
            <ul>
                <li>In this stage we hope that you have already installed the plugin.</li>
                <li>Now you will have an item "Sponsors" at the left admin panel of your site.</li>
                <li>Click on "Sponsors -> Add New" to open the "Add New Post" page that will help you to create new sponsor affiliate link.            
                    <ul>
                        <li>Write the name of your affiliate company in the title field.</li>
                        <li>Write a description of your affiliate company in the editor.</li>
                        <li>Since this plugin offered a shortcode to show your affiliate partners, the featured image will use by the shortcode.</li>
                        <li>
                            In the "Sponsor Settings" box you will get three fields:
                            <ol>
                                <li><strong>Refferel URL</strong> - copy/paste or write the url of your affiliate partner.</li>
                                <li><strong>Sponsor Type</strong> - This option will help you to seperate different type of affiliate partners</li>
                                <li><strong>Sponsor Sorting</strong> - is used to sorting the affiliate partners when showing using the shortcode.</li>
                            </ol>
                        </li>
                    </ul>
                </li>
            </ul>


            <p class="title"><strong>Currently <i>[msbd-srp]</i> offeres the following attributes:</strong></p>
            <ul>
                <li><strong>type</strong> - default value is empty which will retrieve all type of affiliate partner except "Hide". Permitted values for this attribute are - 'premium', 'golden', 'silver', 'standard', or "all". If you like to retireve all type of sponsor link (including hide type) then you can use "all" for this attribute.</li>
                <li><strong>columns</strong> - you can specify the columns number to show the sponsors in grid. Permitted values for this attribute are: 2,3, or 4</li>
                <li><strong>limit</strong> - you can limit the number of sponsor to show using the shortcode among a lot of sponsors. Default is 0 (unlimited/all)</li>
                <li><strong>sorting</strong> - permitted value for this attribute are "ASC" or "DESC". Default is DESC</li>
                <li><strong>wrap_class</strong> - if you like to add any css class to the container of the grid to style yourself, you can write the css class using this attribute. Default is empty.</li>
                <li><strong>thumbnail</strong> - By default this plugin use the default thumbnail image of wordpress. If you know what type of thumbnail sizes are stored on your site and want to use different type of thumbnail then this option will be useful for you. Write the thumbnail size here. By default the value is "thumbnail"</li>
            </ul>


            
            
            <p class="title">Examples of <strong>Shortcode [msbd-srp]</strong></p>
            <ul>
                <li>
                    Following two short code will output same:<br>
                    <strong>[msbd-srp]</strong><br>and<br>
                    <strong>[msbd-srp type="" columns="3" wrap_class="" limit="0" thumbnail="thumbnail" sorting="DESC"]</strong>
                </li>
                <li>
                    Following two short code will output only golden type of sponsors:<br>
                    <strong>[msbd-srp type="golden"]</strong><br>and<br>
                    <strong>[msbd-srp type="golden" columns="3" wrap_class="" limit="0" thumbnail="thumbnail" sorting="DESC"] </strong>
                </li>
            </ul>
            
            <p class="title"><strong>If you have any query about this plugin please feel free to send us a message at <a href="http://microsolutionsbd.com/contact-us/" target="_blank">Micro Solutions Bangladesh</a></strong></p>
        </div>
        <?php
    }
    
    
    
    
    function msbd_srp_settings_page_render($wrapped = false) {
        $options = $this->parent->srp_options_obj->get_option();

        if (!$wrapped) {
            $this->wrap_admin_page('settings');
            return;
        }

        //Check User Permission
        $var_manage_authority = $this->parent->srp_options['msbd_srp_manage_authority'];
        if (!current_user_can($var_manage_authority)) {
            wp_die( __('You do not have sufficient permissions to access this page.') );
        }
        ?>
        <form id="msbd-srp-settings-form" action="" method="post">
            <input type="hidden" name="action" value="msbd-srp-update-options">
            
            <div class="form-table">
                <div class="form-row">
                    <div class="grid_4">
                        <label for="srp_share_new_window">Use new window</label>
                    </div>
                    <div class="grid_8">
                        <?php
                        echo draw_yes_no_select_box('name="srp_share_new_window" id="srp_share_new_window"', $options['srp_share_new_window']);
                        ?>
                    </div>
                </div>
                    
                <div class="form-row">
                    <div class="grid_4">
                        <label for="srp_rel_nofollow">Use nofollow</label>
                    </div>
                    <div class="grid_8">
                        <?php
                        echo draw_yes_no_select_box('name="srp_rel_nofollow" id="srp_rel_nofollow"', $options['srp_rel_nofollow']);
                        ?>
                    </div>
                </div>
                    
                <div class="form-row">
                    <div class="grid_4">
                        <label for="srp_use_masonary">Load masonary library from this plugin</label>
                    </div>
                    <div class="grid_8">
                        <?php
                        echo draw_yes_no_select_box('name="srp_use_masonary" id="srp_use_masonary"', $options['srp_use_masonary']);
                        ?>
                        <p class="note">[If masonary jquery library is already loaded by your theme then make it <strong>"No"</strong>]</p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="grid_6">
                        <input name="resetButton" type="reset" value="Reset" />
                        <input type="submit" class="button" value="Save Settngs">
                    </div>
                </div>
            </div>
        </form>
        <?php
    }





    function load_admin_scripts_styles() {
        wp_enqueue_style( "msbd-srp-admin", MSBD_SRP_URL . 'css/msbd-srp-admin.css', false, false );
    }




    function wrap_admin_page($page = null) {
        $page_header = '';
        switch($page) {                
            case 'settings':
                $page_header = $this->parent->plugin_name.' Settings';
                break;
        }
        
        echo '<div class="wrap msbd-srp">';
        echo '<h2><img src="' . MSBD_SRP_URL . 'images/msbd_favicon_32.png" /> '.$page_header.' </h2>';
        echo '<div class="srp-body-content">';
        
        MsbdSRPAdminHelper::render_container_open('content-container');        
        
        if ($page == 'settings') {
            MsbdSRPAdminHelper::render_postbox_open('Settings');
            echo $this->msbd_srp_settings_page_render(TRUE);
            MsbdSRPAdminHelper::render_postbox_close();
        }
        
        if ($page == 'documentation') {
            MsbdSRPAdminHelper::render_postbox_open('Documentation');
            echo $this->msbd_srp_documentation_page_render(TRUE);
            MsbdSRPAdminHelper::render_postbox_close();
        }
        
        MsbdSRPAdminHelper::render_container_close();
        
        MsbdSRPAdminHelper::render_container_open('sidebar-container');        
        MsbdSRPAdminHelper::render_sidebar();
        MsbdSRPAdminHelper::render_container_close();
        
        echo '</div>'; /* .srp-body-content */
        echo '</div>'; /* .wrap msbd-srp */
    }
    
}
/* end of file msbd-srp-admin.php */
