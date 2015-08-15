<?php

/**
 * プラグインのコアとなるクラス
 *
 *
 *
 *
 * @since      1.0.0
 * @package    Block_Content_Editor
 * @subpackage Block_Content_Editor/includes
 * @author     1shiharat <akeome1369@gmail.com>
 */
class Block_Content_Editor
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Block_Content_Editor_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;
    protected $blocks;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {

        $this->plugin_name = 'block-content-editor';
        $this->version = '1.0.0';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_setup_hooks();

    }

    /**
     * 必要なファイルの読み込み
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * Autoloader を読み込む
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-block-content-editor-loader.php';

        /**
         * 多言語化用の設定の読み込み
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-block-content-editor-i18n.php';

        /**
         * プラグイン自体のセットアップを読み込み
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-block-content-editor-setup.php';
        /**
         * ブロックを読み込み
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'blocks/class-bce-blocks.php';

        $this->loader = new Block_Content_Editor_Loader();


//        add_action('after_setup_theme', function () use ($self) {
        $this->blocks = new BCE_Blocks($this->get_plugin_name(), $this->get_version());
//        });

    }

    /**
     * 多言語化を設定
     *
     * Uses the Block_Content_Editor_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Block_Content_Editor_i18n();
        $plugin_i18n->set_domain($this->get_plugin_name());
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

    }

    /**
     * プラグインのセットアップ
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_setup_hooks()
    {

        $plugin_admin = new Block_Content_Editor_Setup($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        $this->loader->add_action('wp_enqueue_scripts', $plugin_admin, 'front_enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_admin, 'front_enqueue_scripts');

    }


    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Block_Content_Editor_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

}
