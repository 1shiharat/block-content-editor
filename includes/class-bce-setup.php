<?php
/**
 * プラグインのセットアップ
 *
 * @package    Block_Content_Editor
 * @subpackage Block_Content_Editor/Setup
 * @author     1shiharat <akeome1369@gmail.com>
 */
if (!defined('WPINC')) {
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

        BCE_Utilis::set_option();

        // 自動最大高さ機能をオフ
        set_user_setting( 'editor_expand', 'off' );

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
        wp_enqueue_style($this->plugin_name . 'sir-trevor-css', plugin_dir_url(__FILE__) . '../assets/components/bce-trevor-js/build/sir-trevor.css', array(), $this->version, false);
        wp_enqueue_style($this->plugin_name . 'sir-trevor-css-icons', plugin_dir_url(__FILE__) . '../assets/components/bce-trevor-js/sir-trevor-icons.css', array(), $this->version, false);
    }

    /**
     * JavaScript の登録
     *
     * @since    1.0.0
     */
    public function enqueue_scripts($hook)
    {
        $options = BCE_Utilis::get_option();
        if (isset($options["bce_grid_system_framework"]) && "none" !== $options["bce_grid_system_framework"]) {
            switch ($options["bce_grid_system_framework"]) {
                case "foundation" :
                    wp_enqueue_style($this->plugin_name . 'grid-system', plugin_dir_url(__FILE__) . '../assets/css/foundation-grid.css', array(), $this->version, false);
                    break;
                case "bootstrap3" :
                    wp_enqueue_style($this->plugin_name . 'grid-system', plugin_dir_url(__FILE__) . '../assets/css/bootstrap-grid.css', array(), $this->version, false);
                    break;
                default :
                    break;
            }
        }

        if (!BCE_Utilis::is_enabled_editor()) {
            return false;
        }
        if ($hook === 'post-new.php' || $hook === 'post.php') {
            wp_enqueue_script($this->plugin_name . 'sir-trevor-js', plugin_dir_url(__FILE__) . '../assets/components/bce-trevor-js/build/sir-trevor.js', array('jquery', 'underscore'), $this->version, false);
            wp_enqueue_script($this->plugin_name . 'sir-trevor-js-eventable', plugin_dir_url(__FILE__) . '../assets/components/Eventable/eventable.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . '../assets/js/block-content-editor-admin.js', array('jquery'), $this->version, false);

            $types = array_map(function ($t) {
                return ucfirst($t);
            }, $this->blocks->get_types());

            $config = array(
                'config' => array(
                    'language' => get_locale(),
                    'debug' => true,
                    'scribeDebug' => true,
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
        wp_enqueue_style($this->plugin_name . 'sir-trevor-css', plugin_dir_url(__FILE__) . '../assets/components/bce-trevor-js/build/sir-trevor.css', array(), $this->version, false);
        wp_enqueue_style($this->plugin_name . 'sir-trevor-css-icons', plugin_dir_url(__FILE__) . '../assets/components/bce-trevor-js/sir-trevor-icons.css', array(), $this->version, false);

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
        wp_enqueue_script($this->plugin_name . 'sir-trevor-js', plugin_dir_url(__FILE__) . '../assets/components/bce-trevor-js/build/sir-trevor.js', array('jquery'), $this->version, false);
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
        if (!BCE_Utilis::is_enabled_editor()) {
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
                "delete" => __("削除しますか?", $this->plugin_name),
                "drop" => __("ここに__block__をドラッグ", $this->plugin_name),
                "paste" => __("またはここにリンクをコピー", $this->plugin_name),
                "upload" => __("...またはファイルを選択します", $this->plugin_name),
                "close" => __("閉じる", $this->plugin_name),
                "position" => __("ポジション", $this->plugin_name),
                "wait" => __("お待ちください...", $this->plugin_name),
                "link" => __("リンクを紹介", $this->plugin_name),
            ),
            // エラー文
            "errors" => array(
                "title" => __("次のエラーが発生しました。", $this->plugin_name),
                "validation_fail" => __("__type__ブロックが無効です", $this->plugin_name),
                "block_empty" => __("__name__は必ず値が必要です。", $this->plugin_name),
                "type_missing" => __("あなたが__type__タイプのブロックを持っている必要があります", $this->plugin_name),
                "required_type_empty" => __("__type__は必ず値の入力が必要ですが、空になっています。", $this->plugin_name),
                "load_fail" => __("コンテンツの内容を読み込み中に問題が発生しました", $this->plugin_name),
            ),
            // ブロック
            "blocks" => array(
                "tooltiptitle" => array(
                    "text" => __('ブロック', $this->plugin_name),
                ),
                "text" => array(
                    "title" => __("テキスト", $this->plugin_name),
                ),
                "list" => array(
                    "title" => __("リスト", $this->plugin_name),
                ),
                "quote" => array(
                    "title" => __("引用", $this->plugin_name),
                    "credit_field" => __("著者", $this->plugin_name),
                ),
                "image" => array(
                    "title" => __("画像", $this->plugin_name),
                    "upload_error" => __("画像のアップロードに失敗しました", $this->plugin_name),
                ),
                "video" => array(
                    "title" => __("動画", $this->plugin_name),
                ),
                "tweet" => array(
                    "title" => __("ツイート", $this->plugin_name),
                    "fetch_error" => __("ツイートを読み込むことが出来ませんでした。", $this->plugin_name),
                ),
                "embedly" => array(
                    "title" => __("埋め込み", $this->plugin_name),
                    "fetch_error" => __("あなたのビデオを取得中に問題が発生しました", $this->plugin_name),
                    "key_missing" => __("あなたは、関連するAPIキーを持っている必要があります", $this->plugin_name),
                ),
                "heading" => array(
                    "title" => __("見出し", $this->plugin_name),
                ),
                "tinymce" => array(
                    "title" => __("エディタ", $this->plugin_name),
                ),
                "columns" => array(
                    "title" => __("カラム", $this->plugin_name),
                ),
                "break" => array(
                    "title" => __("線", $this->plugin_name),
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
