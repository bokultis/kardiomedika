$(document).ready(function () {
    $(".file-uploader").each(function(){
        var $fileUploader = $(this);
        var fieldId = $fileUploader.data('field_id');        
        $fileUploader.fileupload({
            formData: $fileUploader.data('form_data'),
            dataType: 'json',
            done: function (e, data) {
                $.each(data.result.files, function (index, file) {
                    $('#files-' + fieldId).text(file.name);                
                    $('#clear-file-upload-' + fieldId).show();
                    $('#hidden-' + fieldId).val(file.path);
                });
                if(data.result.error){
                    $('#files-' + fieldId).text(data.result.error);  
                    $('#hidden-' + fieldId).val('');
                    $('#clear-file-upload-' + fieldId).hide();
                }
            }
        }).prop('disabled', !$.support.fileInput)
          .parent().addClass($.support.fileInput ? undefined : 'disabled');

        $('#clear-file-upload-' + fieldId).click(function () {
            $('#hidden-' + fieldId).val('');
            $('#files-'  + fieldId).empty();
            $(this).hide();
        });        
    });
});

