'use strict';

const methods = {

    submitEmail : function(form, data){
        let self = this
        let btn = form.find('.btn');
        data.emails = [ data.email ];
        delete data.email;
        this.setSpinningIcon(btn, 'loading');
        $.post(invitation_link, data)
            .then(function(response){
                self.clearInput(form);
                self.setSpinningIcon(btn, 'finished');
                toastr.success('Invitation has been sent')
            })
    },

    validateEmail : function(form)
    {
        let self = this
        let data = {
            email : form.find("[name='email']").val(),
            book_id : book_id,
            msg : ''
        }
        $.post('/email/validate', data)
            .then(function(response){
                self.clearError(form);
                self.submitEmail(form, data)
            })
            .catch(function(err){
                let status = err.status;
                let error = err.responseJSON;
                self.clearError(form);
                if(status === 422 || status === 500)
                {
                    self.setError(form, error)
                }
            })
    },

    setError : function(form, error)
    {
        $.each(error, function(key, err){
            form.find(`[name='${key}']`).after(`<small class="text-danger"><i class="fa fa-exclamation-circle"></i> ${ err[0] }</small>`)
        })
    },

    clearError : function(form){
        form.find('.col-md-6').each(function(){
            $(this).find("small.text-danger").remove()
        })
    },

    clearInput : function(form)
    {
        form.find('.form-control').each(function(){
            $(this).val('')
        })
    },

    setSpinningIcon : function(el, stat)
    {
        let icon = el.find(".icon")
        if(stat == 'loading')
        {
            el.addClass("disabled")
            el.prop('disabled', true)
            icon.removeClass("display-none")
            return
        }
        el.removeClass("disabled")
        el.prop('disabled', false)
        icon.addClass("display-none")
    },

    sendToMyEmail : function(email)
    {
        let resultDiv = $("#resultDiv");
        let requestingDiv = $("#requestingDiv");
        $.post(invitation_link, {
            book_id : book_id,
            emails : [ email ],
            msg : ''
        })
            .then(function(response){
                resultDiv.removeClass("display-none");
                requestingDiv.addClass("display-none");
            })
    }

};

$("#sentEmailForm").submit(function(e){
    e.preventDefault();
    methods.validateEmail($(this))
});

if(email)
{
    methods.sendToMyEmail(email);
}

