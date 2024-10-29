<?php
add_action('plugins_loaded', 'myplugin_load_textdomain');

function myplugin_load_textdomain() {
    //load_plugin_textdomain('ticketsys', false, basename(dirname(dirname(__FILE__))) . '/languages');
    $locale_admin = get_option('ticketsys_lang_admin');
    $locale_user = get_option('ticketsys_lang_user');
    $domain = 'ticketsys';
    if (is_admin())
        load_textdomain('ticketsys', dirname(dirname(__FILE__)) . '/languages/' . $domain . '-' . $locale_admin . '.mo');
    else
        load_textdomain('ticketsys', dirname(dirname(__FILE__)) . '/languages/' . $domain . '-' . $locale_user . '.mo');
}

/**
 * Adds tsWidget widget.
 */
class tsWidget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
                'tsWidget', // Base ID
                __('Ticketing System', 'ticketsys'), // Name
                array('description' => __('Allows users to submit tickets to report problems or get support on whatever you want. Users can set the division, priority and product of each ticket.', 'ticketsys')) // Args
        );
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) {
        if (isset($_POST['action']) && $_POST['action'] == 'ticketsys-submit') {
            $config = simplexml_load_file(plugin_dir_path(__FILE__) . '../config/config.xml');
            global $wpdb;
            $dbprefix = 'b1st_ts_';
            //finiclude 'views/contact.php';
            return;
        }

        $config = simplexml_load_file(plugin_dir_path(__FILE__) . '../config/config.xml');
        global $wpdb;
        $dbprefix = 'b1st_ts_';
        //
        include 'views/widget.php';
    }

    public function enqueue_styles() {
        wp_register_style('ticketsys-style', plugins_url('assets/css/stylesheet.css', __FILE__), false, '1.0.0');
        wp_register_style('ticketsys-style-bootstrap', plugins_url('assets/css/bootstrap-all.css', __FILE__), false, '1.0.0');
        wp_register_style('ticketsys-style-fancybox', plugins_url('assets/css/jquery.fancybox.css', __FILE__), false, '1.0.0');
        wp_register_style('ticketsys-style-fontawesome', plugins_url('assets/css/font-awesome.css', __FILE__), false, '1.0.0');

        wp_enqueue_style('ticketsys-style');
        wp_enqueue_style('ticketsys-style-bootstrap');
        wp_enqueue_style('ticketsys-style-fontawesome');
        wp_enqueue_style('ticketsys-style-fancybox');
    }

    public function enqueue_scripts() {
        wp_register_script( 'jquery-js', plugins_url('assets/js/jquery.min.js', __FILE__ ), false, '2.0.0' );
        wp_register_script('contact-js', plugins_url('assets/js/contact.js', __FILE__), false, '1.0.0');
        wp_register_script('fancybox-js', plugins_url('assets/js/jquery.fancybox.pack.js', __FILE__), false, '1.0.0');

        wp_enqueue_script('jquery-js');
        wp_enqueue_script('contact-js');
        wp_localize_script('contact-js', 'ajaxcontactajax', array('ajaxurl' => admin_url('admin-ajax.php')));
        wp_enqueue_script('fancybox-js');
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = __('New title', 'ticketsys');
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'ticketsys'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';

        return $instance;
    }

}

// class tsWidget