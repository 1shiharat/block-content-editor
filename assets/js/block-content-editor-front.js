(function ($) {
    'use strict';

    /* global tinymce, tinyMCEPreInit, QTags, setUserSetting */
    var BlockContentEditor = (new function(){

        var editor;
        var self = function(){

            self.options = {
                editorTarget: "#front-block-content-editor",
                editorTargetContainer : "#front-block-content-editor-container",
                editorBlockType: ["Heading", "Text", "Tinymce", "Wpimage", "List", "Video", "Code","Quote", "Columns"],
            }
            //self.prototype.mediaUpload();
            self.prototype.editorInit();
            self.prototype.eventEditorSave();
            //self.prototype.eventEditorTabs();
            //self.prototype.editorTabs();
        };

        self.prototype = {
            // 設定
            editorSetting : function(){
                SirTrevor.config.language = 'ja';
                //SirTrevor.config.debug = true;
                //SirTrevor.config.scribeDebug = true;
                SirTrevor.setDefaults({
                    uploadUrl: '/wp-admin/media-new.php'
                });
            },

            // エディターを反映
            editorInit : function(){
                self.prototype.editorSetting();
                self.prototype.editor = new SirTrevor.Editor({
                    el: $(self.options.editorTarget),
                    blockTypes: self.options.editorBlockType,
                });
            },

            // データ保存時のイベント
            eventEditorSave : function(){

                $('#front-block-content-editor-form').on('submit', function (e) {
                    e.preventDefault();
                    var json = self.prototype.editor.store.toString(true);
                    $('#front-block-content-editor').text(json);
                    $.ajax({
                        url : ajaxurl,
                        type: 'post',
                        data: {
                            action: 'front-block-content-editor-save',
                            json : json,
                            nonce: $('#front-block-content-editor-nonce').val(),
                            post_id: $('#front-block-content-editor-post_id').val(),
                        },
                    }).done(function(res){
                        if ( res.status == true ){
                            alert('記事を保存しました');
                        } else if ( res.status == false ){
                            alert('記事の保存に失敗しました。');
                        }
                    }).fail(function(res){
                        alert('記事の保存に失敗しました。');
                    }).always(function(res){

                    });

                });
            },
        }
        return self;


    });

    $(function () {
        window.BlockContentEditor = new BlockContentEditor();
    });


})(jQuery);
