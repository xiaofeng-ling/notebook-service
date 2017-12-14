$ = function(select) {
    return new $.prototype.init(select)
};

$.prototype = {
    constructor: $,
    init: function(select) {
        if (select === undefined)
            return this;

        if (select.indexOf('.') !== -1)
            return document.getElementsByClassName(select.substr(1));
        else if (select.indexOf('#') !== -1)
            return document.getElementById(select.substr(1));
        else
            return document.getElementsByTagName(select);
    }
}

$.prototype.init.prototype = $.prototype;

$.ajax = function(params) {
    let ajax = new XMLHttpRequest(), data = '';

    ajax.open(params['method'], params['url'], true);
    ajax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    ajax.onreadystatechange = function() {
        if (ajax.readyState === 4 && ajax.status === 200) {
            let response = JSON.parse(ajax.responseText);
            params['success'](response);
        }
        else {
            if ('error' in params)
                params['error']();
        }
    }

    for (let k in params['data'])
        data += k + '=' + params['data'][k]

    ajax.send(data);
}