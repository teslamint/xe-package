/* 정보 입력 */
function completeAddInfo(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];

    if(error) alert(message);

    window.close();

}