<?php

require_once "class-bce-block.php";

class BCE_Blocks
{

    public $plugin_name = '';

    // ブロックのタイプ
    public $types = array();

    public $meta_key = 'block_contents';

    /**
     * 初期化
     * @param $plugin_name
     */
    public function __construct($plugin_name)
    {
        $this->plugin_name = $plugin_name;

        $this->force_post_content_save = true;

        spl_autoload_register(array($this, 'bce_autoloader'));
        $this->set_types();
        $this->set_blocks();

        add_filter('the_content', array($this, 'the_content'), 11, 1);
        add_filter('the_content', array($this, 'filter_post_content'), 10, 1);
        add_action('wp_ajax_front-block-content-editor-save', array($this, 'post_save'));
        add_action('save_post', array($this, 'block_content_update'), 10, 1);
    }

    /**
     * ブロックのタイプを設定
     */
    public function set_types()
    {

        /**
         * デフォルトのブロックタイプ
         */
        $types = array(
            'heading',
            'text',
            'wpimage',
            'tinymce',
            'quote',
            'list',
            'video',
            'columns'
        );

        /**
         * フィルターを追加
         * フィールドを外部から追加できるように
         */
        $this->types = apply_filters('bce_blocks_types', $types);

        return $this->types;
    }

    /**
     * ブロックタイプを取得
     * @return array
     */
    public function get_types()
    {
        return $this->types;
    }

    /**
     * 一つのブロックタイプをセット
     * @param $type
     */
    public function set_type($type)
    {
        $this->types = array_merge($this->types, array($type));
    }

    public function set_blocks()
    {
        foreach ($this->types as $type) {
            $classname = 'BCE_' . ucfirst($type);
            if (class_exists($classname, true)) {
                $this->blocks[$type] = new $classname();
            }
        }
    }

    /**
     * post_content フィルターにかませる
     * @param $content
     * @return string
     */
    public function filter_post_content($content)
    {
        $block_content = get_post_meta( get_the_ID(), 'block_content_html', true );

        if ($block_content) {
            return $block_content;
        }

        return $content;
    }


    /**
     * コンテンツを取得
     * @param int $post_id
     * @return string
     */
    public function get_contents($post_id = 0)
    {

        if (!$post_id) {
            $post_id = get_the_ID();
        }

        $block_content = get_post_meta($post_id, 'block_content', true);

        if ($block_content) {
            $html = '';
            $blocks = json_decode($block_content);
            foreach ($blocks->data as $block) {
                $type = $block->type;
                $data = $block->data;
                $html .= $this->generate($type, $data);

            }
            return $html;
        }
        return "";
    }

    /**
     * 変換
     * @param $type
     * @param $data
     * @return string
     */
    public function generate($type, $data)
    {
        if (isset($this->blocks[$type])) {
            return $this->blocks[$type]->generate_html($data, $this->blocks);

        }
        return "";
    }

    /**
     * ブロッククラスのオートロード
     */
    public function bce_autoloader($class_name)
    {
        if (strpos('BCE_', $classname) >= 0) {
            $class_name = str_replace('BCE_', '', $class_name);
            $classes_dir = realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR . lcfirst($class_name) . DIRECTORY_SEPARATOR;
            $class_file = str_replace('_', DIRECTORY_SEPARATOR, lcfirst($class_name)) . '.php';
            if (file_exists($classes_dir . $class_file)) {
                require_once $classes_dir . $class_file;
            }
        }

    }

    /**
     * コンテンツをエディタに置換
     * @param $content
     * @return string
     */
    public function the_content($content)
    {

        if ($this->check_edit_post()) {

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
     * 編集していい記事がチェック
     * @return bool
     */
    public function check_edit_post()
    {

        $vaild = true;
        $this_post_id = get_the_ID();

        if (!isset($_GET['blockcontenteditor']) || $_GET['blockcontenteditor'] !== 'true') {
            $vaild = false;
        }

        if (isset($_SERVER['HTTP_REFERER'])) {
            $url = parse_url($_SERVER['HTTP_REFERER']);
            if (isset($url['query'])) {
                parse_str($url['query']);
                $post_id = (isset($post)) ? intval($post) : false;
            } else {
                $vaild = false;
            }
            if ($post_id !== $this_post_id) {
                $vaild = false;
            }
        } else {
            $vaild = false;
        }
        return $vaild;
    }

    /**
     * データを保存
     */
    public function post_save()
    {
        $success = 0;
        $data = isset( $_POST['json'] ) ? $_POST['json'] : false;
        $post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : false;
        $nonce = wp_verify_nonce( $_POST['nonce'], __FILE__ );

        if ( $data && is_numeric( $post_id ) && $nonce ){
            $success = update_post_meta( $post_id, 'block_content' , $data );
        }

        echo wp_send_json( array( 'status' => $success ) );
        exit();
    }

    /**
     * 記事を保存するタイミングで、カスタムフィールドとしてブロックエディタのコンテンツを保存する
     * @param $post_id
     */
    public function block_content_update($post_id)
    {
        remove_action('save_post', array($this, 'block_content_update'));
        $block_content = isset($_REQUEST['block_content']) ? $_REQUEST['block_content'] : '';
        update_post_meta($post_id, 'block_content', $block_content);
        update_post_meta($post_id, 'block_content_html', $this->get_contents($post_id));
        if ( $this->force_post_content_save == true ){
            wp_update_post(array(
                'ID' => $post_id,
                'post_content' => $this->get_contents($post_id),
            ));
        }


        add_action('save_post', array($this, 'block_content_update'));
    }
}
