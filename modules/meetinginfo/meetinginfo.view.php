<?php
    /**
     * @class  meetingInfoModel
     * @author teslamint (teslamint+xe@gmail.com)
     * @brief  meetingInfo 모듈의 View class
     **/

    class meetingInfoView extends meetingInfo
    {

        function init()
        {
            // 커뮤니케이션 애드온이 활성화 되어있는지 검사
            $oAddonAdminModel = &getAdminModel('addon');
            $communication_check =
                $oAddonAdminModel->isActivatedAddon("member_communication");
            if(!$communication_check)
                return $this->stop('communication_addon_null');

            // 환경설정 가져오기
            $oMeetingInfoModel = &getModel('meetinginfo');
            $this->config = $oMeetingInfoModel->getConfig();

            // template path 지정
            $this->setTemplatePath(
                $this->module_path.'skins/'.$this->config->skin);
        }

        function dispMeetingInfoAdd()
        {
            // 로그인정보 구해옴
            $logged_info = Context::get('logged_info');
            if(!$logged_info->member_srl)
                return new Object(-1, 'msg_not_logged');

            // 주최자, 참석자 정보 구해옴
            $host_srl = $logged_info->member_srl;
            $guest_srl = Context::get('guest_srl');
            $oMemberModel = &getModel('member');
            $host = $oMemberModel->getMemberInfoByMemberSrl($host_srl);
            $guest = $oMemberModel->getMemberInfoByMemberSrl($guest_srl);

            // 커뮤니케이션 모듈의 getEditor를 호출하여 서명용으로 세팅
            $oCommunicationModel = &getModel('communication');
            $this->communication_config = $oCommunicationModel->getConfig();
            $option->primary_key_name = 'message_srl';
            $option->content_key_name = 'content';
            $option->disable_html = true;
            $option->enable_default_component = true;// false;
            $option->allow_fileupload = false;
            $option->enable_autosave = false;
            $option->enable_component = false;
            $option->resizable = false;
            $option->height = 300;
            $option->skin = $this->communication_config->editor_skin;
            $option->colorset = $this->communication_config->editor_colorset;
            $oEditorModel = &getModel('editor');
            $editor = $oEditorModel->getEditor($logged_info->member_srl, $option);

            Context::set('host', $host);
            Context::set('guest', $guest);
            Context::set('editor', $editor);
            Context::set('config', $config);

            $this->setLayoutFile('popup_layout');
            $this->setTemplateFile('insert_form');
        }
    }


?>