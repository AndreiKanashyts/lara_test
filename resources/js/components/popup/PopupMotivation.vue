<template>
    <div>
        <div
            class="modal fade"
            id="modal-motivation"
            tabindex="-1"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            <slot name="headerMotivation"></slot>
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <slot name="bodyMotivation"></slot>
                    </div>
                    <div class="modal-footer">
                        <slot name="footerMotivation">
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
    </div>
</template>

<script>
export default {
    name: "PopupMotivation",

    data() {
        return {};
    },

    methods: {},
};
</script>
