$(document).ready(function(){
    $('[data-toggle="popover"]').popover({html: true}); 
    $('.select2').select2();

    $('.btn-remove-learner').click(function(){
    	var learner_name = $(this).data('learner');
    	var package_id = $(this).data('package');
    	var learner_id = $(this).data('learner-id');
    	$('#removeLearnerModal #learner_name').text(learner_name);
    	$('#removeLearnerModal input[name=package_id]').val(package_id);
    	$('#removeLearnerModal input[name=learner_id]').val(learner_id);
    });




    $('[data-toggle="tooltip"]').tooltip();


    $('.image-file .image-preview').click(function(){
        $(this).siblings('input[type=file]').click();
    });
    
    $('.image-file input[type=file]').change(function(event){
        if( $(this).val() == '' ){
            var default_image = $('.image-file .image-preview').data('default');
            $('.image-file .image-preview').css('background-image', 'url(' + default_image + ')');
        }
        var input = $(event.currentTarget);
        var file = input[0].files[0];
        if(file.type.match("image/jpeg")||file.type.match("image/png")){
            var photosize=$(this)[0].files[0].size/1000000;
            if(photosize>5){
                alert("Error: Image file too big. Please select image file less than 5MB.");
            }
            else{
                var oFReader = new FileReader();
                oFReader.readAsDataURL(this.files[0]);
                console.log(this.files[0]);
                oFReader.onload = function (oFREvent) {
                $('.image-file .image-preview').css('background-image', 'url('+oFREvent.target.result+')');
                }
            }
        }
        
        else{
        alert("Error: Invalid image file!");
        $('.image-file input[type="file"]').val("");
        }
    });

    $('.video-file input[type=file]').change(function(event){

        var video_file = $(".video-file");
        var newVideo = '<video width="330" controls> <source src="" id="video_here"> </video>';

        if( $(this).val() != '' ){
            var input = $(event.currentTarget);
            var file = input[0].files[0];
            var fileExtension = file.type;
            var checkExtension = 'video';
            var $source = $('#video_here');

            if(fileExtension.indexOf(checkExtension) !== -1){
                var photosize=$(this)[0].files[0].size/1000000;
                if(photosize>5){
                    alert("Error: Video file too big. Please select video file less than 5MB.");
                }
                else{
                    $source[0].src = URL.createObjectURL(this.files[0]);
                    $source.parent()[0].load();
                }
            } else{
                alert("Error: Invalid video file!");
                $('.video-file input[type="file"]').val("");
                video_file.find('video').remove();
                video_file.prepend(newVideo);
            }
        } else {
            video_file.find('video').remove();
            video_file.prepend(newVideo);
        }
    });


    $('.btn-edit-transaction').click(function(){
        var action = $(this).data('action');
        var id = $(this).data('id');
        var mode = $(this).data('mode');
        var mode_transaction = $(this).data('mode-transaction');
        var amount = $(this).data('amount');
        $('#editTransactionModal form').attr('action', action);
        $('#editTransactionModal input[name=transaction_id]').val(id);
        $('#editTransactionModal select[name=mode]').val(mode);
        $('#editTransactionModal input[name=mode_transaction]').val(mode_transaction);
        $('#editTransactionModal input[name=amount]').val(amount);
    });


    $('.btn-delete-transaction').click(function(){
        var action = $(this).data('action');
        var id = $(this).data('id');
        $('#deleteTransactionModal form').attr('action', action);
        $('#deleteTransactionModal input[name=transaction_id]').val(id);
    });

    $('#lesson-delay-toggle').change(function(){
        var delay = $(this).val();
        if(delay == 'days'){
            $('#lesson-delay').attr('type', 'number');
        } else if(delay == 'date')
        {
            $('#lesson-delay').attr('type', 'date');
        }
        $('.lesson-delay-text').text(delay);
    });


    $('.btn-edit-video').click(function(){
        var id = $(this).data('id');
        var embed_code = $(this).data('embed-code');
        var action = $(this).data('action');

        $('#editVideoModal form').attr('action', action);
        $('#editVideoModal input[name=video_id]').val(id);
        $('#editVideoModal textarea').val(embed_code);
    });


    $('.btn-delete-video').click(function(){
        var id = $(this).data('id');
        var action = $(this).data('action');

        $('#deleteVideoModal form').attr('action', action);
        $('#deleteVideoModal input[name=video_id]').val(id);
    });


    $('.btn-delete-feedback').click(function(){
        var action = $(this).data('action');

        var form = $('#deleteFeedbackModal');
        form.find('form').attr('action', action);
    });
});