<?php
    /**
     * @class  meetingInfoModel
     * @author teslamint (teslamint+xe@gmail.com)
     * @brief  meetingInfo 모듈의 Model class
     **/

    class meetingInfoModel extends meetingInfo {

        /**
         * @brief 초기화
         **/
        function init() {
        }

        /**
         * @brief 설정된 내용을 구함
         **/
        function getConfig() {
            $oModuleModel = &getModel('module');
            $meetinginfo_config = $oModuleModel->getModuleConfig('meetinginfo');

            if(!$meetinginfo_config->title_header)
                $meetinginfo_config->title_header = 'meetinginfo';
            if(!$meetinginfo_config->default_onlyme)
                $meetinginfo_config->default_onlyme = 'N';
            if(!$meetinginfo_config->skin)
                $meetinginfo_config->skin = 'default';

            return $meetinginfo_config;
        }

        /**
         * @brief 개별 미팅 정보 가져오기
         * @param[in] $args
         **/
        function getMeetingInfo($args)
        {
            // 로그인 사용자 확인
            if(!Context::get('is_logged'))
                return new Object(-1, Context::getlang('msg_not_logged'));

            if(!$args->message_srl)
                $args->message_srl = Context::get('message_srl');
            if(!$args->message_srl)
                return new Object(-1, 'msg_invalid_request');

            // 쿼리 실행
            $output = executeQuery('meetinginfo.getMeetingInformation', $args);
            if (!$output->toBool()) return $output;

            // object로 돌려줌
            $this->add('output', $output);
        }

        /**
         * @brief 주최자 기준 미팅 정보 목록 가져오기. 참석자는 로그인된 회원.
         **/
        function getMeetingInfoListAsHost()
        {
            // 로그인 사용자 확인
            if(!Context::get('is_logged'))
                return new Object(-1, Context::getlang('msg_not_logged'));
            $logged_info = Context::get('logged_info');

            $args->member_srl = $logged_info->member_srl;
            if(!$args->host_srl) $args->host_srl = Context::get('host_srl');
            if(!$args->host_srl)
                return new Object(-1, Context::getlang('msg_invalid_request'));
            // 쿼리 실행
            $output = executeQuery('meetinginfo.getMeetingInfoListAsHost', $args);

            // object로 돌려줌
            $this->add('output', $output);
        }

        /**
         * @brief 참석자 기준 미팅 정보 목록 가져오기. 주최자는 로그인한 회원
         **/
        function getMeetingInfoListAsGuest()
        {
            // 로그인 사용자 확인
            if(!Context::get('is_logged'))
                return new Object(-1, Context::getlang('msg_not_logged'));
            $logged_info = Context::get('logged_info');

            if(!$args->guest_srl) $args->guest_srl = Context::get('guest_srl');
            $args->host_srl = $logged_info->member_srl;

            // 쿼리 실행
            $output =
                executeQuery('meetinginfo.getMeetingInfoListAsGuest', $args);
            if (!$output->toBool()) return $output;

            // object로 돌려줌
            $this->add('output', $output->data);
        }

        /**
         * @brief 관리용 미팅 정보 목록 가져오기
         * @param[in] $args 필요한 변수들이 모여있는 object (page, list_count, page_count, sort_index)
         * @return 성공시 결과가 포함된 array
         **/
        function getMeetingInfoList($args)
        {
            // 로그인 사용자 확인
            if(!Context::get('is_logged'))
                return new Object(-1, 'msg_not_logged');

            // 목록을 구하기 위한 옵션
            if(!$args->page)
                $args->page = 1; ///< 페이지
            if(!$args->list_count)
                $args->list_count = 20; ///< 한페이지에 보여줄 글 수
            if(!$args->page_count)
                $args->page_count = 10; ///< 페이지 네비게이션에 나타날 페이지의 수

            if(!$args->sort_index)
                $args->sort_index = 'meeting_info.regdate'; ///< 정렬 값

            // 쿼리 실행
            $output =
                executeQuery('meetinginfo.getMeetingInformationList', $args);
            if (!$output->toBool()) return $output;

            // object로 돌려줌
            $this->add('output', $output);
            return $output;
        }

    }

?>
