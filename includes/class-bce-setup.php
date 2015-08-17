<?php

/**
 * プラグインのセットアップ
 *
 * @package    Block_Content_Editor
 * @subpackage Block_Content_Editor/Setup
 * @author     1shiharat <akeome1369@gmail.com>
 */
if ( ! defined( 'WPINC' ) ) {
    die;
}
class BCE_Setup
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
     * 現在のプラグインのバージョン
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version のバージョン
     */
    private $version;

    /**
     * クラスの初期化
     *
     * @since    1.0.0
     * @param      object $blocks ブロックのインスタンス
     */
    public function __construct($blocks)
    {

        $this->plugin_name = BCE_Utilis::get_plugin_name();
        $this->version = BCE_Utilis::get_version();
        $this->blocks = $blocks;

        add_filter('the_editor', array($this, 'the_editor'), 10, 1);
        add_action('wp_footer', array($this, 'output_locales'));
        add_action('admin_footer', array($this, 'output_locales'));

    }

    /**
     * スタイルシートの登録
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        if (!BCE_Utilis::is_enabled_editor()) {
            return false;
        }
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
        if (!BCE_Utilis::is_enabled_editor()) {
            return false;
        }
        if ($hook === 'post-new.php' || $hook === 'post.php') {
            wp_enqueue_script($this->plugin_name . 'sir-trevor-js', plugin_dir_url(__FILE__) . '../assets/components/sir-trevor-js/build/sir-trevor.js', array('jquery', 'underscore'), $this->version, false);
            wp_enqueue_script($this->plugin_name . 'sir-trevor-js-eventable', plugin_dir_url(__FILE__) . '../assets/components/Eventable/eventable.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . '../assets/js/block-content-editor-admin.js', array('jquery'), $this->version, false);

            $types = array_map(function ($t) {
                return ucfirst($t);
            }, $this->blocks->get_types());

            $config = array(
                'config' => array(
                    'language' => get_locale(),
                    'debug' => false,
                    'scribeDebug' => false,
                    'uploadUrl' => admin_url('/media-new.php'),
                ),
                'blockTypes' => $types,
            );
            wp_localize_script($this->plugin_name, 'BCEConfig', $config);
        }
    }

    /**
     * スタイルシートの登録
     *
     * @since    1.0.0
     */
    public function front_enqueue_styles()
    {
        if (!BCE_Utilis::is_enabled_editor()) {
            return false;
        }
        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . '../assets/css/block-content-editor-front.css', array(), $this->version, 'all');
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
        if (!BCE_Utilis::is_enabled_editor()) {
            return false;
        }
        wp_enqueue_script($this->plugin_name . 'sir-trevor-js', plugin_dir_url(__FILE__) . '../assets/components/sir-trevor-js/build/sir-trevor.js', array('jquery'), $this->version, false);
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
        if ( ! BCE_Utilis::is_enabled_editor() ){
            return $content;
        }
        $post_id = isset($_REQUEST['post']) ? intval($_REQUEST['post']) : '';
        $block_content = get_post_meta($post_id, 'block_content', true);
        return $content . '<div id="block-content-editor-container" style="display: none; background: #fff;"><textarea name="block_content" id="block-content-editor">' . $block_content . '</textarea></div>';
    }

    /**
     * 翻訳情報をフッターに出力
     */
    public function output_locales()
    {
        $translate = array(
            // 基本的な設定
            "general" => array(
                "delete" => __("削除しますか?", $this - plugins_name),
                "drop" => __("ここに__block__をドラッグ", $this->plugins_name),
                "paste" => __("またはここにリンクをコピー", $this->plugins_name),
                "upload" => __("...またはファイルを選択します", $this->plugins_name),
                "close" => __("閉じる", $this->plugins_name),
                "position" => __("ポジション", $this->plugins_name),
                "wait" => __("お待ちください...", $this->plugins_name),
                "link" => __("リンクを紹介", $this->plugins_name),
            ),
            // エラー文
            "errors" => array(
                "title" => __("次のエラーが発生しました。", $this->plugins_name),
                "validation_fail" => __("__type__ブロックが無効です", $this->plugins_name),
                "block_empty" => __("__name__は必ず値が必要です。", $this->plugins_name),
                "type_missing" => __("あなたが__type__タイプのブロックを持っている必要があります", $this->plugins_name),
                "required_type_empty" => __("__type__は必ず値の入力が必要ですが、空になっています。", $this->plugins_name),
                "load_fail" => __("コンテンツの内容を読み込み中に問題が発生しました", $this->plugins_name),
            ),
            // ブロック
            "blocks" => array(
                "tooltiptitle" => array(
                    "text" => __('ブロック', $this->plugins_name),
                ),
                "text" => array(
                    "title" => __("テキスト", $this->plugins_name),
                ),
                "list" => array(
                    "title" => __("リスト", $this->plugins_name),
                ),
                "quote" => array(
                    "title" => __("引用", $this->plugins_name),
                    "credit_field" => __("著者", $this->plugins_name),
                ),
                "image" => array(
                    "title" => __("画像", $this->plugins_name),
                    "upload_error" => __("画像のアップロードに失敗しました", $this->plugins_name),
                ),
                "video" => array(
                    "title" => __("動画", $this->plugins_name),
                ),
                "tweet" => array(
                    "title" => __("ツイート", $this->plugins_name),
                    "fetch_error" => __("ツイートを読み込むことが出来ませんでした。", $this->plugins_name),
                ),
                "embedly" => array(
                    "title" => __("埋め込み", $this->plugins_name),
                    "fetch_error" => __("あなたのビデオを取得中に問題が発生しました", $this->plugins_name),
                    "key_missing" => __("あなたは、関連するAPIキーを持っている必要があります", $this->plugins_name),
                ),
                "heading" => array(
                    "title" => __("見出し", $this->plugins_name),
                ),
                "tinymce" => array(
                    "title" => __("エディタ", $this->plugins_name),
                ),
                "columns" => array(
                    "title" => __("カラム", $this->plugins_name),
                ),
                "break" => array(
                    "title" => __("線", $this->plugins_name),
                ),
            ),
        );

        // アクションフックを仕込んでおく
        $translate = apply_filters('bce_register_languages', $translate);

        // JSON に変換
        $data = json_encode($translate);
        // サイトの言語を取得
        $locale = get_locale();

        // 無事 JSON に変換出来た場合はscript タグを出力
        if ($data) {
            echo <<<EOF
<script type="text/javascript">
(function($){
    if ( typeof SirTrevor !== "undefined" ){
        SirTrevor.Locales.$locale = $data;
    }
})(jQuery);
</script>
EOF;
        }

    }

}
