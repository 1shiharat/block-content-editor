(function ($) {
    'use strict';

    /* global tinymce, tinyMCEPreInit, QTags, setUserSetting */
    var BlockContentEditor = (new function(){

        var self = function(){

            self.options = {
                editorTarget: "#block-content-editor",
                editorTargetContainer : "#block-content-editor-container",
                editorBlockType: ["Heading", "Text", "Tinymce", "Wpimage", "List", "Video", "Code","Quote", "Columns"],
            }
            self.prototype.mediaUpload();
            self.prototype.editorInit();
            self.prototype.eventEditorSave();
            self.prototype.eventEditorTabs();
            self.prototype.editorTabs();
            self.prototype.addFrontEditButton();
        };

        self.prototype = {

            addFrontEditButton: function(){
                var url = $('#view-post-btn a').attr('href');
                var btn = $('<a>').attr('href', url + '?blockcontenteditor=true').text('記事画面で編集').addClass('button').fadeIn('100').css({display: "inline-block"});
                $('#wp-content-media-buttons').append(btn);
            },

            editorTabs : function(){
                self.tabsButton = $('#content-html').clone();
                self.tabsButton = self.tabsButton.attr('id', 'content-block').removeClass('switch-html').addClass('switch-block').text('ブロック');
                self.tabsButton.removeAttr('onclick');
                self.tabsButton.appendTo('.wp-editor-tabs');
                if ( window.localStorage && localStorage.contentBlock === "1" ){

                    $('#content-block').click();
                }
            },

            eventEditorTabs : function(){
                $(document).on('click','.wp-editor-tabs .wp-switch-editor',function(){
                    var container = $(self.options.editorTargetContainer);
                    if ( $(this).hasClass('switch-block') ){
                        $('#wp-content-wrap').addClass('block_editor-active');
                        $('#wp-content-wrap').removeClass('tmce-active');
                        $('#wp-content-wrap').removeClass('html-active');
                        $(this).css({
                            background: '#f5f5f5',
                            color: '#555',
                            borderBottomColor: '#f5f5f5',
                        })
                        container.show();
                        $('#wp-content-editor-container').hide();
                        if ( window.localStorage ){
                            localStorage.contentBlock = "1";
                        }

                    } else {
                        $('#wp-content-wrap').removeClass('block_editor-active');
                        $('#content-block').css({
                            background: '',
                            color: '',
                            borderBottomColor: '',
                        })
                        container.hide();
                        $('#wp-content-editor-container').show();
                        if ( window.localStorage ){
                            localStorage.contentBlock = "0";
                        }
                    }

                });
            },

            mediaUpload : function(){

            },

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
                $('#publish').on('submit', function (e) {
                    $('#content').text(self.editor.store.toString(true));
                    //return false;
                });
            },
        }
        return self;


    });

    $(function () {
        window.BlockContentEditor = new BlockContentEditor();
    });


})(jQuery);
