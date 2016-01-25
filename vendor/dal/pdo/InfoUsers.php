<?php

/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */

namespace DAL\PDO;

/**
 * example DAL layer class for test purposes
 * @author Mustafa Zeynel Dağlı
 */
class InfoUsers extends \DAL\DalSlim {

    /**
     * basic delete from database  example for PDO prepared
     * statements, table names are irrevelant and should be changed on specific 
     * returned result set example;
     * for success result
     * Array
      (
      [found] => 1
      [errorInfo] => Array
      (
      [0] => 00000
      [1] =>
      [2] =>
      )

      [affectedRowsCount] => 1
      )
     * for error result
     * Array
      (
      [found] => 0
      [errorInfo] => 42P01
      )
     * usage
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function delete($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $statement = $pdo->prepare("
                    UPDATE info_users 
                    SET deleted = 1, active =1,
                    user_id = " . intval($params['user_id']) . "                     
                    WHERE id = :id
                    ");
            //Bind our value to the parameter :id.
            $statement->bindValue(':id', $params['id'], \PDO::PARAM_INT);
            $update = $statement->execute();
            $affectedRows = $statement->rowCount();
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            $pdo->commit();
            return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows);
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * basic select from database  example for PDO prepared
     * statements, table names are irrevelant and should be changed on specific 
     * returned result set example;
     * for success result
     * Array
      (
      [found] => 1
      [errorInfo] => Array
      (
      [0] => 00000
      [1] =>
      [2] =>
      )

      [resultSet] => Array
      (
      [0] => Array
      (
      [id] => 1
      [name] => zeyn dag
      [international_code] => 12
      [active] => 1
      )

      [1] => Array
      (
      [id] => 4
      [name] => zeyn dag
      [international_code] => 12
      [active] => 1
      )

      [2] => Array
      (
      [id] => 5
      [name] => zeyn dag new
      [international_code] => 25
      [active] => 1
      )

      [3] => Array
      (
      [id] => 3
      [name] => zeyn zeyn oldu şimdik
      [international_code] => 12
      [active] => 1
      )

      )

      )
     * usage
     * @param array | null $args
     * @return type
     * @throws \PDOException
     */
    public function getAll($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');    
            $statement = $pdo->prepare(" 
                    SELECT 
                        a.id, 
                        a.profile_public, 
                        a.f_check, 
                        a.s_date, 
                        a.c_date, 
                        a.operation_type_id,
                        op.operation_name, 
                        a.name, 
                        a.surname, 
                        a.username, 
                        a.password, 
                        a.auth_email,                   
                        a.language_code, 
                        COALESCE(NULLIF(l.language_eng, ''), l.language) AS language_name,
                        sd2.description AS state_deleted, 
                        a.active, 
                        sd3.description AS state_active,  
                        a.deleted, 
                        a.user_id,
                        u.username ,
                        a.act_parent_id, 
                        a.auth_allow_id, 
                        sd.description AS auth_alow ,
                        a.cons_allow_id,
                        sd1.description AS cons_allow ,
                        a.preferred_language
                    FROM info_users a    
                    INNER JOIN sys_operation_types op ON op.id = a.operation_type_id and  op.language_code = a.language_code
                    INNER JOIN sys_specific_definitions sd ON sd.main_group = 13 AND sd.language_code = a.language_code AND a.auth_allow_id = sd.first_group 
                    INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 14 AND  sd1.language_code = a.language_code AND a.cons_allow_id = sd1.first_group 
                    INNER JOIN sys_specific_definitions sd2 ON sd2.main_group = 15 AND sd2.first_group= a.deleted AND sd2.language_code = a.language_code AND sd2.deleted =0 AND sd2.active =0 
                    INNER JOIN sys_specific_definitions sd3 ON sd3.main_group = 16 AND sd3.first_group= a.active AND sd3.language_code = a.language_code AND sd3.deleted = 0 AND sd3.active = 0                    
                    INNER JOIN sys_language l ON l.language_main_code = a.language_code AND l.deleted =0 AND l.active =0 
                    INNER JOIN info_users u ON u.id = a.user_id  
                    WHERE a.deleted =0 AND a.language_code = :language_code   
                    ORDER BY a.name, a.surname
                ");
            $statement->bindValue(':language_code', $params['language_code'], \PDO::PARAM_STR); 
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * basic have records control  
     * * returned result set example;
     * for success result  
     * usage     
     * @author Okan CIRAN
     * @ info_users tablosunda name sutununda daha önce oluşturulmuş mu? 
     * @version v 1.0 20.01.2016
     * @return array
     * @throws \PDOException
     */
    public function haveRecords($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');        
            $addSql = "";
            if (isset($params['id'])) {
                $addSql = " AND id != " . intval($params['id']) . " ";
            }
            $sql = " 
            SELECT  
                name AS name , 
                '" . $params['username'] . "' AS value , 
                name ='" . $params['username'] . "' AS control,
                CONCAT(name , ' daha önce kayıt edilmiş. Lütfen Kontrol Ediniz !!!' ) AS message                             
            FROM info_users                
            WHERE   
                LOWER(auth_email) = LOWER('" . $params['auth_email'] . "') "                    
                    . $addSql . " 
               AND deleted =0   
                               ";
            $statement = $pdo->prepare($sql);
            //   echo debugPDO($sql, $params);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * basic insert database example for PDO prepared
     * statements, table names are irrevelant and should be changed on specific 
     * * returned result set example;
     * for success result
     * Array
      (
      [found] => 1
      [errorInfo] => Array
      (
      [0] => 00000
      [1] =>
      [2] =>
      )

      [lastInsertId] => 5
      )
     * for error result
     * Array
      (
      [found] => 0
      [errorInfo] => 42P01
      )
     * usage
     * @param array | null $args
     * @return array
     * @throws PDOException
     */
    public function insert($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $kontrol = $this->haveRecords($params);
            if (!\Utill\Dal\Helper::haveRecord($kontrol)) {
                $statement = $pdo->prepare(" 
                INSERT INTO info_users(
                            profile_public, 
                            name, 
                            surname, 
                            username, 
                            password, 
                            auth_email,
                            language_code,
                            user_id ,
                            cons_allow_id,
                            operation_type_id,
                            preferred_language)
                VALUES (  :profile_public,
                          :name, 
                          :surname,
                          :username,                      
                          :password, 
                          :auth_email,  
                          :language_code,
                          :user_id,
                          :cons_allow_id,
                          :operation_type_id,
                          :preferred_language
                    )");
                $statement->bindValue(':profile_public', $params['profile_public'], \PDO::PARAM_INT);
                $statement->bindValue(':name', $params['name'], \PDO::PARAM_STR);
                $statement->bindValue(':surname', $params['surname'], \PDO::PARAM_STR);
                $statement->bindValue(':username', $params['username'], \PDO::PARAM_STR);
                $statement->bindValue(':password', $params['password'], \PDO::PARAM_STR);
                $statement->bindValue(':auth_email', $params['auth_email'], \PDO::PARAM_STR);                
                $statement->bindValue(':language_code', $params['language_code'], \PDO::PARAM_INT);
                $statement->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);
                $statement->bindValue(':cons_allow_id', $params['cons_allow_id'], \PDO::PARAM_INT);
                $statement->bindValue(':operation_type_id', $params['operation_type_id'], \PDO::PARAM_INT);
                $statement->bindValue(':preferred_language', $params['preferred_language'], \PDO::PARAM_STR); 
                $result = $statement->execute();
                $insertID = $pdo->lastInsertId('info_users_id_seq');
                $errorInfo = $statement->errorInfo();
                if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                    throw new \PDOException($errorInfo[0]);
                $pdo->commit();
                return array("found" => true, "errorInfo" => $errorInfo, "lastInsertId" => $insertID);
            } else {              
                $errorInfo = '23505';   // 23505  unique_violation
                $pdo->commit();
                $result = $kontrol;
                return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => '');
            }
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * basic update database example for PDO prepared
     * statements, table names are irrevelant and should be changed on specific
     * returned result set example;
     * for success result
     * Array
      (
      [found] => 1
      [errorInfo] => Array
      (
      [0] => 00000
      [1] =>
      [2] =>
      )

      [affectedRowsCount] => 1
      )
     * for error result
     * Array
      (
      [found] => 0
      [errorInfo] => 42P01
      )
     * usage
     * @param array | null $args
     * @param type $params
     * @return array
     * @throws PDOException
     */
    public function update($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $kontrol = $this->haveRecords($params);
            if (!\Utill\Dal\Helper::haveRecord($kontrol)) {
                $act_parent_id = intval($params['act_parent_id']);
                if ($act_parent_id = 0) {
                    $act_parent_id = intval($params['id']);
                }              
                $statement = $pdo->prepare("                                      
                    UPDATE info_users
                    SET                         
                        f_check = :f_check,                         
                        c_date =  timezone('Europe/Istanbul'::text, ('now'::text)::timestamp(0) with time zone) , 
                        active = 1,                    
                        act_parent_id = :act_parent_id,                    
                        user_id = :user_id
                    WHERE id = :id                    
                   ");                
                $statement->bindValue(':id', $params['id'], \PDO::PARAM_INT);
                $statement->bindValue(':act_parent_id', $act_parent_id, \PDO::PARAM_INT);              
                $statement->bindValue(':f_check', $params['f_check'], \PDO::PARAM_INT);
                $statement->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);                                
                $update = $statement->execute();
                $affectedRows = $statement->rowCount();
                $errorInfo = $statement->errorInfo();
                if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                    throw new \PDOException($errorInfo[0]);                
                $statement_act_insert = $pdo->prepare(" 
                INSERT INTO info_users(
                           profile_public, 
                           f_check, 
                           s_date, 
                           c_date, 
                           operation_type_id, 
                           name, 
                           surname, 
                           username,
                           password, 
                           auth_email, 
                           auth_allow_id,                         
                           language_code,                           
                           user_id, 
                           act_parent_id,
                           cons_allow_id,
                           preferred_language)
                  VALUES (:profile_public, 
                          :f_check, 
                          :s_date, 
                          timezone('Europe/Istanbul'::text, ('now'::text)::timestamp(0) with time zone), 
                          :operation_type_id, 
                          :name, 
                          :surname, 
                          :username, 
                          :password, 
                          :auth_email, 
                          :auth_allow_id,                       
                          :language_code,                        
                          :user_id, 
                          :act_parent_id,
                          :cons_allow_id,
                          :preferred_language
                    )");
                $statement_act_insert->bindValue(':profile_public', $params['profile_public'], \PDO::PARAM_INT);
                $statement_act_insert->bindValue(':f_check', $params['f_check'], \PDO::PARAM_INT);
                $statement_act_insert->bindValue(':s_date', $params['s_date'], \PDO::PARAM_STR);
                $statement_act_insert->bindValue(':operation_type_id', $params['operation_type_id'], \PDO::PARAM_INT);
                $statement_act_insert->bindValue(':name', $params['name'], \PDO::PARAM_STR);
                $statement_act_insert->bindValue(':surname', $params['surname'], \PDO::PARAM_STR);    
                $statement_act_insert->bindValue(':username', $params['username'], \PDO::PARAM_STR);                    
                $statement_act_insert->bindValue(':password', $params['password'], \PDO::PARAM_STR);
                $statement_act_insert->bindValue(':auth_email', $params['auth_email'], \PDO::PARAM_STR);
                $statement_act_insert->bindValue(':auth_allow_id', $params['auth_allow_id'], \PDO::PARAM_STR);                
                $statement_act_insert->bindValue(':language_code', $params['language_code'], \PDO::PARAM_INT);
                $statement_act_insert->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);
                $statement_act_insert->bindValue(':act_parent_id', $act_parent_id, \PDO::PARAM_INT);
                $statement_act_insert->bindValue(':cons_allow_id', $params['cons_allow_id'], \PDO::PARAM_INT);
                $statement_act_insert->bindValue(':preferred_language', $params['preferred_language'], \PDO::PARAM_STR);                
                $insert_act_insert = $statement_act_insert->execute();
                $affectedRows = $statement_act_insert->rowCount();
                $errorInfo = $statement_act_insert->errorInfo();
                if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                    throw new \PDOException($errorInfo[0]);  
                $pdo->commit();
                return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows);
            } else {               
                $errorInfo = '23505';  // 23505  unique_violation 
                $pdo->commit();
                $result = $kontrol;
                return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => '');
            }
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * Datagrid fill function used for testing
     * user interface datagrid fill operation
     * @param array | null $args
     * @return Array
     * @throws \PDOException
     */
    public function fillGrid($args = array()) {
        if (isset($args['page']) && $args['page'] != "" && isset($args['rows']) && $args['rows'] != "") {
            $offset = ((intval($args['page']) - 1) * intval($args['rows']));
            $limit = intval($args['rows']);
        } else {
            $limit = 10;
            $offset = 0;
        }
        
        $sortArr = array();
        $orderArr = array();
        if (isset($args['sort']) && $args['sort'] != "") {
            $sort = trim($args['sort']);
            $sortArr = explode(",", $sort);
            if (count($sortArr) === 1)
                $sort = trim($args['sort']);
        } else {       
            $sort = "a.name";
        }

        if (isset($args['order']) && $args['order'] != "") {
            $order = trim($args['order']);
            $orderArr = explode(",", $order);
            //print_r($orderArr);
            if (count($orderArr) === 1)
                $order = trim($args['order']);
        } else {
            $order = "ASC";
        }     
        
        $whereNameSQL = '';
        if (isset($args['search_name']) && $args['search_name'] != "") {
            $whereNameSQL = " AND a.name LIKE '%" . $args['search_name'] . "%' ";             
        }        
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $sql = "    
                    SELECT 
                        a.id, 
                        a.profile_public, 
                        a.f_check, 
                        a.s_date, 
                        a.c_date, 
                        a.operation_type_id,
                        op.operation_name, 
                        a.name, 
                        a.surname, 
                        a.username, 
                        a.password, 
                        a.auth_email, 
                        a.language_code, 
                        COALESCE(NULLIF(l.language_eng, ''), l.language) AS language_name,
                        sd2.description AS state_deleted, 
                        a.active, 
                        sd3.description AS state_active,  
                        a.deleted, 
                        a.user_id,
                        u.username ,
                        a.act_parent_id, 
                        a.auth_allow_id, 
                        sd.description AS auth_alow ,
                        a.cons_allow_id,
                        sd1.description AS cons_allow ,
                        a.preferred_language
                    FROM info_users a    
                    INNER JOIN sys_operation_types op ON op.id = a.operation_type_id and  op.language_code = a.language_code
                    INNER JOIN sys_specific_definitions sd ON sd.main_group = 13 AND sd.language_code = a.language_code AND a.auth_allow_id = sd.first_group 
                    INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 14 AND  sd1.language_code = a.language_code AND a.cons_allow_id = sd1.first_group 
                    INNER JOIN sys_specific_definitions sd2 ON sd2.main_group = 15 AND sd2.first_group= a.deleted AND sd2.language_code = a.language_code AND sd2.deleted =0 AND sd2.active =0 
                    INNER JOIN sys_specific_definitions sd3 ON sd3.main_group = 16 AND sd3.first_group= a.active AND sd3.language_code = a.language_code AND sd3.deleted = 0 AND sd3.active = 0                    
                    INNER JOIN sys_language l ON l.language_main_code = a.language_code AND l.deleted =0 AND l.active =0 
                    INNER JOIN info_users u ON u.id = a.user_id 
                    WHERE a.language_code = :language_code AND a.deleted = 0 
                      " . $whereNameSQL . "
                    ORDER BY  " . $sort . " "
                    . "" . $order . " "
                    . "LIMIT " . $pdo->quote($limit) . " "
                    . "OFFSET " . $pdo->quote($offset) . " ";
            $statement = $pdo->prepare($sql); 
            $statement->bindValue(':language_code', $args['language_code'], \PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);          
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {
            //$debugSQLParams = $statement->debugDumpParams();
            return array("found" => false, "errorInfo" => $e->getMessage()/* , 'debug' => $debugSQLParams */);
        }
    }
    /**
     * user interface datagrid fill operation get row count for widget
     * @param array | null $params
     * @return array
     * @throws \PDOException
     */
    public function fillGridRowTotalCount($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $whereNameSQL = '';
            $whereNameSQL1 = '';
            $whereNameSQL2 = '';
            if (isset($params['search_name']) && $params['search_name'] != "") {
                $whereNameSQL = " AND a.name LIKE '%" . $params['search_name'] . "%' ";
                $whereNameSQL1 = " AND a1.name LIKE '%" . $params['search_name'] . "%' ";
                $whereNameSQL2 = " AND a2.name LIKE '%" . $params['search_name'] . "%' ";                
            }
            $sql = "
                   SELECT 
                        count(a.id) as count ,
                        (SELECT count(a1.id) AS toplam FROM info_users a1  		   
                        INNER JOIN sys_operation_types op1 ON op1.id = a1.operation_type_id and op1.language_code = a1.language_code
                        INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 13 AND sd1.language_code = a1.language_code AND a1.auth_allow_id = sd1.first_group 
                        INNER JOIN sys_specific_definitions sd11 ON sd11.main_group = 14 AND  sd11.language_code = a1.language_code AND a1.cons_allow_id = sd11.first_group 
                        INNER JOIN sys_specific_definitions sd21 ON sd21.main_group = 15 AND sd21.first_group= a1.deleted AND sd21.language_code = a1.language_code AND sd21.deleted =0 AND sd21.active =0 
                        INNER JOIN sys_specific_definitions sd31 ON sd31.main_group = 16 AND sd31.first_group= a1.active AND sd31.language_code = a1.language_code AND sd31.deleted = 0 AND sd31.active = 0
                        INNER JOIN sys_specific_definitions sd41 ON sd41.main_group = 3 AND sd41.first_group= a1.active AND sd41.language_code = a1.language_code AND sd41.deleted = 0 AND sd41.active = 0
                        INNER JOIN sys_language l1 ON l1.language_main_code = a1.language_code AND l1.deleted =0 AND l1.active =0 
                        INNER JOIN info_users u1 ON u1.id = a1.user_id 
                        WHERE a1.language_code = '" . $params['language_code'] . "' AND a1.deleted = 0
                             " . $whereNameSQL1 . ") AS undeleted_count,                         
                        (SELECT count(a2.id) AS toplam FROM info_users a2
                        INNER JOIN sys_operation_types op2 ON op2.id = a2.operation_type_id and op2.language_code = a2.language_code
                        INNER JOIN sys_specific_definitions sd2 ON sd2.main_group = 13 AND sd2.language_code = a2.language_code AND a2.auth_allow_id = sd2.first_group 
                        INNER JOIN sys_specific_definitions sd12 ON sd12.main_group = 14 AND sd12.language_code = a2.language_code AND a2.cons_allow_id = sd12.first_group 
                        INNER JOIN sys_specific_definitions sd22 ON sd22.main_group = 15 AND sd22.first_group = a2.deleted AND sd22.language_code = a2.language_code AND sd22.deleted =0 AND sd22.active =0 
                        INNER JOIN sys_specific_definitions sd32 ON sd32.main_group = 16 AND sd32.first_group = a2.active AND sd32.language_code = a2.language_code AND sd32.deleted = 0 AND sd32.active = 0
                        INNER JOIN sys_specific_definitions sd42 ON sd42.main_group = 3 AND sd42.first_group = a2.active AND sd42.language_code = a2.language_code AND sd42.deleted = 0 AND sd42.active = 0
                        INNER JOIN sys_language l2 ON l2.language_main_code = a2.language_code AND l2.deleted =0 AND l2.active =0 
                        INNER JOIN info_users u2 ON u2.id = a2.user_id 
                        WHERE a2.language_code = '" . $params['language_code'] . "' AND a2.deleted = 1
                             " . $whereNameSQL2 . " ) AS deleted_count                  
                    FROM info_users a  		   
		    INNER JOIN sys_operation_types op ON op.id = a.operation_type_id and  op.language_code = a.language_code
		    INNER JOIN sys_specific_definitions sd ON sd.main_group = 13 AND sd.language_code = a.language_code AND a.auth_allow_id = sd.first_group 
		    INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 14 AND  sd1.language_code = a.language_code AND a.cons_allow_id = sd1.first_group 
		    INNER JOIN sys_specific_definitions sd2 ON sd2.main_group = 15 AND sd2.first_group= a.deleted AND sd2.language_code = a.language_code AND sd2.deleted =0 AND sd2.active =0 
		    INNER JOIN sys_specific_definitions sd3 ON sd3.main_group = 16 AND sd3.first_group= a.active AND sd3.language_code = a.language_code AND sd3.deleted = 0 AND sd3.active = 0
		    INNER JOIN sys_specific_definitions sd4 ON sd4.main_group = 3 AND sd4.first_group= a.active AND sd4.language_code = a.language_code AND sd4.deleted = 0 AND sd4.active = 0
		    INNER JOIN sys_language l ON l.language_main_code = a.language_code AND l.deleted =0 AND l.active =0 
		    INNER JOIN info_users u ON u.id = a.user_id 
		    WHERE a.language_code = '" . $params['language_code'] . "' 
                    " . $whereNameSQL . " 
                         ";
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':language_code', $params['language_code'], \PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {
            //$debugSQLParams = $statement->debugDumpParams();
            return array("found" => false, "errorInfo" => $e->getMessage()/* , 'debug' => $debugSQLParams */);
        }
    }

    /**
     * action delete from database  example for PDO prepared
     * statements, table names are irrevelant and should be changed on specific 
     * returned result set example;
     * for success result
      (
      [found] => 1
      [errorInfo] => Array
      (
      [0] => 00000
      [1] =>
      [2] =>
      )

      [affectedRowsCount] => 1
      )
     * for error result
     * Array
      (
      [found] => 0
      [errorInfo] => 42P01
      )
     * usage
     * @param type $id
     * @param type $params
     * @return array
     * @throws PDOException
     */
    public function deletedAct(  $params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $act_parent_id = intval($params['act_parent_id']);
            if ($act_parent_id = 0) {
                $act_parent_id = intval($params['id']);
            }
            $statement = $pdo->prepare("                                      
                    UPDATE info_users
                    SET                                                                
                        c_date =  timezone('Europe/Istanbul'::text, ('now'::text)::timestamp(0) with time zone) ,                                              
                        active = 1,
                        deleted = 0
                        act_parent_id = :act_parent_id 
                    WHERE id = :id                    
                    ");     
            $statement->bindValue(':id',$params['id'], \PDO::PARAM_INT);
            $statement->bindValue(':act_parent_id', $act_parent_id, \PDO::PARAM_INT);  
            $statement->bindValue(':f_check', $params['f_check'], \PDO::PARAM_INT);            
            $update = $statement->execute();
            $affectedRows = $statement->rowCount();
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);            
            $statement_act_insert = $pdo->prepare(" 
                INSERT INTO info_users(
                           profile_public, 
                           f_check, 
                           s_date, 
                           c_date, 
                           operation_type_id, 
                           name, 
                           surname, 
                           username, 
                           password, 
                           auth_email, 
                           auth_allow_id,                        
                           language_code,                           
                           user_id, 
                           act_parent_id,
                           cons_allow_id,
                           active,
                           deleted,
                           preferred_language)
                  VALUES (:profile_public, 
                          :f_check, 
                          :s_date, 
                          timezone('Europe/Istanbul'::text, ('now'::text)::timestamp(0) with time zone), 
                          :operation_type_id, 
                          :name, 
                          :surname, 
                          :username, 
                          :password, 
                          :auth_email, 
                          :auth_allow_id,                        
                          :language_code,                        
                          :user_id, 
                          :act_parent_id,
                          :cons_allow_id,
                          1,
                          1,
                          :preferred_language
                    )");
            $statement_act_insert->bindValue(':profile_public', $params['profile_public'], \PDO::PARAM_INT);
            $statement_act_insert->bindValue(':f_check', $params['f_check'], \PDO::PARAM_INT);
            $statement_act_insert->bindValue(':s_date', $params['s_date'], \PDO::PARAM_STR);
            $statement_act_insert->bindValue(':operation_type_id', $params['operation_type_id'], \PDO::PARAM_INT);
            $statement_act_insert->bindValue(':name', $params['name'], \PDO::PARAM_STR);
            $statement_act_insert->bindValue(':surname', $params['surname'], \PDO::PARAM_STR);
            $statement_act_insert->bindValue(':username', $params['username'], \PDO::PARAM_STR);
            $statement_act_insert->bindValue(':password', $params['password'], \PDO::PARAM_STR);
            $statement_act_insert->bindValue(':auth_email', $params['auth_email'], \PDO::PARAM_STR);
            $statement_act_insert->bindValue(':auth_allow_id', $params['auth_allow_id'], \PDO::PARAM_STR);            
            $statement_act_insert->bindValue(':language_code', $params['language_code'], \PDO::PARAM_STR);
            $statement_act_insert->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);
            $statement_act_insert->bindValue(':act_parent_id', $act_parent_id, \PDO::PARAM_INT);
            $statement_act_insert->bindValue(':cons_allow_id', $params['cons_allow_id'], \PDO::PARAM_INT);
            $statement_act_insert->bindValue(':preferred_language', $params['preferred_language'], \PDO::PARAM_STR);
            $insert_act_insert = $statement_act_insert->execute();
            $affectedRows = $statement_act_insert->rowCount();
            $errorInfo = $statement_act_insert->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            $pdo->commit();
            return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows);
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

}
