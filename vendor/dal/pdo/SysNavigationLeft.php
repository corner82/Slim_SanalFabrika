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
 * @author Okan CIRAN
 */
class SysNavigationLeft extends \DAL\DalSlim {

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
     * @ sys_navigation_left tablosundan parametre olarak  gelen id kaydını siler. !!
     * @version v 1.0  14.12.2015
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
                UPDATE sys_navigation_left
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
     * @ sys_navigation_left tablosundaki tüm kayıtları getirir.  !!
     * @version v 1.0  14.12.2015    
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
              SELECT a.id, 
                    a.menu_name, 
                    a.language_id, 
                    a.menu_name_eng, 
                    a.url, 
                    a.parent, 
                    a.icon_class, 
                    a.page_state, 
                    a.collapse, 
                    a.active, 
                    a.deleted, 
                    case 
                            when a.deleted = 0 then 'Aktif' 
                            when a.deleted = 1 then 'Silinmiş' 
                    end as state,    
                    a.warning, 
                    a.warning_type, 
                    a.hint, z_index, 
                    a.language_parent_id, 
                    a.hint_eng, 
                    a.warning_class
              FROM sys_navigation_left a 
              where a.language_id = 91  

                 
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
     * @ sys_navigation_left tablosuna yeni bir kayıt oluşturur.  !!
     * @version v 1.0  14.12.2015
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
                INSERT INTO sys_navigation_left(
                    menu_name, 
                    language_id, 
                    menu_name_eng, 
                    url, 
                    parent, 
                    icon_class, 
                    page_state, 
                    collapse, 
                    warning, 
                    warning_type, 
                    hint, 
                    z_index, 
                    language_parent_id, 
                    hint_eng, 
                    warning_class)    
                VALUES (
                        :menu_name, 
                        :language_id, 
                        :menu_name_eng, 
                        :url, 
                        :parent, 
                        :icon_class, 
                        :page_state, 
                        :collapse, 
                        :warning, 
                        :warning_type, 
                        :hint, 
                        :z_index, 
                        :language_parent_id, 
                        :hint_eng, 
                        :warning_class )
                                                ");
            $statement->bindValue(':menu_name', $params['menu_name'], \PDO::PARAM_STR);
            $statement->bindValue(':language_id', $params['language_id'], \PDO::PARAM_INT);
            $statement->bindValue(':menu_name_eng', $params['menu_name_eng'], \PDO::PARAM_STR);
            $statement->bindValue(':url', $params['url'], \PDO::PARAM_STR);
            $statement->bindValue(':parent', $params['parent'], \PDO::PARAM_INT);
            $statement->bindValue(':icon_class', $params['icon_class'], \PDO::PARAM_STR);
            $statement->bindValue(':page_state', $params['page_state'], \PDO::PARAM_INT);
            $statement->bindValue(':collapse', $params['collapse'], \PDO::PARAM_INT);
            $statement->bindValue(':warning', $params['warning'], \PDO::PARAM_INT);
            $statement->bindValue(':warning_type', $params['warning_type'], \PDO::PARAM_INT);
            $statement->bindValue(':hint', $params['hint'], \PDO::PARAM_STR);
            $statement->bindValue(':z_index', $params['z_index'], \PDO::PARAM_INT);
            $statement->bindValue(':language_parent_id', $params['language_parent_id'], \PDO::PARAM_INT);
            $statement->bindValue(':hint_eng', $params['hint_eng'], \PDO::PARAM_STR);
            $statement->bindValue(':warning_class', $params['warning_class'], \PDO::PARAM_STR);

            $result = $statement->execute();

            $insertID = $pdo->lastInsertId('sys_navigation_left_id_seq');

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
     * sys_navigation_left tablosuna parametre olarak gelen id deki kaydın bilgilerini günceller   !!
     * @version v 1.0  14.12.2015
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
                UPDATE sys_navigation_left
                SET              
                    menu_name = :menu_name, 
                    language_id = :language_id, 
                    menu_name_eng = :menu_name_eng, 
                    parent = :parent, 
                    icon_class = :icon_class, 
                    page_state = :page_state, 
                    collapse = :collapse, 
                    active = :active, 
                    warning = :warning, 
                    warning_type = :warning_type, 
                    hint = :hint, 
                    z_index = :z_index, 
                    language_parent_id = :language_parent_id, 
                    hint_eng = :hint_eng, 
                    warning_class = :warning_class            
                WHERE id = :id");
            //Bind our value to the parameter :id.

            $statement->bindValue(':id', $id, \PDO::PARAM_INT);
            //Bind our :model parameter.
            $statement->bindValue(':menu_name', $params['menu_name'], \PDO::PARAM_STR);
            $statement->bindValue(':language_id', $params['language_id'], \PDO::PARAM_INT);
            $statement->bindValue(':menu_name_eng', $params['menu_name_eng'], \PDO::PARAM_STR);
            $statement->bindValue(':parent', $params['parent'], \PDO::PARAM_INT);
            $statement->bindValue(':icon_class', $params['icon_class'], \PDO::PARAM_STR);
            $statement->bindValue(':page_state', $params['page_state'], \PDO::PARAM_INT);
            $statement->bindValue(':collapse', $params['collapse'], \PDO::PARAM_INT);
            $statement->bindValue(':active', $params['active'], \PDO::PARAM_INT);
            $statement->bindValue(':warning', $params['warning'], \PDO::PARAM_INT);
            $statement->bindValue(':warning_type', $params['warning_type'], \PDO::PARAM_INT);
            $statement->bindValue(':hint', $params['hint'], \PDO::PARAM_STR);
            $statement->bindValue(':z_index', $params['z_index'], \PDO::PARAM_INT);
            $statement->bindValue(':language_parent_id', $params['language_parent_id'], \PDO::PARAM_INT);
            $statement->bindValue(':hint_eng', $params['hint_eng'], \PDO::PARAM_STR);
            $statement->bindValue(':warning_class', $params['warning_class'], \PDO::PARAM_STR);

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
     * @ Gridi doldurmak için sys_navigation_left tablosundan kayıtları döndürür !!
     * @version v 1.0  14.12.2015
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
               SELECT a.id, 
                    a.menu_name, 
                    a.language_id, 
                    a.menu_name_eng, 
                    a.url, 
                    a.parent, 
                    a.icon_class, 
                    a.page_state, 
                    a.collapse, 
                    a.active, 
                    a.deleted, 
                    case 
                            when a.deleted = 0 then 'Aktif' 
                            when a.deleted = 1 then 'Silinmiş' 
                    end as state,    
                    a.warning, 
                    a.warning_type, 
                    a.hint, z_index, 
                    a.language_parent_id, 
                    a.hint_eng, 
                    a.warning_class
              FROM sys_navigation_left a 
                where language_id = 91 
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
            //   echo debugPDO($sql, $parameters);

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
     * @ Gridi doldurmak için sys_navigation_left tablosundan çekilen kayıtlarının kaç tane olduğunu döndürür   !!
     * @version v 1.0  14.12.2015
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
                       (SELECT count(id) as toplam FROM sys_navigation_left where deleted =0 ) as aktif_toplam   ,
                       (SELECT count(id) as toplam FROM sys_navigation_left where deleted =1 ) as silinmis_toplam    
                    FROM sys_navigation_left
                    where language_id = 91 
                    ";
            $statement = $pdo->prepare($sql);

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
     * @ sys_navigation_left tablosundaki tüm kayıtları getirir.  !!
     * @version v 1.0  14.12.2015    
     * @return array
     * @throws \PDOException
     */
    public function getLeftMenu($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            /**
             * table names and column names will be changed for specific use
             */
            $sql = "SELECT a.id, 
                    COALESCE(NULLIF(a.menu_name, ''), a.menu_name_eng) as menu_name, 
                    a.language_id, 
                    a.menu_name_eng, 
                    a.url, 
                    a.parent, 
                    a.icon_class, 
                    a.page_state, 
                    a.collapse, 
                    a.active, 
                    a.deleted, 
                    case 
                            when a.deleted = 0 then 'Aktif' 
                            when a.deleted = 1 then 'Silinmiş' 
                    end as state,    
                    a.warning, 
                    a.warning_type, 
                    COALESCE(NULLIF(hint, ''), hint_eng) as hint, 
                    a.z_index, 
                    a.language_parent_id, 
                    a.hint_eng, 
                    a.warning_class
              FROM sys_navigation_left a 
              where a.language_id = 91 
              and a.parent = :parent
                                 ";           
            $statement = $pdo->prepare($sql);
            $statement->bindValue(':parent',  $params['parent'], \PDO::PARAM_INT);
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

}
