(function ($) {
    'use strict';

    /* global tinymce, tinyMCEPreInit, QTags, setUserSetting */
    var BlockContentEditor = (new function(){

        var config = BCEConfig || {};
        var self = function(){

            self.options = {
                editorTarget: "#block-content-editor",
                editorTargetContainer : "#block-content-editor-container",
                editorBlockType: config.blockTypes,
            }

            self.prototype.editorInit();

            self.prototype.eventEditorTabs();
            self.prototype.editorTabs();
            self.prototype.addFrontEditButton();
            self.prototype.buttonRippleEffect();
            self.prototype.eventEditorSave();
        };

        self.prototype = {

            /**
             * 記事画面で編集ボタンを設置
             * @returns {boolean}
             */
            addFrontEditButton: function(){

                var url = $('#view-post-btn a').attr('href');

                if ( typeof url === "undefined" ) {
                    return false;
                }
                var btn = $('<a>').attr('href', url + '?blockcontenteditor=true').text('記事画面で編集').addClass('button').fadeIn('100').css({display: "inline-block"});
                $('#wp-content-media-buttons').append(btn);
            },

            /**
             * ブロックタブを追加
             */
            editorTabs : function(){
                self.tabsButton = $('#content-html').clone();
                self.tabsButton = self.tabsButton.attr('id', 'content-block').removeClass('switch-html').addClass('switch-block').text('ブロック');
                self.tabsButton.removeAttr('onclick');
                self.tabsButton.appendTo('.wp-editor-tabs');
                if ( window.localStorage && localStorage.contentBlock === "1" ){

                    $('#content-block').click();
                }
            },

            /**
             * ブロックタブをクリックした時のイベントを登録
             */
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


            // 設定
            editorSetting : function(){
                SirTrevor.config.language = config.config.language;
                SirTrevor.config.debug = config.config.debug;
                SirTrevor.config.scribeDebug = config.config.scribeDebug;
                SirTrevor.setDefaults({
                    uploadUrl: config.config.uploadUrl
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
                $('#publish').on('click', function(e) {
                    if ( !$('#block-content-editor').hasClass('already-save') ) {
                        e.preventDefault();
                        var button = this;

                        $('#block-content-editor').addClass('already-save');

                        $('#block-content-editor').text(self.editor.store.toString(true));
                        $(button).click();

                    }
                });
            },

            buttonRippleEffect: function(){
                $('.st-block-control').on('click', function (event) {
                    event.preventDefault();

                    var $div = $('<div/>'),
                        btnOffset = $(this).offset(),
                        xPos = event.pageX - btnOffset.left,
                        yPos = event.pageY - btnOffset.top;

                    $div.addClass('ripple-effect');
                    var $ripple = $(".ripple-effect");
                    $ripple.css("height", $(this).height());
                    $ripple.css("width", $(this).height());
                    $div
                        .css({
                            top: yPos - ($ripple.height()/2),
                            left: xPos - ($ripple.width()/2),
                            background: $(this).data("ripple-color")
                        })
                        .appendTo($(this));

                    window.setTimeout(function(){
                        $div.remove();
                    }, 2000);
                });
            }
        }
        return self;

    });

    $(function () {
        window.BlockContentEditor = new BlockContentEditor();
    });


})(jQuery);
