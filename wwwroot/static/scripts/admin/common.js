(function(window, undefined){
    function _alert(str, style) {
        $("#main").prepend($('<h4 class="alert_' + style + '">' + str + "</h4>").hide().fadeIn());
    }
    function success(str) {
        _alert(str, 'success');
    }
    function info(str) {
        _alert(str, 'info');
    }
    function warn(str) {
        _alert(str, 'warning');
    }
    function error(str) {
        _alert(str, 'error');
    }
    var common = {
        success: success,
        info: info,
        warn: warn,
        error: error,
    };
    window.common = common;
})(window);
