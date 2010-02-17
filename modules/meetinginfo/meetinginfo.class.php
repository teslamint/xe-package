<?php
    /**
     * @class  meetingInfo
     * @author teslamint (teslamint+xe@gmail.com)
     * @brief  high class of meetingInfo
     **/

	class meetingInfo extends ModuleObject {

        /**
         * @brief implement when it needs extra process
         **/
        function moduleInstall() {

            return new Object();
        }

        /**
         * @brief check if there is a problem with install
         **/
        function checkUpdate() {
            return false;
        }

        /**
         * @brief perform update
         **/
        function moduleUpdate() {
            return new Object();
        }

        /**
         * @brief recreate cache file
         **/
        function recompileCache() {
        }

    }
?>
