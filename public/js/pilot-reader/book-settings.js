'use strict';

const settings = {

    submitReadingStatus : function(status, form){
        let self = this;
        let book_id = $("[name='book_id']").val();
        let data = {
            status : status,
            book_id : book_id
        };
        if(form)
        {
            data.reasons = form.find("[name='reasons']").val()
        }
        $.post('/account/book/settings/reading/status/set', data)
            .then(function(response){
                self.clearInput(form);
                self.clearError(form);
                toastr.success(response.success+" You'll be redirected after 3 seconds.", "Success");
                setTimeout(function(){
                    window.location.href = '/account/book-author';
                }, 3000);
            })
            .catch(function(err){
                self.clearError(form);
                let status = err.status;
                let error = err.responseJSON;
                if(status === 422)
                {
                    self.setError(form, error)
                }
            });
    },

    toggleReasonContainer : function(stat)
    {
        let btn_container = $("#btn-container");
        btn_container.css({ 'display' : (stat !== 'show'? "block" : "none")});
        let reasons_container = $("#reasons_div");
        reasons_container.css({ 'display' : (stat === 'show'? "block" : "none")});
        if(stat === 'show')
        {
            let form = $("#quitForm");
            this.clearInput(form);
            this.clearError(form);
        }
    },

    clearInput : function(form)
    {
        if(form)
        {
            form.find(".form-control").each(function(){
                $(this).val('')
            })
        }
    },

    setError : function(form, error)
    {
        $.each(error, function(key, err){
            form.find(`[name='${key}']`).after(`<small class="text-danger"><i class="fa fa-exclamation-circle"></i> ${ err[0] }</small>`)
        })
    },

    clearError : function(form){
        if(form)
        {
            form.find('.form-group').each(function(){
                $(this).find("small.text-danger").remove()
            })
        }
    },

};

$("#quitForm").submit(function(e){
    e.preventDefault();
    settings.submitReadingStatus(2, $(this))
});