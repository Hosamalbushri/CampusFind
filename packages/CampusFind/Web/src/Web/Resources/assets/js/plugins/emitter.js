import mitt from "mitt";

const emitter = mitt();

window.emitter = emitter;

export default {
    install: (app) => {
        app.config.globalProperties.$emitter = emitter;
    },
};
