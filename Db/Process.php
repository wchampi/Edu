<?php

if (!class_exists('Edu_DB_Process')) {

    require_once dirname(__FILE__) . '/PDO.php';

    class Edu_DB_Process {

        /**
         *
         * @var Edu_Db_PDO 
         */
        private $_conn = null;
        
        public function __construct(Edu_Db_PDO $conn) {
            $this->_conn = $conn;
        }
        
        public function getLocked() {
            $rowsLocked = array();
            $rows = $this->_conn->fetchAll('SHOW PROCESSLIST');
            foreach ($rows as $row) {
                if ($row['State'] == 'Locked' && $row['Time'] > 20) {
                    $rowsLocked[] = $row;
                }
            }
            
            return $rowsLocked;
        }
        
    }

}