import Vue from 'vue'
import VueToasted from 'vue-toasted'

Vue.use( VueToasted, {
    duration : 5000,
    theme: "bubble",
    position: "bottom-center",
    singleton : true
});

Vue.toasted.register('showSuccessMsg',
    (payload) => {

        if(! payload.message) {
            return "Success";
        }

        return "<i class='fa fa-check'></i> <span class='ml-1'>"+ payload.message+"</span>";
    },
    {
        type : 'success'
    }
);


Vue.toasted.register('showErrorMsg',
    (payload) => {
        if(! payload.message) {
            return "Opps... Something went wrong.";
        }

        return "<i class='fa fa-exclamation-circle'></i> <span class='ml-1'>"+ payload.message+"</span>";
    },
    {
        type : 'error',
    }
);
