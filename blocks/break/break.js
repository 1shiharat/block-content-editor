/*
 An example of a SirTrevor.Block
 --
 Author: C J Bell @ madebymany
 */

(function ($) {

    SirTrevor.Blocks.Break = (function () {

        return SirTrevor.Block.extend({

            type: 'Break',

            title: function () {
                return i18n.t('blocks:break:title');
            },
            icon_name: "−",
            toolbarEnabled: true,
            droppable: false,
            pastable: false,
            uploadable: false,
            fetchable: false,
            ajaxable: false,
            formattable: false,
            editorHTML: function () {
                return "<div class='st-break-block'><hr class='' /></div>";
            },
            saveAndReturnData: function () {
                this.save();
                return this.blockStorage;
            },
            toHTML: function (html) {
                return html;
            },
            setTextBlockHTML: function(html) {
                return html;
            }
        });

    })();
})(jQuery);
