<?php

if (!class_exists('Edu_Controller_Manager')) {

    class Edu_Controller_Manager
    {
        public function run() {
            $oper = isset($_POST['oper']) ? $_POST['oper'] : '';
            if ($oper == '') {
                $oper = isset($_GET['oper']) ? $_GET['oper'] : '';
            }
            $callback = str_replace(' ', '', $oper) . 'Action';

            if (function_exists($callback)) {
                echo call_user_func($callback);
            }
        }
    }
}
