$(document).ready(function(){


    $('[data-toggle="popover"]').popover({html: true}); 




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
            var photosize=$('input[type="file"]')[0].files[0].size/1000000;
            if(photosize>5){
                alert("Error: Image file too big. Please select image file less than 5MB.");
            }
            else{
                var oFReader = new FileReader();
                oFReader.readAsDataURL(this.files[0]);
                console.log(this.files[0]);
                oFReader.onload = function (oFREvent) {
                $('.image-file .image-preview').css('background-image', 'url('+oFREvent.target.result+')')
                    .closest('form').submit();
                }
            }
        }
        
        else{
        alert("Error: Invalid image file!");
        $('.image-file input[type="file"]').val("");
        }
    });


    $('[data-toggle="tooltip"]').tooltip();

    


    $('#tab-login').click(function(){
        window.history.pushState("", "", '/auth/login');   
        $(document).prop('title', 'Login › Forfatterskolen');  
    });


    $('#tab-register').click(function(){
        window.history.pushState("", "", '/auth/login?t=register');  
        $(document).prop('title', 'Register › Forfatterskolen');    
    });


    $('#tab-passwordreset').click(function(){
        window.history.pushState("", "", '/auth/login?t=passwordreset');     
        $(document).prop('title', 'Password Reset › Forfatterskolen'); 
    });

    $(".pilotleser-link").click(function(){
        $.ajax({
            url: '/api/pilotleser/login',
            type: 'GET',
            success: function(response){
                window.open(response.redirect_url, "_blank");
            }
        });
    });

    $(".redirectForum").click(function(){
        $.ajax({
            url: '/account/forum',
            type: 'GET',
            success: function(response){
                window.open(response.redirect_url, "_blank");
            }
        });
    });

});