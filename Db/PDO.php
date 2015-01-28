<?php

if (!class_exists('Edu_Db_PDO')) {

    class Edu_Db_PDO extends PDO {

        private $_instanceName = '';
        private static $_lastQuery;
        private static $_lastParams;
        private static $_cache = array();

        /**
         * 
         * @param string $instanceName
         */
        public function setInstanceName($instanceName) {
            $this->_instanceName = $instanceName;
        }

        /**
         * 
         * 
         * @param string $query
         * @param array $params
         * @param boolean $cache
         * @return type
         */
        public function fetchOne($query, $params = array(), $cache = false) {
            self::$_lastQuery = $query;
            self::$_lastParams = $params;
            $hash = $this->_instanceName . 'fetchOne@' . $query . md5(print_r($params, TRUE));
            if (isset(self::$_cache[$hash]) && $cache) {
                return self::$_cache[$hash];
            } else {
                if (!is_array($params)) {
                    $params = array($params);
                }
                $stm = $this->prepare($query);
                $stm->execute($params);

                if ($cache) {
                    self::$_cache[$hash] = $stm->fetch(PDO::FETCH_ASSOC);
                    return self::$_cache[$hash];
                } else {
                    return $stm->fetch(PDO::FETCH_ASSOC);
                }
            }
        }

        public function fetchAll($query, $params = array(), $cache = false) {
            self::$_lastQuery = $query;
            self::$_lastParams = $params;
            $hash = $this->_instanceName . 'fetchAll@' . $query . md5(print_r($params, TRUE));
            if (isset(self::$_cache[$hash]) && $cache) {
                return self::$_cache[$hash];
            } else {
                if (!is_array($params)) {
                    $params = array($params);
                }
                $stm = $this->prepare($query);
                $stm->execute($params);

                if ($cache) {
                    self::$_cache[$hash] = $stm->fetchAll(PDO::FETCH_ASSOC);
                    return self::$_cache[$hash];
                } else {
                    return $stm->fetchAll(PDO::FETCH_ASSOC);
                }
            }
        }

        public function fetchBy($where, $table, $orderby = null, $limit = null, $params = array()) {
            return self::fetchAll(self::_prepareSelectSimple($where, $table, $orderby, $limit), $params);
        }

        public function fetchOneBy($where, $table, $orderby = null, $params = array(), $cache = false) {
            return self::fetchOne(self::_prepareSelectSimple($where, $table, $orderby, 1), $params, $cache);
        }

        public function queryKeyVal($query, $params = array(), $cache = false) {
            self::$_lastQuery = $query;
            self::$_lastParams = $params;
            $hash = $this->_instanceName . 'queryKeyVal@' . $query . md5(print_r($params, TRUE));
            if (isset(self::$_cache[$hash]) && $cache) {
                return self::$_cache[$hash];
            } else {
                if (!is_array($params)) {
                    $params = array($params);
                }
                $stm = $this->prepare($query);
                $stm->execute($params);

                if ($cache) {
                    self::$_cache[$hash] = $stm->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE);
                    return self::$_cache[$hash];
                } else {
                    return $stm->fetchAll(PDO::FETCH_COLUMN | PDO::FETCH_UNIQUE);
                }
            }
        }

        public function queryKeyArray($query, $params = array(), $cache = false) {
            self::$_lastQuery = $query;
            self::$_lastParams = $params;
            $hash = $this->_instanceName . 'queryKeyArray@' . $query . md5(print_r($params, TRUE));
            if (isset(self::$_cache[$hash]) && $cache) {
                return self::$_cache[$hash];
            } else {
                if (!is_array($params)) {
                    $params = array($params);
                }
                $stm = $this->prepare($query);
                $stm->execute($params);

                if ($cache) {
                    self::$_cache[$hash] = $stm->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
                    return self::$_cache[$hash];
                } else {
                    return $stm->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_UNIQUE);
                }
            }
        }

        public function queryKeyGroup($query, $params = array(), $cache = false) {
            self::$_lastQuery = $query;
            self::$_lastParams = $params;
            $hash = $this->_instanceName . 'queryKeyGroup@' . $query . md5(print_r($params, TRUE));
            if (isset(self::$_cache[$hash]) && $cache) {
                return self::$_cache[$hash];
            } else {
                if (!is_array($params)) {
                    $params = array($params);
                }
                $stm = $this->prepare($query);
                $stm->execute($params);

                if ($cache) {
                    self::$_cache[$hash] = $stm->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);
                    return self::$_cache[$hash];
                } else {
                    return $stm->fetchAll(PDO::FETCH_ASSOC | PDO::FETCH_GROUP);
                }
            }
        }

        public function execute($query, $params = array()) {
            self::$_lastQuery = $query;
            self::$_lastParams = $params;
            if (!is_array($params)) {
                $params = array($params);
            }
            $stm = $this->prepare($query);

            return $stm->execute($params);
        }

        private static function _prepareSelectSimple($where, $table, $orderby = null, $limit = null) {
            $table = self::_prepareName($table);

            $query = "SELECT * FROM $table"
                . self::_prepareWhere($where)
                . self::_prepareOrderBy($orderby)
                . self::_prepareLimit($limit);
            return $query;
        }

        private static function _prepareWhere($where) {
            if (is_string($where)) {
                $where = array($where);
            }

            if (is_array($where)) {
                return ' WHERE ' . implode(' AND ', $where);
            }
            return '';
        }

        private static function _prepareOrderBy($orderby) {
            if (is_string($orderby)) {
                $orderby = array($orderby);
            }

            if (is_array($orderby)) {
                return ' ORDER BY ' . implode(' , ', $orderby);
            }
            return '';
        }

        private static function _prepareLimit($limit) {
            if ((int) $limit > 0) {
                return ' LIMIT ' . $limit;
            }

            return '';
        }

        private static function _prepareName($name) {
            return "`" . str_replace('`', '', $name) . "`";
        }

        public function prepareSqlInsert($table, array $params) {
            $table = self::_prepareName($table);

            $columns = array();
            $paramsValue = array();
            $values = array();
            foreach ($params as $column => $value) {
                $columns[] = self::_prepareName($column);
                if (is_array($value)) {
                    if (!key_exists('value', $value)) {
                        throw new Exception('invalid param insert');
                    }
                    if (key_exists('tagAllow', $value)) {
                        $paramsValue[] = $value['value'];
                    } else {
                        $paramsValue[] = strip_tags($value['value']);
                    }
                } else {
                    $paramsValue[] = strip_tags($value);
                }
                $values[] = "'" . str_replace("'", "''", $value) . "'";
            }
            $query = "INSERT INTO $table (" . implode(',', $columns) . ")"
                . " VALUES (" . implode(',', $values) . ");";
            return $query;
        }

        public function prepareSqlInsertMulti($table, array $rows) {
            $table = self::_prepareName($table);

            if (!isset($rows[0])) {
                return '';
            }

            $params = $rows[0];
            foreach ($params as $column => $value) {
                $columns[] = self::_prepareName($column);
            }

            $values = array();
            foreach ($rows as $params) {
                $columnValues = array();
                foreach ($params as $column => $value) {
                    $columnValues[] = $this->quote($value);
                }
                $values[] = "\n(" . implode(',', $columnValues) . ")";
            }

            $query = "INSERT INTO $table (" . implode(',', $columns) . ")"
                . " VALUES " . implode(',', $values) . ";";
            return $query;
        }

        public function insert($table, array $params) {
            $table = self::_prepareName($table);

            $columns = array();
            $paramsValue = array();
            $values = array();
            foreach ($params as $column => $value) {
                $columns[] = self::_prepareName($column);
                if (is_array($value)) {
                    if (!key_exists('value', $value)) {
                        throw new Exception('invalid param insert');
                    }
                    if (key_exists('tagAllow', $value)) {
                        $paramsValue[] = $value['value'];
                    } else {
                        $paramsValue[] = strip_tags($value['value']);
                    }
                } else {
                    $paramsValue[] = strip_tags($value);
                }
                $values[] = '?';
            }
            $query = "INSERT INTO $table (" . implode(',', $columns) . ")"
                . " VALUES (" . implode(',', $values) . ")";

            self::$_lastQuery = $query;
            self::$_lastParams = $paramsValue;

            $stm = $this->prepare($query);
            $stm->execute($paramsValue);

            return $this->lastInsertId($table);
        }

        public function update($table, array $params, $where = null, $paramsWhere = array()) {
            if (count($params) == 0) {
                return false;
            }

            $table = self::_prepareName($table);

            $paramsValue = array();
            $columns = array();
            foreach ($params as $column => $value) {
                $columns[] = self::_prepareName($column) . ' = ?';
                if (is_array($value)) {
                    if (!key_exists('value', $value)) {
                        throw new Exception('invalid param update');
                    }
                    if (key_exists('tagAllow', $value)) {
                        $paramsValue[] = $value['value'];
                    } else {
                        $paramsValue[] = strip_tags($value['value']);
                    }
                } else {
                    $paramsValue[] = strip_tags($value);
                }
            }

            if (!is_array($paramsWhere)) {
                $paramsWhere = array($paramsWhere);
            }
            foreach ($paramsWhere as $value) {
                $paramsValue[] = $value;
            }

            $query = "UPDATE $table"
                . " SET " . implode(',', $columns)
                . self::_prepareWhere($where);

            self::$_lastQuery = $query;
            self::$_lastParams = $paramsValue;

            $stm = $this->prepare($query);
            return $stm->execute($paramsValue);
        }

        public function getCache() {
            return self::$_cache;
        }

        public function getLastQuery() {
            return self::$_lastQuery;
        }

        public function getLastParams() {
            return self::$_lastParams;
        }

        public function generateBackupLight($fileBackup, $limitInsert = 500) {
            echo "\n" . date('H:i:s') . "\n";
            
            set_time_limit(0);
            $this->exec("set names utf8");
            
            $data = "-- BACKUP - " . date('Y-m-d H:i:s') . "
\n/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40101 SET GLOBAL max_allowed_packet=16*1024*1024  */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;";

            file_put_contents($fileBackup, $data);

            $query = "SHOW FULL TABLES WHERE TABLE_TYPE LIKE 'BASE TABLE';";
            $rows = $this->fetchAll($query);

            echo "\n[TABLES]";
            foreach ($rows as $row) {
                $table = current($row);
                echo "\n$table";

                # CREATE TABLE
                $query = "SHOW CREATE TABLE $table";
                $row = $this->fetchOne($query);
                $data = "\n\n" . $row['Create Table'] . ';';

                file_put_contents($fileBackup, $data, FILE_APPEND);

                # INSERTS
                $data = "\n\n/*!40000 ALTER TABLE `$table` DISABLE KEYS */;";

                $query = "SELECT * FROM " . $table . " ORDER BY 1 DESC LIMIT $limitInsert";
                $rows = $this->fetchAll($query);
                $data .= "\n" . $this->prepareSqlInsertMulti($table, $rows);

                $data .= "\n/*!40000 ALTER TABLE `$table` ENABLE KEYS */;";

                file_put_contents($fileBackup, $data, FILE_APPEND);
            }

            $query = "SHOW FUNCTION STATUS WHERE Db = '" . DB_MASTER_NAME . "';";
            $rows = $this->fetchAll($query);

            echo "\n\n[FUNCTION]";
            foreach ($rows as $row) {
                $function = $row['Name'];
                echo "\n$function";

                # CREATE FUNCTION
                $query = "SHOW CREATE FUNCTION " . DB_MASTER_NAME . ".$function";
                $row = $this->fetchOne($query);
                $data = "\n\nDELIMITER //\n" . $row['Create Function'] . "//\nDELIMITER ;";

                file_put_contents($fileBackup, $data, FILE_APPEND);
            }

            $query = "SHOW PROCEDURE STATUS WHERE Db = '" . DB_MASTER_NAME . "';";
            $rows = $this->fetchAll($query);

            echo "\n\n[PROCEDURE]";
            foreach ($rows as $row) {
                $procedure = $row['Name'];
                echo "\n$procedure";

                # CREATE PROCEDURE
                $query = "SHOW CREATE PROCEDURE " . DB_MASTER_NAME . ".$procedure";
                $row = $this->fetchOne($query);
                $data = "\n\nDELIMITER //\n" . $row['Create Procedure'] . "//\nDELIMITER ;";

                file_put_contents($fileBackup, $data, FILE_APPEND);
            }
            
            $query = "SHOW FULL TABLES WHERE TABLE_TYPE LIKE 'VIEW';";
            $rows = $this->fetchAll($query);

            echo "\n\n[VIEWS]";
            foreach ($rows as $row) {
                $view = current($row);
                echo "\n$view";

                # CREATE VIEW
                $query = "SHOW CREATE VIEW $view";
                $row = $this->fetchOne($query);
                $data = "\n\n" . $row['Create View'] . ';';

                file_put_contents($fileBackup, $data, FILE_APPEND);
            }

            $query = "SHOW TRIGGERS;";
            $rows = $this->fetchAll($query);

            echo "\n\n[TRIGGERS]";
            foreach ($rows as $row) {
                $trigger = $row['Trigger'];
                echo "\n$trigger";

                # CREATE TRIGGER
                $query = "SHOW CREATE TRIGGER $trigger";
                $row = $this->fetchOne($query);
                $data = "\n\nDELIMITER //\n" . $row['SQL Original Statement'] . "//\nDELIMITER ;";

                file_put_contents($fileBackup, $data, FILE_APPEND);
            }

            $data = "\n\n/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;";
            file_put_contents($fileBackup, $data, FILE_APPEND);
            
            echo "\n\n" . date('H:i:s');
        }

    }

}