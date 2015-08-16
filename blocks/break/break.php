<?php
/**
 * ブロック例
 */
class BCE_Break extends BCE_Block{

    /**
     * ブロックタイプ
     * @var
     */
    public $type;

    /**
     * ブロックのクラス
     * @var
     */
    public $class;

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

    public function init(){
        $this->type = 'break';
        $this->class = 'break';
        $this->param = array( 'text' );
        $this->admin_javascript = array(
            plugin_dir_url( __FILE__ ) . 'break.js'
        );
        $this->public_javascript = array(
            plugin_dir_url( __FILE__ ) . 'break.js'
        );
        $this->template = '
        <div class="bce-block bce-block__break">
            <hr />
        </div>
        ';
    }
}

?>
