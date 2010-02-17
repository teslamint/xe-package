<?php
    /**
     * @class  meetinginfoSPhone
     * @author teslamint (teslamint+xe@gmail.com)
     * @brief  meetinginfo 모듈의 SmartPhone(iPhone) class
     **/

    class meetinginfoSPhone extends meetingInfo {


        /**
         * @brief 스마트폰일 때 템플릿 처리
         **/
        function procSmartPhone(&$oSmartPhone) {
            // 로그인정보 구해옴
            $logged_info = Context::get('logged_info');
            if(!$logged_info->member_srl)
                return $oSmartPhone->setContent(
                    Context::getLang('msg_not_logged'));

            // 환경설정 가져오기
            $oMeetingInfoModel = &getModel('meetinginfo');
            $this->config = $oMeetingInfoModel->getConfig();
            Context::set('config', $this->config);
            $template_path = sprintf('%sskins/%s/smartphone',
                                     $this->module_path,
                                     $this->config->skin);

            // 주최자, 참석자 정보 구해옴
            $host_srl = $logged_info->member_srl;
            $guest_srl = Context::get('guest_srl');
            if ($guest_srl) {
                $oMemberModel = &getModel('member');
                $host = $oMemberModel->getMemberInfoByMemberSrl($host_srl);
                $guest = $oMemberModel->getMemberInfoByMemberSrl($guest_srl);

                Context::set('host', $host);
                Context::set('guest', $guest);

                $tpl_file = 'insert_form';
            } else {
                return $oSmartPhone->setContent(
                    Context::getLang('msg_invalid_request'));
            }

            $oTemplate = new TemplateHandler();
            $content = $oTemplate->compile($template_path, $tpl_file);
            return $oSmartPhone->setContent($content);
        }

    }
?>