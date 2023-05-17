import Vue from 'vue';

Vue.mixin({

    methods: {
        removeValidationError() {
            $('.validation-err').remove();
        },

        processError(error) {
            const err_data = error.response.data;
            console.log(error.response.status);
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

            if (err_data['errors']) {
                $.each(err_data['errors'],function(k, v){
                    errList[k] = v[0];
                    let element = $("[name="+k+"]");

                    if (element.closest('.input-group').length) {
                        element = element.closest('.input-group');
                    }

                    element.after("<small class='text-danger validation-err'>" +
                        "<i class='fa fa-exclamation-circle'></i> " +
                        "<span>" + v[0]+"</span></small>");
                });
            } else {
                $.each(err_data,function(k, v){
                    errList[k] = v[0];
                    let element = $("[name="+k+"]");

                    if (element.closest('.input-group').length) {
                        element = element.closest('.input-group');
                    }

                    element.after("<small class='text-danger validation-err'>" +
                        "<i class='fa fa-exclamation-circle'></i> " +
                        "<span>" + v[0]+"</span></small>");
                });
            }
        },

        customFieldError(element_name, message) {
            let element = $("[name="+element_name+"]");

            if (element.closest('.input-group').length) {
                element = element.closest('.input-group');
            }

            element.after("<small class='text-danger validation-err'>" +
                "<i class='fa fa-exclamation-circle'></i> " +
                "<span>" + message +"</span></small>");
        },

        updateRecordFromObject(obj, id, updatedData) {
            const index = _.findIndex(obj, {id: id});
            obj.splice(index, 1, updatedData);
        },

        deleteRecordFromObject(obj, id) {
            const index = _.findIndex(obj, {id: id});
            obj.splice(index, 1);
        },

        roundCount(count, min){
            return (parseFloat(count) / parseFloat(min)) * parseFloat(min)
            //return Math.ceil(parseFloat(count) / parseFloat(min)) * parseFloat(min)
        },
    }


});