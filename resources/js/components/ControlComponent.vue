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
                        :src="phoneRecords"
                        :class="this.phoneRecords.length > 30 ? '' : 'd-none'"
                        autoplay="autoplay"
                        controls
                        controlsList="nodownload"
                        oncontextmenu="return false"
                        preload="metadata"
                    ></audio>
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
                <tr v-for="control in paginatedControls" :key="control.IdCall">
                    <th>{{ control.number }}</th>
                    <td>{{ control.dateCall.date | date("datetime") }}</td>
                    <td>{{ control.fullName }}</td>
                    <td>{{ control.phoneClient }}</td>
                    <td>{{ control.score }}</td>
                    <td>
                        <span :class="[control.classStatus]">
                            {{ control.nameStatus }}
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
                                        control.IdCall,
                                        control.phoneClient
                                    ),
                                        getControlCheck(control.IdCall),
                                        getPhoneComments(control.IdCall),
                                        getChainSwitch(control.IdCall)
                                "
                            >
                                Подробнее
                            </button>
                            <button
                                type="button"
                                class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >
                                <span class="visually-hidden"
                                    >Переключатель выпадающего списка</span
                                >
                            </button>
                            <ul class="dropdown-menu">
                                <li class="ml-3">
                                    <button
                                        :class="{
                                            'd-none':
                                                authoperator.Role ===
                                                'controller',
                                        }"
                                        type="button"
                                        :disabled="
                                            control.IdStatus === 2 ||
                                            control.IdStatus === 4 ||
                                            control.IdStatus === 5 ||
                                            control.IdStatus === 6 ||
                                            control.IdStatus === 7 ||
                                            control.IdStatus === 8
                                        "
                                        class="btn btn-outline-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#alertControlStatus"
                                        @click.prevent="
                                            takeIdCall(
                                                control.IdCall,
                                                control.phoneClient,
                                                control.IdStatus
                                            ),
                                                getPhoneComments(control.IdCall)
                                        "
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
                                            control.IdStatus === 2 ||
                                            control.IdStatus === 5
                                        "
                                        class="btn btn-outline-success"
                                        data-bs-toggle="modal"
                                        data-bs-target="#alertControlStatus"
                                        @click.prevent="
                                            takeIdCall(
                                                control.IdCall,
                                                control.phoneClient,
                                                control.IdStatus
                                            ),
                                                getPhoneComments(control.IdCall)
                                        "
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
                                            control.IdStatus === 2 ||
                                            control.IdStatus === 3 ||
                                            control.IdStatus === 4 ||
                                            control.IdStatus === 5 ||
                                            control.IdStatus === 6 ||
                                            control.IdStatus === 7 ||
                                            control.IdStatus === 8
                                        "
                                        class="btn btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modal-dispute"
                                        @click.prevent="
                                            takeIdCall(
                                                control.IdCall,
                                                control.phoneClient,
                                                control.IdStatus
                                            ),
                                                getPhoneComments(control.IdCall)
                                        "
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
                                            control.IdStatus === 2 ||
                                            control.IdStatus === 4 ||
                                            control.IdStatus === 5
                                        "
                                        class="btn btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modal-dispute"
                                        @click.prevent="
                                            takeIdCall(
                                                control.IdCall,
                                                control.phoneClient,
                                                control.IdStatus
                                            ),
                                                getPhoneComments(control.IdCall)
                                        "
                                    >
                                        Не согласен
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </td>
                    <td>
                        <button
                            :class="{ 'd-none': control.source === 1 }"
                            class="btn btn-outline-info"
                            type="button"
                            @click.prevent="
                                takeIdCall(control.IdCall, control.phoneClient),
                                    getPhoneRecords(control.IdCall)
                            "
                        >
                            Прослушать
                        </button>
                        <div
                            :class="{ 'd-none': control.source === 0 }"
                            class="btn-group"
                        >
                            <a
                                role="button"
                                class="btn btn-outline-info"
                                :href="`https://sp-oktell-stat1.patio-minsk.by/ChatCard/?id=&quot;${control.IdCall}&quot;`"
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
                            <ul class="dropdown-menu">
                                <li>
                                    <a
                                        class="dropdown-item"
                                        :href="`https://sp-oktell-stat1.patio-minsk.by/ChatCard/?id=&quot;${control.IdCall}&quot;`"
                                        target="_blank"
                                    >
                                        Открыть в браузере
                                    </a>
                                </li>
                                <li>
                                    <a
                                        class="dropdown-item"
                                        :href="`https://app.jivo.ru/chat/archive/${control.phoneClient}_chat-490834-${control.phoneClient}`"
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
                            :key="controlCheck.number"
                            :class="{
                                'table-danger':
                                    controlCheck.currentWeight === 0,
                            }"
                        >
                            <th>
                                <div class="with-info">
                                    <div class="mr-3">
                                        {{ controlCheck.nameQuestion }}
                                    </div>
                                    <div
                                        :class="{
                                            'd-none':
                                                !controlCheck.description
                                                    .length,
                                        }"
                                        :data-tooltip="controlCheck.description"
                                        class="ml-auto"
                                    >
                                        <i class="icon fas fa-info-circle"></i>
                                    </div>
                                </div>
                            </th>
                            <td>{{ controlCheck.nameOption }}</td>
                            <td>{{ controlCheck.currentWeight }}</td>
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
                            :key="phoneComment.id"
                            :class="[phoneComment.classMarcup]"
                        >
                            <td>
                                {{
                                    phoneComment.dateTime.date
                                        | date("datetime")
                                }}
                            </td>
                            <td>{{ phoneComment.fullName }}</td>
                            <td>{{ phoneComment.timeMark }}</td>
                            <td>{{ phoneComment.comment }}</td>
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
                                        takeIdObject(chainSwitch.IdObject),
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
                <div
                    :class="{
                        'd-none':
                            checkVisible === true ||
                            checkCommentVisible === false,
                    }"
                    class="mb-3"
                >
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
                    <button
                        :disabled="!isDisabledAddComment"
                        type="button"
                        class="btn btn-danger mt-3"
                        @click.prevent="addComment"
                    >
                        Отправить комментарий
                    </button>
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
                            takeIdObject(chainSwitch.IdObject)
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
                            :key="phoneComment.id"
                            :class="[phoneComment.classMarcup]"
                        >
                            <td>
                                {{
                                    phoneComment.dateTime.date
                                        | date("datetime")
                                }}
                            </td>
                            <td>{{ phoneComment.fullName }}</td>
                            <td>{{ phoneComment.timeMark }}</td>
                            <td>{{ phoneComment.comment }}</td>
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
                            :key="phoneComment.id"
                            :class="[phoneComment.classMarcup]"
                        >
                            <td>
                                {{
                                    phoneComment.dateTime.date
                                        | date("datetime")
                                }}
                            </td>
                            <td>{{ phoneComment.fullName }}</td>
                            <td>{{ phoneComment.timeMark }}</td>
                            <td>{{ phoneComment.comment }}</td>
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
            oldStatus: "",
            controlChecks: [],
            phoneComments: [],
            chainSwitches: [],
            phoneRecords: "",
            phoneRecordsChainSwitch: "",
            editIdCallChainSwitch: "",
            checkVisible: true,
            checkCommentVisible: false,
            userPerPage: 10,
            pageNumber: 1,
            commentDispute: "",
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
        getControls() {
            axios
                .get(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/QCS/GetAssessment.php",
                    {
                        params: {
                            Month: `${this.upMonth}`,
                            Year: `${this.upYear}`,
                            IdOperator: `${this.authoperator.IdOperator}`,
                        },
                    }
                )
                .then((response) => {
                    const controls = response.data;
                    this.controls = controls.map((control) => {
                        return {
                            ...control,
                            classStatus:
                                control.nameStatus === "Новое"
                                    ? "badge badge-danger"
                                    : control.nameStatus === "Не принят"
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
        takeIdCall(IdCall, phoneClient, IdStatus) {
            this.checkVisible = true;
            this.editIdCall = IdCall;
            this.editPhone = phoneClient;
            this.oldStatus = IdStatus;
        },
        //Получаем ID звонка связанной коммутации
        takeIdObject(IdObject) {
            this.editIdCallChainSwitch = IdObject;
        },

        isTakeIdCall(IdCall) {
            return this.editIdCall === IdCall;
        },

        //Получение чек листа оцененной коммутации
        getControlCheck() {
            axios
                .get(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/QCS/GetAssessmentCheckList.php",
                    {
                        params: {
                            IdCall: `${this.editIdCall}`,
                        },
                    }
                )
                .then((response) => {
                    this.controlChecks = response.data;
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        //Получение комментариев оцененной коммутации
        getPhoneComments() {
            axios
                .get(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/QCS/GetComment.php",
                    {
                        params: {
                            IdOperator: `${this.authoperator.IdOperator}`,
                            IdCall: `${this.editIdCall}`,
                        },
                    }
                )
                .then((response) => {
                    const phoneComments = response.data;
                    this.phoneComments = phoneComments.map((phoneComment) => {
                        return {
                            ...phoneComment,
                            classMarcup:
                                phoneComment.markup === 1
                                    ? "table-danger"
                                    : phoneComment.markup === 2
                                    ? "table-warning"
                                    : phoneComment.markup === 3
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
        getChainSwitch() {
            axios
                .get(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/QCS/GetLinkObject.php",
                    {
                        params: {
                            IdParentObject: `${this.editIdCall}`,
                            Section: "1",
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
        //Получаем запись разговоров
        getPhoneRecords() {
            this.phoneRecords = "";
            axios
                .get(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/GetAudio.php",
                    {
                        params: {
                            IdCall: `${this.editIdCall}`,
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
                    this.phoneRecords = "";
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        //Добавление комментариев оператором
        addComment() {
            axios
                .post(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/QCS/SetAssessmentComment.php",
                    {
                        IdCall: `${this.editIdCall}`,
                        IdOktell: `${this.authoperator.IdOperator}`,
                        TimeMark: "00:00:00",
                        Comment: this.commentDispute,
                        Markup: 0,
                    }
                )
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
        changeAssessmentOk() {
            axios
                .post(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/QCS/SetAssessmentStatus.php",
                    {
                        IdCall: `${this.editIdCall}`,
                        oldStatus: `${this.oldStatus}`,
                        newStatus:
                            `${this.authoperator.Role}` === "controller"
                                ? 5
                                : 2,
                    }
                )
                .then(() => {
                    this.getControls();
                    this.$parent.$refs.cont.getMessages();
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        //Смена оценки оператором на "Не согласен"
        changeAssessmentNo() {
            axios
                .post(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/QCS/SetAssessmentStatus.php",
                    {
                        IdCall: `${this.editIdCall}`,
                        oldStatus: `${this.oldStatus}`,
                        newStatus:
                            `${this.authoperator.Role}` === "controller"
                                ? 4
                                : 3,
                    }
                )
                .then(() => {
                    this.getControls();
                    this.$parent.$refs.cont.getMessages();
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
