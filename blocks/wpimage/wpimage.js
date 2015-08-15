SirTrevor.Blocks.Wpimage = (function () {

    return SirTrevor.Block.extend({

        type: "wpimage",
        title: function () {
            return i18n.t('blocks:image:title');
        },

        droppable: true,
        uploadable: true,

        icon_name: 'image',

        loadData: function (data) {
            // Create our image tag
            this.$editor.html($('<img>', {src: data.file.url}));
        },

        onBlockRender: function () {
            /* Setup the upload button */
            this.$inputs.find('button').bind('click', function (ev) {
                ev.preventDefault();
            });
            this.$inputs.find('input').on('change', (function (ev) {
                this.onDrop(ev.currentTarget);
            }).bind(this));
        },

        onDrop: function (transferData) {
            var file = transferData.files[0],
                urlAPI = (typeof URL !== "undefined") ? URL : (typeof webkitURL !== "undefined") ? webkitURL : null;

            // Handle one upload at a time
            if (/image/.test(file.type)) {
                this.loading();
                // Show this image on here
                this.$inputs.hide();
                this.$editor.html($('<img>', {src: urlAPI.createObjectURL(file)})).show();

                this.uploader(
                    file,
                    function (data) {
                        this.setData(data);
                        this.ready();
                    },
                    function (error) {
                        this.addMessage(i18n.t('blocks:image:upload_error'));
                        this.ready();
                    }
                );
            }
        },
        uploader: function (block, file, success, error) {
            var uid = [block.blockID, (new Date()).getTime(), 'raw'].join('-');
            var data = new FormData();
            data.append('async-upload', file);
            data.append('html-upload', 'Upload');

            block.resetMessages();

            $.get(ajaxurl, {action: 'stwp_nonce'}, function (nonce, status, xhr) {
                data.append('_wpnonce', nonce);

                var callbackSuccess = function (data) {

                    $.get('/wp-admin/upload.php?mode=list', function (data, status, xhr) {
                        var imgid = $(data).find('#the-list').children(":first").attr('id');
                        imgid = imgid.substr(imgid.indexOf('-') + 1, 10);

                        // Get Image URL
                        $.get(ajaxurl, {action: 'stwp_imgurl', id: imgid}, function (url, status, xhr) {

                            var data = {file: {url: url.disp, full: url.full}};

                            SirTrevor.log('アップロードに成功しました');
                            if (!_.isUndefined(success) && _.isFunction(success)) {
                                success.bind(block)(data);
                            }

                        }, 'json');

                    });
                };

                var callbackError = function (jqXHR, status, errorThrown) {
                    SirTrevor.log('アップロードに失敗しました。');

                    if (!_.isUndefined(error) && _.isFunction(error)) {
                        error.bind(block)(status);
                    }
                };


                var xhr = $.ajax({
                    url: SirTrevor.config.defaults.uploadUrl,
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: 'POST'
                });

                block.addQueuedItem(uid, xhr);


                xhr.done(callbackSuccess)
                    .fail(callbackError)
                    .always(block.removeQueuedItem.bind(block, uid));
                return xhr;
            });
        }
    });
})();
