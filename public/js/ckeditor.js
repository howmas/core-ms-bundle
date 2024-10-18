const {
    Plugin, ButtonView, MenuBarMenuListItemButtonView,
    ClassicEditor,
    Essentials,
    Heading,
    Font, Highlight,
    Alignment, Autoformat,
    Bold, Code, Italic, Strikethrough, Subscript, Superscript, Underline,
    Indent, IndentBlock,
    BlockQuote,
    CodeBlock,
    HorizontalLine,
    GeneralHtmlSupport, HtmlEmbed,
    Image, ImageCaption, ImageResize, ImageStyle, ImageToolbar, LinkImage, AutoImage, ImageInsert,
    AutoLink, Link,
    List, ListProperties, TodoList,
    MediaEmbed,
    Mention,
    PasteFromOffice, Clipboard,
    RemoveFormat,
    SelectAll,
    ShowBlocks,
    SourceEditing,
    SpecialCharacters, SpecialCharactersEssentials,
    Table, TableToolbar, TableCellProperties, TableProperties, TableCaption, TableColumnResize,
    WordCount,
    FindAndReplace
} = CKEDITOR;

const hcoreButton = {
    label: 'Chọn từ thư viện',
    withText: true,
    tooltip: true
};

class HcoreLibraryPlugin extends Plugin {
    init() {
        const editor = this.editor;
        // The button must be registered among the UI components of the editor
        // to be displayed in the toolbar.
        editor.ui.componentFactory.add('hcoreLibraryPlugin', () => {
            const button = new ButtonView();
            button.set(hcoreButton);
            button.on('execute', () => {
                openLibrary(editor);
            });

            return button;
        } );
    }
}

class HcoreLibraryView extends Plugin {
    init() {
        const editor = this.editor;

        // Register the toolbar button.
        editor.ui.componentFactory.add('menuBar:hcoreLibraryView', locale => {
            const view = new MenuBarMenuListItemButtonView(locale);
            view.set(hcoreButton);
            view.on( 'execute', () => {
                openLibrary(editor);
            } );

            return view;
        } );

        // Add your component in the preferred position.
        // editor.ui.extendMenuBar( {
        //     item: 'hcoreLibraryView',
        //     position: 'after:menuBar:bold'
        // } );
        editor.ui.extendMenuBar( {
          menu: {
            menuId: 'hcoreLibrary',
            label: 'Thư viện',
            groups: [
              {
                groupId: 'hcoreLibraryViews',
                items: [
                  'menuBar:hcoreLibraryView'
                ]
              }
            ]
          },
          position: 'before:help'
        } );
    }
}

const wysiwygConfig = {
    language: {
        ui: 'vi',
        content: 'vi',
    },
    placeholder: 'Nhập nội dung ...',
    htmlSupport: {
        allow: [],
        disallow: []
    },
    plugins: [
        Essentials,
        Heading,
        Font, Highlight,
        Alignment, Autoformat,
        Bold, Code, Italic, Strikethrough, Subscript, Superscript, Underline,
        Indent, IndentBlock,
        BlockQuote,
        CodeBlock,
        HorizontalLine,
        GeneralHtmlSupport, HtmlEmbed,
        Image, ImageCaption, ImageResize, ImageStyle, ImageToolbar, LinkImage, AutoImage, ImageInsert,
        AutoLink, Link,
        List, ListProperties, TodoList,
        MediaEmbed,
        Mention,
        PasteFromOffice, Clipboard,
        RemoveFormat,
        SelectAll,
        ShowBlocks,
        SourceEditing,
        SpecialCharacters, SpecialCharactersEssentials,
        Table, TableToolbar, TableCellProperties, TableProperties, TableCaption, TableColumnResize, TableProperties,
        WordCount,
        FindAndReplace,
        HcoreLibraryPlugin,
        HcoreLibraryView
    ],
    menuBar: {
        isVisible: true 
    },
    toolbar: {
        items: [
            'undo', 'redo',
            '|',
            'selectAll',
            '|',
            'findAndReplace',
            '|',
            'sourceEditing',
            '|',
            'showBlocks',
            '|',
            'insertImage', 'insertTable',
            '|',
            'link',
            '|',
            'mediaEmbed', 'specialCharacters', 'blockQuote', 'codeBlock', 'htmlEmbed',
            '|',
            'horizontalLine',
            '|',
            'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'code',
            '|',
            'fontSize', 'fontFamily',
            '|',
            'fontColor', 'fontBackgroundColor', 'highlight',
            '|',
            'heading',
            '|',
            'bulletedList', 'numberedList', 'todoList',
            '|',
            'alignment', 'outdent', 'indent',
            '|',
            'removeFormat',
            '|',
            'hcoreLibraryPlugin'
        ],
        shouldNotGroupWhenFull: true
    },
    image: {
        toolbar: [
            'imageStyle:block',
            'imageStyle:side',
            '|',
            'toggleImageCaption',
            'imageTextAlternative',
            '|',
            'linkImage',
            'insertImage',
            '|',
            'hcoreLibraryPlugin'
        ],
        insert: {
            // This is the default configuration, you do not need to provide
            // this configuration key if the list content and order reflects your needs.
            integrations: ['url']
        }
    },
    list: {
        properties: {
            styles: true,
            startIndex: true,
            reversed: true
        },
        multiBlock: false 
    },
    fontFamily: {
        options: [
            'default',
            'Arial, Helvetica, sans-serif',
            'Courier New, Courier, monospace',
            'Georgia, serif',
            'Lucida Sans Unicode, Lucida Grande, sans-serif',
            'Tahoma, Geneva, sans-serif',
            'Times New Roman, Times, serif',
            'Trebuchet MS, Helvetica, sans-serif',
            'Verdana, Geneva, sans-serif',
            'Roboto, sans-serif',                 // Google Fonts
            'Open Sans, sans-serif',              // Google Fonts
            'Lato, sans-serif',                   // Google Fonts
            'Montserrat, sans-serif',             // Google Fonts
            'Oswald, sans-serif',                 // Google Fonts
            'Merriweather, serif',                // Google Fonts
            'Ubuntu, sans-serif',                 // Google Fonts
            'Raleway, sans-serif',                // Google Fonts
            'Playfair Display, serif',            // Google Fonts
            'Poppins, sans-serif',                // Google Fonts
            'Fira Sans, sans-serif',              // Google Fonts
            'Comic Sans MS, cursive, sans-serif', // Fun font!
            'Impact, Charcoal, sans-serif',       // Bold fonts
            'Gill Sans, sans-serif',              // Classic font
            'Palatino Linotype, Book Antiqua, Palatino, serif', // Elegant font
            'Arial Black, Gadget, sans-serif',    // Extra bold
            'Brush Script MT, sans-serif'         // Script font
        ],
        supportAllValues: true
    },
    heading: {
        options: [
            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
            { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
            { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
            { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
            { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
        ]
    },
    // highlight: {
    //     options: [
    //         {
    //             model: 'greenMarker',
    //             class: 'marker-green',
    //             title: 'Green marker',
    //             color: 'var(--ck-highlight-marker-green)',
    //             type: 'marker'
    //         },
    //         {
    //             model: 'redPen',
    //             class: 'pen-red',
    //             title: 'Red pen',
    //             color: 'var(--ck-highlight-pen-red)',
    //             type: 'pen'
    //         }
    //     ]
    // },
    link: {
        addTargetToExternalLinks: true,
        decorators: [
            {
                addTargetToExternalLinks: true,
                defaultProtocol: 'https://',
                mode: 'manual',
                label: 'Downloadable',
                attributes: {
                    rel: 'download'
                }
            },
            { // New decorator for rel="dofollow"
                addTargetToExternalLinks: true,
                defaultProtocol: 'https://',
                mode: 'manual',
                label: 'Dofollow',
                attributes: {
                    rel: 'dofollow'
                }
            },
            { // New decorator for rel="nofollow"
                addTargetToExternalLinks: true,
                defaultProtocol: 'https://',
                mode: 'manual',
                label: 'Nofollow',
                attributes: {
                    rel: 'nofollow'
                }
            },
            { // New decorator for rel="nofollow"
                addTargetToExternalLinks: true,
                defaultProtocol: 'https://',
                mode: 'manual',
                label: 'Blank',
                attributes: {
                    target: '_blank'
                }
            },
        ]
    },
    mention: {
        feeds: [
            {
                marker: '@',
                feed: [ '@Barney', '@Lily', '@Marry Ann', '@Marshall', '@Robin', '@Ted' ],
                minimumCharacters: 1
            }
        ]
    },
    table: {
        defaultHeadings: { rows: 1, columns: 1 },
        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties', 'toggleTableCaption']
    },
    htmlEmbed: {
        showPreviews: true,
        // sanitizeHtml: (inputHtml) => {
        //     // const inputHtml = sanitize( inputHtml );

        //     return {
        //         html: inputHtml,
        //         // true or false depending on whether the sanitizer stripped anything.
        //         hasChanged: true
        //     };
        // }
    },
    wordCount: {
        onUpdate: stats => {
            // console.log(stats)
        }
    },
    // translations: [
    //     coreTranslations,
    // ],
    shouldNotGroupWhenFull: true
};

// Function to create and display a modal dialog
function openLibrary(editor) {
    var url = $('#assetLibraryModal').data('url') + '&isCkeditor=1';
    callGet(url, '#assetLibraryModal .modal-content');
    $('#assetLibraryModal').addClass('ckeditor');
    $('#assetLibraryModal').modal('show');

    $('body').delegate('#assetLibraryModal.ckeditor .asset-modal-select-button', 'click', function() {
        var assetKey = $('#assetLibraryModalBody input.js-custom-checkbox-card-input:checked').data('id');
        var assetSelected ='#' + assetKey;
        editor.model.change(writer => {
            var src = $(assetSelected).data('path');
            var alt = $(assetSelected).attr('alt');
            var type = $(assetSelected).data('type');

            if (type == 'image') {
                var element = writer.createElement('imageBlock', {
                    src: src,
                    alt: alt,
                });
            } else if (type == 'video') {
                var element = writer.createElement('rawHtml', {
                    value: `
                        <p style="text-align:center;">
                            <video controls="">
                                <source src="${src}" type="video/mp4">
                            </video>
                        </p>
                    `,
                });
            } else if (type == 'file') {
                var element = writer.createText(alt, {
                    linkHref: src
                });
            }

            editor.model.insertContent(element);
        });

        $('#assetLibraryModal').modal('hide');
    });
}

var allCkeditor = document.querySelectorAll(hcoreWysiwygCls);
allCkeditor.forEach((item, index) => {
    createWysiwyg(item);
});

function createWysiwyg(wysiwyg)
{
    var isDisplayNone = wysiwyg.closest(".d-none");
    if (isDisplayNone) {
        return;
    }

    var wordCountId = wysiwyg.getAttribute("data-wordcount");
    var wysiwygName = wysiwyg.getAttribute("name");
    var noteditable = wysiwygName == undefined;

    ClassicEditor
    .create(wysiwyg, wysiwygConfig)
    .then(editor => {
        if (noteditable) {
            editor.enableReadOnlyMode('');
        } else {
            if (wysiwygName.indexOf('[]') != -1) {
                var mainId = wysiwyg.getAttribute("id");
                var mainName = wysiwygName.replace("[]", "");

                if (!hcoreWysiwygContent?.[mainName]?.[mainId]) {
                    var wysiwygValue = {};
                    wysiwygValue[mainId] = editor;

                    if (!hcoreWysiwygContent?.[mainName]) {
                        var wysiwygValue = {};
                        wysiwygValue[mainId] = editor;
                        hcoreWysiwygContent[mainName] = wysiwygValue;
                    } else {
                        hcoreWysiwygContent[mainName][mainId] = editor;
                    }
                } else {
                    hcoreWysiwygContent[mainName][mainId] = editor;
                }
            } else {
                hcoreWysiwygContent[wysiwygName] = editor;
            }
        }

        if (wordCountId) {
            var wordCountPlugin = editor.plugins.get('WordCount');
            var wordCountWrapper = document.getElementById(wordCountId);
            wordCountWrapper.appendChild(wordCountPlugin.wordCountContainer);
        }
    })
    .catch( err => {
    });
}
