function setTime() {
    function formatWeekDay(day) {
        switch (day)
        {
            case 0:
                return '星期日';
            case 1:
                return '星期一';
            case 2:
                return '星期二';
            case 3:
                return '星期三';
            case 4:
                return '星期四';
            case 5:
                return '星期五';
            case 6:
                return '星期六';
            default:
                return '';
        }
    }

    function strpad(str) {
        return str.toString().padStart(2, 0);
    }

    var date = new Date();

    var dateStr = date.getFullYear()+'/'+strpad(date.getMonth())+'/'+strpad(date.getDate())+' '
                    +strpad(date.getHours())+':'+strpad(date.getMinutes())+':'+strpad(date.getSeconds())+' '
                    +formatWeekDay(date.getDay());

    $(".time").text(dateStr);
}

setInterval(setTime, 1000);

(function setTitle() {
    if (sessionStorage.notebook_name !== undefined) {
        $(".notebook-name").text(sessionStorage.notebook_name);
    }
})();