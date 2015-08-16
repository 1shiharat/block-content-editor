<?php

/**
 * ブロック例
 */
class BCE_Wpimage extends BCE_Block
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
        add_action('init',function(){
            wp_enqueue_media();
        });
        $this->type = 'wpimage';
        $this->class = 'wpimage';
        $this->param = array('url','alt');
        $this->admin_javascript = array( plugin_dir_url(__FILE__) . 'wpimage.js');
        $this->template = '
        <div class="bce-block bce-block__image">
            <img src="%url%" alt="%alt%"/>
        </div>
        ';

        add_action('wp_ajax_stwp_nonce', array($this, 'wp_ajax_stwp_nonce'));
        add_action('wp_ajax_stwp_imgurl', array($this, 'wp_ajax_stwp_imgurl'));
    }

    /**
     * 画像登録用のnonceフィールドを返す
     */
    public function wp_ajax_stwp_nonce()
    {
        if (strpos($_SERVER['HTTP_REFERER'], get_site_url()) == 0 && current_user_can('edit_posts'))
            echo wp_create_nonce('media-form');
        die();
    }


    /**
     * アップロード完了した画像のURLを返す
     */
    public function wp_ajax_stwp_imgurl()
    {
        if (strpos($_SERVER['HTTP_REFERER'], get_site_url()) == 0 && current_user_can('edit_posts')) {
            $image_id = isset($_GET['id']) ? intval($_REQUEST['id']) : 0;
            $imagefull = wp_get_attachment_image_src($image_id, 'full');
            $imagedisp = wp_get_attachment_image_src($image_id, 'large');
            echo json_encode(array('full' => $imagefull[0], 'disp' => $imagedisp[0]));
        }
        die();
    }
}

?>
