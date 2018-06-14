
<?php

class Rating_Settings extends Rating_App {


    public $rating_text;

    public function __construct() {


        add_action('admin_init', array($this,'lp_ratings_settings_init'));
        add_action( 'admin_init', array($this, 'register_main_setting'));
        add_action('admin_menu', array($this, 'lp_rating_options_page'));


    }

    public function register_main_setting() {
        // Registering the setting 'lp_rating_types' for the page 'lp_rating_settings'
        register_setting( 'lp_rating_settings', 'lp_rating_types');
        register_setting( 'lp_rating_settings', 'lp_rating_text');
        register_setting( 'lp_rating_settings', 'lp_rating_text_font_size');
        register_setting( 'lp_rating_settings', 'lp_rating_image_size');
        register_setting( 'lp_rating_settings', 'lp_rating_change_image');
    }

    public function lp_rating_options_page()
    {
        // add top level menu page
        add_menu_page(
            __( 'Ratings', 'lp' ),
            __( 'Ratings', 'lp' ),
            'manage_options',
            'lp_rating',
            array($this, 'lp_rating_page_html'),
            'dashicons-star-empty'
        );

        add_submenu_page( 
            'lp_rating', 
            __( 'Settings', 'lp' ), 
            __( 'Settings', 'lp' ), 
            'manage_options', 
            'lp_rating_settings', 
            array($this, 'lp_rating_settings_html')
         );
    }

    public function lp_rating_page_html() {
        // check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }
        global $wpdb;

        // SQL query to get all the content which has the meta key 'lp_rating'. Group the content by the ID and get an average rating on each
        $sql = "SELECT * FROM ( SELECT p.post_title 'title', p.guid 'link', post_id, AVG(meta_value) AS rating, count(meta_value) 'count' FROM {$wpdb->prefix}postmeta pm";
        $sql .= " LEFT JOIN wp_posts p ON p.ID = pm.post_id";
        $sql .= " where meta_key = 'lp_rating' group by post_id ) as ratingTable ORDER BY rating DESC";
        
        $result = $wpdb->get_results( $sql, 'ARRAY_A' );
       // echo '<pre>'.print_r(   $result, true).'</pre>';
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <div id="poststuff">
                <table class="form-table widefat">
                    <thead>
                        <tr>
                            <td>
                                <strong><?php _e( 'Content', 'lp' ); ?></strong>
                            </td>
                            <td>
                                <strong><?php _e( 'Rating', 'lp' ); ?></strong>
                            </td>
                            <td>
                               <strong><?php _e( 'No. of Ratings', 'lp' ); ?></strong>
                            </td>
                            <td>
                               <strong><?php _e( 'Delete Data', 'lp' ); ?></strong>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            foreach ( $result as $row ) {
                                echo '<tr>';
                                    echo '<td>' . $row['title'] . '<br/><a href="' . $row['link'] . '" target="_blank">' . __( 'View the Content', 'lp' ) . '</a></td>';
                                    echo '<td>' . round( $row['rating'], 2 ) . '</td>';
                                    echo '<td>' . $row['count'] . '</td>';
                                     echo '<td><button data-id="'.$row['post_id'].'" id="clearRatings" type="button">Clear Ratings</button></td>';
                                echo '</tr>';
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }

    /**
     * Displaying the form with our Rating settings
     * @return void 
     */
    public function lp_rating_settings_html() {
        // check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }
        
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                // output security fields for the registered setting "lp_rating_settings"
                settings_fields('lp_rating_settings');
        
                // output setting sections and their fields
                do_settings_sections('lp_rating_settings');
        
                // output save settings button
                submit_button('Save Settings');
                ?>
            </form>
        </div>
        <?php
    }



    /**
     * Registering Settings for Rating Settings
     */
    public function lp_ratings_settings_init()
    {

        // Registering the section 'lp_rating_section' for the page 'lp_rating_settings'
        add_settings_section(
            'lp_rating_section',
            '',
            '',
            'lp_rating_settings'
        );
     
        // Registering the field for the setting 'lp_rating_types' on the page 'lp_rating_settings' under section 'lp_rating_section'
        add_settings_field(
            'lp_rating_types', // as of WP 4.6 this value is used only internally
            // use $args' label_for to populate the id inside the callback
            __('Show Rating on Content:', 'wporg'),
            array($this, 'lp_rating_types_html'),
            'lp_rating_settings',
            'lp_rating_section',
            [
                'label_for'         => 'lp_rating_pages',
                'class'             => 'wporg_row',
                'wporg_custom_data' => 'custom',
            ]
        );

    

        add_settings_field( 'lp_rating_text'
           , __('Change default Rating text:', 'wporg'), 
            array( $this, 'lp_rating_text_html' ), 
            'lp_rating_settings',   
            'lp_rating_section',
            [
                'label_for'         => 'lp_rating_pages',
                'class'             => 'wporg_row',
                'wporg_custom_data' => 'custom',
            ] 
        );

        add_settings_field( 'lp_rating_text_font_size'
           , __('Change Font Size:', 'wporg'), 
            array( $this, 'lp_rating_text_font_size_html' ), 
            'lp_rating_settings',   
            'lp_rating_section',
            [
                'label_for'         => 'lp_rating_pages',
                'class'             => 'wporg_row',
                'wporg_custom_data' => 'custom',
            ] 
        );

        add_settings_field( 'lp_rating_image_size'
           , __('Change Image Size:', 'wporg'), 
            array( $this, 'lp_rating_image_size_html' ), 
            'lp_rating_settings',   
            'lp_rating_section',
            [
                'label_for'         => 'lp_rating_pages',
                'class'             => 'wporg_row',
                'wporg_custom_data' => 'custom',
            ] 
        );

        add_settings_field( 'lp_rating_change_image'
           , __('Change Image:', 'wporg'), 
            array( $this, 'lp_rating_change_image_html' ), 
            'lp_rating_settings',   
            'lp_rating_section',
            [
                'label_for'         => 'lp_rating_pages',
                'class'             => 'wporg_row',
                'wporg_custom_data' => 'custom',
            ] 
        );
    }


    /**
     * Get all Custom Post Types that are available publicly
     * For each of those add a checkbox to choose 
     * @param  array $args 
     * @return void       
     */
    public function lp_rating_types_html( $args ) {   
        $post_types = get_post_types( array( 'public' => true ), 'objects' );
        
        // get the value of the setting we've registered with register_setting()
        $rating_types = get_option('lp_rating_types', array());
        
        if( ! empty( $post_types ) ) {
            foreach ( $post_types as $key => $value ) {
                $isChecked = in_array( $key, $rating_types );
                echo '<input ' . ( $isChecked ? 'checked="checked"' : '' ) . ' type="checkbox" name="lp_rating_types[]" value="' . $key . '" /> ' . $value->label . '<br/>';
            }
        }
    }

    public function lp_rating_text_html() {   

        $rating_text = get_option('lp_rating_text');

        //echo '<pre>'.print_r($options_text, true).'</pre>';
       ?>
        <input type="text" placeholder="Rating:" value="<?php echo $rating_text; ?>" name="lp_rating_text" id="lp_rating_text" />
        <?php


        $this->rating_text = $rating_text;

    }

    public function lp_rating_text_font_size_html() {   

        $rating_text_font_size    = get_option('lp_rating_text_font_size');

        //echo '<pre>'.print_r($options_text, true).'</pre>';
       ?>
        <input type="text" placeholder="18" value="<?php echo $rating_text_font_size; ?>" name="lp_rating_text_font_size" id="lp_rating_text_font_size" />
        <?php


     

    }

    public function lp_rating_image_size_html() {   

        $rating_image_size    = get_option('lp_rating_image_size');

        //echo '<pre>'.print_r($options_text, true).'</pre>';
       ?>
        <input type="text" placeholder="20" value="<?php echo $rating_image_size; ?>" name="lp_rating_image_size" id="lp_rating_image_size" />
        <?php


      
    }


    public function misha_image_uploader_field( $name, $value = '') {
        $image = ' button">Upload image';
        $image_size = 'full'; // it would be better to use thumbnail size here (150x150 or so)
        $display = 'none'; // display state ot the "Remove image" button
        $name = 'lp_rating_change_image';

        $rating_change_image  = get_option('lp_rating_change_image');
     
        if( $image_attributes = wp_get_attachment_image_src(  $rating_change_image, $image_size ) ) {
     
            // $image_attributes[0] - image URL
            // $image_attributes[1] - image width
            // $image_attributes[2] - image height
     
            $image = '"><img src="' . $image_attributes[0] . '" style="max-width: 150px;display:block;" />';
            $display = 'inline-block';
     
        } 
     
        return '
        <div>
            <a href="#" class="misha_upload_image_button' . $image . '</a>
            <input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $rating_change_image . '" />
            <a href="#" class="misha_remove_image_button" style="display:inline-block;display:' . $display . '">Remove image</a>
        </div>';
    }

    public function lp_rating_change_image_html() {   

        $rating_change_image  = get_option('lp_rating_change_image');

        //echo '<pre>'.print_r($rating_change_image, true).'</pre>';
       ?>
      <!--   <input type="text" placeholder="20" value="<?php echo $rating_image_size; ?>" name="lp_rating_image_size" id="lp_rating_image_size" /> -->
        <?php

 
    if ( isset( $_REQUEST['saved'] ) ){
        echo '<div class="updated"><p>Saved.</p></div>';
    }
    echo '<div class="wrap">'//'<form method="post">'
    . $this->misha_image_uploader_field( $rating_change_image, get_option( $rating_change_image ) );
    //. '<p class="submit">
       // <input name="save" type="submit" class="button-primary" value="Save changes" />
        //<input type="hidden" name="action" value="save" />
       // </p>';//</form>

        echo '</div>';


       

    }




}


