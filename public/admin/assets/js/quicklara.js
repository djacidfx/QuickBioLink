jQuery(function ($) {
    "use strict";

    /**************
     * copy shortcode
     * *************/
    $('.quick-shortcode-box button').on('click',function () {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(this).data('code')).select();
        document.execCommand("copy");
        $temp.remove();
    });

    /**************
     * Select2
     * *************/
    let select2 = $(".select2");
    if (select2.length) {
        select2.select2();
    }

    /**************
     * Menu Drag
     * *************/
    let quickTargetMenu = $('.quick-sort-menu'),
        nestable = $('.nestable'),
        idsInput = $('#ids');

    if (quickTargetMenu.length) {
        quickTargetMenu.sortable({
            handle: '.quick-navigation-handle',
            placeholder: 'quick-navigation-placeholder',
            axis: "y",
            update: function() {
                const quickSortData = quickTargetMenu.sortable('toArray', {
                    attribute: 'data-id'
                })
                idsInput.attr('value', quickSortData.join(','));
            }
        });
    }

    if (nestable.length) {
        nestable.nestable({ maxDepth: 2 });
        nestable.on('change', function() {
            var data = JSON.stringify(nestable.nestable('serialize'));
            idsInput.attr('value', data);
        });
    }

    /**************
     * ckeditor
     * *************/

    let ckeditor = document.querySelector('.ckeditor');
    if (ckeditor) {
        function UploadAdapterPlugin(editor) {
            editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                return new UploadAdapter(loader);
            };
        }
        ClassicEditor.create(ckeditor, {
            extraPlugins: [UploadAdapterPlugin],
        }).catch(error => {
            alert(error);
        });
    }
    /**************
     * tinymce editor
     * *************/
    if($('.tiny-editor').length){
        tinymce.init({
            selector: '.tiny-editor',
            height: 500,
            resize: true,
            plugins: 'quickbars image advlist lists code table codesample autolink link wordcount fullscreen help searchreplace media anchor',
            toolbar:[
                "blocks | bold italic underline strikethrough | alignleft aligncenter alignright  | link image media blockquote hr",
                "undo redo | removeformat | table | bullist numlist | outdent indent | anchor | code fullscreen"
            ],
            menubar: "edit view insert format table tools help",
            // link
            relative_urls: false,
            link_assume_external_targets: true,
            link_class_list: [
                {title: 'None', value: ''},
                {title: 'Primary Button', value: 'btn btn-sm btn-primary shadow-primary'},
                {title: 'Secondary Button', value: 'btn btn-sm btn-secondary shadow-secondary'},
                {title: 'Danger Button', value: 'btn btn-sm btn-danger shadow-danger'},
                {title: 'Warning Button', value: 'btn btn-sm btn-warning shadow-warning'},
                {title: 'Info Button', value: 'btn btn-sm btn-info shadow-info'},
                {title: 'Dark Button', value: 'btn btn-sm btn-dark shadow-dark'},
            ],
            // images
            image_advtab: true,
            extended_valid_elements: 'i[*]',
            content_style: 'body { font-size:16px }',
            setup: function (editor) {
                editor.on('change', function () {
                    tinymce.triggerSave();
                });
            }
        });
    }

    /**************
     * Get category on change language
     * *************/
    let articleLang = $('#articleLang'),
        articleCategory = $('#articleCategory');

    articleLang.on('change', function() {
        const langCode = $(this).val();
        if (langCode) {
            $.ajax({
                url: BASE_URL + '/blog/articles/categories/' + langCode,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    if ($.isEmptyObject(data.info)) {
                        articleCategory.empty();
                        $.each(data, function(key, value) {
                            articleCategory.append('<option value="' + key + '">' + value + '</option>');
                        });
                    } else {
                        articleCategory.empty();
                        articleCategory.append('<option value="" selected disabled>Choose</option>');
                        quick_alert(data.info, 'info');
                    }
                }
            });
        } else {
            articleCategory.empty();
        }
    });

    /**************
     * selectFileBtn
     * *************/
    let selectFileBtn = $('#selectFileBtn'),
        selectedFileInput = $("#selectedFileInput"),
        filePreviewBox = $('.file-preview-box'),
        filePreviewImg = $('#filePreview');

    selectFileBtn.on('click', function() {
        selectedFileInput.trigger('click');
    });

    selectedFileInput.on('change', function() {
        var file = true,
            readLogoURL;
        if (file) {
            readLogoURL = function(input_file) {
                if (input_file.files && input_file.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        filePreviewBox.removeClass('d-none');
                        filePreviewImg.attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input_file.files[0]);
                }
            }
        }
        readLogoURL(this);
    });


});
