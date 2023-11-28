<template>
    <span>
        <div
            class="badge badge-danger"
            :class="{ 'd-none': message.countMistake === 0 }"
            v-for="message in messages"
            :key="message.id"
        >
            <big>{{ message.countMistake }}</big>
        </div>
    </span>
</template>

<script>
export default {
    name: "MessageComment",

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
                .get(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/GetCountNewData.php",
                    {
                        params: {
                            IdOperator: `${this.authoperator.IdOperator}`,
                        },
                    }
                )
                .then((response) => {
                    this.messages = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
    },
};
</script>
