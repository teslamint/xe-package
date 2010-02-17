<?php
    /**
     * @class  meetingInfoAdminView
     * @author teslamint (teslamint+xe@gmail.com)
     * @brief  meetingInfo 모듈의 Admin View class
     **/

    class meetingInfoAdminView extends meetingInfo {

        /**
         * @brief 초기화
         **/
        function init() {
            // tpl인 관리자 템플릿으로 경로 설정
            // 템플릿 경로를 미리 셋팅 하면 action member method에서 따로 지정할 필요가 없음.
            $this->setTemplatePath($this->module_path.'tpl');
        }

        /**
         * @brief 관리용 정보 목록
         **/
        function dispMeetingInfoAdminMessages()
        {
            // 목록을 구하기 위한 옵션
            $args->page = Context::get('page'); ///< 페이지
            $args->list_count = Context::get('list_count'); ///< 한페이지에 보여줄 글 수
            $args->page_count = Context::get('page_count'); ///< 페이지 네비게이션에 나타날 페이지의 수
            $args->sort_index = Context::get('sort_index'); ///< 정렬 값

            // 목록 구함
            $oMeetingInfoModel = &getModel('meetinginfo');
            $output = $oMeetingInfoModel->getMeetingInfoList($args);
            $oMemberModel = &getModel('member');

            // 템플릿에 쓰기 위해서 return object에 있는 값들을 세팅
            Context::set('oMemberModel', $oMemberModel);
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('information_list', $output->data);
            Context::set('page_navigation', $output->page_navigation);

            // 템플릿 지정
            $this->setTemplateFile('list');
        }

        /**
         * @brief 환경 설정 기본 페이지
         **/
        function dispMeetingInfoAdminIndex()
        {
            // retrieve configuration via module model instance
            $oMeetingInfoModel = &getModel('meetinginfo');
            $config = $oMeetingInfoModel->getConfig();
            Context::set('config',$config);

            // 스킨 목록을 구해옴
            $oModuleModel = &getModel('module');
            $skin_list = $oModuleModel->getSkins($this->module_path);
            Context::set('skin_list', $skin_list);

            // 템플릿 지정
            $this->setTemplateFile('index');
        }

    }

?>