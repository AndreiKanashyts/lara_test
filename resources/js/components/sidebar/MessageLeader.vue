<template>
    <span>
        <div
            class="badge badge-danger"
            :class="{ 'd-none': message._CountWIU == 0 }"
            v-for="message in messages"
            :key="message.id"
        >
            <big>{{ message._CountWIU }}</big>
        </div>
    </span>
</template>

<script>
export default {
    name: "MessageLeader",

    props: {
        authoperator: {
            type: Object,
            default: "",
        },
    },

    data() {
        return {
            messages: [],
        };
    },

    mounted() {
        this.getMessages();
    },

    methods: {
        getMessages() {
            //Получение информации для счетчиков
            axios
                .get("https://4cc.patio-minsk.by/api/v2/badge", {
                    params: {
                        system_user: `${this.authoperator.SystemUser}`,
                    },
                })
                .then((response) => {
                    this.messages = response.data.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
    },
};
</script>
