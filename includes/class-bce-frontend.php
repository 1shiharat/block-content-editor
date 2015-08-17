<?php
/**
 * フロントエンドでの編集の動作クラス
 */
if ( ! defined( 'WPINC' ) ) {
    die;
}
class BCE_Frontend
{

    /**
     * クラスの初期化
     *
     */
    public function __construct()
    {
        add_filter('the_content', array($this, 'replace_content'), 11, 1);
        add_action('wp_ajax_front-block-content-editor-save', array($this, 'post_save'));
    }


    /**
     * フロントエンドにて編集ができるように、the_content にフィルタをかける
     *
     *
     * @param $content
     * @return string
     */
    public function replace_content($content)
    {

        if (BCE_Utilis::check_edit_post()) {

            $post_id = get_the_ID();
            $block_content = get_post_meta($post_id, 'block_content', true);
            require_once WPINC . '/class-wp-editor.php';
            wp_editor('', 'content', array(
                'textarea_rows' => 15,
                'teeny' => true,
                'quicktags' => true,
                'media_buttons' => true,
            ));
            $save_text = __('Save', 'block-content-editor');
            $nonce = wp_create_nonce(__FILE__);
            $form = <<<FORM
<div id="front-block-content-editor-container" style="display: block; background: #fff;">
    <form name="front-block-content-editor-form" id="front-block-content-editor-form" action="?" method="post">
        <input type="hidden" name="front-block-content-editor-nonce" id="front-block-content-editor-nonce" value="$nonce" />
        <input type="hidden" name="front-block-content-editor-post_id" id="front-block-content-editor-post_id" value="$post_id" />
        <textarea name="block_content" id="front-block-content-editor" style="display: none;">$block_content</textarea>
        <div class="front-block-content-editor__submit">
            <button id="front-block-content-editor-submit" type="submit">$save_text</button>
        </div>
    </form>
    </div>
FORM;
            return $form;
        }
        return $content;

    }


    /**
     * Ajax で受けたフロントからのデータを、update_post_metaを使用し、post_metaへ保存する
     *
     * @return string $json array( 'status' => 0|1 )
     */
    public function post_save()
    {
        $success = 0;
        $data = isset($_POST['json']) ? $_POST['json'] : false;
        $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : false;
        $nonce = wp_verify_nonce($_POST['nonce'], __FILE__);

        if ($data && is_numeric($post_id) && $nonce) {
            $success = update_post_meta($post_id, 'block_content', $data);
        }

        echo wp_send_json(array('status' => $success));
        exit();
    }

}
