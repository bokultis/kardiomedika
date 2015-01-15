/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function () {
    $('.modal').on('shown.bs.modal', function(e) {
        $(this).find('.modal-dialog').css({
            'margin-top': function () {
                return -($(this).height() / 2) + 'px';
            },
            'margin-left': function () {
                return -($(this).width() / 2) + 'px';
            }
        });
    });
});
