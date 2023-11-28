<template>
    <div>
        <table>
            <col width="250" valign="top" />
            <col width="1000" valign="top" />
            <tr>
                <td>
                    <div>
                        <div class="mb-3">
                            <select
                                v-model="upMonth"
                                class="form-select"
                                aria-label="Default select example"
                            >
                                <option value="1">январь</option>
                                <option value="2">февраль</option>
                                <option value="3">март</option>
                                <option value="4">апрель</option>
                                <option value="5">май</option>
                                <option value="6">июнь</option>
                                <option value="7">июль</option>
                                <option value="8">август</option>
                                <option value="9">сентябрь</option>
                                <option value="10">октябрь</option>
                                <option value="11">ноябрь</option>
                                <option value="12">декабрь</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <select
                                v-model="upYear"
                                class="form-select"
                                aria-label="Default select example"
                            >
                                <option value="2020">2020</option>
                                <option value="2021">2021</option>
                                <option value="2022">2022</option>
                                <option value="2023">2023</option>
                                <option value="2024">2024</option>
                                <option value="2025">2025</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <input
                                @click.prevent="getMotivations"
                                type="submit"
                                value="Обновить"
                                class="btn btn-primary"
                            />
                        </div>
                    </div>
                </td>
                <td>
                    <audio
                        :src="phoneRecordsChainSwitch"
                        :class="
                            this.phoneRecordsChainSwitch.length > 30
                                ? ''
                                : 'd-none'
                        "
                        autoplay="autoplay"
                        controls
                        controlsList="nodownload"
                        oncontextmenu="return false"
                        preload="metadata"
                    ></audio>
                </td>
            </tr>
        </table>

        <p class="center" v-if="!motivations.length">
            Мотиваций в этом месяце нет!
        </p>

        <table class="table table-bordered" v-else>
            <thead>
                <tr>
                    <th scope="col">№</th>
                    <th scope="col">Название</th>
                    <th scope="col">Описание</th>
                    <th scope="col">Коэффициент</th>
                    <th>Подробнее</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="motivation in motivations"
                    :key="motivation.number"
                    :class="[motivation.classColorMotivation]"
                >
                    <th>{{ motivation.number }}</th>
                    <td>{{ motivation.name }}</td>
                    <td>{{ motivation.description }}</td>
                    <td>{{ motivation.cost }}</td>
                    <td>
                        <button
                            class="btn btn-outline-primary"
                            type="button"
                            data-bs-toggle="modal"
                            data-bs-target="#modal-motivation"
                            @click.prevent="
                                takeIdObject(motivation.IdObject),
                                    getChainSwitch(motivation.IdObject)
                            "
                        >
                            Подробнее
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
        <PopupMotivation>
            <template v-slot:headerMotivation>
                Информация по связанным коммутациям
            </template>
            <template v-slot:bodyMotivation>
                <p class="center" v-if="!chainSwitches.length">
                    Связанные коммутации отсутствуют!
                </p>
                <table class="table table-bordered" v-else>
                    <thead>
                        <tr>
                            <th scope="col">№</th>
                            <th scope="col">Дата</th>
                            <th scope="col">ФИО оператора</th>
                            <th scope="col">Ознакомиться</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="chainSwitch in chainSwitches"
                            :key="chainSwitch.number"
                        >
                            <td>{{ chainSwitch.number }}</td>
                            <td>
                                {{ chainSwitch.dateTime | date("datetime") }}
                            </td>
                            <td>{{ chainSwitch.fullName }}</td>
                            <td>
                                <button
                                    :class="{
                                        'd-none': chainSwitch.type === 1,
                                    }"
                                    class="btn btn-outline-info"
                                    type="button"
                                    data-bs-dismiss="modal"
                                    @click.prevent="
                                        takeIdObjectChainSwitch(
                                            chainSwitch.IdObject
                                        ),
                                            getPhoneRecordsChainSwitch(
                                                chainSwitch.IdObject
                                            )
                                    "
                                >
                                    Прослушать
                                </button>
                                <div
                                    :class="{
                                        'd-none': chainSwitch.type === 0,
                                    }"
                                    class="btn-group"
                                >
                                    <a
                                        role="button"
                                        class="btn btn-outline-info"
                                        :href="`https://sp-oktell-stat1.patio-minsk.by/ChatCard/?id=&quot;${chainSwitch.IdObject}&quot;`"
                                        target="_blank"
                                    >
                                        Прочитать
                                    </a>
                                    <button
                                        type="button"
                                        class="btn btn-outline-info dropdown-toggle dropdown-toggle-split"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                    >
                                        <span class="visually-hidden"
                                            >Переключатель выпадающего
                                            списка</span
                                        >
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a
                                                class="dropdown-item"
                                                :href="`https://sp-oktell-stat1.patio-minsk.by/ChatCard/?id=&quot;${chainSwitch.IdObject}&quot;`"
                                                target="_blank"
                                            >
                                                Открыть в браузере
                                            </a>
                                        </li>
                                        <li>
                                            <a
                                                class="dropdown-item"
                                                :href="`https://app.jivo.ru/chat/archive/${chainSwitch.chat_Id}_chat-490834-${chainSwitch.chat_Id}`"
                                                target="_blank"
                                            >
                                                Открыть в Jivo
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </template>
            <template v-slot:footerMotivation>
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Закрыть
                </button>
            </template>
        </PopupMotivation>
    </div>
</template>

<script>
import PopupMotivation from "./popup/PopupMotivation.vue";

export default {
    name: "MotivationComponent",

    props: {
        authoperator: {
            type: Object,
            default: "",
        },
    },

    components: {
        PopupMotivation,
    },

    data() {
        return {
            motivations: "",
            upMonth: 1,
            upYear: 2022,
            editIdCall: "",
            editIdCallChainSwitch: "",
            chainSwitches: [],
            phoneRecordsChainSwitch: "",
        };
    },

    mounted() {
        this.getNewDate();
        this.getMotivations();
    },

    methods: {
        //Получение списка жалоб и благодарностей (мотивация)
        getMotivations() {
            axios
                .get(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/QCS/GetMotivation.php",
                    {
                        params: {
                            Month: `${this.upMonth}`,
                            Year: `${this.upYear}`,
                            IdOperator: `${this.authoperator.IdOperator}`,
                        },
                    }
                )
                .then((response) => {
                    const motivations = response.data;
                    this.motivations = motivations.map((motivation) => {
                        return {
                            ...motivation,
                            classColorMotivation:
                                motivation.type === 1
                                    ? "table-danger"
                                    : "table-success",
                        };
                    });
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        //Получение связанных коммутаций
        getChainSwitch() {
            axios
                .get(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/QCS/GetLinkObject.php",
                    {
                        params: {
                            IdParentObject: `${this.editIdCall}`,
                            Section: "2",
                        },
                    }
                )
                .then((response) => {
                    this.chainSwitches = response.data;
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        //Получаем запись разговоров связанной коммутации
        getPhoneRecordsChainSwitch() {
            this.phoneRecordsChainSwitch = "";
            axios
                .get(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/GetAudio.php",
                    {
                        params: {
                            IdCall: `${this.editIdCallChainSwitch}`,
                            login: `${this.authoperator.LoginOperator}`,
                        },
                    }
                )
                .then((response) => {
                    let linkChainSwitch =
                        "data:audio/ogg;base64," + response.data;
                    this.phoneRecordsChainSwitch = linkChainSwitch;
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        getNewDate() {
            let dateNaw = new Date();
            let monthNaw = dateNaw.getMonth() + 1;
            let yearNaw = dateNaw.getFullYear();
            this.upMonth = monthNaw;
            this.upYear = yearNaw;
        },
        //Получаем ID мотивации
        takeIdObject(IdObject) {
            this.editIdCall = IdObject;
        },
        //Получаем ID звонка связанной коммутации
        takeIdObjectChainSwitch(IdObject) {
            this.editIdCallChainSwitch = IdObject;
        },
    },
};
</script>
<style scoped>
audio {
    width: 95%;
    background-color: #fff;
    border: 4px solid gray;
    margin-bottom: 30px;
    margin-left: 15px;
    margin-right: 15px;
}
</style>
