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
     * @param type $id
     * @return array
     * @throws \PDOException
     */
    public function delete($id = null) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            /**
             * table names and  column names will be changed for specific use
             */
            //Prepare our UPDATE SQL statement.
            $statement = $pdo->prepare("
                    UPDATE info_users 
                    SET deleted = 1  
                    WHERE id = :id
                    ");
            //Bind our value to the parameter :id.
            $statement->bindValue(':id', $id, \PDO::PARAM_INT);
           

            //Execute our DELETE statement.
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
     * @return type
     * @throws \PDOException
     */
    public function getAll() {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            /**
             * table names and column names will be changed for specific use
             */
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
                        a.gender_id, 
                        sd4.description AS gender,  
                        a.language_id, 
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
                        sd1.description AS cons_allow 
                    FROM info_users a    
                    INNER JOIN sys_operation_types op ON op.id = a.operation_type_id and  op.language_id = a.language_id
                    INNER JOIN sys_specific_definitions sd ON sd.main_group = 13 AND sd.language_id = a.language_id AND a.auth_allow_id = sd.first_group 
                    INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 14 AND  sd1.language_id = a.language_id AND a.cons_allow_id = sd1.first_group 
                    INNER JOIN sys_specific_definitions sd2 ON sd2.main_group = 15 AND sd2.first_group= a.deleted AND sd2.language_id = a.language_id AND sd2.deleted =0 AND sd2.active =0 
                    INNER JOIN sys_specific_definitions sd3 ON sd3.main_group = 16 AND sd3.first_group= a.active AND sd3.language_id = a.language_id AND sd3.deleted = 0 AND sd3.active = 0
                    INNER JOIN sys_specific_definitions sd4 ON sd4.main_group = 3 AND sd4.first_group= a.active AND sd4.language_id = a.language_id AND sd4.deleted = 0 AND sd4.active = 0
                    INNER JOIN sys_language l ON l.id = a.language_id AND l.deleted =0 AND l.active =0 
                    INNER JOIN info_users u ON u.id = a.user_id  
                    ORDER BY a.name, a.surname
 
                
                ");
            
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            /* while ($row = $statement->fetch()) {
              print_r($row);
              } */
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
     * @param type $params
     * @return array
     * @throws PDOException
     */
    public function insert($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            /**
             * table names and column names will be changed for specific use
             */
            $statement = $pdo->prepare(" 
                INSERT INTO info_users(
                            profile_public, 
                            name, 
                            surname, 
                            username, 
                            password, 
                            auth_email,                            
                            gender_id, 
                            language_id,
                            user_id ,
                            cons_allow_id,
                            operation_type_id)
                  VALUES (:profile_public,
                          :name, 
                          :surname,
                          :username,                      
                          :password, 
                          :auth_email,                          
                          :gender_id,
                          :language_id,
                          :user_id,
                          :cons_allow_id,
                          :operation_type_id
                          
                    )");
            $statement->bindValue(':profile_public', $params['profile_public'], \PDO::PARAM_INT);
            $statement->bindValue(':name', $params['name'], \PDO::PARAM_STR);
            $statement->bindValue(':surname', $params['surname'], \PDO::PARAM_STR);
            $statement->bindValue(':username', $params['username'], \PDO::PARAM_STR);
            $statement->bindValue(':password', $params['password'], \PDO::PARAM_STR);
            $statement->bindValue(':auth_email', $params['auth_email'], \PDO::PARAM_STR);
            $statement->bindValue(':gender_id', $params['gender_id'], \PDO::PARAM_INT);
            $statement->bindValue(':language_id', $params['language_id'], \PDO::PARAM_INT);
            $statement->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);
            $statement->bindValue(':cons_allow_id', $params['cons_allow_id'], \PDO::PARAM_INT);
            $statement->bindValue(':operation_type_id', $params['operation_type_id'], \PDO::PARAM_INT);

            $result = $statement->execute();
            $insertID = $pdo->lastInsertId('info_users_id_seq');
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            $pdo->commit();
            return array("found" => true, "errorInfo" => $errorInfo, "lastInsertId" => $insertID);
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
     * @param type $id
     * @param type $params
     * @return array
     * @throws PDOException
     */
    public function update($id = null, $params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
             
            $act_parent_id = intval($params['act_parent_id']);            
            if ($act_parent_id =0 ){
                $act_parent_id =  intval($id);                
            }                
            print_r('*******  act_parent_id = '. $act_parent_id);
                    

             /**
             * table names and  column names will be changed for specific use
             */          
            //Prepare our UPDATE SQL statement.
            $statement = $pdo->prepare("
                                      
                    UPDATE info_users
                    SET                         
                        f_check = :f_check,                         
                        c_date =  timezone('Europe/Istanbul'::text, ('now'::text)::timestamp(0) with time zone) , 
                        operation_type_id= :operation_type_id,                         
                        active = 1,
                        user_id = :user_id,
                        deleted = :deleted  
                        act_parent_id = :act_parent_idi,
                        language_id = :language_id
                    WHERE id = :id
                    
                    ");
            //Bind our value to the parameter :id.
            $statement->bindValue(':id', $id, \PDO::PARAM_INT);
            $statement->bindValue(':act_parent_id', $act_parent_id, \PDO::PARAM_INT);
            
            //Bind our :model parameter.
            $statement->bindValue(':language_id', $params['language_id'], \PDO::PARAM_INT);  
            $statement->bindValue(':f_check', $params['f_check'], \PDO::PARAM_INT);
            $statement->bindValue(':operation_type_id', $params['operation_type_id'], \PDO::PARAM_INT);
            $statement->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);
            $statement->bindValue(':deleted', $params['deleted'], \PDO::PARAM_INT);
            
            //Execute our UPDATE statement.
            $update = $statement->execute();
            $affectedRows = $statement->rowCount();
            $errorInfo = $statement->errorInfo();            
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            
            
      //    -----------------------------------------------------------------------------------  
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
                           gender_id, 
                           language_id,                           
                           user_id, 
                           act_parent_id,
                           cons_allow_id)
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
                          :gender_id, 
                          :language_id,                        
                          :user_id, 
                          :act_parent_id,
                          :cons_allow_id
                          
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
            $statement_act_insert->bindValue(':gender_id', $params['gender_id'], \PDO::PARAM_INT);
            $statement_act_insert->bindValue(':language_id', $params['language_id'], \PDO::PARAM_INT);
            $statement_act_insert->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);
            $statement_act_insert->bindValue(':act_parent_id', $act_parent_id, \PDO::PARAM_INT);
            $statement_act_insert->bindValue(':cons_allow_id', $params['cons_allow_id'], \PDO::PARAM_INT);
            
            $insert_act_insert = $statement_act_insert->execute();
            $affectedRows = $statement_act_insert->rowCount();
            $errorInfo = $statement_act_insert->errorInfo();            
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
         //------------------------------------------------------------------------------   
            
             
            
            
            $pdo->commit();
            
            return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows);
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
            //$sort = "id";
            $sort = "r_date";
        }

        if (isset($args['order']) && $args['order'] != "") {
            $order = trim($args['order']);
            $orderArr = explode(",", $order);
            //print_r($orderArr);
            if (count($orderArr) === 1)
                $order = trim($args['order']);
        } else {
            //$order = "desc";
            $order = "ASC";
        }

        /* if(count($sortArr)===2 AND count($orderArr)===2) {
          $sort = $sortArr[0]. " ".$orderArr[0].", ";
          $order = $sortArr[1]. " ".$orderArr[1];
          } */
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
                        a.gender_id, 
                        sd4.description AS gender,  
                        a.language_id, 
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
                        sd1.description AS cons_allow 
                    FROM info_users a    
                    INNER JOIN sys_operation_types op ON op.id = a.operation_type_id and  op.language_id = a.language_id
                    INNER JOIN sys_specific_definitions sd ON sd.main_group = 13 AND sd.language_id = a.language_id AND a.auth_allow_id = sd.first_group 
                    INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 14 AND  sd1.language_id = a.language_id AND a.cons_allow_id = sd1.first_group 
                    INNER JOIN sys_specific_definitions sd2 ON sd2.main_group = 15 AND sd2.first_group= a.deleted AND sd2.language_id = a.language_id AND sd2.deleted =0 AND sd2.active =0 
                    INNER JOIN sys_specific_definitions sd3 ON sd3.main_group = 16 AND sd3.first_group= a.active AND sd3.language_id = a.language_id AND sd3.deleted = 0 AND sd3.active = 0
                    INNER JOIN sys_specific_definitions sd4 ON sd4.main_group = 3 AND sd4.first_group= a.active AND sd4.language_id = a.language_id AND sd4.deleted = 0 AND sd4.active = 0
                    INNER JOIN sys_language l ON l.id = a.language_id AND l.deleted =0 AND l.active =0 
                    INNER JOIN info_users u ON u.id = a.user_id   

                    WHERE   
                            a.language_id = :language_id and 
                            deleted = 0 and 
                            active =0                    
                    ORDER BY  " . $sort . " "
                    . "" . $order . " "
                    . "LIMIT " . $pdo->quote($limit) . " "
                    . "OFFSET " . $pdo->quote($offset) . " ";
            $statement = $pdo->prepare($sql);

            /**
             * For debug purposes PDO statement sql
             * uses 'Panique' library located in vendor directory
             */
            /* $parameters = array(
              'sort' => $sort,
              'order' => $order,
              'limit' => $pdo->quote($limit),
              'offset' => $pdo->quote($offset),
              );
              echo debugPDO($sql, $parameters); */
            $statement->bindValue(':language_id', $args['language_id'], \PDO::PARAM_INT);  
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            /* while ($row = $statement->fetch()) {
              print_r($row);
              } */
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
            $sql = "
                   SELECT 
                        count(a.id) as count ,
                        (SELECT count(a1.id) AS toplam FROM info_users a1  		   
                        INNER JOIN sys_operation_types op1 ON op1.id = a1.operation_type_id and op1.language_id = a1.language_id
                        INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 13 AND sd1.language_id = a1.language_id AND a1.auth_allow_id = sd1.first_group 
                        INNER JOIN sys_specific_definitions sd11 ON sd11.main_group = 14 AND  sd11.language_id = a1.language_id AND a1.cons_allow_id = sd11.first_group 
                        INNER JOIN sys_specific_definitions sd21 ON sd21.main_group = 15 AND sd21.first_group= a1.deleted AND sd21.language_id = a1.language_id AND sd21.deleted =0 AND sd21.active =0 
                        INNER JOIN sys_specific_definitions sd31 ON sd31.main_group = 16 AND sd31.first_group= a1.active AND sd31.language_id = a1.language_id AND sd31.deleted = 0 AND sd31.active = 0
                        INNER JOIN sys_specific_definitions sd41 ON sd41.main_group = 3 AND sd41.first_group= a1.active AND sd41.language_id = a1.language_id AND sd41.deleted = 0 AND sd41.active = 0
                        INNER JOIN sys_language l1 ON l1.id = a1.language_id AND l1.deleted =0 AND l1.active =0 
                        INNER JOIN info_users u1 ON u1.id = a1.user_id 
                        WHERE a1.language_id = :language_id  AND a1.deleted = 0) AS undeleted_count, 
                        
                        (SELECT count(a2.id) AS toplam FROM info_users a2
                        INNER JOIN sys_operation_types op2 ON op2.id = a2.operation_type_id and op2.language_id = a2.language_id
                        INNER JOIN sys_specific_definitions sd2 ON sd2.main_group = 13 AND sd2.language_id = a2.language_id AND a2.auth_allow_id = sd2.first_group 
                        INNER JOIN sys_specific_definitions sd12 ON sd12.main_group = 14 AND sd12.language_id = a2.language_id AND a2.cons_allow_id = sd12.first_group 
                        INNER JOIN sys_specific_definitions sd22 ON sd22.main_group = 15 AND sd22.first_group = a2.deleted AND sd22.language_id = a2.language_id AND sd22.deleted =0 AND sd22.active =0 
                        INNER JOIN sys_specific_definitions sd32 ON sd32.main_group = 16 AND sd32.first_group = a2.active AND sd32.language_id = a2.language_id AND sd32.deleted = 0 AND sd32.active = 0
                        INNER JOIN sys_specific_definitions sd42 ON sd42.main_group = 3 AND sd42.first_group = a2.active AND sd42.language_id = a2.language_id AND sd42.deleted = 0 AND sd42.active = 0
                        INNER JOIN sys_language l2 ON l2.id = a2.language_id AND l2.deleted =0 AND l2.active =0 
                        INNER JOIN info_users u2 ON u2.id = a2.user_id 
                        WHERE a2.language_id = :language_id  AND a2.deleted = 1) AS deleted_count 
                 
                    FROM info_users   a  		   
		    INNER JOIN sys_operation_types op ON op.id = a.operation_type_id and  op.language_id = a.language_id
		    INNER JOIN sys_specific_definitions sd ON sd.main_group = 13 AND sd.language_id = a.language_id AND a.auth_allow_id = sd.first_group 
		    INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 14 AND  sd1.language_id = a.language_id AND a.cons_allow_id = sd1.first_group 
		    INNER JOIN sys_specific_definitions sd2 ON sd2.main_group = 15 AND sd2.first_group= a.deleted AND sd2.language_id = a.language_id AND sd2.deleted =0 AND sd2.active =0 
		    INNER JOIN sys_specific_definitions sd3 ON sd3.main_group = 16 AND sd3.first_group= a.active AND sd3.language_id = a.language_id AND sd3.deleted = 0 AND sd3.active = 0
		    INNER JOIN sys_specific_definitions sd4 ON sd4.main_group = 3 AND sd4.first_group= a.active AND sd4.language_id = a.language_id AND sd4.deleted = 0 AND sd4.active = 0
		    INNER JOIN sys_language l ON l.id = a.language_id AND l.deleted =0 AND l.active =0 
		    INNER JOIN info_users u ON u.id = a.user_id 
		    WHERE a.language_id = :language_id 

 
                         ";
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':language_id', $params['language_id'], \PDO::PARAM_INT);  
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
    
      public function deletedAct($id = null, $params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
             
            $act_parent_id = intval($params['act_parent_id']);            
            if ($act_parent_id =0 ){
                $act_parent_id =  intval($id);                
            }                
            print_r('******* delete act_parent_id = '. $act_parent_id);
                    

             /**
             * table names and  column names will be changed for specific use
             */          
            //Prepare our UPDATE SQL statement.
            $statement = $pdo->prepare("
                                      
                    UPDATE info_users
                    SET                                                                
                        c_date =  timezone('Europe/Istanbul'::text, ('now'::text)::timestamp(0) with time zone) , 
                        operation_type_id= :operation_type_id,                         
                        active = 1,
                        user_id = :user_id,
                        deleted = 0
                        act_parent_id = :act_parent_id 
                    WHERE id = :id                    
                    ");
            //Bind our value to the parameter :id.
            $statement->bindValue(':id', $id, \PDO::PARAM_INT);
            $statement->bindValue(':act_parent_id', $act_parent_id, \PDO::PARAM_INT);
            
            //Bind our :model parameter.
            $statement->bindValue(':f_check', $params['f_check'], \PDO::PARAM_INT);
            $statement->bindValue(':operation_type_id', $params['operation_type_id'], \PDO::PARAM_INT);
            $statement->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);
            
            
            
            //Execute our UPDATE statement.
            $update = $statement->execute();
            $affectedRows = $statement->rowCount();
            $errorInfo = $statement->errorInfo();            
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            
            
      //    -----------------------------------------------------------------------------------  
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
                           gender_id, 
                           language_id,                           
                           user_id, 
                           act_parent_id,
                           cons_allow_id,
                           active,
                           deleted)
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
                          :gender_id, 
                          :language_id,                        
                          :user_id, 
                          :act_parent_id,
                          :cons_allow_id,
                          1,
                          1
                          
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
            $statement_act_insert->bindValue(':gender_id', $params['gender_id'], \PDO::PARAM_INT);
            $statement_act_insert->bindValue(':language_id', $params['language_id'], \PDO::PARAM_INT);
            $statement_act_insert->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);
            $statement_act_insert->bindValue(':act_parent_id', $act_parent_id, \PDO::PARAM_INT);
            $statement_act_insert->bindValue(':cons_allow_id', $params['cons_allow_id'], \PDO::PARAM_INT);
            
            $insert_act_insert = $statement_act_insert->execute();
            $affectedRows = $statement_act_insert->rowCount();
           
            $errorInfo = $statement_act_insert->errorInfo();            
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
         //------------------------------------------------------------------------------   
            
              
            $pdo->commit();
            
            return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows);
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }


}
