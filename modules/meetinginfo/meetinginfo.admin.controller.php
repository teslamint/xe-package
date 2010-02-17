<?php
    /**
     * @class  meetingInfoAdminController
     * @author teslamint (teslamint+xe@gmail.com)
     * @brief  meetingInfo 모듈의 Admin Controller class
     **/

    class meetingInfoAdminController extends meetingInfo {

        /**
         * @brief 초기화
         **/
        function init() {
        }

        /**
         * @brief 환경 설정 저장
         **/
        function procMeetingInfoAdminSetConfig()
        {
            // 기본 정보를 받음
            $args = Context::gets(
                'title_header', 'default_onlyme', 'skin'
            );

            if(!$args->title_header) $args->title_header = "MeetingInfo";
            if(!$args->default_onlyme) $args->default_onlyme = 'N';
            if(!$args->skin) $args->skin = 'default';

            $oModuleController = &getController('module');
            $output = $oModuleController->insertModuleConfig('meetingInfo',$args);
            if(!$output->toBool()) return $output;

            $this->setMessage('msg_success');
        }

        /**
         * @brief 정보 일괄삭제 (관리자용)
         **/
        function procMeetingInfoAdminDeleteAll() {

            // 변수 체크
            $cart = trim(Context::get('cart'));
            if(!$cart) return new Object(-1, 'msg_cart_is_null');

            $cart_list = explode('|@|', $cart);
            if(!count($cart_list)) return new Object(-1, 'msg_cart_is_null');

            $target = array();
            for($i=0;$i<count($cart_list);$i++) {
                $message_srl = (int)trim($cart_list[$i]);
                if(!$message_srl) continue;
                $target[] = $message_srl;
            }
            if(!count($target)) return new Object(-1,'msg_cart_is_null');

            // 삭제
            $args->message_srls = implode(',',$target);
            $output = executeQuery('meetinginfo.deleteMeetingInformations', $args);
            if(!$output->toBool()) return $output;

            $this->setMessage('success_deleted');
        }

    }

?>
