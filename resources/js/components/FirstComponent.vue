<template>
    <div class="mt-3">
        <div
            class="container-fluid"
            v-for="userInfo in startInfo"
            :key="userInfo.number"
        >
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ userInfo.avgQualityService }}</h3>

                            <p>
                                Среднее качество<br />
                                обслуживания
                            </p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-align-center"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ userInfo.countInputCommunication }}</h3>

                            <p>
                                Входящие вызовы<br />
                                ЧАТ-ы
                            </p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-phone">вх</i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ userInfo.countOutputCommunication }}</h3>

                            <p>
                                Исходящие<br />
                                Вызовы
                            </p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-phone">исх</i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>
                                {{ userInfo.timePostProcessing
                                }}<sup style="font-size: 20px">мин</sup>
                            </h3>

                            <p>
                                Время обработки<br />
                                звонка
                            </p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <!-- /.row -->
        </div>
    </div>
</template>

<script>
export default {
    name: "FirstComponent",

    props: {
        authoperator: {
            type: Object,
            default: "",
        },
    },

    data() {
        return {
            startInfo: [],
            upMonth: 1,
            upYear: 2022,
        };
    },

    mounted() {
        this.getNewDate();
        this.getStartInfo();
    },

    methods: {
        getStartInfo() {
            axios
                .get(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/PA_Home.php",
                    {
                        params: {
                            Month: `${this.upMonth}`,
                            Year: `${this.upYear}`,
                            IdOperator: `${this.authoperator.IdOperator}`,
                        },
                    }
                )
                .then((response) => {
                    this.startInfo = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        getNewDate() {
            let dateNaw = new Date();
            let monthNaw = dateNaw.getMonth() + 1;
            let yearNaw = dateNaw.getFullYear();
            this.upMonth = monthNaw;
            this.upYear = yearNaw;
        },
    },
};
</script>
