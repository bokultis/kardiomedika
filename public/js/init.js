$(document).ready(function () {
    //flash messanger - parse static html
    $("#flashMessenger p").each(function () {
        $.flashMessenger($(this).text(), {clsName: $(this).attr('class')});
    });

    window.alert = function (str) {
        $.flashMessenger(str, {
            autoClose: false,
            modal: true,
            clsName: "warn"
        });
    }

    var $frontEditBtn = $("#enableFrontEdit");

    function updateFrontEditStatus() {
        if ($frontEditBtn.data('status') == '1') {
            $frontEditBtn.text($frontEditBtn.data('disable_text'));
        } else {
            $frontEditBtn.text($frontEditBtn.data('enable_text'));
        }
    }

    updateFrontEditStatus();

    $frontEditBtn.click(function () {
        var newStatus = ($frontEditBtn.data('status') == '1') ? '0' : '1';
        $.post('/' + CURR_LANG + '/cms/admin-front/mode/', {
            'enabled': newStatus
        }, function (data) {
            if (data.success) {
                $frontEditBtn.data('status', newStatus);
                updateFrontEditStatus();
            }
            else {
                alert('Unknown Error');
            }
        });
        return false;
    });
});


