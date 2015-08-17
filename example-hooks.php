<?php
/**
 * アクションフックの例
 */

/**
 * カラムブロックの総数を変更
 *
 * Bootstrap、Foundationに対応しています。
 * グリッドの総数を変更した場合は、このアクションフックを利用して
 * グリッドの総数をプラグインに登録してください。
 * デフォルトは12個です。
 */
add_filter('bce_blocks_type_columns_grid_num', function ($base) {
    return 24;
});

/**
 * ブロックを追加
 *
 * ブロックはテーマフォルダに作成し、このアクションフックで登録することで追加できます。
 *
 * /wp-content/
 *     themes/
 *         hogehoge/
 *             example/
 *                 example.php
 *                 example.js
 *
 * 詳細はサンプルブロックを参照してください。
 * @see blocks/example/example.php
 *
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
 * Sir Trevor のデバッグモードを有効にします。
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
