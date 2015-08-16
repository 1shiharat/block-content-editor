(function($){

SirTrevor.Blocks.Wpimage = (function () {

    var template  = _.template(
        '<div class="st-media--uploader <%= imgActive()%>">' +
            '<div class="st-media--uploader__input">' +
                '<p style="text-align:center;"><button class="st-block st-mediaupload button add-media button-hero">メディアを追加</button></p>' +
                '<input class="st-url" name="url" type="hidden" value="<%= getImgUrl() %>">' +
            '</div>' +
            '<div class="st-media--render">' +
                '<img src="<%= getImgUrl() %>">' +
                '<div><button class="button st-mediaremove button-secondary">画像を削除</button></div>' +
            '</div>' +
        '</div>');

    return SirTrevor.Block.extend({

        type: "Wpimage",
        title: function () {
            return i18n.t('blocks:image:title');
        },
        imgActive: function(){
          if ( typeof this.blockStorage.data.url !== 'undefined' ){
              return 'exit_image';
          } else {
              return "";
          }
        },
        getImgUrl: function(){
            if ( typeof this.blockStorage.data.url !== 'undefined' ) {
                return this.blockStorage.data.url;
            } else {
                return '';
            }
        },

        droppable: false,
        uploadable: false,

        icon_name: 'image',
        editorHTML: function () {
            return template(this);
        },
        onBlockRender: function(){
            // media uploader

            var closestWrap = '.st-media--uploader';
            $('.st-mediaupload').on('click',function (e) {
                e.preventDefault();
                var file_frame, thumbnails;
                var button = $(this);
                var inputId = button.closest(closestWrap).find('.st-url');
                var render = button.closest(closestWrap).find('.st-media--render img');
                if (file_frame) {
                    file_frame.open();
                    return;
                }
                // create the file frame
                file_frame = wp.media.frames.file_frame = wp.media({
                    title: $(this).data('uploader_title'),
                    button: $(this).data('uploader_button_text'),
                    multiple: false,
                    library: {
                        type: 'image'
                    }
                });
                // get the selected attachments when user confirms selection
                file_frame.on('select', function (e) {

                    var selected = file_frame.state().get('selection').toJSON(),
                        store = inputId,
                        urls = [];
                    for (var i = selected.length - 1; i >= 0; i--) {
                        urls.push(selected[i].url);
                    }

                    store.val(urls).trigger('change');
                    render.attr('src',urls);
                    button.closest(closestWrap).addClass('exit_image');
                    //updateThumbnails(urls, store);
                });
                // open the file frame
                file_frame.open();
            });


            $('.st-mediaremove').on( 'click',function ( e ) {
                e.preventDefault();
                var button = $( this ),
                    parent = $(this).closest(closestWrap).find('.st-media--render img'),
                    input = $( button.data( 'store' ) ),
                    store = $(input);
                parent.attr('src', '');
                $(this).closest(closestWrap).removeClass('exit_image');
                input.val( '' ).trigger( 'change' );
            } );
        },
        loadData: function (data) {
            // Create our image tag
            //this.$editor.html($('<img>', {src: data.file.url}));
        },

    });
})();

})(jQuery);
