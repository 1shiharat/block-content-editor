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
        spl_autoload_register(array($this, 'bce_autoloader'));
        $this->set_types();
        $this->set_blocks();

        add_filter('the_content', array($this, 'the_content'), 11, 1);
        add_filter('the_content', array($this, 'filter_post_content'), 10, 1);
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
        $block_content = $this->get_contents();

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
     * ブロックをオートロードする
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

    public function the_content($content)
    {

        if (isset($_GET['blockcontenteditor']) && $_GET['blockcontenteditor'] === 'true') {
            $post_id = get_the_ID();
            $block_content = get_post_meta($post_id, 'block_content', true);


            require_once WPINC . '/class-wp-editor.php';
            wp_editor( '', 'front_editor_init', array(
                'textarea_rows' => 15,
                'teeny' => true,
                'quicktags' => false,
                'media_buttons' => true,
            ) );
            return '<div id="front-block-content-editor-container" style="display: block; background: #fff;"><form><textarea name="block_content" id="front-block-content-editor" style="display: none;">' . $block_content . '</textarea><div class="front-block-content-editor__submit"><button id="front-block-content-editor-submit" type="submit">'. __( 'Save', 'block-content-editor' ) .'</button></div></form></div>';
        }
        return $content;

    }

}
