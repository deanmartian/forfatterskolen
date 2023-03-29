<template>
    <div class="row">
        <div class="d-block w-100">
            <h1>Chat</h1>
        </div> <br>
        <div class="form-group text-center">
            <input type="text" v-model="message" placeholder="Type your message" class="form-control">
            <button class="btn btn-primary pull-right" v-on:click="sendMessage">Send</button>
        </div>
        <div v-for="(message, index) in messages" :key="index">
            <p>@{{ message }}</p>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return {
            message: '',
            messages: [],
        }
    },

    methods: {
        sendMessage: function() {
            axios.post('/chat', {
                message: this.message,
            }).then(response => {
                console.log(response);
                this.messages.push(response.data.message);
                this.message = '';
            });
        },
    },

    mounted() {
        console.log("inside chat here");
    }
}
</script>
