<?php
/**
 * ブロック例
 */
class BCE_List extends BCE_Block {

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
        $this->type = 'list';
        $this->class = 'list';
        $this->param = array( 'text' );
        $this->template = '
        <div class="block-editor__heading">
            <h1>%text%</h1>
        </div>
        ';
    }

    public function generate_html($data, $block_instance = null){
        $html = '<div class="bce-block bce-block__list"><ul>';
        foreach ( $data->listItems as $list ){
            $html .= '<li>' . $list->content . '</li>';
        }
        $html .= '</ul></div>';

        return $html;
    }
}

