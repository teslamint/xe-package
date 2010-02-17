/* 정보 변경 */
function completeUpdateConfig(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];

    alert(message);

    var url = current_url.setQuery('act','dispMeetinginfoAdminIndex');

    location.href = url;
}

/* 정보 삭제 */
function completeDelete(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    var message_type = ret_obj['message_type'];
    var page = ret_obj['page'];

    alert(message);

    var url = current_url.setQuery('act','dispMeetinginfoAdminMessages');
    if(page) url = url.setQuery('page', page);

    location.href = url;
}
