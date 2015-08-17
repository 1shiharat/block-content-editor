<?php

/**
 * プラグインのコアクラス
 *
 *
 *
 *
 * @since      1.0.0
 * @package    BCE
 * @subpackage BCE/includes
 * @author     1shiharat <akeome1369@gmail.com>
 */
class BCE
{

    /**
     * プラグインのすべてのフックを登録するローダーをプロパティ
     *
     * @since    1.0.0
     * @access   protected
     * @var      BCE_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * ユニークなプラグインの識別子
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * 現在のバージョン
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * ブロックオブジェクト
     *
     * @var object $blocks すべてのブロッククラスを格納する
     */
    protected $blocks;

    /**
     * 初期化
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
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-bce-utilis.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-bce-loader.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-bce-parser.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-bce-i18n.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-bce-setup.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-bce-blocks.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-bce-frontend.php';

        $this->loader = new BCE_Loader();
        $this->blocks = new BCE_Blocks(BCE_Utilis::get_plugin_name(), BCE_Utilis::get_version());

    }

    /**
     * 多言語化を設定
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new BCE_i18n();
        $plugin_i18n->set_domain(BCE_Utilis::get_plugin_name());
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

        $plugin_admin = new BCE_Setup($this->blocks);
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

        $this->loader->add_action('wp_enqueue_scripts', $plugin_admin, 'front_enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_admin, 'front_enqueue_scripts');

    }


    /**
     * ローダーに登録したアクションフックを実行する
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }


    /**
     * ローダーを取得する
     *
     * @since     1.0.0
     * @return    BCE_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

}
