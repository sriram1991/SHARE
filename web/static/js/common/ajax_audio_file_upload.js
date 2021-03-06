    // Action -> AJAX Call PDF File Upload with Validation
    $(function() {
        $('#ajax_mentor_audio_upload').submit(function(e) {
            e.preventDefault();
            if($('#ajax_mentor_audio_upload').valid()){
                $('#add_audio_file_btn').button('loading');
                $.ajaxFileUpload({
                    url             :'/resource/ajax_mentor_audio_upload/', 
                    secureuri       :false,
                    fileElementId   :'resource_audio_link',
                    dataType: 'JSON',
                    data: { 
                        subject_name: $('#subject_name').val(),
                        resource_name : $('#resource_name').val(), 
                        resource_tag: $("#resource_tag").val(),
                        file_type: $('#file_type').val() 
                    },
                    success : function (data) {
                        var obj = jQuery.parseJSON(data);                
                        if(obj['status'] == 'success') {
                            $('#files').html(obj['msg']);
                            $("#mentor_add_audio_modal").modal('hide');
                            // get_resource_list();
                            get_mentor_resource_list();
                        } else {
                            $('#files').html(obj['msg']);
                            $('#add_audio_file_btn').button('reset');
                        }
                    }
                });
                return false;   
            } else { $('#add_audio_file_btn').button('reset'); }
            
        });
    });