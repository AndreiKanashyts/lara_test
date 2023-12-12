import Vue from "vue";
import dateFilter from "./filters/date.filter";
import FirstComponent from "./components/FirstComponent";
import CommentComponent from "./components/CommentComponent";
import ControlComponent from "./components/ControlComponent";
import LeaderComponent from "./components/LeaderComponent";
import JournalComponent from "./components/JournalComponent";
import MotivationComponent from "./components/MotivationComponent";
import HelperComponent from "./components/HelperComponent";
import PopupControl from "./components/popup/PopupControl";
import PopupDispute from "./components/popup/PopupDispute";
import PopupMotivation from "./components/popup/PopupMotivation";
import AlertStatus from "./components/popup/AlertStatus";
import MessageControl from "./components/sidebar/MessageControl";
import MessageLeader from "./components/sidebar/MessageLeader";
import MessageComment from "./components/sidebar/MessageComment";

require("./bootstrap");

Vue.filter("date", dateFilter);

const app = new Vue({
    el: "#app",

    components: {
        FirstComponent,
        CommentComponent,
        ControlComponent,
        LeaderComponent,
        JournalComponent,
        MotivationComponent,
        HelperComponent,
        PopupControl,
        PopupDispute,
        PopupMotivation,
        AlertStatus,
        MessageControl,
        MessageLeader,
        MessageComment,
    },
});
