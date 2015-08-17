<?php
/**
 * ユーティティクラス
 */
if ( ! defined( 'WPINC' ) ) {
    die;
}
class BCE_Utilis
{

    static public $version = '1.0.0';

    static public $plugin_name = 'block-content-editor';

    /**
     * 有効な投稿タイプを返す
     *
     * @return mixed|void
     */
    static public function get_enabled_post_type()
    {
        return apply_filters('bce_post_types', array('post', 'page'));
    }

    /**
     * エディタが有効かどうか判断する
     *
     * @return bool
     */
    static public function is_enabled_editor()
    {

        if (is_admin()) {
            $screen = get_current_screen();
            $post_types = self::get_enabled_post_type();
            foreach ($post_types as $type) {
                if ($screen->id === $type) {
                    return true;
                }
            }

        } else {
            return (isset($_GET['blockcontenteditor']) && $_GET['blockcontenteditor'] == 'true');
        }

        return false;
    }

    /**
     * バージョンを取得する
     *
     * @return string $version 現在のプラグインのバージョン
     */
    static public function get_version()
    {
        return self::$version;
    }

    /**
     * プラグイン名を取得
     *
     * @return string $plugin_name 現在のプラグインのバージョン
     */
    static public function get_plugin_name()
    {
        return self::$plugin_name;
    }

    /**
     * プラグインのベースとなるディレクトリパスを取得
     *
     * @return string $path ディレクトリへのパス
     */
    static public function get_base_dir()
    {
        $path = realpath(dirname(__FILE__) . '/../');
        return $path;
    }

    /**
     * プラグインフォルダのディレクトリ名を取得
     *
     * @return string
     */
    static public function get_base_dir_name()
    {
        $name = basename(realpath(dirname(__FILE__) . '/../'));
        return $name;
    }

    /**
     * プラグインディレクトリのURLを取得
     *
     * @return string
     */
    static public function get_plugin_url()
    {
        $dir_name = self::get_base_dir_name();
        return get_option('site_url') . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $dir_name;
    }

    /**
     * ブロックへのURLを取得
     * @return string
     */
    static public function get_blocks_url()
    {
        return self::get_plugin_url() . DIRECTORY_SEPARATOR . 'blocks';
    }

    /**
     * フロントエンドで記事を編集して良いが判断する関数
     *
     * @todo nonce を利用する
     * @return bool
     */
    static public function check_edit_post()
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

}
