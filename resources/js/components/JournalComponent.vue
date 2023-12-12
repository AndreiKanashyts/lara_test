<template>
    <div>
        <table>
            <col width="250" valign="top" />
            <col width="250" valign="top" />
            <col width="1000" valign="top" />
            <tr>
                <td>
                    <div class="form-check form-check-inline">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="TypeCallRadioOptions"
                            id="inlineRadio2"
                            value="optionAllCall"
                            v-model="TypeCallRadioOptions"
                        />
                        <label class="form-check-label" for="inlineRadio2"
                            >Все звонки</label
                        >
                    </div>
                </td>
                <td>
                    <div class="form-check form-check-inline">
                        <input
                            class="form-check-input"
                            type="radio"
                            name="TypeCallRadioOptions"
                            id="inlineRadio3"
                            value="optionVoiceMail"
                            v-model="TypeCallRadioOptions"
                        />
                        <label class="form-check-label" for="inlineRadio3"
                            >Голосовая почта</label
                        >
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    Дата с:
                    <input type="date" name="timeSince" v-model="timeSince" />
                </td>
                <td>
                    Дата по:
                    <input type="date" name="timeTo" v-model="timeTo" />
                </td>
                <td>
                    <audio
                        :src="phoneRecords"
                        :class="this.phoneRecords.length > 30 ? '' : 'd-none'"
                        autoplay="autoplay"
                        controls
                        controlsList="nodownload"
                        oncontextmenu="return false"
                        preload="metadata"
                    ></audio>
                </td>
            </tr>
            <tr>
                <td>
                    <div
                        class="input-group"
                        style="margin-top: 7px; margin-bottom: 7px"
                    >
                        <span
                            class="input-group-text"
                            :class="{
                                'd-none':
                                    this.TypeCallRadioOptions ===
                                    'optionVoiceMail',
                            }"
                        >
                            Номер
                        </span>
                        <input
                            type="text"
                            aria-label="441111111"
                            class="form-control"
                            v-model="phone"
                            :class="{
                                'd-none':
                                    this.TypeCallRadioOptions ===
                                    'optionVoiceMail',
                            }"
                        />
                    </div>
                </td>
                <td>
                    <select
                        class="form-select"
                        id="idSelectFullName"
                        v-model="loginOperator"
                        :class="{
                            'd-none':
                                this.TypeCallRadioOptions === 'optionVoiceMail',
                        }"
                    >
                        <option value="_AllManager_">Все</option>
                        <option
                            v-for="user in users"
                            :key="user.login"
                            :value="user.login"
                        >
                            {{ user.FullName }}
                        </option>
                    </select>
                </td>
            </tr>

            <tr>
                <td>
                    <div>
                        <div class="mb-3">
                            <input
                                @click.prevent="getControls"
                                type="submit"
                                value="Поиск"
                                class="btn btn-primary"
                            />
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <p class="center" v-if="!journals.length">
            За выбранный период записи отсутствуют
        </p>

        <table class="table table-bordered" v-else>
            <thead>
                <tr>
                    <th scope="col">№</th>
                    <th scope="col">Дата и время звонка</th>
                    <th scope="col">Абонент А</th>
                    <th scope="col">Абонент Б</th>
                    <th scope="col">Длительность</th>
                    <th scope="col">Запись</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="journal in paginatedControls" :key="journal.number">
                    <td>{{ journal.number }}</td>
                    <td>{{ journal.timeAnswer }}</td>
                    <td>{{ journal.astr }}</td>
                    <td>{{ journal.bstr }}</td>
                    <td>{{ journal.timeCall }}</td>
                    <td>
                        <button
                            :class="{ 'd-none': journal.successful === 0 }"
                            class="btn btn-outline-info"
                            type="button"
                            @click.prevent="
                                takeIdCall(journal.IdCall, journal.phoneClient),
                                    getPhoneRecords(journal.IdCall)
                            "
                        >
                            Прослушать
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
        <nav
            :class="{ 'd-none': journals.length < 11 }"
            aria-label="Page navigation example"
        >
            <ul class="pagination">
                <li
                    class="page-item"
                    v-for="page in pages"
                    :key="page"
                    :class="{ 'page-item active': page === pageNumber }"
                    @click.prevent="pageClick(page)"
                >
                    <ul class="page-link">
                        {{
                            page
                        }}
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script>
export default {
    name: "JournalComponent",

    props: {
        authoperator: {
            type: Object,
            default: "",
        },
    },

    data() {
        return {
            journals: [],
            timeSince: "2022-01-01",
            timeTo: "2022-01-01",
            editIdCall: "",
            phone: "",
            loginOperator: "_AllManager_",
            TypeCallRadioOptions: "optionAllCall",
            phoneRecords: "",
            checkVisible: true,
            userPerPage: 10,
            pageNumber: 1,
            users: [],
        };
    },

    computed: {
        //Пагинация 1 этап
        pages() {
            return Math.ceil(this.journals.length / 10);
        },
        //Пагинация 2 этап
        paginatedControls() {
            let from = (this.pageNumber - 1) * this.userPerPage;
            let to = from + this.userPerPage;
            return this.journals.slice(from, to);
        },
    },

    mounted() {
        this.getNewDate();
        this.getControls();
        this.getUserFullName();
    },

    methods: {
        //Данные из API список звонков
        getControls() {
            this.pageNumber = 1;
            var selectFullNameValue = this.loginOperator;
            var searchType = 0;
            if (selectFullNameValue == "_AllManager_") {
                searchType = 0;
            } else {
                searchType = 1;
            }
            axios
                .get(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/GetVoiceCall.php",
                    {
                        params: {
                            type: searchType,
                            typeCall: `${this.TypeCallRadioOptions}`,
                            login: `${this.loginOperator}`,
                            phone: `${this.phone}`,
                            date1: `${this.timeSince}`,
                            date2: `${this.timeTo}`,
                        },
                    }
                )
                .then((response) => {
                    this.journals = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        //Получаем ID звонка
        takeIdCall(IdCall, phoneClient) {
            this.checkVisible = true;
            this.editIdCall = IdCall;
            this.editPhone = phoneClient;
        },

        isTakeIdCall(IdCall) {
            return this.editIdCall === IdCall;
        },
        //Получаем запись разговоров
        getPhoneRecords() {
            this.phoneRecords = "";
            var link = "";
            switch (this.TypeCallRadioOptions) {
                case "optionAllCall":
                    link =
                        "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/GetAudio.php";
                    break;
                case "optionVoiceMail":
                    link =
                        "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/PA_GetAudioVoiceMail.php";
                    break;
            }
            axios
                .get(link, {
                    params: {
                        IdCall: `${this.editIdCall}`,
                        login: `${this.authoperator.LoginOperator}`,
                    },
                })
                .then((response) => {
                    let link = "data:audio/ogg;base64," + response.data;
                    this.phoneRecords = link;
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        //Выбор страницы
        pageClick(page) {
            this.pageNumber = page;
        },
        getNewDate() {
            let dateNaw = new Date();
            let yearNaw = dateNaw.getFullYear();
            let monthNaw = String(dateNaw.getMonth() + 1).padStart(2, "0");
            let dayNaw = String(dateNaw.getDate()).padStart(2, "0");
            this.timeSince = yearNaw + "-" + monthNaw + "-" + dayNaw;
            this.timeTo = yearNaw + "-" + monthNaw + "-" + dayNaw;
        },
        getUserFullName() {
            axios
                .get(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/PA_GetUserFullName.php"
                )
                .then((response) => {
                    this.addtoListFullName(response.data);
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        addtoListFullName(json) {
            for (let i = 0; i < json.length; i++) {
                this.users.push(json[i]);
            }
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
