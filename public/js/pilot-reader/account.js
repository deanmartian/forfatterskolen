'use strict';

const methods = {

    viewUserPreferences : function()
    {
        let self = this;
        $.get('/account/pilot-reader/profile/preferences/view')
            .then(function(response){
                if(response)
                {
                    let pills_tab = $(".sub-right-content");
                    pills_tab.find("input[type='radio']").each(function(){
                        let radio = $(this);
                        if(radio.val() == response.role)
                        {
                            radio.prop('checked', true);
                            return false
                        }
                    });
                    pills_tab.find("input[type='checkbox']").prop('checked', response.joined_reader_community)
                }
                self.showReaderProfileTab(response)
            })
    },

    setUserPreferences : function()
    {
        let self = this;
        let role = $("[name='role']:checked").val();
        let joined_reader_community = +$("[name='joined_reader_community']").is(":checked");
        $.post('/account/pilot-reader/profile/preferences/set', {
            role : role,
            joined_reader_community : joined_reader_community
        })
            .then(function(response){
                self.showReaderProfileTab({ role : role });
                toastr.success(response.success, 'Success')
            })
    },

    viewReaderProfile : function(){
        $.get('/account/pilot-reader/profile/reader/view')
            .then(function(response){
                if(response)
                {
                    let form = $("#readerProfileForm");
                    form.find('textarea').each(function(){
                        let textarea = $(this);
                        textarea.val(response[textarea.attr('name')])
                    });
                    form.find("input[type='checkbox']").prop('checked', response.availability)
                }
            })
    },

    showReaderProfileTab : function(data)
    {
        let role = parseInt(data.role);
        if (data.role && role !== 2) {
            if ($(".reader-profile-link").length === 0) {
                let profile_link = "<a href='"+reader_profile_link+"' class='item link reader-profile-link '>Reader Profile</a>";
                $(".book-menu").find(".inner").append(profile_link);
            }
        } else {
            $(".reader-profile-link").remove();
        }
    },

    saveReaderProfile : function(index, form)
    {
        let url = '/account/pilot-reader/profile/reader/set';
        let params = form.serialize();
        let self = this;
        $.post(url, params)
            .then(function(response){
                self.clearError(form);
                self.clearInputs(form);
                toastr.success(response.success, 'Success')
            })
            .catch(function(err){
                self.clearError(form);
                let errors = err.responseJSON;
                let err_code = err.status;
                if(err_code === 422)
                {
                    self.setError(errors, form)
                }
            })
    },

    clearError : function(form){
        form.find('.form-group').each(function(){
            $(this).find('.text-danger').remove()
        })
    },

    clearInputs : function(form){
        form.find('.form-group').each(function(){
            $(this).find('input').val('')
        })
    },

    setError : function(errors, form){
        $.each(errors, function(key, error){
            form.find(`[name='${ key }']`).closest('.form-group').append(`<small class='text-danger'><i class='fas fa-exclamation-circle'></i> ${ error[0] }</small>`)
        })
    },
};

methods.viewUserPreferences();
methods.viewReaderProfile();

$('#readerProfileForm').each(function(key){
    $(this).submit(function(e){
        e.preventDefault();
        methods.saveReaderProfile(key, $(this))
    })
});