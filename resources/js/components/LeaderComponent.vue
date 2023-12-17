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
                                @click.prevent="getJobs(), firstPage()"
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

        <p class="center" v-if="!jobs.length">Работ в этом месяце нет!</p>

        <table class="table table-bordered" v-else>
            <thead>
                <tr>
                    <th scope="col">№</th>
                    <th scope="col">Дата</th>
                    <th scope="col">Тип</th>
                    <th scope="col">Тема</th>
                    <th scope="col">Вопрос в теме</th>
                    <th scope="col">Приоритет</th>
                    <th scope="col">Действие</th>
                    <th scope="col">Результат</th>
                    <th scope="col">Инициатор</th>
                    <th scope="col">Статус</th>
                    <th scope="col">Ознакомиться</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="job in paginatedJobs" :key="job.number">
                    <th>{{ job.number }}</th>
                    <td>{{ job._DateIncident }}</td>
                    <td>{{ job._TypeName }}</td>
                    <td>{{ job._TopicName }}</td>
                    <td>{{ job._TopicQuestionName }}</td>
                    <td>{{ job._PriorityName }}</td>
                    <td>{{ job._ActionName }}</td>
                    <td>{{ job._ResultName }}</td>
                    <td>{{ job._CreatedByName }}</td>
                    <td>
                        <span :class="[job.classStatus]">
                            {{ job._StatusName }}
                        </span>
                    </td>
                    <td>
                        <button
                            type="button"
                            class="btn btn-outline-primary position-relative"
                            data-bs-toggle="modal"
                            data-bs-target="#popupControlModal"
                            @click.prevent="
                                takeIdObject(job._Id, job._TopicQuestionName),
                                    getWIUForID(job._Id),
                                    getPhoneComments(job._Id),
                                    getChainSwitch(job._Id),
                                    getFileDownloads(job._Id),
                                    changeJobStatusOk(job._Id)
                            "
                        >
                            Подробнее
                            <span
                                v-if="job._ActionRequired == 1"
                                class="position-absolute top-0 start-100 translate-middle badge border border-light rounded-circle bg-danger p-2"
                                ><span class="visually-hidden"
                                    >непрочитанные сообщения</span
                                ></span
                            >
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
        <nav
            :class="{ 'd-none': jobs.length < 11 }"
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
        <PopupControl>
            <template v-slot:header>
                Вопрос в теме: {{ editQuestionName }}
            </template>
            <template v-slot:body>
                <p class="center" v-if="!jobForIds.length">
                    Идёт загрузка данных...
                </p>
                <div v-else>
                    <form v-if="checkVisible">
                        <div
                            v-for="jobForId in jobForIds"
                            :key="jobForId._Description"
                        >
                            <div class="row">
                                <div class="w-25 col-auto">
                                    <label for="Дата" class="col-form-label"
                                        >Дата:
                                    </label>
                                </div>
                                <div class="w-75 col-auto">
                                    <input
                                        class="form-control"
                                        type="text"
                                        :value="[jobForId._DateIncident]"
                                        aria-label="Дата"
                                        disabled
                                        readonly
                                    />
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="w-25 col-auto">
                                    <label
                                        for="Инициатор"
                                        class="col-form-label"
                                        >Инициатор:
                                    </label>
                                </div>
                                <div class="w-75 col-auto">
                                    <input
                                        class="form-control"
                                        type="text"
                                        :value="[jobForId._CreatedByName]"
                                        aria-label="Инициатор"
                                        disabled
                                        readonly
                                    />
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="w-25 col-auto">
                                    <label for="Тип" class="col-form-label"
                                        >Тип:
                                    </label>
                                </div>
                                <div class="w-75 col-auto">
                                    <input
                                        class="form-control"
                                        type="text"
                                        :value="[jobForId._TypeName]"
                                        aria-label="Тип"
                                        disabled
                                        readonly
                                    />
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="w-25 col-auto">
                                    <label for="Тема" class="col-form-label"
                                        >Тема:
                                    </label>
                                </div>
                                <div class="w-75 col-auto">
                                    <input
                                        class="form-control"
                                        type="text"
                                        :value="[jobForId._TopicName]"
                                        aria-label="Тема"
                                        disabled
                                        readonly
                                    />
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="w-25 col-auto">
                                    <label
                                        for="Вопрос в теме"
                                        class="col-form-label"
                                        >Вопрос в теме:
                                    </label>
                                </div>
                                <div class="w-75 col-auto">
                                    <input
                                        class="form-control"
                                        type="text"
                                        :value="[jobForId._TopicQuestionName]"
                                        aria-label="Вопрос в теме"
                                        disabled
                                        readonly
                                    />
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="w-25 col-auto">
                                    <label
                                        for="Приоритет"
                                        class="col-form-label"
                                        >Приоритет:
                                    </label>
                                </div>
                                <div class="w-75 col-auto">
                                    <input
                                        class="form-control"
                                        type="text"
                                        :value="[jobForId._PriorityName]"
                                        aria-label="Приоритет"
                                        disabled
                                        readonly
                                    />
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="w-25 col-auto">
                                    <label
                                        for="Результат"
                                        class="col-form-label"
                                        >Результат:
                                    </label>
                                </div>
                                <div class="w-75 col-auto">
                                    <input
                                        class="form-control"
                                        type="text"
                                        :value="[jobForId._ResultName]"
                                        aria-label="Результат"
                                        disabled
                                        readonly
                                    />
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="w-25 col-auto">
                                    <label for="Действие" class="col-form-label"
                                        >Действие:
                                    </label>
                                </div>
                                <div class="w-75 col-auto">
                                    <input
                                        class="form-control"
                                        type="text"
                                        :value="[jobForId._ActionName]"
                                        aria-label="Действие"
                                        disabled
                                        readonly
                                    />
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="w-25 col-auto">
                                    <label for="Статус" class="col-form-label"
                                        >Статус:
                                    </label>
                                </div>
                                <div class="w-75 col-auto">
                                    <input
                                        class="form-control"
                                        type="text"
                                        :value="[jobForId._StatusName]"
                                        aria-label="Статус"
                                        disabled
                                        readonly
                                    />
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="w-25 col-auto">
                                    <label for="Описание" class="col-form-label"
                                        >Описание:
                                    </label>
                                </div>
                                <div class="w-75 col-auto">
                                    <p
                                        class="p-3 text-primary-emphasis bg-light border border-primary-subtle rounded-3"
                                    >
                                        {{ jobForId._Description }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </form>
                    <table
                        class="table table-bordered"
                        v-else-if="checkCommentVisible"
                    >
                        <thead>
                            <tr>
                                <th scope="col">Дата</th>
                                <th scope="col">Автор</th>
                                <th scope="col">Временная метка</th>
                                <th scope="col">Комментарий</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="phoneComment in phoneComments"
                                :key="phoneComment._CreatedOn"
                                :class="[phoneComment.classMarcup]"
                            >
                                <td>
                                    {{
                                        phoneComment._CreatedOn
                                            | date("datetime")
                                    }}
                                </td>
                                <td>{{ phoneComment._FullName }}</td>
                                <td>{{ phoneComment._TimeMark }}</td>
                                <td>{{ phoneComment._Comment }}</td>
                            </tr>
                        </tbody>
                    </table>
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
                                    {{
                                        chainSwitch._TimeStart
                                            | date("datetime")
                                    }}
                                </td>
                                <td>{{ chainSwitch._FullName }}</td>
                                <td>
                                    <button
                                        v-if="
                                            chainSwitch._TypeCommunication == 1
                                        "
                                        class="btn btn-outline-info"
                                        type="button"
                                        data-bs-dismiss="modal"
                                        @click.prevent="
                                            takeIdObjectChain(
                                                chainSwitch._IDCommutation
                                            ),
                                                getPhoneRecordsChainSwitch(
                                                    chainSwitch._IDCommutation
                                                )
                                        "
                                    >
                                        Прослушать
                                    </button>
                                    <div v-else class="btn-group">
                                        <a
                                            role="button"
                                            class="btn btn-outline-info"
                                            :href="`https://sp-oktell-stat2.patio-minsk.by/melissaProduction/WebView2/JivoChat/?Id=${chainSwitch._IDCommutation}#current`"
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
                                        <ul class="dropdown-menu m-0">
                                            <li>
                                                <a
                                                    class="dropdown-item"
                                                    :href="`https://sp-oktell-stat2.patio-minsk.by/melissaProduction/WebView2/JivoChat/?Id=${chainSwitch._IDCommutation}#current`"
                                                    target="_blank"
                                                >
                                                    Открыть в браузере
                                                </a>
                                            </li>
                                            <li>
                                                <a
                                                    class="dropdown-item"
                                                    :href="`https://app.jivo.ru/chat/archive/${chainSwitch._Identifier}_chat-490834-${chainSwitch._Identifier}`"
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
                    <div
                        :class="{
                            'd-none':
                                checkVisible === true ||
                                checkCommentVisible === false,
                        }"
                        class="mb-3"
                    >
                        <label
                            for="exampleFormControlTextarea1"
                            class="form-label"
                            >Введите ваш комментарий:</label
                        >
                        <textarea
                            class="form-control"
                            id="exampleFormControlTextarea1"
                            placeholder="Перед отправкой комментария введите сообщение"
                            aria-label="Перед отправкой комментария введите сообщение"
                            rows="3"
                            v-model="commentDispute"
                        >
                        </textarea>
                        <button
                            :disabled="!isDisabledAddComment"
                            type="button"
                            class="btn btn-danger mt-3"
                            @click.prevent="addComment"
                        >
                            Отправить комментарий
                        </button>
                    </div>
                    <div>
                        <ul>
                            <li v-for="file in fileDownloads" :key="file._ID">
                                <a :href="file._PathFile" target="_blank">{{
                                    file._Name
                                }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </template>
            <template v-slot:footer>
                <button
                    type="button"
                    class="btn btn-primary"
                    @click.prevent="swapTablesCheck"
                >
                    Основное
                </button>
                <button
                    type="button"
                    class="btn btn-warning"
                    @click.prevent="swapTablesComment"
                >
                    Комментарии
                </button>
                <button
                    v-for="chainSwitch in chainSwitches"
                    :key="chainSwitch.number"
                    :class="{ 'd-none': chainSwitch.number !== 1 }"
                    type="button"
                    class="btn btn-success"
                    @click.prevent="
                        swapTablesCheckComment(),
                            takeIdObjectChain(chainSwitch._IDCommutation)
                    "
                >
                    Связка
                </button>
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Закрыть
                </button>
            </template>
        </PopupControl>
    </div>
</template>

<script>
import PopupControl from "./popup/PopupControl.vue";

export default {
    name: "LeaderComponent",

    components: {
        PopupControl,
    },

    props: {
        authoperator: {
            type: Object,
            default: "",
        },
    },

    data() {
        return {
            upMonth: 1,
            upYear: 2022,
            jobs: [],
            jobForIds: [],
            phoneComments: [],
            chainSwitches: [],
            fileDownloads: [],
            editIdObject: "",
            editIdObjectChain: "",
            editQuestionName: "",
            phoneRecordsChainSwitch: "",
            checkVisible: true,
            checkCommentVisible: false,
            userPerPage: 10,
            pageNumber: 1,
            commentDispute: "",
            apiBaseUrl: "https://4cc.patio-minsk.by/api",
        };
    },

    computed: {
        //Пагинация 1 этап
        pages() {
            return Math.ceil(this.jobs.length / 10);
        },
        //Пагинация 2 этап
        paginatedJobs() {
            let from = (this.pageNumber - 1) * this.userPerPage;
            let to = from + this.userPerPage;
            return this.jobs.slice(from, to);
        },
        //Выключение кнопки добавление комментариев
        isDisabledAddComment() {
            return this.commentDispute;
        },
    },

    mounted() {
        this.getNewDate();
        this.getJobs();
    },

    methods: {
        //Получение реестра (Проработка)
        async getJobs() {
            await axios
                .get(`${this.apiBaseUrl}/v2/get-wiu-list`, {
                    params: {
                        month: `${this.upMonth}`,
                        year: `${this.upYear}`,
                        system_user: `${this.authoperator.SystemUser}`,
                    },
                })
                .then((response) => {
                    const jobs = response.data.data;
                    this.jobs = jobs.map((job) => {
                        return {
                            ...job,
                            classStatus:
                                job._StatusName === "Новый"
                                    ? "badge badge-danger"
                                    : job._StatusName === "В работе"
                                    ? "badge badge-info"
                                    : "badge badge-success",
                        };
                    });
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        //Сбрасываем страницы
        firstPage() {
            this.pageNumber = 1;
        },
        //Получаем IdObject
        takeIdObject(_Id, _TopicQuestionName) {
            this.checkVisible = true;
            this.editIdObject = _Id;
            this.editQuestionName = _TopicQuestionName;
        },
        //Получаем IdObjectChain
        takeIdObjectChain(_IDCommutation) {
            this.editIdObjectChain = _IDCommutation;
        },
        //Получение основной информации по ID (Работа с оператором)
        async getWIUForID() {
            this.jobForIds = "";
            await axios
                .get(`${this.apiBaseUrl}/v2/get-wiu`, {
                    params: {
                        id: `${this.editIdObject}`,
                        system_user: `${this.authoperator.SystemUser}`,
                    },
                })
                .then((response) => {
                    this.jobForIds = response.data.data;
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        //Получение комментариев оцененной коммутации
        async getPhoneComments() {
            await axios
                .get(`${this.apiBaseUrl}/v2/comment`, {
                    params: {
                        id: `${this.editIdObject}`,
                        section_id: 22,
                        system_user: `${this.authoperator.SystemUser}`,
                    },
                })
                .then((response) => {
                    const phoneComments = response.data.data;
                    this.phoneComments = phoneComments.map((phoneComment) => {
                        return {
                            ...phoneComment,
                            classMarcup:
                                phoneComment._MarkupComment === "2"
                                    ? "table-danger"
                                    : phoneComment._MarkupComment === "3"
                                    ? "table-warning"
                                    : phoneComment._MarkupComment === "4"
                                    ? "table-info"
                                    : "",
                        };
                    });
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        //Данные из API связанные коммутации
        async getChainSwitch() {
            await axios
                .get(`${this.apiBaseUrl}/v2/linked-object`, {
                    params: {
                        id: `${this.editIdObject}`,
                        section_id: "22",
                    },
                })
                .then((response) => {
                    this.chainSwitches = response.data.data;
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        //Установка параметра "Ознакомился" (Работа с оператором)
        async changeJobStatusOk() {
            await axios
                .post(`${this.apiBaseUrl}/v2/wiu-employee-familiarize`, {
                    id: `${this.editIdObject}`,
                    system_user: `${this.authoperator.SystemUser}`,
                })
                .then(() => {
                    this.getJobs();
                    this.$parent.$refs.lead.getMessages();
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        //Получаем запись разговоров связанной коммутации
        async getPhoneRecordsChainSwitch() {
            this.phoneRecordsChainSwitch = "";
            await axios
                .get(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/GetAudio.php",
                    {
                        params: {
                            IdCall: `${this.editIdObjectChain}`,
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
        //Добавление комментариев оператором
        async addComment() {
            await axios
                .post(`${this.apiBaseUrl}/v2/add-comment`, {
                    id: `${this.editIdObject}`,
                    type_communication: 0,
                    comment: this.commentDispute,
                    section: 22,
                    system_user: `${this.authoperator.SystemUser}`,
                })
                .then(() => {
                    this.commentDispute = "";
                    this.getJobs();
                    this.getPhoneComments();
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        // Получение файлов по ID
        async getFileDownloads() {
            await axios
                .get(`${this.apiBaseUrl}/v2/file`, {
                    params: {
                        id: `${this.editIdObject}`,
                        section_id: 22,
                    },
                })
                .then(async (response) => {
                    this.fileDownloads = response.data.data;
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        //Включаем Основное в модалке
        swapTablesCheck() {
            this.checkVisible = true;
            this.checkCommentVisible = false;
        },
        //Включаем комментарии в модалке
        swapTablesComment() {
            this.checkVisible = false;
            this.checkCommentVisible = true;
        },
        //Включаем связанные коммутации в модалке
        swapTablesCheckComment() {
            this.checkVisible = false;
            this.checkCommentVisible = false;
        },
        //Выбор страницы
        pageClick(page) {
            this.pageNumber = page;
        },
        //Выбор текущей даты
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
