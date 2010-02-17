<?php
    /**
     * @class friend_info
     * @author teslamint (teslamint+xe@gmail.com)
     * @brief 친구 위치 정보 출력
     * @version 0.1.8
     **/

    class friend_info extends WidgetHandler {

        /**
         * @brief 위젯의 실행 부분
         *
         * ./widgets/위젯/conf/info.xml 에 선언한 extra_vars를 args로 받는다
         * 결과를 만든후 print가 아니라 return 해주어야 한다
         **/
        function proc($args) {
            // 이미 실행됐으면 중지
            $friends_map_info = Context::get('friends_map_info');
            if (isset($friends_map_info->launched) && $friends_map_info->launched) {
                return;
            }
            $friends_map_info->launched = true;
            Context::set('friends_map_info', $friends_map_info);

            // 언어파일 로드
            Context::loadLang($this->widget_path.'lang');

            // 비로그인 사용자면 중지
            $logged_info = Context::get('logged_info');
            if(!$logged_info) {
                // 템플릿 컴파일
                $tpl_path = sprintf('%sskins/%s', $this->widget_path, $args->skin);
                $tpl_file = 'login_required';

                $oTemplate = &TemplateHandler::getInstance();
                return $oTemplate->compile($tpl_path, $tpl_file);
            }

            $oMemberModel = &getModel('member');

            // 로그인 회원 확장 정보 추출
            $user_info[$logged_info->member_srl] = $oMemberModel->getMemberInfoByMemberSrl($logged_info->member_srl);
            $geo_key = "";

            // 위치 정보가 입력된 확장 변수를 찾는다.
            foreach ($user_info[$logged_info->member_srl] as $key => $value) {
                if(@preg_match("/[0-9]+(\.[0-9]+)?,([\s]*)?([-+])?[0-9]+(\.[0-9]+)?/", $value)) {
                    $geo_key = $key;
                    $widget_info->geolocation[$logged_info->member_srl] = $value;
                }
            }

            // 변수를 찾으면 정보 공개 설정 변수를 설정.
            if ($geo_key != "") $geo_open_key = "open_". $key;
            else {
                // 위치 설정이 안 되어 있으면 기본 좌표 설정.
                $widget_info->geolocation[$logged_info->member_srl] = "0.0,0.0";
            }

            // 친구 그룹 정보 추출
            $oCommModel = &getModel('communication');
            $output = $oCommModel->getFriendGroups();

            // 친구 그룹이 있는 경우
            if(!is_null($output)) {
                $friend_group_list = $output;
                $friend_group_srl = $output->friend_group_srl;
                Context::set('friend_group_list', $friend_group_info);
            } else {
                $friend_group_srl = 0;
            }

            // 친구 정보 추출
            $output = $oCommModel->getFriends($friend_group_srl);
            if (is_array($output->data)) {
                foreach ($output->data as $key=>$value) {
                    $friend_srl = $value->member_srl;
                    $user_info[$friend_srl] = $value;
                    // getFriends 함수는 확장 변수를 정리해주지 않기 때문에 직접 정리.
                    $user_info[$friend_srl] = $oMemberModel->arrangeMemberInfo($user_info[$friend_srl]);
                    foreach($user_info[$friend_srl] as $key => $value) {
                        if(@preg_match("/[0-9]+(\.[0-9]+)?,([\s]*)?([-+])?[0-9]+(\.[0-9]+)?/", $value)) {
                            $is_openinfo = isset($user_info[$friend_srl]->$geo_open_key) ?
                                $user_info[$friend_srl]->$geo_open_key : 'Y';
                            if ($is_openinfo) {
                                $widget_info->geolocation[$friend_srl] = $value;
                            }
                        }
                    }
                }
            }

            // 지역 설정
            if(Context::getLangType() == 'ko') $region = '.co.kr';
            else $region = '.com';

            $header_script = '<script src="http://maps.google'.$region.'/maps?file=api&amp;v=2&amp;key='.$args->friends_google_map_api_key.'" type="text/javascript"></script>';
            // 구글 지도 API 버전 3 전용
            $header_script_v3 = '<script src="http://maps.google.'.$region.'/maps/api/js?sensor=true&amp;language=ko&amp;key='.$args->friends_google_map_api_key.'" type="text/javascript"></script>';

            $widget_info->map_width = ($args->friends_map_width ? $args->friends_map_width : 600);
            $widget_info->map_height = ($args->friends_map_height ? $args->friends_map_height : 400);
            Context::addHtmlHeader($header_script);
            Context::set('user_info', $user_info);
            Context::set('widget_info', $widget_info);
            Context::set('colorset', $args->colorset);

            // 템플릿 컴파일
            $tpl_path = sprintf('%sskins/%s', $this->widget_path, $args->skin);
            // 스마트폰인지 확인
            $is_smartphone = is_null(Context::get('smart_content')) ? false : true;
            if ($is_smartphone) $tpl_file = 'friend_info.smartphone';
            else $tpl_file = 'friend_info';

            $oTemplate = &TemplateHandler::getInstance();
            return $oTemplate->compile($tpl_path, $tpl_file);
        }
    }

?>
