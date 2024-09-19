/**
 * Module's JavaScript.
 */

function eupInitSettings()
{
	$(document).ready(function(){

        summernoteInit('#eup-settings-footer', {
            insertVar: false,
            disableDragAndDrop: false,
            callbacks: {
                onImageUpload: function(files) {
                    if (!files) {
                        return;
                    }
                    for (var i = 0; i < files.length; i++) {
                        editorSendFile(files[i], false, false, '#eup-settings-footer');
                    }
                }
            }
        });
        
        summernoteInit('#eup-privacy', {
            toolbar: [
                ['style', ['style', 'bold', 'italic', 'underline', 'color', 'lists', 'paragraph', 'removeformat', 'link', 'table']],
                ['insert', ['picture']],
                ['view', ['codeview']]
            ],
            insertVar: false,
            disableDragAndDrop: false,
            callbacks: {
                onImageUpload: function(files) {
                    if (!files) {
                        return;
                    }
                    for (var i = 0; i < files.length; i++) {
                        editorSendFile(files[i], false, false, '#eup-privacy');
                    }
                }
            }
        });

        /*$('#eup-locales').select2({
            multiple: true
            //tags: true
            // Causes JS error on clear
            //allowClear: true,
            //placeholder: category_placeholder
        }).on('select2:select', function (e) {
               //Append selected element to the end of the list, otherwise it follows the same order as the dropdown
               var element = e.params.data.element;
               var $element = $(element);
               $(this).append($element);
               $(this).trigger("change");
        });*/

		$('#eup-show-preview').click(function(e) {
			//$('body:first').append($('#eup-widget-code').val());

            FreeScoutW = JSON.parse($('#eup-widget-settings').val());
            var a = document.createElement('script');
            var m = document.getElementsByTagName('script')[0];
            a.async = 1;
            a.id = "freescout-w";
            a.src = $('#eup-widget-url').val();
            m.parentNode.insertBefore(a, m);

			e.preventDefault();

			$(this).fadeOut();
		});

		$('#eup-widget-form input:visible,#eup-widget-form select:visible').on('change keyup', function(e) {
			$('#eup-widget-code-wrapper').addClass('hidden');
			$('#eup-widget-save-wrapper').removeClass('hidden');
		});

		$(".eup-colorpicker").colorpicker({
            customClass: 'colorpicker-2x',
            sliders: {
                saturation: {
                    maxLeft: 200,
                    maxTop: 200
                },
                hue: {
                    maxTop: 200
                },
                alpha: {
                    maxTop: 200
                }
            }
        }).on('changeColor.colorpicker', function(event) {
            $('#eup-widget-code-wrapper').addClass('hidden');
			$('#eup-widget-save-wrapper').removeClass('hidden');
			 return true;
        }).trigger("change");

        $('#eup_consent').change(function(e) {
            $('#eup-privacy-container').toggleClass('hidden'); 
        });
	});
}