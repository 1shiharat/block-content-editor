<?php
/**
 * カラム
 */
class BCE_Columns extends BCE_Block{

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

        $this->framework = apply_filters( 'bce_blocks_type_columns_framework', 'foundation' );

        $this->type = 'columns';

        $this->class = 'columns';

        $this_dir_url = plugin_dir_url(__FILE__);
        $this->admin_javascript = array(
            $this_dir_url . 'columns-block.js',
        );
        $this->admin_css = array(
            $this_dir_url . 'columns-block.all.css',
        );
    }

    public function generate_html($data, $block_instance){

        $html = '<div class="bce-block bce-block__columns ' . $this->get_row_markup() . '">';
        foreach( $data->columns as $key => $column ){
            $width = $column->width;
            $blocks = $column->blocks;
            $html .= '<div class="bce-block__columns__item ' . $this->get_grid_classname( $width ) . '">';
            foreach( $blocks as $block ) {
                $html .= $block_instance[$block->type]->generate_html($block->data, $this->blocks);
            }
            $html .= '</div>';
        }
        $html .= '</div>';
        return $html;
    }

    public function get_row_markup(){
        if ( $this->framework == 'foundation' ){
            return "row";
        } else if ( $this->framework == 'bootstrap' ){
            return "row";
        }
    }

    public function get_grid_classname( $num ){
        if ( $this->framework == 'foundation' ){
            return "columns large-" . $this->calc_num($num);
        } else if ( $this->framework == 'bootstrap' ){
            return "col-lg-" . $this->calc_num($num);
        }
    }

    /**
     * グリッドの比率に合わせて出力を変更
     * add_filter( 'bce_blocks_type_columns_grid_num', function(){
     *   return 24; // 24 個のグリッド
     * });
     * @param $num
     * @return mixed|void
     */
    public function calc_num($num){
        $base = 12;
        $base_num = $num/$base;
        $grid_num = apply_filters( 'bce_blocks_type_columns_grid_num', 12 );
        return $grid_num * $base_num;
    }

}

?>
