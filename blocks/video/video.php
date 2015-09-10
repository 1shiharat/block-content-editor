<?php

/**
 * ブロック例
 */
class BCE_Video extends BCE_Block
{

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

    public function init()
    {
        $this->type = 'video';
        $this->class = 'video';
        $this->param = array('source', 'remote_id');
    }

    public function generate_html($data,$block_instance = null)
    {
        $html = '<div class="bce-block bce-block__video">';
        if ( $data->source == 'youtube' ) {
            $html = '<iframe src="//www.youtube-nocookie.com/embed/' . $data->remote_id . '" frameborder="0" allowfullscreen=""></iframe>';
        } elseif( $data->source == 'vimeo' ) {
            $html = '<iframe src="http://player.vimeo.com/video/' . $data->remote_id . '?title=0&byline=0" frameborder="0" allowfullscreen=""></iframe>';
        }
        $html .= '</div>';
        return $html;
    }
}
