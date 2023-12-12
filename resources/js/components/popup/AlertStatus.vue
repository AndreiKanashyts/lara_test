<template>
    <div
        class="modal fade"
        id="alertControlStatus"
        tabindex="-1"
        aria-labelledby="exampleModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <slot name="headerAlertStatus"></slot>
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Закрыть"
                    ></button>
                </div>
                <div class="modal-body">
                    <slot name="bodyAlertStatus"></slot>
                </div>
                <div class="modal-footer">
                    <slot name="footerAlertStatus">
                        <button type="button" class="btn btn-primary">
                            Reload
                        </button>
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Close
                        </button>
                    </slot>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "AlertStatus",

    props: {},

    data() {
        return {};
    },

    maunted() {},

    methods: {},
};
</script>
