(function(window, undefined){
    function info(str, style) {
        $("#main").prepend('<h4 class="' + style + '_info">' + str + "</h4>");
    }
    function alert(str) {
        info(str, 'alert');
    }
    function warn(str) {
        info(str, 'warn');
    }
    function error(str) {
        info(str, 'error');
    }
    var common = {
        alert: alert,
        warn: warn,
        error: error,
    };
    window.common = common;
})(window);