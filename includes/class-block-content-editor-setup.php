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
     * 現在のプラグインのバージョン
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version のバージョン
     */
    private $version;

    /**
     * 初期化
     *
     * @since    1.0.0
     * @param      string $plugin_name プラグインの名称
     * @param      string $version プラグインのバージョン
     */
    public function __construct($plugin_name, $version, $blocks)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->blocks = $blocks;
        add_filter('the_editor', array($this, 'the_editor'), 10, 1);
        add_action( 'wp_footer', array( $this, 'output_locales' ) );
        add_action( 'admin_footer', array( $this, 'output_locales' ) );

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

        if ($hook === 'post-new.php' || $hook === 'post.php') {
            wp_enqueue_script($this->plugin_name . 'sir-trevor-js', plugin_dir_url(__FILE__) . '../assets/components/sir-trevor-js/build/sir-trevor.js', array('jquery', 'underscore'), $this->version, false);
            wp_enqueue_script($this->plugin_name . 'sir-trevor-js-eventable', plugin_dir_url(__FILE__) . '../assets/components/Eventable/eventable.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . '../assets/js/block-content-editor-admin.js', array('jquery'), $this->version, false);

            $types = array_map(function($t) {
                return ucfirst($t);
            } , $this->blocks->get_types() );

            $config = array(
                'config' => array(
                    'language' => get_locale(),
                    'debug' => false,
                    'scribeDebug' => false,
                    'uploadUrl' => admin_url( '/media-new.php' ),
                ),
                'blockTypes' => $types,
            );
            wp_localize_script( $this->plugin_name, 'BCEConfig', $config );
        }
    }

    /**
     * スタイルシートの登録
     *
     * @since    1.0.0
     */
    public function front_enqueue_styles()
    {
        if ( self::is_front_editor() ) {
            wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . '../assets/css/block-content-editor-front.css', array(), $this->version, 'all');
            wp_enqueue_style($this->plugin_name . 'sir-trevor-css', plugin_dir_url(__FILE__) . '../assets/components/sir-trevor-js/build/sir-trevor.css', array(), $this->version, false);
            wp_enqueue_style($this->plugin_name . 'sir-trevor-css-icons', plugin_dir_url(__FILE__) . '../assets/components/sir-trevor-js/sir-trevor-icons.css', array(), $this->version, false);
        }
    }

    /**
     * JavaScript の登録
     *
     * @since    1.0.0
     */
    public function front_enqueue_scripts($hook)
    {
        if ( self::is_front_editor() ) {
            wp_enqueue_script($this->plugin_name . 'sir-trevor-js', plugin_dir_url(__FILE__) . '../assets/components/sir-trevor-js/build/sir-trevor.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->plugin_name . 'sir-trevor-js-eventable', plugin_dir_url(__FILE__) . '../assets/components/Eventable/eventable.js', array('jquery'), $this->version, false);
            wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . '../assets/js/block-content-editor-front.js', array('jquery'), $this->version, false);
        }
    }

    /**
     * フロントエディタが有効か判定
     * @return bool
     */
    static public function is_front_editor(){
        return ( isset( $_GET['blockcontenteditor'] ) && $_GET['blockcontenteditor'] == 'true' );
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
     * 翻訳情報をフッターに出力
     */
    public function output_locales(){
        $translate = array(
            // 基本的な設定
            "general" => array(
                "delete" => __( "削除しますか?", "block-content-editor" ),
                "drop" => __( "ここに__block__をドラッグ", "block-content-editor" ),
                "paste" => __( "またはここにリンクをコピー", "block-content-editor" ),
                "upload" => __( "...またはファイルを選択します", "block-content-editor" ),
                "close" => __( "閉じる", "block-content-editor" ),
                "position" => __( "ポジション", "block-content-editor" ),
                "wait" => __( "お待ちください...", "block-content-editor" ),
                "link" => __( "リンクを紹介", "block-content-editor" ),
            ),
            // エラー文
            "errors" => array(
                "title" => __( "次のエラーが発生しました。", "block-content-editor" ),
                "validation_fail" => __( "__type__ブロックが無効です", "block-content-editor" ),
                "block_empty" => __( "__name__は必ず値が必要です。", "block-content-editor" ),
                "type_missing" => __( "あなたが__type__タイプのブロックを持っている必要があります", "block-content-editor" ),
                "required_type_empty" => __( "__type__は必ず値の入力が必要ですが、空になっています。", "block-content-editor" ),
                "load_fail" => __( "コンテンツの内容を読み込み中に問題が発生しました", "block-content-editor" ),
            ),
            // ブロック
            "blocks" => array(
                "tooltiptitle" => array(
                  "text" => __( 'ブロック', "block-content-editor" ),
                ),
                "text" => array(
                    "title" => __( "テキスト", "block-content-editor" ),
                ),
                "list" => array(
                    "title" => __( "リスト", "block-content-editor" ),
                ),
                "quote" => array(
                    "title" => __( "引用", "block-content-editor" ),
                    "credit_field" => __( "著者", "block-content-editor" ),
                ),
                "image" => array(
                    "title" => __( "画像", "block-content-editor" ),
                    "upload_error" => __( "画像のアップロードに失敗しました", "block-content-editor" ),
                ),
                "video" => array(
                    "title" => __( "動画", "block-content-editor" ),
                ),
                "tweet" => array(
                    "title" => __( "ツイート", "block-content-editor" ),
                    "fetch_error" => __( "ツイートを読み込むことが出来ませんでした。", "block-content-editor" ),
                ),
                "embedly" => array(
                    "title" => __( "埋め込み", "block-content-editor" ),
                    "fetch_error" => __( "あなたのビデオを取得中に問題が発生しました", "block-content-editor" ),
                    "key_missing" => __( "あなたは、関連するAPIキーを持っている必要があります", "block-content-editor" ),
                ),
                "heading" => array(
                    "title" => __( "見出し", "block-content-editor" ),
                ),
                "tinymce" => array(
                    "title" => __( "エディタ", "block-content-editor" ),
                ),
                "columns" => array(
                    "title" => __( "カラム", "block-content-editor" ),
                ),
                "break" => array(
                    "title" => __( "線", "block-content-editor" ),
                ),
            ),
        );

        // アクションフックを仕込んでおく
        $translate = apply_filters( 'bce_editor_locales', $translate );

        // JSON に変換
        $data = json_encode($translate);
        // サイトの言語を取得
        $locale = get_locale();

        // 無事 JSON に変換出来た場合はscript タグを出力
        if ( $data ){
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
