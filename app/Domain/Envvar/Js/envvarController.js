leantime.envvarController = (function () {
    var openCaptureModal = function () {
        window.location.hash = '#/envvar/capture';
        leantime.modals.openModal();
    };
    return { openCaptureModal: openCaptureModal };
})();