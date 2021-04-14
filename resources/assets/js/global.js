import Vue from 'vue';

Vue.mixin({

    methods: {
        removeValidationError() {
            $('.validation-err').remove();
        },

        processError(error) {
            const err_data = error.response.data;
            if (error.response.status === 422) {
                this.modifyErrorList(err_data); // call the parent function to append error
            } else {
                this.$toasted.global.showErrorMsg({
                    message : err_data.message
                });
            }
        },

        modifyErrorList(err_data) {
            let errList = [];
            $('.validation-err').remove();
            $.each(err_data,function(k, v){
                errList[k] = v[0];
                let element = $("[name="+k+"]");

                if (element.closest('.input-group').length) {
                    element = element.closest('.input-group');
                }

                element.after("<small class='text-danger validation-err'>" +
                    "<i class='fas fa-exclamation-circle'></i> " +
                    "<span>" + v[0]+"</span></small>");
            });
        },
    }


});