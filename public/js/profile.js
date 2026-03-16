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
                    <div class="pf-email-item">
                        <span class="pf-email-item__address">${ primary.email }</span>
                        <span class="pf-email-item__badge">Hoved</span>
                    </div>
                `);
                $.each( secondary, function(key, value){
                    email_list.append(`
                        <div class="pf-email-item">
                            <span class="pf-email-item__address">${ value.email }</span>
                            <div class="pf-email-item__actions">
                                <button class="pf-btn pf-btn--secondary" onclick="methods.setPrimaryEmail(${ value.id }, this)">Sett som hoved</button>
                                <button class="pf-btn pf-btn--secondary" style="color:#c62828;border-color:rgba(198,40,40,0.3)" onclick="methods.removeSecondaryEmail(${ value.id })">Fjern</button>
                            </div>
                        </div>
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
        let isLoading = btn.data('loading');
        if (!isLoading) {
            btn.data('original-text', btn.html());
            btn.html('<i class="fa fa-spinner fa-pulse fa-fw"></i>');
            btn.addClass("disabled").prop('disabled', true);
            btn.data('loading', true);
        } else {
            btn.html(btn.data('original-text'));
            btn.removeClass("disabled").prop('disabled', false);
            btn.data('loading', false);
        }
    },

    setError : function(errors, form){
        $.each(errors, function(key, error){
            var input = form.find(`[name='${ key }']`);
            var parent = input.closest('.form-group, .pf-email-add');
            parent.append(`<small class='text-danger' style="display:block;margin-top:0.25rem;font-size:0.78rem;"><i class='fa fa-exclamation-circle'></i> ${ error[0] }</small>`)
        })
    },
    clearError : function(form){
        form.find('.text-danger').remove();
    },
    clearInputs : function(form){
        form.find('input').not('[type=hidden]').val('');
    },
};

methods.listEmails();