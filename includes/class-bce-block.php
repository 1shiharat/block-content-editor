<?php
/**
 * Class BCE_Block
 *
 * すべてのブロックはこのクラスを継承して生成
 *
 */
if ( ! defined( 'WPINC' ) ) {
    die;
}

class BCE_Block
{
    /**
     * ブロックタイプ
     * @var
     */
    public $type;

    /**
     * ブロックのパラメータ
     * @var array
     */
    public $param = array();

    /**
     * HTMLのテンプレート
     * @var
     */
    public $template;

    /**
     * ブロックの保存されているデータ
     * @var
     */
    public $data;

    /**
     * 管理画面で読み込む JavaScriptファイルへのパス
     * @var array
     */
    public $admin_javascript = array();

    /**
     * 管理画面で読み込むCSSファイルへのパス
     * @var array
     */
    public $admin_css = array();

    /**
     * フロントのJavaScriptファイル
     * @var array
     */
    public $public_javascript = array();

    /**
     * アイコン
     * @var array
     */
    public $icon = array();

    /**
     * フロントで読み込むCSS
     * @var array
     */
    public $public_css = array();


    /**
     * 初期化
     */
    public function __construct()
    {
        // すべてのブロックの init メソッドを実行
        $this->init();

        // すべてのブロックで指定されている管理画面用JavaScriptファイルを読み込み
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_script'), 10, 1);
        add_action('wp_enqueue_scripts', array($this, 'admin_enqueue_script'), 10, 1);

        // 公開用として指定されているJavaScriptファイルを読み込み
        add_action('wp_enqueue_scripts', array($this, 'public_enqueue_script'), 10, 1);
    }

    /**
     * 子クラスの初期化メソッド
     */
    protected function init()
    {

    }

    /**
     * HTMLへ変換
     *
     * BCE_Parser クラスにて記述
     *
     * @param $data
     * @return mixed
     */
    public function generate_html($data)
    {
        if ($this->template) {
            return BCE_Parser::template($this->template, $data);
        }
    }

    /**
     * フロント画面の静的ファイルを登録
     *
     * @param $hook
     * @return bool
     */
    public function public_enqueue_script($hook)
    {

        if (!BCE_Utilis::is_enabled_editor()) {
            return false;
        }
        if (is_array($this->public_javascript) && $this->public_javascript) {
            foreach ($this->public_javascript as $i => $js_path) {
                wp_enqueue_script($this->type . '_' . $i . '_' . 'js', $js_path, array());
            }
        }

        if (is_array($this->public_css) && $this->public_css) {
            foreach ($this->public_css as $i => $css_path) {
                wp_enqueue_style($this->type . '_' . $i . '_' . 'css', $css_path, array());
            }
        }


    }

    /**
     * 管理画面の静的ファイルを登録
     *
     * @param $hook
     * @return bool
     */
    public function admin_enqueue_script($hook)
    {

        if (!BCE_Utilis::is_enabled_editor()) {
            return false;
        }

        if (isset($this->admin_javascript) && is_array($this->admin_javascript)) {
            foreach ($this->admin_javascript as $i => $js_path) {
                wp_enqueue_script($this->type . '_' . $i . '_' . 'js', $js_path, array(), null, true);
                if ($this->type == 'tinymce') {
                    wp_localize_script('tinymce_1_js', 'bce_tinymce_url', array('url' => plugin_dir_url(__FILE__) . 'tinymce/'));
                }
            }
        }

        if (isset($this->admin_css) && is_array($this->admin_css)) {
            foreach ($this->admin_css as $i => $css_path) {
                wp_enqueue_style($this->type . '_' . $i . '_' . 'css', $css_path, array());
            }
        }
    }


}
