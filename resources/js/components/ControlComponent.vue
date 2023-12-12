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
                                @click.prevent="getControls(), firstPage()"
                                type="submit"
                                value="Обновить"
                                class="btn btn-primary"
                            />
                        </div>
                    </div>
                </td>
                <td>
                    <audio
                        v-if="phoneRecords.length > 30"
                        :src="phoneRecords"
                        autoplay="autoplay"
                        controls
                        controlsList="nodownload"
                        oncontextmenu="return false"
                        preload="metadata"
                    ></audio>
                    <audio
                        v-if="phoneRecordsChainSwitch.length > 30"
                        :src="phoneRecordsChainSwitch"
                        autoplay="autoplay"
                        controls
                        controlsList="nodownload"
                        oncontextmenu="return false"
                        preload="metadata"
                    ></audio>
                </td>
            </tr>
        </table>

        <p class="center" v-if="!controls.length">Оценок в этом месяце нет!</p>

        <table class="table table-bordered" v-else>
            <thead>
                <tr>
                    <th scope="col">№</th>
                    <th scope="col">Дата и время звонка</th>
                    <th scope="col">ФИО сотрудника</th>
                    <th scope="col">Номер/ID</th>
                    <th scope="col">Оценка</th>
                    <th scope="col">Статус</th>
                    <th scope="col">Чек-лист</th>
                    <th scope="col">Запись</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="control in paginatedControls" :key="control._Id">
                    <th>{{ control.number }}</th>
                    <td>{{ control._TimeStart | date("datetime") }}</td>
                    <td>{{ control._FullName }}</td>
                    <td>{{ control._Identifier }}</td>
                    <td>{{ control._Score }}</td>
                    <td>
                        <span :class="[control.classStatus]">
                            {{ control._StatusName }}
                        </span>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button
                                type="button"
                                class="btn btn-outline-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#popupControlModal"
                                @click.prevent="
                                    takeIdCall(
                                        control._Id,
                                        control._Identifier,
                                        control._IdCommutation
                                    ),
                                        getControlCheck(control._Id),
                                        getPhoneComments(control._Id),
                                        getChainSwitch(control._Id),
                                        getFileDownloads(control._Id)
                                "
                            >
                                Подробнее
                            </button>
                            <button
                                type="button"
                                class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                @click.prevent="
                                    takeIdCall(
                                        control._Id,
                                        control._Identifier,
                                        control._IdCommutation
                                    ),
                                        getPhoneComments(control._Id),
                                        takeStatusId(control._StatusId)
                                "
                            >
                                <span class="visually-hidden"
                                    >Переключатель выпадающего списка</span
                                >
                            </button>
                            <ul class="dropdown-menu m-0">
                                <li class="ml-3">
                                    <button
                                        :class="{
                                            'd-none':
                                                authoperator.Role ===
                                                'controller',
                                        }"
                                        type="button"
                                        :disabled="
                                            control._StatusId == 2 ||
                                            control._StatusId == 4 ||
                                            control._StatusId == 5 ||
                                            control._StatusId == 6 ||
                                            control._StatusId == 7 ||
                                            control._StatusId == 8
                                        "
                                        class="btn btn-outline-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#alertControlStatus"
                                    >
                                        Принять
                                    </button>
                                    <button
                                        :class="{
                                            'd-none':
                                                authoperator.Role === 'user',
                                        }"
                                        type="button"
                                        :disabled="
                                            control._StatusId == 2 ||
                                            control._StatusId == 5
                                        "
                                        class="btn btn-outline-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#alertControlStatus"
                                    >
                                        Принять
                                    </button>
                                </li>
                                <li class="ml-3 mt-1">
                                    <button
                                        :class="{
                                            'd-none':
                                                authoperator.Role ===
                                                'controller',
                                        }"
                                        type="button"
                                        :disabled="
                                            control._StatusId == 2 ||
                                            control._StatusId == 3 ||
                                            control._StatusId == 4 ||
                                            control._StatusId == 5 ||
                                            control._StatusId == 6 ||
                                            control._StatusId == 7 ||
                                            control._StatusId == 8
                                        "
                                        class="btn btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modal-dispute"
                                    >
                                        Не согласен
                                    </button>
                                    <button
                                        :class="{
                                            'd-none':
                                                authoperator.Role === 'user',
                                        }"
                                        type="button"
                                        :disabled="
                                            control._StatusId == 2 ||
                                            control._StatusId == 4 ||
                                            control._StatusId == 5
                                        "
                                        class="btn btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modal-dispute"
                                    >
                                        Не согласен
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <td>
                        <button
                            v-if="control._TypeCommunication == 1"
                            class="btn btn-outline-info"
                            type="button"
                            @click.prevent="
                                takeIdCall(
                                    control._Id,
                                    control._Identifier,
                                    control._IdCommutation
                                ),
                                    getPhoneRecords(control._IdCommutation)
                            "
                        >
                            Прослушать
                        </button>
                        <div v-else class="btn-group">
                            <a
                                role="button"
                                class="btn btn-outline-info"
                                :href="`https://sp-oktell-stat2.patio-minsk.by/melissaProduction/WebView2/JivoChat/?Id=${control._IdCommutation}#current`"
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
                                    >Переключатель выпадающего списка</span
                                >
                            </button>
                            <ul class="dropdown-menu m-0">
                                <li>
                                    <a
                                        class="dropdown-item"
                                        :href="`https://sp-oktell-stat2.patio-minsk.by/melissaProduction/WebView2/JivoChat/?Id=${control._IdCommutation}#current`"
                                        target="_blank"
                                    >
                                        Открыть в браузере
                                    </a>
                                </li>
                                <li>
                                    <a
                                        class="dropdown-item"
                                        :href="`https://app.jivo.ru/chat/archive/${control._Identifier}_chat-490834-${control._Identifier}`"
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
        <nav
            :class="{ 'd-none': controls.length < 11 }"
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
                Информация по номеру: {{ editPhone }}
            </template>
            <template v-slot:body>
                <p class="center" v-if="!controlChecks.length">
                    Идёт загрузка данных...
                </p>
                <div v-else>
                    <table class="table table-bordered" v-if="checkVisible">
                        <thead>
                            <tr>
                                <th scope="col">Вопрос</th>
                                <th scope="col">Ответ</th>
                                <th scope="col">Стоимость</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="controlCheck in controlChecks"
                                :key="controlCheck._QuestionName"
                                :class="{
                                    'table-danger': controlCheck._Weight == 0,
                                }"
                            >
                                <th>
                                    <div class="with-info">
                                        <div class="mr-3">
                                            {{ controlCheck._QuestionName }}
                                        </div>
                                        <div
                                            :class="{
                                                'd-none':
                                                    !controlCheck._Description
                                                        .length,
                                            }"
                                            :data-tooltip="
                                                controlCheck._Description
                                            "
                                            class="ml-auto"
                                        >
                                            <i
                                                class="icon fas fa-info-circle"
                                            ></i>
                                        </div>
                                    </div>
                                </th>
                                <td>{{ controlCheck._OptionName }}</td>
                                <td>{{ controlCheck._Weight }}</td>
                            </tr>
                        </tbody>
                    </table>
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
                                            takeIdObject(
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
                    Чек-лист
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
                            takeIdObject(chainSwitch._IDCommutation)
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
        <PopupDispute>
            <template v-slot:headerDispute>
                Информация по номеру: {{ editPhone }}
            </template>
            <template v-slot:bodyDispute>
                <p class="center" v-if="!phoneComments.length"></p>
                <table class="table table-bordered" v-else>
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
                                {{ phoneComment._CreatedOn | date("datetime") }}
                            </td>
                            <td>{{ phoneComment._FullName }}</td>
                            <td>{{ phoneComment._TimeMark }}</td>
                            <td>{{ phoneComment._Comment }}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="mb-3">
                    <label for="exampleFormControlTextarea1" class="form-label"
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
                </div>
            </template>
            <template v-slot:footerDispute>
                <button
                    :disabled="!isDisabledAddComment"
                    type="button"
                    class="btn btn-danger"
                    @click.prevent="addComment(), changeAssessmentNo()"
                >
                    Отправить комментарий
                </button>
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Закрыть
                </button>
            </template>
        </PopupDispute>
        <AlertStatus>
            <template v-slot:headerAlertStatus>
                Оценка диалога с номером: {{ editPhone }}
            </template>
            <template v-slot:bodyAlertStatus>
                <p class="center" v-if="!phoneComments.length"></p>
                <table class="table table-bordered" v-else>
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
                                {{ phoneComment._CreatedOn | date("datetime") }}
                            </td>
                            <td>{{ phoneComment._FullName }}</td>
                            <td>{{ phoneComment._TimeMark }}</td>
                            <td>{{ phoneComment._Comment }}</td>
                        </tr>
                    </tbody>
                </table>
                <p>Вы действительно принимаете данную оценку?</p>
            </template>
            <template v-slot:footerAlertStatus>
                <button
                    type="button"
                    class="btn btn-success"
                    data-bs-dismiss="modal"
                    @click.prevent="changeAssessmentOk()"
                >
                    Да
                </button>
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Закрыть
                </button>
            </template>
        </AlertStatus>
    </div>
</template>

<script>
import PopupControl from "./popup/PopupControl.vue";
import PopupDispute from "./popup/PopupDispute.vue";
import AlertStatus from "./popup/AlertStatus.vue";

export default {
    name: "ControlComponent",

    components: {
        PopupControl,
        PopupDispute,
        AlertStatus,
    },

    props: {
        authoperator: {
            type: Object,
            default: "",
        },
    },

    data() {
        return {
            controls: [],
            upMonth: 1,
            upYear: 2022,
            editIdCall: "",
            editPhone: "",
            editIdCommutation: "",
            oldStatus: "",
            controlChecks: [],
            phoneComments: [],
            chainSwitches: [],
            fileDownloads: [],
            phoneRecords: "",
            phoneRecordsChainSwitch: "",
            editIdCallChainSwitch: "",
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
            return Math.ceil(this.controls.length / 10);
        },
        //Пагинация 2 этап
        paginatedControls() {
            let from = (this.pageNumber - 1) * this.userPerPage;
            let to = from + this.userPerPage;
            return this.controls.slice(from, to);
        },
        //Выключение кнопки добавление комментариев
        isDisabledAddComment() {
            return this.commentDispute;
        },
    },

    mounted() {
        this.getNewDate();
        this.getControls();
    },

    methods: {
        //Данные из API контроль-качества
        async getControls() {
            await axios
                .get(`${this.apiBaseUrl}/v2/get-assessments`, {
                    params: {
                        month: `${this.upMonth}`,
                        year: `${this.upYear}`,
                        system_user: `${this.authoperator.SystemUser}`,
                    },
                })
                .then((response) => {
                    const controls = response.data.data;
                    this.controls = controls.map((control) => {
                        return {
                            ...control,
                            classStatus:
                                control._StatusName === "Новый"
                                    ? "badge badge-danger"
                                    : control._StatusName === "Не принят"
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
        //Получаем ID звонка
        takeIdCall(_Id, _Identifier, _IdCommutation) {
            this.checkVisible = true;
            this.editIdCall = _Id;
            this.editPhone = _Identifier;
            this.editIdCommutation = _IdCommutation;
        },
        //Получаем Статус
        takeStatusId(_StatusId) {
            this.oldStatus = _StatusId;
        },
        //Получаем ID звонка связанной коммутации
        takeIdObject(_IDCommutation) {
            this.editIdCallChainSwitch = _IDCommutation;
        },

        //Получение чек листа оцененной коммутации
        async getControlCheck() {
            this.controlChecks = "";
            this.fileDownloads = null;
            await axios
                .get(`${this.apiBaseUrl}/v2/check-list`, {
                    params: {
                        id: `${this.editIdCall}`,
                    },
                })
                .then((response) => {
                    this.controlChecks = response.data.data;
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
                        system_user: `${this.authoperator.SystemUser}`,
                        id: `${this.editIdCall}`,
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
                        id: `${this.editIdCall}`,
                        section_id: "17",
                    },
                })
                .then((response) => {
                    this.chainSwitches = response.data.data;
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        //Получаем запись разговоров
        async getPhoneRecords() {
            this.phoneRecords = "";
            await axios
                .get(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/GetAudio.php",
                    {
                        params: {
                            IdCall: `${this.editIdCommutation}`,
                            login: `${this.authoperator.LoginOperator}`,
                        },
                    }
                )
                .then((response) => {
                    let link = "data:audio/ogg;base64," + response.data;
                    this.phoneRecords = link;
                    this.phoneRecordsChainSwitch = "";
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
                            IdCall: `${this.editIdCallChainSwitch}`,
                            login: `${this.authoperator.LoginOperator}`,
                        },
                    }
                )
                .then((response) => {
                    let linkChainSwitch =
                        "data:audio/ogg;base64," + response.data;
                    this.phoneRecordsChainSwitch = linkChainSwitch;
                    this.phoneRecords = "";
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        //Добавление комментариев оператором
        async addComment() {
            await axios
                .post(`${this.apiBaseUrl}/v2/add-comment`, {
                    id: `${this.editIdCall}`,
                    type_communication: 0,
                    comment: this.commentDispute,
                    section: 17,
                    system_user: `${this.authoperator.SystemUser}`,
                })
                .then(() => {
                    this.commentDispute = "";
                    this.getControls();
                    this.getPhoneComments();
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        //Смена оценки оператором на "Принял"
        async changeAssessmentOk() {
            await axios
                .post(`${this.apiBaseUrl}/v2/status`, {
                    id: `${this.editIdCall}`,
                    old_status: `${this.oldStatus}`,
                    new_status:
                        `${this.authoperator.Role}` === "controller" ? 5 : 2,
                })
                .then(() => {
                    this.getControls();
                    this.$parent.$refs.cont.getMessages();
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        //Смена оценки оператором на "Не согласен"
        async changeAssessmentNo() {
            await axios
                .post(`${this.apiBaseUrl}/v2/status`, {
                    id: `${this.editIdCall}`,
                    old_status: `${this.oldStatus}`,
                    new_status:
                        `${this.authoperator.Role}` === "controller" ? 4 : 3,
                })
                .then(() => {
                    this.getControls();
                    this.$parent.$refs.cont.getMessages();
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
                        id: `${this.editIdCall}`,
                        section_id: 17,
                    },
                })
                .then(async (response) => {
                    this.fileDownloads = response.data.data;
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },

        //Включаем чек-лист в модалке
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

.with-info {
    display: flex;
    flex-direction: row;
    justify-content: left;
}

[data-tooltip] {
    position: relative; /* Относительное позиционирование */
}
[data-tooltip]::after {
    content: attr(data-tooltip); /* Выводим текст */
    position: absolute; /* Абсолютное позиционирование */
    width: 400px; /* Ширина подсказки */
    right: 20px;
    top: 0; /* Положение подсказки */
    background: #3989c9; /* Синий цвет фона */
    border-radius: 10px;
    color: #fff; /* Цвет текста */
    padding: 0.5em; /* Поля вокруг текста */
    box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3); /* Параметры тени */
    pointer-events: none; /* Подсказка */
    opacity: 0; /* Подсказка невидима */
    transition: 1s; /* Время появления подсказки */
}
[data-tooltip]:hover::after {
    opacity: 1; /* Показываем подсказку */
    top: 2em; /* Положение подсказки */
}
</style>
