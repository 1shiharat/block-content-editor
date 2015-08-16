<?php
/**
 * ブロックの例
 *
 * 新しいブロックを作成したい時には、このクラスを参考にブロックを追加できます。
 * 但し、PHPと別に JavaScript も作成する必要があります。
 *
 */
class Example extends BCE_Block{

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


    /**
     * ブロックの初期化
     */
    public function init(){

        // タイプ名をセット
        $this->type = 'heading';

        // クラス名をセット
        $this->class = 'heading';

        // パラメーターをセット
        $this->param = array( 'text' );

        /**
         * テンプレートをセット
         * パラメータで指定した値のプロパティに変換します。
         *
         * @see BCE_Block->template method
         */
        $this->template = '
        <div class="block-editor__heading">
            <h1>%text%</h1>
        </div>
        ';
    }
}
