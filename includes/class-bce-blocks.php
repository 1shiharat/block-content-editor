<?php

require_once "class-bce-block.php";

class BCE_Blocks
{

    public $plugin_name = '';

    public $types = array();

    public $meta_key = 'block_contents';

    private static $instance = null;

    /**
     * クラスの初期化
     */
    public function __construct()
    {
        $this->plugin_name = BCE_Utilis::get_plugin_name();

        $this->force_post_content_save = apply_filters( 'bce_force_replace_post_content', true );

        spl_autoload_register(array($this, 'bce_autoloader'));

        $this->set_types();
        $this->set_blocks();

        add_filter('the_content', array($this, 'filter_post_content'), 10, 1);

        add_action('save_post', array($this, 'block_content_update'), 10, 1);
        add_action('wp_enqueue_scripts', array($this, 'output_localize_script'), 0, 1);
        add_action('admin_enqueue_scripts', array($this, 'output_localize_script'), 0, 1);
    }


    /**
     * シングルトンインスタンスを取得
     *
     * @return BCE_Blocks|null
     */
    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }


    /**
     * JavaScript へ値を渡すためのメソッド
     */
    public function output_localize_script()
    {
        if ( ! BCE_Utilis::is_enabled_editor() ){
            return false;
        }

        $types = array_map(function ($t) {
            return ucfirst($t);
        }, $this->get_types());

        $config = array(
            'config' => array(
                'language' => get_locale(),
                'debug' => false,
                'scribeDebug' => false,
                'uploadUrl' => admin_url('/media-new.php'),
            ),
            'blockTypes' => $types,
        );
        $id = $this->plugin_name;
        wp_localize_script($id, 'BCEConfig', $config);
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
            'columns',
            'break',
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
     *
     * @param $type
     */
    public function set_type($type)
    {
        $this->types = array_merge($this->types, array($type));
    }

    /**
     * 各ブロックのインスタンスを生成し、blocks プロパティに格納する
     *
     * @return void
     */
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
     *
     * @param $content
     * @return string
     */
    public function filter_post_content($content)
    {
        // 強制更新がONの場合はそのまま返す
        if ( $this->force_post_content_save ){
            return $content;
        } else {
            // オフの場合は、カスタムフィールドからHTMLを取得
            $block_content = get_post_meta(get_the_ID(), 'block_content_html', true);
            if ($block_content) {
                return $block_content;
            }
        }

        return $content;
    }


    /**
     * ブロックコンテンツを取得
     *
     * カスタムフィールドからJSONを取得し、HTMLへと変換後、値を返す
     *
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
     * 各ブロックの generate_html メソッドを呼出し、HTMLへと変換後、値を返す
     *
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
     *
     * 擬似名前空間として "BCE_" のプレフィックスを持つものに関してのみ、ファイルが存在する場合には読み込む
     *
     * @return void
     */
    public function bce_autoloader($class_name)
    {
        if (strpos('BCE_', $classname) >= 0) {
            $class_name = str_replace('BCE_', '', $class_name);
            $themeclassesdir = get_template_directory() . DIRECTORY_SEPARATOR . 'blocks' . DIRECTORY_SEPARATOR . lcfirst($class_name) . DIRECTORY_SEPARATOR;
            $classbasedir = BCE_Utilis::get_base_dir() . '/blocks';
            $classes_dir = $classbasedir . DIRECTORY_SEPARATOR . lcfirst($class_name) . DIRECTORY_SEPARATOR;
            $class_file = str_replace('_', DIRECTORY_SEPARATOR, lcfirst($class_name)) . '.php';
            if (file_exists($themeclassesdir . $class_file)) {
                require_once $themeclassesdir . $class_file;
            } else if (file_exists($classes_dir . $class_file)) {
                require_once $classes_dir . $class_file;
            }
        }

    }


    /**
     * 記事を保存するタイミングで、カスタムフィールドとしてブロックエディタのコンテンツを保存する
     * @param $post_id
     *
     * @return bool
     */
    public function block_content_update($post_id)
    {
        remove_action('save_post', array($this, 'block_content_update'));

        if ( ! BCE_Utilis::is_enabled_editor() ){
            return false;
        }

        $block_content = isset($_REQUEST['block_content']) ? $_REQUEST['block_content'] : '';

        if (!$block_content) {
            return false;
        }

        update_post_meta($post_id, 'block_content', $block_content);

        /**
         * 強制保存が有効な場合は投稿コンテンツに保存。
         * それ以外の場合には、カスタムフィールドに値を保存する
         **/
        if ($this->force_post_content_save == true) {
            wp_update_post(array(
                'ID' => $post_id,
                'post_content' => $this->get_contents($post_id),
            ));
        } else {
            update_post_meta($post_id, 'block_content_html', $this->get_contents($post_id));
        }
        add_action('save_post', array($this, 'block_content_update'));
    }

}
