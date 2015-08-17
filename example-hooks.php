<?php
/**
 * アクションフックの例
 */


/**
 * カラムブロックの総数を変更
 */
add_filter('bce_blocks_type_columns_grid_num', function ($base) {
    return 24;
});

/**
 * ブロックを追加
 */
add_filter('bce_blocks_types', function ($types) {

    // return $types[] = 'faq'; でもOK
    return array_merge($types, array(
        'faq'
    ));
});

/**
 * カスタム投稿タイプに対応
 */
add_filter('bce_post_types', function ($post_types) {
    return array_merge($post_types, array(
        'reform',
    ));
});


/**
 * Sir Trevor のデバッグモードを有効に
 */
add_filter('bce_editor_debug', function ($post_types) {
    return true;
});


/**
 * $post->post_content の強制更新をオフに
 *
 * これにより、カスタムフィールドのみにJSONの値を保存します。
 */
add_filter('bce_force_replace_post_content', function () {
    return false;
});
