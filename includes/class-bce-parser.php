<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
class BCE_Parser {

    /**
     * 初期化
     */
    public function __construct(){
        $this->blocks = BCE_Blocks::get_instance();
    }

    /**
     * テンプレートをコンパイル
     *
     * %property% の形式でプロパティを展開する
     *
     * @param $template
     * @param array $data
     * @return mixed
     */
    static public function template($template, $data = array())
    {
        return preg_replace_callback('/\%(\w+)%/', function ($m) use ($data) {
            if (!isset($data->$m[1])) {
                return '';
            }
            return $data->$m[1];
        }, $template);
    }

}

