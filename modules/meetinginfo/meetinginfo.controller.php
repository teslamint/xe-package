<?php
    /**
     * @class  meetingInfoController
     * @author teslamint (teslamint+xe@gmail.com)
     * @brief  meetingInfo 모듈의 Controller class
     **/

    class meetingInfoController extends meetingInfo {

        /**
         * @brief 초기화
         **/
        function init() {

        }

        /**
         * @brief 정보 추가
         **/
        function procMeetingInfoAdd()
        {
            // 로그인 사용자 확인.
            $logged_info = Context::get('logged_info');
            if (!$logged_info)
                return new Object(-1, Context::getlang('msg_not_logged'));

            // 환경설정 가져오기
            $oMeetingInfoModel = &getModel('meetinginfo');
            $meetinginfo_config = $oMeetingInfoModel->getConfig();

            // 언어파일 로드
            $lang = Context::get('lang');

            // 내용
            $content = Context::get('content');
            $receiver_srl = Context::get('guest_srl');

            $sender_log =
                (Context::get('sender_log') == 'Y') ? false : true;
            // 상대방에게 전송할 경우
            if (!$sender_log) {
                $sender_srl = $logged_info->member_srl;

                // 쪽지 받을 회원이 존재하는지 확인.
                $oMemberModel = &getModel('member');
                $receiver_member_info =
                    $oMemberModel->getMemberInfoByMemberSrl($receiver_srl);
                if($receiver_member_info->member_srl != $receiver_srl)
                    return new Object(-1, 'msg_not_exists_member');

                // 제목 - '머릿말:기록한 날짜'
                $meetinginfo_header = $meetinginfo_config->title_header;
                $title =
                    implode(':', array($meetinginfo_header, date("Y/m/d")));

                // 만났던 상대방 닉네임을 내용 앞에 붙임.
                $content2 = '<p>'. htmlspecialchars(
                            implode(':', array($lang->participants_list,
                    $receiver_member_info->nick_name) ) ) . '</p>' . $content;

                // 쪽지 발송
                $oCommunicationController = &getController('communication');
                $result = $oCommunicationController->sendMessage(
                        $sender_srl,
                        $receiver_srl,
                        $title,
                        $content2,
                        $sender_log
                    );
                if (!$result->toBool()) return $result;
            }

            // 테이블에 기록
            $args->message_srl = getNextSequence();
            $args->host_srl = $logged_info->member_srl;
            $args->guest_srl = $receiver_srl;
            $args->content = $content;

            $result =
                executeQuery('meetinginfo.insertMeetingInformation', $args);
            if (!$result->toBool()) return $result;

            $this->setMessage(Context::getlang('success_added'));
        }

        /**
         * @brief 정보 삭제
         **/
        function procMeetingInfoDelete()
        {
            // 로그인 사용자 확인
            if (!Context::get('is_logged'))
                return new Object(-1, 'msg_not_logged');

            $args->message_srl = Context::get('message_srl');
            if (!$args->message_srl)
                return new Object(-1, 'msg_invalid_request');

            // 해당 메시지를 삭제
            $result =
                executeQuery('meetinginfo.deleteMeetingInformation', $args);
            if (!$result->toBool()) return $result;

            $this->setMessage(Context::getlang('success_deleted'));
        }

    }

?>