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
 * Class using Zend\ServiceManager\FactoryInterface
 * created to be used by DAL MAnager
 * @
 * @author Okan CİRANĞ
 */
class SysCity extends \DAL\DalSlim {

    /**
     * basic delete from database  example for PDO prepared
     * statements, table names are irrelevant and should be changed on specific 
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
     * @author Okan CIRAN
     * @ sys_city tablosundan parametre olarak  gelen id kaydını siler. !!
     * @version v 1.0  18.12.2015
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
                UPDATE sys_city
                SET  deleted= 1
                WHERE id = :id");
            //Bind our value to the parameter :id.
            $statement->bindValue(':id', $id, \PDO::PARAM_INT);
            //Execute our DELETE statement.
            $update = $statement->execute();
            $afterRows = $statement->rowCount();
            $errorInfo = $statement->errorInfo();

            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            $pdo->commit();
            return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $afterRows);
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
     * @author Okan CIRAN
     * @ sys_city tablosundaki tüm kayıtları getirir.  !!
     * @version v 1.0  18.12.2015    
     * @return array
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
                    a.country_id, 
                    a.name, 
                    a.name_eng, 
                    a.deleted, 
                    sd.description as state,                 
                    a.active, 
                    a.parent_eng_id, 
                    a.language_id, 
                    a.user_id, 
                    a.priority, 
                    a.city_id     
                FROM sys_city  a
                INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group= a.deleted AND sd.language_id = a.language_id
                           
                ORDER BY a.priority ASC , a.name

                
                                 ");   
            $statement->execute();
            $result = $statement->fetcAll(\PDO::FETCH_ASSOC);
            /* while ($row = $statement->fetch()) {
              print_r($row);
              } */
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
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
     * @author Okan CIRAN
     * @ sys_city tablosuna yeni bir kayıt oluşturur.  !!
     * @version v 1.0  18.12.2015
     * @return array
     * @throws \PDOException
     */
    public function insert($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            /**
             * table names and column names will be changed for specific use
             */
            $statement = $pdo->prepare("
                INSERT INTO sys_city(
                        country_id, 
                        name, 
                        name_eng, 
                        deleted, 
                        active, 
                        language_parent_id, 
                        language_id, 
                        user_id, 
                        priority, 
                        city_id  )
                VALUES (
                        :country_id, 
                        :name, 
                        :name_eng, 
                        :deleted, 
                        :active, 
                        :language_parent_id, 
                        :language_id, 
                        :user_id, 
                        :priority, 
                        :city_id
                                                ");
            $statement->bindValue(':name', $params['name'], \PDO::PARAM_STR);
            $statement->bindValue(':name_eng', $params['name_eng'], \PDO::PARAM_STR);
            $statement->bindValue(':language_id', $params['language_id'], \PDO::PARAM_INT);
            $statement->bindValue(':language_parent_id', $params['language_parent_id'], \PDO::PARAM_INT);
            $statement->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);
            $statement->bindValue(':priority', $params['priority'], \PDO::PARAM_INT);
            $statement->bindValue(':country_id', $params['country_id'], \PDO::PARAM_INT);
            $statement->bindValue(':city_id', $params['city_id'], \PDO::PARAM_INT);
            $statement->bindValue(':active', $params['active'], \PDO::PARAM_INT);

            $result = $statement->execute();

            $insertID = $pdo->lastInsertId('sys_city_id_seq');

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
     * @author Okan CIRAN
     * sys_city tablosuna parametre olarak gelen id deki kaydın bilgilerini günceller   !!
     * @version v 1.0  18.12.2015
     * @param type $id
     * @return array
     * @throws \PDOException
     */
    public function update($id = null, $params = array()) {
        try {

            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            /**
             * table names and  column names will be changed for specific use
             */
            //Prepare our UPDATE SQL statement.            
            $statement = $pdo->prepare("
                UPDATE sys_city
                SET    
                    country_id= :country_id, 
                    name=:name,  
                    name_eng=:name_eng,  
                    active= :active,                 
                    language_parent_id = :language_parent_id,
                    language_id=:language_id,  
                    user_id = :user_id,
                    priority= :priority, 
                    city_id= :city_id                    
                WHERE id = :id");
            //Bind our value to the parameter :id.
            $statement->bindValue(':id', $id, \PDO::PARAM_INT);
            //Bind our :model parameter.
            $statement->bindValue(':name', $params['name'], \PDO::PARAM_STR);
            $statement->bindValue(':name_eng', $params['name_eng'], \PDO::PARAM_STR);
            $statement->bindValue(':language_id', $params['language_id'], \PDO::PARAM_INT);
            $statement->bindValue(':language_parent_id', $params['language_parent_id'], \PDO::PARAM_INT);
            $statement->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);
            $statement->bindValue(':priority', $params['priority'], \PDO::PARAM_INT);
            $statement->bindValue(':country_id', $params['country_id'], \PDO::PARAM_INT);
            $statement->bindValue(':city_id', $params['city_id'], \PDO::PARAM_INT);
            $statement->bindValue(':active', $params['active'], \PDO::PARAM_INT);



            //Execute our UPDATE statement.
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
     * Datagrid fill function used for testing
     * user interface datagrid fill operation   
     * @author Okan CIRAN
     * @ Gridi doldurmak için sys_city tablosundan kayıtları döndürür !!
     * @version v 1.0  18.12.2015
     * @param array | null $args
     * @return array
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


        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $sql = "
                SELECT 
                    a.id, 
                    a.country_id, 
                    a.name, 
                    a.name_eng, 
                    a.deleted, 
                    sd.description as state,                 
                    a.active, 
                    a.parent_eng_id, 
                    a.language_id, 
                    a.user_id, 
                    a.priority, 
                    a.city_id     
                FROM sys_city  a
                INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group= a.deleted AND sd.language_id = a.language_id
                WHERE a.language_id = :language_id     
                AND country_id = :country_id 
                ORDER BY    " . $sort . " "
                    . "" . $order . " "
                    . "LIMIT " . $pdo->quote($limit) . " "
                    . "OFFSET " . $pdo->quote($offset) . " ";
            $statement = $pdo->prepare($sql);
            /**
             * For debug purposes PDO statement sql
             * uses 'Panique' library located in vendor directory
             */
            $parameters = array(
                'sort' => $sort,
                'order' => $order,
                'limit' => $pdo->quote($limit),
                'offset' => $pdo->quote($offset),
            );
            // echo debugPDO($sql, $parameters);

            
            $statement->bindValue(':country_id', $args['country_id'], \PDO::PARAM_INT);
            $statement->bindValue(':language_id', $args['language_id'], \PDO::PARAM_INT);  
            
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
     * @author Okan CIRAN
     * @ Gridi doldurmak için sys_city tablosundan çekilen kayıtlarının kaç tane olduğunu döndürür   !!
     * @version v 1.0  18.12.2015
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillGridRowTotalCount($params = array()) {
        try {

            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $sql = "
                    SELECT 
                       count(id) as toplam  , 
                       (SELECT count(x.id) AS toplam FROM sys_city x where x.deleted =0  AND x.country_id = :country_id AND language_id = :language_id) AS aktif_toplam   ,
                       (SELECT count(y.id) AS toplam FROM sys_city y where y.deleted =1  AND y.country_id = :country_id AND language_id = :language_id) AS silinmis_toplam    
                    FROM sys_city
                    where language_id = :language_id 
                    AND country_id = :country_id 
                    ";
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':country_id', $params['country_id'], \PDO::PARAM_INT);
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
     * user interface datagrid fill operation get row count for widget
     * @author Okan CIRAN
     * @ combobox ı doldurmak için sys_city tablosundan çekilen kayıtları döndürür   !!
     * @version v 1.0  17.12.2015
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillComboBox($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            /**
             * table names and column names will be changed for specific use
             */
            $sql = "
               SELECT 
                    a.city_id as id,                     
                    a.name                     
                FROM sys_city  a
                INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group = a.deleted AND sd.language_id = a.language_id
                WHERE a.language_id = :language_id AND a.active = 0 AND a.deleted= 0 
                AND country_id = :country_id 
                ORDER BY a.priority ASC, a.name
                
                                 ";
            $statement = $pdo->prepare($sql);
          // echo debugPDO($sql, $params);
            
            $statement->bindValue(':country_id', $params['country_id'], \PDO::PARAM_INT);
            $statement->bindValue(':language_id', $params['language_id'], \PDO::PARAM_INT);  
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
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

}
