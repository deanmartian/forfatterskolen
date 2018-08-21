'use strict';

const authorSettings = {

    setBookSettings : function(data)
    {
        let self = this;
        data.book_id = $("[name='book_id']").val();
        $.post('/account/book-author/book/settings/set', data)
            .then(function(response){
                if(_.size(data) > 1)
                {
                    toastr.success('Book Settings Updated.', 'Success')
                }
                self.displayBookSettings(response);
            })
    },

    displayBookSettings : function(data)
    {
        let self = this;
        $.each(data, function(key, val){
            let input = $("#settings").find(`[name='${ key }']`);
            if(input.length)
            {
                if(input.is("input[type='checkbox']"))
                {
                    input.prop('checked', parseInt(val));
                    if(key === "is_reading_reminder_on")
                    {
                        self.toggleReminders(input, 'get');
                    }
                }else{
                    //self.data.selectedBookUnits = val;
                    input.val(val)
                }
            }
        })
    },

    prepareBookSettingData : function()
    {
        let data = {}
        $("#settingsDiv :input").each(function(){
            let key = $(this).prop('name');
            if(! key)
            {
                return true
            }
            let type = $(this).prop('type');
            let val = type === 'checkbox'? +$(this).is(":checked") : $(this).val();
            data[key] = val
        });
        this.setBookSettings(data)
    },

    toggleReminders : function(el, request)
    {
        let status = +$(el).is(':checked');
        $("[name='days_of_reminder']").prop('disabled', !status);
        $(".reminder-status").html(status? "enabled" : "disabled");
        $(el).parent().next().html((status? "Disabled" : "Enabled") + " Reading Reminders");
        if(request === 'get')
        {
            return;
        }
        this.setBookSettings({ is_reading_reminder_on : status });
    },

    deleteBook : function()
    {
        $.confirm({
            title: 'Delete Book',
            content: 'Are you sure to delete it?',
            type: 'red',
            typeAnimated: true,
            buttons: {
                tryAgain: {
                    text: 'Ok',
                    btnClass: 'btn-red',
                    action: function(){
                        let book_id = $("[name='book_id']").val();
                        $.post('/account/book-author/book/destroy', { id : book_id })
                            .then(function(){
                                window.location.href = '/account/book-author';
                            })
                    }
                },
                close: function () {
                }
            }
        })
    }

};

/*$("[name=is_reading_reminder_on]").bootstrapToggle({
    on: '',
    off: ''
});*/
authorSettings.setBookSettings({});