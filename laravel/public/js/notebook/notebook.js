function setTime() {
    var time = (new Date()).toLocaleString();

    $(".time").text(time);
}

setInterval(setTime, 1000);