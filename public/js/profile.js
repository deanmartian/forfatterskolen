'use strict';

const methods = {
    listEmails : function(){
        $.get('/account/email/list')
            .then(function(response){
                let email_list = $("#email-list");
                let primary = response.primary;
                let secondary = response.secondary;
                email_list.html("");
                email_list.append(`
                <li class="list-group-item py-3 px-4"> 
                    <span class="pull-left mt-0">${ primary.email }</span>
                    <h5 class="pull-right mb-0 mt-0"><span class="badge badge-success py-2 px-4">Hoved</span></h5>   
                </li>
            `);
                $.each( secondary, function(key, value){
                    email_list.append(`
                    <li class="list-group-item py-3 px-4"> 
                        <span class="pull-left mt-0">${ value.email }</span>
                        <h5 class="pull-right mb-0 mt-0"><span class="badge badge-info py-2 px-4 mr-2 hand" 
                        onclick="methods.setPrimaryEmail(${ value.id }, this)">Set Primary</span>
                        <span class="badge badge-danger py-2 px-4 hand" onclick="methods.removeSecondaryEmail(${ value.id })">Remove</span></h5>   
                    </li>
                `)
                })
            })
    },

    sendConfirmation : function(event){
        if(event && (event.which !== 13 || event.keyCode !== 13)){
            return
        }
        let input = $("[name='email']");
        let email = input.val();
        let email_btn = $(".email-btn");
        this.setLoadingIcon(email_btn);
        let self = this;
        let form = $(".email-container");
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        $.post(`/account/email/confirmation`, {
            email : email,
            _token: token
        })
            .then(function(response){
                self.clearError(form);
                self.clearInputs(form);
                self.setLoadingIcon(email_btn);
                toastr.success(response.success, 'Success');
            })
            .catch(function(err){
                self.clearError(form);
                self.setLoadingIcon(email_btn);
                let errors = err.responseJSON;
                let err_code = err.status;
                if(err_code === 422)
                {
                    self.setError(errors, form)
                }
            })
    },

    setPrimaryEmail : function(id, t){
        let self = this;
        let self_span = $(t);
        $.confirm({
            title: 'Set as Primary Email',
            content: 'Are you sure to set this as a primary email?',
            type: 'blue',
            typeAnimated: true,
            buttons: {
                tryAgain: {
                    text: 'Ok',
                    btnClass: 'btn-blue',
                    action: function(){

                        self_span.prepend('<i class="fa fa-spinner fa-pulse mr-2"></i>');
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                        $.post('/account/email/primary/set', {
                            id : id,
                            _token: token
                        })
                            .then(function(response){
                                $("#profile_email").val(response.primary_email);
                                self.listEmails();
                                self_span.find('i').remove();
                                toastr.success(response.success, 'Success');
                            })
                    }
                },
                close: function () {
                }
            }
        })
    },
    removeSecondaryEmail : function(id){
        let self = this;
        $.confirm({
            title: 'Remove Email',
            content: 'Are you sure to remove it?',
            type: 'red',
            typeAnimated: true,
            buttons: {
                tryAgain: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        $.post('/account/email/destroy', {
                            id : id,
                            _token: token
                        })
                            .then(function(response){
                                self.listEmails();
                                toastr.success(response.success, 'Success');
                            })
                    }
                },
                close: function () {
                }
            }
        })
    },

    setLoadingIcon : function(btn){
        let icon = btn.find("i");
        let spinner = "fa fa-spinner fa-pulse fa-fw";
        let plus = "plus";
        let has_spinner = icon.hasClass('fa-spinner');
        has_spinner? btn.removeClass("disabled") : btn.addClass("disabled");
        btn.prop('disabled', !has_spinner);
        icon.removeClass(has_spinner? spinner : plus).addClass(has_spinner? plus : spinner)
    },

    setError : function(errors, form){
        $.each(errors, function(key, error){
            form.find(`[name='${ key }']`).closest('.form-group').append(`<small class='text-danger'><i class='fa fa-exclamation-circle'></i> ${ error[0] }</small>`)
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
};

methods.listEmails();