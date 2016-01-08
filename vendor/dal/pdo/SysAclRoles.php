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
class SysAclRoles extends \DAL\DalSlim {

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
     * @ sys_acl_roles tablosundan parametre olarak  gelen id kaydını siler. !!
     * @version v 1.0  07.01.2016
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
                UPDATE sys_acl_roles
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
     * @ sys_acl_roles tablosundaki tüm kayıtları getirir.  !!
     * @version v 1.0  07.01.2016    
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
                    a.name AS name,
		    a.icon_class, 
		    a.c_date as create_date,
		    a.start_date,
		    a.end_date,
		    a.parent,                   
                    a.deleted, 
		    sd.description as state_deleted,                 
                    a.active, 
		    sd1.description as state_active,  
                    a.description,                                     
                    a.user_id,
                    u.username,
                    a.root,
                    sar.name as root_parent                                            
            FROM sys_acl_roles  a
            INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group= a.deleted AND sd.language_code = 'tr' AND sd.deleted = 0 AND sd.active = 0
            INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 16 AND sd1.first_group= a.active AND sd1.language_code = 'tr' AND sd1.deleted = 0 AND sd1.active = 0                             
            INNER JOIN info_users u ON u.id = a.user_id 
            LEFT JOIN sys_acl_roles sar ON a.root > 0 AND sar.id = a.root AND sar.active =0 AND sar.deleted =0 
            ORDER BY a.name 
                
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
     * @ sys_acl_roles tablosuna yeni bir kayıt oluşturur.  !!
     * @version v 1.0  07.01.2016
     * @return array
     * @throws \PDOException
     */
    public function insert($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');

            $sql = " 
            SELECT  
                name as name , 
                '" . $params['name'] . "' as value , 
                name ='" . $params['name'] . "' as control,
                concat(name , ' daha önce kayıt edilmiş. Lütfen Kontrol Ediniz !!!' ) as message                             
            FROM sys_acl_roles        
            WHERE name = '" . $params['name'] . "'               
                               ";
            $statement = $pdo->prepare($sql);            
            $statement->execute();
            $kontrol = $statement->fetchAll(\PDO::FETCH_ASSOC);          

            if (!isset($kontrol[0]['control'])) {               
    
                $valuesSqlStartDate = '';
                if (isset($params['start_date']) && $params['start_date'] != "") {
                    $valuesSqlStartDate = ':start_date,';
                } else {
                    $valuesSqlStartDate = ' null,';
                }

                $valuesSqlEndDate = '';
                if (isset($params['end_date']) && $params['end_date'] != "") {
                    $valuesSqlEndDate = ':end_date,';
                } else {
                    $valuesSqlEndDate = ' null,';
                }

                $pdo->beginTransaction();
                /**
                 * table names and column names will be changed for specific use
                 */
                $sql = "
                INSERT INTO sys_acl_roles(
                        name, icon_class,  start_date,  end_date,
                        parent, user_id, description, root )
                VALUES (
                        :name,
                        :icon_class,                       
                        " . $valuesSqlStartDate . "
                        " . $valuesSqlEndDate . "
                        :parent,                       
                        :user_id,
                        :description,
                        :root
                                             )   ";

                $statement = $pdo->prepare($sql);
                $statement->bindValue(':name', $params['name'], \PDO::PARAM_STR);
                $statement->bindValue(':icon_class', $params['icon_class'], \PDO::PARAM_STR);
                if (isset($params['start_date']) && $params['start_date'] != "") {
                    $statement->bindValue(':start_date', $params['start_date'], \PDO::PARAM_STR);
                }
                if (isset($params['end_date']) && $params['end_date'] != "") {
                    $statement->bindValue(':end_date', $params['end_date'], \PDO::PARAM_STR);
                }
                $statement->bindValue(':parent', $params['parent'], \PDO::PARAM_INT);
                $statement->bindValue(':description', $params['description'], \PDO::PARAM_STR);
                $statement->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);
                $statement->bindValue(':root', $params['root'], \PDO::PARAM_INT);

               // echo debugPDO($sql, $params);
                $result = $statement->execute();
                $insertID = $pdo->lastInsertId('sys_acl_roles_id_seq');
                $errorInfo = $statement->errorInfo();
                if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                    throw new \PDOException($errorInfo[0]);
                $pdo->commit();

                return array("found" => true, "errorInfo" => $errorInfo, "lastInsertId" => $insertID);
                 
            } else {
           
                $result  = $kontrol;
             //   print_r($result);
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
     * @author Okan CIRAN
     * sys_acl_roles tablosuna parametre olarak gelen id deki kaydın bilgilerini günceller   !!
     * @version v 1.0  07.01.2016
     * @param type $id
     * @return array
     * @throws \PDOException
     */
    public function update($id = null, $params = array()) {
        try {

            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            print_r($id);
            $valuesSqlStartDate = '';
            if (isset($params['start_date']) && $params['start_date'] != "") {
                $valuesSqlStartDate = ' start_date = :start_date,';
            } else {
                $valuesSqlStartDate = ' start_date = null,';
            }

            $valuesSqlEndDate = '';
            if (isset($params['end_date']) && $params['end_date'] != "") {
                $valuesSqlEndDate = 'end_date = :end_date,';
            } else {
                $valuesSqlEndDate = 'end_date = null,';
            }

            $pdo->beginTransaction();
            /**
             * table names and  column names will be changed for specific use
             */
            //Prepare our UPDATE SQL statement.            
            $sql = "
                UPDATE sys_acl_roles
                SET   
                    name = :name, 
                    icon_class = :icon_class, 
                    active = :active, 
                    " . $valuesSqlStartDate . "
                    " . $valuesSqlEndDate . "
                    parent = :parent,
                    user_id= :user_id,  
                    description = :description, 
                    root = :root
                WHERE id = :id ";

            $statement = $pdo->prepare($sql);
            //Bind our value to the parameter :id.         
            $statement->bindValue(':id', $id, \PDO::PARAM_INT);
            //Bind our :model parameter.                  
            $statement->bindValue(':name', $params['name'], \PDO::PARAM_STR);
            $statement->bindValue(':icon_class', $params['icon_class'], \PDO::PARAM_STR);
            $statement->bindValue(':active', $params['active'], \PDO::PARAM_INT);
            if (isset($params['start_date']) && $params['start_date'] != "") {
                $statement->bindValue(':start_date', $params['start_date'], \PDO::PARAM_STR);
            }
            if (isset($params['end_date']) && $params['end_date'] != "") {
                $statement->bindValue(':end_date', $params['end_date'], \PDO::PARAM_STR);
            }
            $statement->bindValue(':parent', $params['parent'], \PDO::PARAM_INT);
            $statement->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);
            $statement->bindValue(':description', $params['description'], \PDO::PARAM_STR);
            $statement->bindValue(':root', $params['root'], \PDO::PARAM_INT);

            echo debugPDO($sql, $params);
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
     * @ Gridi doldurmak için sys_acl_roles tablosundan kayıtları döndürür !!
     * @version v 1.0  07.01.2016
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
                    a.name AS name,
		    a.icon_class, 
		    a.c_date as create_date,
		    a.start_date,
		    a.end_date,
		    a.parent,                   
                    a.deleted, 
		    sd.description as state_deleted,                 
                    a.active, 
		    sd1.description as state_active,  
                    a.description,                                     
                    a.user_id,
                    u.username,
                    a.root,
                    sar.name as root_parent                                            
                FROM sys_acl_roles  a
                INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group= a.deleted AND sd.language_code = 'tr' AND sd.deleted = 0 AND sd.active = 0
                INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 16 AND sd1.first_group= a.active AND sd1.language_code = 'tr' AND sd1.deleted = 0 AND sd1.active = 0                             
                INNER JOIN info_users u ON u.id = a.user_id 
                LEFT JOIN sys_acl_roles sar ON a.root > 0 AND sar.id = a.root AND sar.active =0 AND sar.deleted =0              
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
            //  echo debugPDO($sql, $parameters);
            $statement->bindValue(':language_code', $args['language_code'], \PDO::PARAM_INT);
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
     * @ Gridi doldurmak için sys_acl_roles tablosundan çekilen kayıtlarının kaç tane olduğunu döndürür   !!
     * @version v 1.0  07.01.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillGridRowTotalCount($params = array()) {
        try {

            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $sql = "
                SELECT 
                    COUNT(a.id) AS COUNT ,
                    (SELECT COUNT(a1.id) FROM sys_acl_roles a1  
                    INNER JOIN sys_specific_definitions sd1x ON sd1x.main_group = 15 AND sd1x.first_group= a1.deleted AND sd1x.language_code = 'tr' AND sd1x.deleted = 0 AND sd1x.active = 0
                    INNER JOIN sys_specific_definitions sd11 ON sd11.main_group = 16 AND sd11.first_group= a1.active AND sd11.language_code = 'tr' AND sd11.deleted = 0 AND sd11.active = 0                             
                    INNER JOIN info_users u1 ON u1.id = a1.user_id 
                    WHERE a1.deleted =0) AS undeleted_count, 
                    (SELECT COUNT(a2.id) FROM sys_acl_roles a2
                    INNER JOIN sys_specific_definitions sd2 ON sd2.main_group = 15 AND sd2.first_group= a2.deleted AND sd2.language_code = 'tr' AND sd2.deleted = 0 AND sd2.active = 0
                    INNER JOIN sys_specific_definitions sd12 ON sd12.main_group = 16 AND sd12.first_group= a2.active AND sd12.language_code = 'tr' AND sd12.deleted = 0 AND sd12.active = 0                             
                    INNER JOIN info_users u2 ON u2.id = a2.user_id 			
                    WHERE a2.deleted =1) AS deleted_count                        
                FROM sys_acl_roles a
                INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group= a.deleted AND sd.language_code = 'tr' AND sd.deleted = 0 AND sd.active = 0
                INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 16 AND sd1.first_group= a.active AND sd1.language_code = 'tr' AND sd1.deleted = 0 AND sd1.active = 0                             
                INNER JOIN info_users u ON u.id = a.user_id 

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
     * Combobox fill function used for testing
     * user interface combobox fill operation   
     * @author Okan CIRAN
     * @ combobox doldurmak için sys_acl_roles tablosundan parent ı 0 olan kayıtları (Ana grup) döndürür !!
     * @version v 1.0  07.01.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillComboBoxMainRoles() {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            /**
             * table names and column names will be changed for specific use
             */
            $statement = $pdo->prepare("
              SELECT                    
                  a.id, 	
                  a.name AS name                                 
              FROM sys_acl_roles a       
              WHERE a.active =0 AND a.deleted = 0 AND parent =0                 
              ORDER BY name                
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
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * Combobox fill function used for testing
     * user interface combobox fill operation   
     * @author Okan CIRAN
     * @ combobox doldurmak için sys_acl_roles tablosundan tüm kayıtları döndürür !!
     * @version v 1.0  07.01.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillComboBoxFullRoles($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            /**
             * table names and column names will be changed for specific use
             */
            $id = 0;
            if (isset($_GET['id']) && $_GET['id'] != "") {
                $id = $_GET['id'];
            }


            $statement = $pdo->prepare("
                SELECT                    
                    a.id, 	
                    a.name AS name,
                    a.parent
                FROM sys_acl_roles a       
                WHERE 
                    a.active =0 AND a.deleted = 0 AND
                    a.parent = " . $id . "
                ORDER BY name                
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
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

}