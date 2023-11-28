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
                                @click.prevent="getComments(), firstPage()"
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

        <p class="center" v-if="!comments.length">
            Замечаний в этом месяце нет!
        </p>

        <table class="table table-bordered" v-else>
            <thead>
                <tr>
                    <th scope="col">№</th>
                    <th scope="col">Дата</th>
                    <th scope="col">Тип замечания</th>
                    <th scope="col">Стоимость</th>
                    <th scope="col">Статус</th>
                    <th scope="col">Сообщения</th>
                    <th scope="col">Ознакомиться</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="comment in paginatedComments" :key="comment.number">
                    <th>{{ comment.number }}</th>
                    <td>{{ comment.date }}</td>
                    <td>{{ comment.name }}</td>
                    <td>{{ comment.cost }}</td>
                    <td>
                        <span :class="[comment.classStatus]">
                            {{ comment.textStatus }}
                        </span>
                    </td>
                    <td>
                        <button
                            type="button"
                            class="btn btn-outline-warning"
                            data-bs-toggle="modal"
                            data-bs-target="#popupControlModal"
                            @click.prevent="
                                takeIdObject(comment.id),
                                    takeIdName(comment.name),
                                    takeIdCost(comment.cost),
                                    getWIUForID(comment.id),
                                    getFileDownloads(comment.id),
                                    getCommunicationMessage(comment.id),
                                    swapTablesComment(),
                                    getChainSwitch(comment.id)
                            "
                        >
                            <i class="fas fa-envelope"></i> Сообщения
                            <span
                                :class="{ 'd-none': comment.newMessage < 1 }"
                                class="badge bg-danger"
                                >{{ comment.newMessage }}</span
                            >
                        </button>
                    </td>
                    <td>
                        <button
                            type="button"
                            class="btn btn-outline-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#popupControlModal"
                            @click.prevent="
                                takeIdObject(comment.id),
                                    takeIdName(comment.name),
                                    takeIdCost(comment.cost),
                                    getWIUForID(comment.id),
                                    getFileDownloads(comment.id),
                                    changeCommentStatusOk(),
                                    swapTablesCheck(),
                                    getChainSwitch(comment.id)
                            "
                        >
                            Подробнее
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
        <nav
            :class="{ 'd-none': comments.length < 11 }"
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
                <h2>Тип замечания: "{{ editName }}"</h2>
                <div
                    :class="{ 'd-none': checkCommentVisible === false }"
                    class="sticky"
                >
                    <div class="image-back">
                        <img
                            :src="previewImage"
                            class="uploading-image"
                            width="270"
                        />
                        <input
                            style="display: none"
                            type="file"
                            accept="image/jpeg"
                            @change="uploadImage"
                            ref="fileInput"
                        />
                        <div class="mt-2">
                            <button
                                type="button"
                                class="btn btn-success"
                                @click.prevent="$refs.fileInput.click()"
                            >
                                Выбрать файл
                            </button>
                            <button
                                :disabled="!previewImage"
                                type="button"
                                class="btn btn-danger"
                                @click.prevent="clearPreview"
                            >
                                Очистить
                            </button>
                            <button
                                :disabled="!previewImage"
                                type="button"
                                class="btn btn-info"
                                @click.prevent="addImageMessage()"
                            >
                                <i class="fa fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                    <form action="#" class="bg-light">
                        <div class="input-group">
                            <textarea
                                class="form-control"
                                placeholder="Перед отправкой комментария введите сообщение"
                                aria-label="Перед отправкой комментария введите сообщение"
                                rows="2"
                                v-model="commentDispute"
                            >
                            </textarea>
                            <div class="input-group-append">
                                <button
                                    :disabled="!isDisabledAddComment"
                                    type="button"
                                    class="btn btn-info"
                                    @click.prevent="
                                        addCommunicationNewMessage(),
                                            createNewUUID()
                                    "
                                >
                                    <i class="fa fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </template>
            <template v-slot:body>
                <div v-if="checkVisible">
                    <div
                        v-for="commentForId in commentForIds"
                        :key="commentForId.IdObject"
                        class="modal-body"
                    >
                        <table>
                            <tbody>
                                <tr>
                                    <td class="align-baseline">
                                        <p class="fw-bold fs-3">Дата:</p>
                                    </td>
                                    <td class="align-baseline">
                                        <p class="fs-4 ms-1">
                                            {{ commentForId.date }}
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table>
                            <tbody>
                                <tr>
                                    <td class="align-baseline">
                                        <p class="fw-bold fs-3">Стоимость:</p>
                                    </td>
                                    <td class="align-baseline">
                                        <p class="fs-4 ms-1">
                                            {{ editCost }}
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="fw-bold fs-3">Описание:</p>
                        <p class="fs-5">{{ commentForId.description }}</p>
                    </div>
                    <div class="card-body bg-white">
                        <ul
                            v-for="fileDownload in fileDownloads"
                            :key="fileDownload.number"
                            class="mailbox-attachments d-flex align-items-stretch clearfix"
                        >
                            <li>
                                <div
                                    class="mailbox-attachment-info"
                                    style="border: solid gray"
                                >
                                    <a
                                        :href="`https://sp-oktell-stat1.patio-minsk.by/WIUCatalog/${fileDownload.nameFile}`"
                                        target="_blank"
                                        class="mailbox-attachment-name"
                                        ><i class="fas fa-paperclip"></i>
                                        {{ fileDownload.titleFile }}
                                    </a>
                                    <span
                                        class="mailbox-attachment-size clearfix mt-1"
                                    >
                                        <a
                                            :href="`https://sp-oktell-stat1.patio-minsk.by/WIUCatalog/${fileDownload.nameFile}`"
                                            target="_blank"
                                            class="btn btn-default btn-sm float-right"
                                            ><i
                                                class="fas fa-cloud-download-alt"
                                            ></i
                                        ></a>
                                    </span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div v-else-if="checkCommentVisible">
                    <div class="mx-auto rounded" style="width: 100%">
                        <div
                            v-for="communicationMessage in reversedMessage"
                            :key="communicationMessage.Id"
                        >
                            <!-- Супервизор -->
                            <div
                                :class="{
                                    'd-none':
                                        communicationMessage.systemSource ===
                                        'UPA',
                                }"
                                class="media w-50 mb-0"
                                @mouseover.once="
                                    takeIdMessage(communicationMessage.Id)
                                "
                            >
                                <div class="media-body ml-2">
                                    <p class="text-purple mb-0 mt=0">
                                        {{ communicationMessage.fullName }}
                                    </p>
                                    <div class="svisor-mess py-2 px-3">
                                        <p class="text-small mb-0 text-black">
                                            <a
                                                :href="`https://sp-oktell-stat1.patio-minsk.by/MessageCatalog/${communicationMessage.pathFile}`"
                                                target="_blank"
                                            >
                                                <img
                                                    :class="{
                                                        'd-none':
                                                            communicationMessage
                                                                .pathFile
                                                                .length === 0,
                                                    }"
                                                    :src="`https://sp-oktell-stat1.patio-minsk.by/MessageCatalog/${communicationMessage.pathFile}`"
                                                    width="300"
                                                />
                                            </a>
                                            {{ communicationMessage.text }}
                                        </p>
                                    </div>
                                    <p class="small text-muted">
                                        {{ communicationMessage.dateTime }}
                                        <i
                                            :class="{
                                                'd-none':
                                                    communicationMessage.listener ===
                                                    1,
                                            }"
                                            class="fas fa-check"
                                            style="
                                                position: absolute;
                                                right: 50%;
                                            "
                                        ></i>
                                        <i
                                            :class="{
                                                'd-none':
                                                    communicationMessage.listener ===
                                                    0,
                                            }"
                                            class="fas fa-check-double"
                                            style="
                                                position: absolute;
                                                right: 50%;
                                            "
                                        ></i>
                                    </p>
                                </div>
                            </div>
                            <!-- Оператор -->
                            <div
                                :class="{
                                    'd-none':
                                        communicationMessage.systemSource !==
                                        'UPA',
                                }"
                                class="media w-50 ml-auto mb-0 mr-2"
                            >
                                <div class="media-body">
                                    <p class="text-navy mb-0 mt=0">
                                        {{ communicationMessage.fullName }}
                                    </p>
                                    <div class="operator-mess py-2 px-3">
                                        <p class="text-small mb-0 text-black">
                                            <a
                                                :href="`https://sp-oktell-stat1.patio-minsk.by/MessageCatalog/${communicationMessage.pathFile}`"
                                                target="_blank"
                                            >
                                                <img
                                                    :class="{
                                                        'd-none':
                                                            communicationMessage
                                                                .pathFile
                                                                .length === 0,
                                                    }"
                                                    :src="`https://sp-oktell-stat1.patio-minsk.by/MessageCatalog/${communicationMessage.pathFile}`"
                                                    width="300"
                                                />
                                            </a>
                                            {{ communicationMessage.text }}
                                        </p>
                                    </div>
                                    <p class="small text-muted">
                                        {{ communicationMessage.dateTime }}
                                        <i
                                            :class="{
                                                'd-none':
                                                    communicationMessage.listener ===
                                                    1,
                                            }"
                                            class="fas fa-check"
                                            style="
                                                position: absolute;
                                                right: 25px;
                                            "
                                        ></i>
                                        <i
                                            :class="{
                                                'd-none':
                                                    communicationMessage.listener ===
                                                    0,
                                            }"
                                            class="fas fa-check-double"
                                            style="
                                                position: absolute;
                                                right: 25px;
                                            "
                                        ></i>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
            </template>
            <template v-slot:footer>
                <button
                    type="button"
                    class="btn btn-primary"
                    @click.prevent="swapTablesCheck(), changeCommentStatusOk()"
                >
                    Основное
                </button>
                <button
                    v-for="chainSwitch in chainSwitches"
                    :key="chainSwitch.number"
                    :class="{ 'd-none': chainSwitch.number !== 1 }"
                    type="button"
                    class="btn btn-success"
                    @click.prevent="
                        swapTablesCheckComment(),
                            takeIdObjectChain(chainSwitch.IdObject)
                    "
                >
                    Связка
                </button>
                <button
                    type="button"
                    class="btn btn-warning"
                    @click.prevent="
                        getCommunicationMessage(),
                            swapTablesComment(),
                            createNewUUID()
                    "
                >
                    Сообщения
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
    name: "CommentComponent",

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
            comments: [],
            commentForIds: [],
            chainSwitches: [],
            fileDownloads: [],
            communicationMessages: [],
            upMonth: 1,
            upYear: 2022,
            editIdObject: "",
            editIdObjectChain: "",
            editName: "",
            editCost: "",
            userPerPage: 10,
            pageNumber: 1,
            checkVisible: true,
            checkCommentVisible: false,
            GUID: this.generateUUID,
            commentDispute: "",
            phoneRecordsChainSwitch: "",
            editIdMessage: "",
            previewImage: "",
        };
    },

    computed: {
        //Пагинация 1 этап
        pages() {
            return Math.ceil(this.comments.length / 10);
        },
        //Пагинация 2 этап
        paginatedComments() {
            let from = (this.pageNumber - 1) * this.userPerPage;
            let to = from + this.userPerPage;
            return this.comments.slice(from, to);
        },
        //Все заглавные в GUID
        shownGUID() {
            let newGuid = this.GUID;
            newGuid = newGuid.toUpperCase();
            return newGuid;
        },
        //Выключение кнопки добавление комментариев
        isDisabledAddComment() {
            return this.commentDispute;
        },
        //Реверс сообщений
        reversedMessage() {
            return this.communicationMessages.slice().reverse();
        },
        //Обрезаем первые символы у ссылки изображения
        cutUrlImage() {
            let str = this.previewImage;
            let n = 23;
            str = str.substring(n);
            return str;
        },
    },

    mounted() {
        this.getNewDate();
        this.getComments();
        this.GUID = this.generateUUID();
    },

    methods: {
        //Получение реестра (Замечания руководителя)
        getComments() {
            axios
                .get(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/Mistake/GetMistake.php",
                    {
                        params: {
                            Month: `${this.upMonth}`,
                            Year: `${this.upYear}`,
                            IdOperator: `${this.authoperator.IdOperator}`,
                        },
                    }
                )
                .then((response) => {
                    const comments = response.data;
                    this.comments = comments.map((comment) => {
                        return {
                            ...comment,
                            classStatus:
                                comment.familiarize === 0
                                    ? "badge badge-danger"
                                    : "badge badge-success",
                            textStatus:
                                comment.familiarize === 0
                                    ? "Новое"
                                    : "Просмотрено",
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
        //Получаем Id
        takeIdObject(id) {
            this.editIdObject = id;
        },
        //Получаем IdObjectChain
        takeIdObjectChain(IdObject) {
            this.editIdObjectChain = IdObject;
        },
        //Получаем Name
        takeIdName(name) {
            this.editName = name;
        },
        //Получаем Cost
        takeIdCost(cost) {
            this.editCost = cost;
        },
        //Получаем Id сообщения
        takeIdMessage(Id) {
            this.editIdMessage = Id;
            this.changeCheckMessage();
        },
        //Получение основной информации по ID (Замечания руководителя)
        getWIUForID() {
            axios
                .get(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/Mistake/GetMistakeForID.php",
                    {
                        params: {
                            Id: `${this.editIdObject}`,
                        },
                    }
                )
                .then((response) => {
                    this.commentForIds = response.data;
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        //Установка параметра "Ознакомился" (Замечания руководителя)
        changeCommentStatusOk() {
            axios
                .post(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/Mistake/SetFamiliarizeMistake.php",
                    {
                        Id: `${this.editIdObject}`,
                    }
                )
                .then(() => {
                    this.getComments();
                    this.$parent.$refs.comm.getMessages();
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        //Установка параметра "Прочитано" (Неоперативная аналитика)
        changeCheckMessage() {
            axios
                .post(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/Messenger/SetCommunicationDataListenerMessage.php",
                    {
                        Id: `${this.editIdMessage}`,
                    }
                )
                .then(() => {
                    this.getComments();
                    this.getCommunicationMessage();
                    this.$parent.$refs.comm.getMessages();
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        //Получение файлов по ID
        getFileDownloads() {
            axios
                .get(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/GetFilesForID.php",
                    {
                        params: {
                            Id: `${this.editIdObject}`,
                            Section: "4",
                        },
                    }
                )
                .then((response) => {
                    this.fileDownloads = response.data;
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        //Получение связанных коммутаций (Общее)
        getChainSwitch() {
            axios
                .get(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/GetLinkObjectForID.php",
                    {
                        params: {
                            IdParentObject: `${this.editIdObject}`,
                            Section: "4",
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
                            IdCall: `${this.editIdObject}`,
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
        //Новый GUID
        createNewUUID() {
            this.GUID = this.generateUUID();
        },
        //Генератор GUID
        generateUUID() {
            // Public Domain/MIT
            let d = new Date().getTime();
            if (
                typeof performance !== "undefined" &&
                typeof performance.now === "function"
            ) {
                d += performance.now(); //use high-precision timer if available
            }
            let newGuid = "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(
                /[xy]/g,
                function (c) {
                    var r = (d + Math.random() * 16) % 16 | 0;
                    d = Math.floor(d / 16);
                    return (c === "x" ? r : (r & 0x3) | 0x8).toString(16);
                }
            );

            return newGuid;
        },
        //Получение списка сообщений (Неоперативная переписка)
        getCommunicationMessage() {
            axios
                .get(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/Messenger/GetCommunicationMessage.php",
                    {
                        params: {
                            IdParentObject: `${this.editIdObject}`,
                            Section: "4",
                        },
                    }
                )
                .then((response) => {
                    this.communicationMessages = response.data;
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        //Создание нового сообщения (Неоперативная переписка)
        addCommunicationNewMessage() {
            axios
                .post(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/Messenger/CommunicationNewMessage.php",
                    {
                        Id: `${this.shownGUID}`,
                        IdParentObject: `${this.editIdObject}`,
                        Section: 4,
                        IdOperator: `${this.authoperator.IdOperator}`,
                        Text: this.commentDispute,
                        TypeMessage: 0,
                        PathFile: "",
                    }
                )
                .then(() => {
                    this.commentDispute = "";
                    this.getComments();
                    this.getCommunicationMessage();
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        //Загрузка изображений
        uploadImage(e) {
            const image = e.target.files[0];
            const reader = new FileReader();
            reader.readAsDataURL(image);
            reader.onload = (e) => {
                this.previewImage = e.target.result;
            };
        },
        //Отправка нового изображения (Неоперативная переписка) part 1
        addImageMessage() {
            axios
                .post(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/Messenger/CommunicationNewMessage.php",
                    {
                        Id: `${this.shownGUID}`,
                        IdParentObject: `${this.editIdObject}`,
                        Section: 4,
                        IdOperator: `${this.authoperator.IdOperator}`,
                        Text: "",
                        TypeMessage: 1,
                        PathFile: `${this.shownGUID}`,
                    }
                )
                .then(() => {
                    this.addImageFile();
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        //Отправка нового изображения (Неоперативная переписка) part 2
        addImageFile() {
            axios
                .post(
                    "https://sp-oktell-stat1.patio-minsk.by/SSA_Integration_External_System/integration/UPA/Messenger/UploadImage.php",
                    {
                        fileName: `${this.shownGUID}`,
                        base64: `${this.cutUrlImage}`,
                    }
                )
                .then(() => {
                    this.previewImage = "";
                    this.getComments();
                    this.getCommunicationMessage();
                    this.generateUUID();
                })
                .catch((error) => {
                    console.log(error.response);
                });
        },
        //Удалить изображение из пред. просмотра
        clearPreview() {
            this.previewImage = "";
        },
        //Включаем Основное в модалке
        swapTablesCheck() {
            this.checkVisible = true;
            this.checkCommentVisible = false;
        },
        //Включаем Сообщения в модалке
        swapTablesComment() {
            this.checkVisible = false;
            this.checkCommentVisible = true;
        },
        //Включаем Связку в модалке
        swapTablesCheckComment() {
            this.checkVisible = false;
            this.checkCommentVisible = false;
        },
        //Выбор текущей даты
        getNewDate() {
            let dateNaw = new Date();
            let monthNaw = dateNaw.getMonth() + 1;
            let yearNaw = dateNaw.getFullYear();
            this.upMonth = monthNaw;
            this.upYear = yearNaw;
        },
        //Выбор страницы
        pageClick(page) {
            this.pageNumber = page;
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

.sticky {
    background: #f5f5f5;
}

.mx-auto {
    background: #f5f5f5;
}

.operator-mess {
    background: #dcf8c5;
    border-radius: 10px;
    box-shadow: rgba(17, 12, 46, 0.15) 0px 48px 100px 0px;
}

.svisor-mess {
    background: #c3e3fa;
    border-radius: 10px;
    box-shadow: rgba(17, 12, 46, 0.15) 0px 48px 100px 0px;
}
.uploading-image {
    display: flex;
}
.image-back {
    background: #f5f5f5;
    margin-bottom: 10px;
    margin-left: 10px;
    margin-right: 10px;
    margin-top: 10px;
}
</style>
