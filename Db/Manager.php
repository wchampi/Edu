<?php

if (!class_exists('Edu_Db_Manager')) {

    require_once dirname(__FILE__) . '/PDO.php';
    
    class Edu_Db_Manager
    {

        private static $_objInstances = array();

        /**
         * Returns DB instance or create initial connection
         * @param
         * @return Edu_Db_PDO;
         */
        public static function getInstance($instanceName = '', $host = '', $dbName = '', $user = '', $pass = '')
        {
            if (!isset(self::$_objInstances[$instanceName])) {
                if ($host != '' && $dbName != '' && $user != '' && $pass != '') {
                    self::$_objInstances[$instanceName] = new Edu_Db_PDO('mysql:host=' . $host . ';dbname=' . $dbName, $user, $pass, array(PDO::MYSQL_ATTR_LOCAL_INFILE=>1));
                    self::$_objInstances[$instanceName]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    #self::$_objInstances[$instanceName]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
                    #self::$_objInstances[$instanceName]->setAttribute(PDO::MYSQL_ATTR_LOCAL_INFILE, true);
                    self::$_objInstances[$instanceName]->setInstanceName($instanceName);
                } else {
                    return null;
                }    
            }

            return self::$_objInstances[$instanceName];
        }

        
    }

}