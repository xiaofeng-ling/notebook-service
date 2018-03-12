function setTime() {
    var time = (new Date()).toLocaleString();

    $(".time").text(time);
}

setInterval(setTime, 1000);

(function setTitle() {
    if (sessionStorage.notebook_name !== undefined) {
        $(".notebook-name").text(sessionStorage.notebook_name);
    }
})();