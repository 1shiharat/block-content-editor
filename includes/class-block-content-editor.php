<?php
/**
 * プラグインのコアクラス
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
     * プラグインのすべてのフックを登録するローダーをプロパティ
     *
     * @since    1.0.0
     * @access   protected
     * @var      Block_Content_Editor_Loader $loader Maintains and registers all hooks for the plugin.
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

        $this->blocks = new BCE_Blocks($this->get_plugin_name(), $this->get_version());

    }

    /**
     * 多言語化を設定
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

        $plugin_admin = new Block_Content_Editor_Setup($this->get_plugin_name(), $this->get_version(), $this->blocks);
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
     * プラグインの識別子を返す
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * ローダーを取得する
     *
     * @since     1.0.0
     * @return    Block_Content_Editor_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * プラグインのバージョンを取得する
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

}
