/*
 An example of a SirTrevor.Block
 --
 Author: C J Bell @ madebymany
 */

(function ($) {

    function bce_tinymce_init(selector){
        if ( $('#').length > 0 ){
            $('#wp-front_editor_init-wrap').remove();
        }
        if ( typeof tinyMCEPreInit.mceInit.content !== "undefined" ) {
            var settings = _.clone(tinyMCEPreInit.mceInit.content);
        } else {
            var settings = {};
        }
        console.log(selector);
        settings.selector = selector;
        settings.height = "500";
        settings.plugins = "paste,wordpress,media,fullscreen,wpeditimage,wpgallery,wpview,wplink,hr,tabfocus,textcolor,wpautoresize,codemirror,wpemoji";
        settings.theme_advanced_toolbar_location = "top";
        settings.theme_advanced_styles = "Header 1=h1;Header 2=header2;Header 3=header3;",
        settings.theme_advanced_buttons1 = "bold,italic,strikethrough,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,|,link,unlink,wp_more,|,fullscreen,wp_adv,separator,separator,code",
        settings.theme_advanced_buttons2 = "formatselect,underline,justifyfull,forecolor,|,pastetext,pasteword,removeformat,|,media,charmap,|,outdent,indent,|,undo,redo,wp_help";
        settings.menubar = true;
        settings.wpautop = true;
        settings.language = "ja";
        settings.remove_linebreaks =  false;
        settings.force_br_newlines =  true;
        settings.force_p_newlines =  false;
        settings.forced_root_block =  '';
        settings.codemirror = {
            indentOnInit: true, // Whether or not to indent code on init.
            path: "https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.5.0/", // Path to CodeMirror distribution
            config: {           // CodeMirror config object
                mode: 'application/x-httpd-php',
                lineNumbers: false
            },
            jsFiles: [          // Additional JS files to load
                'mode/clike/clike.js',
                'mode/php/php.js'
            ]
        };


        settings.wp_autoresize_on = false;

        tinyMCE.init(settings);
    }

    SirTrevor.Blocks.Tinymce = (function () {

        return SirTrevor.Block.extend({


            // String; Names the block
            // Note – please use underscores when naming
            // Eg example_block should be TinymceBlock
            type: 'Tinymce',

            // Function; the title displayed in the toolbar
            // Can return a translated string (if required)
            title: function () {
                // return i18n.t('blocks:example:title');
                return i18n.t('blocks:tinymce:title');
            },

            // Boolean; show this blockType of the toolbar
            toolbarEnabled: true,

            // Block Mixins
            // Allow different UI components / methods to be mixed into the block

            // Enable drop functionality on the block
            droppable: false,

            // Enable paste functionality (to paste URLS etc)
            pastable: false,

            // Enable an upload button to be added
            // Mixins Ajaxable automatically
            // Exposes an uploader method
            // Usage: this.uploader(file, success, failure)
            uploadable: false,

            // Enable queued remote fetching
            // Exposes a small wrapper around the $.ajax method
            // Usage: this.fetch(ajaxOptions, success, failure)
            fetchable: false,

            // Add an ajax queue to the block
            // Added to uploadable & fetchable automatically
            ajaxable: false,

            formattable: false,

            // String or Function; The HTML for the inner portion of the editor block
            // In this example, the editorHTML is an editable input div (like we use for a TextBlock)

            scribeOptions: {
                allowBlockElements: true,
                tags: {
                    p: true,
                    img: true,
                    a: true,
                    i: true,
                    span: true,
                    iframe: true,
                    h1: true,
                    h2: true,
                    h3: true,
                    h4: true,
                    h5: true,
                    h6: true,
                    div: true,
                    section: true,
                    aside: true,
                    hr: true,
                    code: true,
                    pre: true,
                    font: true,
                    article: true,
                    nav: true,
                    ul: true,
                    li: true,
                    ol: true,
                    dl: true,
                    dt: true,
                    dd: true,
                    table: true,
                    figure: true,
                    caption: true,
                    table: true,
                    tbody: true,
                    thead: true,
                    tr: true,
                    td: true,
                    th: true,
                    colspan: true,
                    colgroup: true,
                    tfoot: true,
                }
            },
            // Classes:
            // st-required   – indicates this input must be present to pass validation
            // st-text-block – gives the block the ability to use the formatting controls

            editorHTML: function () {
                var template = _.template("<div class='st-text-block st-tinymce-block st-tinymce-block_<%= blockID%>' contenteditable='false'></div>");
                return template( {blockID: this.blockID} );
            },

            // Function; Executed on render of the block if some data is provided.
            // LoadData gives us a means to convert JSON data into the editor dom
            // In this example we convert the text from markdown to HTML and show it inside the element
            loadData: function (data) {
                this.getTextBlock().html(data.text);
            },

            // Function; Executed on save of the block, once the block is validated
            // toData expects a way for the block to be transformed from inputs into structured data
            // The default toData function provides a pretty comprehensive way of turning data into JSON
            // In this example we take the text data and save it to the data object on the block
            toData: function () {
                var dataObj = {};

                var content = this.blockStorage.data.text;
                if (content.length > 0) {
                    dataObj.text = content;
                }

                this.setData(dataObj);
            },

            // Function; Returns true or false whether there is data in the block
            isEmpty: function () {
                return _.isEmpty(this.saveAndReturnData()); // Default implementation
            },

            saveAndReturnData: function () {
                this.save();
                return this.blockStorage;
            },

            // Other data functions
            // --
            // getData            – returns the data in the store
            // save               - Invokes the toData method
            // saveAndReturnData  - Saves and returns the entire store
            // saveAndGetData     - Save and only return the data part of the store


            // Function; Hook executed at the end of the block rendering method.
            // Useful for initialising extra pieces of UI or binding extra events.
            // In this example we add an extra button, just because.
            onBlockRender: function () {
                bce_tinymce_init('.st-tinymce-block_' + this.blockID);

            },

            // Function; Optional hook method executed before the rendering of a block
            // Beware, $el and any shorthand element variables won't be setup here.
            beforeBlockRender: function () {
            },

            // Function; Executed once content has been dropped onto the dropzone of this block
            // Only required if you have enabled dropping and have provided a dropzone for this block
            // Always is passed the ev.transferData object from the drop
            // Please see the image block (https://github.com/madebymany/sir-trevor-js/blob/master/src/blocks/image.js) for an example
            onDrop: function (transferData) {
            },

            // Function; executed once content has been pasted into a pastable block
            // See the tweet block as an example (https://github.com/madebymany/sir-trevor-js/blob/master/src/blocks/tweet.js)
            onContentPasted: function (event) {
            },

            // Function; Any extra HTML parsing can be defined in here.
            // Returns; String (Required)
            toHTML: function (html) {
                return html;
            },
            setTextBlockHTML: function(html) {
                return html;
            }
        });

    })();
})(jQuery);
