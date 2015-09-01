<?php

/**
 * WordPress settings API BCE ADMIN PAGES
 *
 * @author Tareq Hasan
 */
class BCE_Admin
{
    private $settings_api;

    public function __construct()
    {
        $this->settings_api = new settingApi();
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_script'));
    }

    public function enqueue_script($hook)
    {
        if ($hook !== "settings_page_bce_setting") {
            return false;
        }

    }

    public function admin_init()
    {
        $this->settings_api->set_sections($this->get_settings_sections());
        $this->settings_api->set_fields($this->get_settings_fields());
        $this->settings_api->admin_init();
    }

    public function admin_menu()
    {
        add_options_page(__('BCE', 'block-content-editor'), __('BCE', 'block-content-editor'), 'delete_posts', 'bce_setting', array(
            $this,
            'plugin_page'
        ));
    }

    public function get_settings_sections()
    {
        $sections = array(
            array(
                'id' => 'bce_basics',
                'title' => __('Basic Settings', 'bce')
            ),
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    public function get_settings_fields()
    {
        $settings_fields = array(
            'bce_basics' => array(
                array(
                    'name' => 'bce_post_type',
                    'label' => __('Post Type', 'bce'),
                    'desc' => __('Please select the post type to enable the editor', 'bce'),
                    'type' => 'post_type',
                    'default' => array('post', 'page', 'attachment'),
                ),
                array(
                    'name' => 'bce_background_color',
                    'label' => __('Background Color', 'bce'),
                    'desc' => __('Please select the background color.', 'bce'),
                    'type' => 'color',
                    'default' => '#41605b'
                ),
                array(
                    'name' => 'bce_user_select',
                    'label' => __('Choose a user', 'bce'),
                    'desc' => __('Please select the user to enable.', 'bce'),
                    'type' => 'user_select',
                    'default' => array(get_current_user_id())
                ),
            ),
        );
        return $settings_fields;
    }

    public function plugin_page()
    {
        echo '<div class="wrap"><h1>' . __('BCE Settings', 'bce') . '</h1>';
        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();
        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    public function get_pages()
    {
        $pages = get_pages();
        $pages_options = array();
        if ($pages) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }
        return $pages_options;
    }
}



