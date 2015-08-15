<?php

/**
 * プラグインのセットアップ
 *
 * @package    Block_Content_Editor
 * @subpackage Block_Content_Editor/Setup
 * @author     1shiharat <akeome1369@gmail.com>
 */
class Block_Content_Editor_Setup
{

    /**
     * プラグインの名称
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name プラグインの固有ID
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version 現在のプラグインのバージョン
     */
    private $version;

    /**
     * 初期化
     *
     * @since    1.0.0
     * @param      string $plugin_name プラグインの名称
     * @param      string $version プラグインのバージョン
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

        add_filter('the_editor', array($this, 'the_editor'), 10, 1);
        add_action('save_post', array($this, 'block_content_update'), 10, 1);

    }

    /**
     * スタイルシートの登録
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . '../assets/css/block-content-editor-admin.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . 'sir-trevor-css', plugin_dir_url(__FILE__) . '../assets/components/sir-trevor-js/build/sir-trevor.css', array(), $this->version, false);
        wp_enqueue_style($this->plugin_name . 'sir-trevor-css-icons', plugin_dir_url(__FILE__) . '../assets/components/sir-trevor-js/sir-trevor-icons.css', array(), $this->version, false);
    }

    /**
     * JavaScript の登録
     *
     * @since    1.0.0
     */
    public function enqueue_scripts($hook)
    {

        if ($hook === 'post-new.php' || $hook === 'post.php' ) {
            wp_enqueue_script($this->plugin_name . 'sir-trevor-js', plugin_dir_url(__FILE__) . '../assets/components/sir-trevor-js/build/sir-trevor.js', array('jquery', 'underscore'), $this->version, false);
            wp_enqueue_script($this->plugin_name . 'sir-trevor-js-ja', plugin_dir_url(__FILE__) . '../assets/components/sir-trevor-js/locales/ja.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->plugin_name . 'sir-trevor-js-eventable', plugin_dir_url(__FILE__) . '../assets/components/Eventable/eventable.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . '../assets/js/block-content-editor-admin.js', array('jquery'), $this->version, false);
        }
    }

    /**
     * スタイルシートの登録
     *
     * @since    1.0.0
     */
    public function front_enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . '../assets/css/block-content-editor-admin.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . 'sir-trevor-css', plugin_dir_url(__FILE__) . '../assets/components/sir-trevor-js/build/sir-trevor.css', array(), $this->version, false);
        wp_enqueue_style($this->plugin_name . 'sir-trevor-css-icons', plugin_dir_url(__FILE__) . '../assets/components/sir-trevor-js/sir-trevor-icons.css', array(), $this->version, false);
    }

    /**
     * JavaScript の登録
     *
     * @since    1.0.0
     */
    public function front_enqueue_scripts($hook)
    {


        wp_enqueue_script($this->plugin_name . 'sir-trevor-js', plugin_dir_url(__FILE__) . '../assets/components/sir-trevor-js/build/sir-trevor.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name . 'sir-trevor-js-ja', plugin_dir_url(__FILE__) . '../assets/components/sir-trevor-js/locales/ja.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name . 'sir-trevor-js-eventable', plugin_dir_url(__FILE__) . '../assets/components/Eventable/eventable.js', array('jquery'), $this->version, false);
        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . '../assets/js/block-content-editor-front.js', array('jquery'), $this->version, false);

    }



    /**
     * 投稿エディタにブロックコンテンツエディタ用のマークアップを追加
     * @param $content
     * @return string
     */
    public function the_editor($content)
    {
        $post_id = isset($_REQUEST['post']) ? intval($_REQUEST['post']) : '';
        $block_content = get_post_meta($post_id, 'block_content', true);
        return $content . '<div id="block-content-editor-container" style="display: none; background: #fff;"><textarea name="block_content" id="block-content-editor">' . $block_content . '</textarea></div>';
    }


    /**
     * 記事を保存するタイミングで、カスタムフィールドとしてブロックエディタのコンテンツを保存する
     * @param $post_id
     */
    public function block_content_update($post_id)
    {
        remove_action('save_post', array($this, 'block_content_update'));
        $block_content = isset($_REQUEST['block_content']) ? $_REQUEST['block_content'] : '';
        update_post_meta($post_id, 'block_content', $block_content);
        add_action('save_post', array($this, 'block_content_update'));
    }



}
