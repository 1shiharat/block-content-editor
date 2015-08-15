(function ($) {
    'use strict';

    /* global tinymce, tinyMCEPreInit, QTags, setUserSetting */
    var BlockContentEditor = (new function(){

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
                SirTrevor.config.debug = true;
                SirTrevor.config.scribeDebug = true;
                SirTrevor.setDefaults({
                    uploadUrl: '/wp-admin/media-new.php'
                });
            },

            // エディターを反映
            editorInit : function(){
                self.prototype.editorSetting();
                self.editor = new SirTrevor.Editor({
                    el: $(self.options.editorTarget),
                    blockTypes: self.options.editorBlockType,
                });
            },

            // データ保存時のイベント
            eventEditorSave : function(){
                $('#front-block-content-editor-submit').on('submit', function (e) {
                    e.preventDefault();
                    $('#front-block-content-editor').text(self.editor.store.toString(true));
                    $.ajax({
                        url : ajaxurl,
                        type: post,
                        data: {
                            action: 'front-block-content-editor-save',
                            json : $('#front-block-content-editor').text(),
                        },
                    }).done(function(res){
                        console.log(res);
                        alert('保存に成功しました');
                    }).fail(function(res){
                        console.log(res);
                    }).always(function(res){
                        console.log(res);
                    })

                });
            },
        }
        return self;


    });

    $(function () {
        window.BlockContentEditor = new BlockContentEditor();
    });


})(jQuery);
