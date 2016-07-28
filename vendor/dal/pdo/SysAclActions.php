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
class SysAclActions extends \DAL\DalSlim {

    /**
     * @author Okan CIRAN
     * @ sys_acl_actions tablosundan parametre olarak  gelen id kaydını siler. !!
     * @version v 1.0 26.07.2016
     * @param type $params
     * @return array
     * @throws \PDOException
     */
    public function delete($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $opUserId = InfoUsers::getUserId(array('pk' => $params['pk']));
            if (\Utill\Dal\Helper::haveRecord($opUserId)) {
                $ModuleId = $this -> haveMenuTypeRecords(array('id' => $params['id']));
                if (!\Utill\Dal\Helper::haveRecord($ModuleId)) {
                $opUserIdValue = $opUserId ['resultSet'][0]['user_id'];
                $sql = " 
                UPDATE sys_acl_actions
                SET  deleted= 1, active = 1,
                     op_user_id = " . intval($opUserIdValue) . "
                WHERE id = " . intval($params['id'])
                ;
                $statement = $pdo->prepare($sql);
               // echo debugPDO($sql, $params);                
                $update = $statement->execute();
                $afterRows = $statement->rowCount();
                $errorInfo = $statement->errorInfo();
                if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                    throw new \PDOException($errorInfo[0]);
                $pdo->commit();
                return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $afterRows);
                 } else {
                $errorInfo = '23503';   // 23503  foreign_key_violation
                $errorInfoColumn = 'menu_type_id';
                $pdo->rollback();
                return array("found" =>false, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
            }
            } else {
                $errorInfo = '23502';  /// 23502  not_null_violation
                $pdo->rollback();
                return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '');
            }
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

        /**
     * @author Okan CIRAN
     * @ sys_acl_actions tablosundan parametre olarak  gelen id kaydını siler. !!
     * @version v 1.0  26.07.2016
     * @param type $params
     * @return array
     * @throws \PDOException
     */
    public function deleteAct($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $opUserId = InfoUsers::getUserId(array('pk' => $params['pk']));
            if (\Utill\Dal\Helper::haveRecord($opUserId)) {                
                $opUserIdValue = $opUserId ['resultSet'][0]['user_id'];
                $sql = " 
                UPDATE sys_acl_actions
                SET  deleted= 1, active = 1,
                     op_user_id = " . intval($opUserIdValue) . "
                WHERE id = " . intval($params['id'])
                ;
                $statement = $pdo->prepare($sql);
               // echo debugPDO($sql, $params);                
                $update = $statement->execute();
                $afterRows = $statement->rowCount();
                $errorInfo = $statement->errorInfo();
                if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                    throw new \PDOException($errorInfo[0]);
                $pdo->commit();
                return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $afterRows);                 
            } else {
                $errorInfo = '23502';  /// 23502  not_null_violation
                $pdo->rollback();
                return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '');
            }
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * @author Okan CIRAN
     * @ sys_acl_actions tablosundaki tüm kayıtları getirir.  !!
     * @version v 1.0  26.07.2016    
     * @param type $params
     * @return array
     * @throws \PDOException
     */
    public function getAll($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');            
            $statement = $pdo->prepare("
                SELECT 
                    a.id,
                    a.name AS name,   
                    sam.id AS module_id,
                    sam.name AS module_name,                     
                    a.c_date AS create_date,                        
                    a.deleted,
                    sd.description AS state_deleted,
                    a.active,
                    sd1.description AS state_active,
                    a.description,
                    a.op_user_id,
                    u.username
                FROM sys_acl_actions a                                
                INNER JOIN sys_language l ON l.id = 647 AND l.deleted =0 AND l.active =0                
                INNER JOIN sys_acl_modules sam ON sam.id = a.module_id AND sam.deleted = 0 AND sam.active = 0
                INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group= a.deleted AND sd.language_id = l.id AND sd.deleted = 0 AND sd.active = 0
                INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 16 AND sd1.first_group= a.active AND sd1.language_id = l.id AND sd1.deleted = 0 AND sd1.active = 0
                INNER JOIN info_users u ON u.id = a.op_user_id                
                ORDER BY sam.name, a.name
                                 ");
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
     * @author Okan CIRAN
     * @ sys_acl_actions tablosuna yeni bir kayıt oluşturur.  !!
     * @version v 1.0  26.07.2016
     * @param type $params
     * @return array
     * @throws \PDOException
     */
    public function insert($params = array()) {
        try {         
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $opUserId = InfoUsers::getUserId(array('pk' => $params['pk']));
            if (\Utill\Dal\Helper::haveRecord($opUserId)) {
                $opUserIdValue = $opUserId ['resultSet'][0]['user_id'];
                $kontrol = $this->haveRecords($params);
                if (!\Utill\Dal\Helper::haveRecord($kontrol)) {
                            
                    $sql = "
                INSERT INTO sys_acl_actions(
                        name, 
                        module_id,
                        op_user_id, 
                        description)
                VALUES (
                        '".$params['name']."', 
                        " . intval( $params['module_id']) . ",
                        " . intval($opUserIdValue) . ",
                        '".$params['description']."'
                                              )  ";
                    $statement = $pdo->prepare($sql);                    
                    //   echo debugPDO($sql, $params);
                    $result = $statement->execute();
                    $insertID = $pdo->lastInsertId('sys_acl_actions_id_seq');
                    $errorInfo = $statement->errorInfo();
                    if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                        throw new \PDOException($errorInfo[0]);
                    $pdo->commit();
                    return array("found" => true, "errorInfo" => $errorInfo, "lastInsertId" => $insertID);
                } else {
                    $errorInfo = '23505';
                    $errorInfoColumn = 'name';
                    $pdo->rollback();
                    return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
                }
            } else {
                $errorInfo = '23502';   // 23502  not_null_violation
                $errorInfoColumn = 'pk';
                $pdo->rollback();
                return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
            }
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * @author Okan CIRAN
     * sys_acl_actions tablosuna parametre olarak gelen id deki kaydın bilgilerini günceller   !!
     * @version v 1.0  26.07.2016
     * @param type $params
     * @return array
     * @throws \PDOException
     */
    public function update($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $opUserId = InfoUsers::getUserId(array('pk' => $params['pk']));
            if (\Utill\Dal\Helper::haveRecord($opUserId)) {
                $opUserIdValue = $opUserId ['resultSet'][0]['user_id'];
                $kontrol = $this->haveRecords($params);
                if (!\Utill\Dal\Helper::haveRecord($kontrol)) {

                    $sql = "
                UPDATE sys_acl_actions
                SET
                    name = '" . $params['name'] . "',  
                    module_id = " . intval( $params['module_id']) . ",
                    op_user_id= " . intval($opUserIdValue) . ",
                    description = '" . $params['description'] . "'
                WHERE id = " . intval($params['id']) . "
                    ";
                    $statement = $pdo->prepare($sql);
                    // echo debugPDO($sql, $params);
                    $update = $statement->execute();
                    $affectedRows = $statement->rowCount();
                    $errorInfo = $statement->errorInfo();
                    if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                        throw new \PDOException($errorInfo[0]);
                    $pdo->commit();
                    return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows);
                } else {
                    $errorInfo = '23505';
                    $errorInfoColumn = 'name';
                    $pdo->rollback();                
                return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
                }
            } else {
                $errorInfo = '23502';   // 23502  not_null_violation
                $errorInfoColumn = 'pk';
                $pdo->rollback();
                return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
            }
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * @author Okan CIRAN
     * @ sys_acl_roles tablosunda name sutununda daha önce oluşturulmuş mu? 
     * @version v 1.0  26.07.2016
     * @param type $params
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
                name as name , 
                '" . $params['name'] . "' as value , 
                name ='" . $params['name'] . "' as control,
                concat(name , ' daha önce kayıt edilmiş. Lütfen Kontrol Ediniz !!!' ) as message                             
            FROM sys_acl_actions                
            WHERE LOWER(REPLACE(name,' ','')) = LOWER(REPLACE('" . $params['name'] . "',' ','')) AND
                module_id = ".intval($params['module_id'])."                      
                " . $addSql . " 
               AND deleted =0   
                               ";
            $statement = $pdo->prepare($sql);
            //  echo debugPDO($sql, $params);
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
     * @author Okan CIRAN
     * @ Gridi doldurmak için sys_acl_actions tablosundan kayıtları döndürür !!
     * @version v 1.0  26.07.2016
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
            $sort = "sam.name, a.name";
        }

        if (isset($args['order']) && $args['order'] != "") {
            $order = trim($args['order']);
            $orderArr = explode(",", $order);
            if (count($orderArr) === 1)
                $order = trim($args['order']);
        } else {
            $order = "ASC";
        } 
                            
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $sql = "                   
                SELECT 
                    a.id,
                    a.name AS name,
                    sam.id AS module_id,
                    sam.name AS module_name,
                    a.c_date AS create_date,
                    a.deleted,
                    sd.description AS state_deleted,
                    a.active,
                    sd1.description AS state_active,
                    a.description,
                    a.op_user_id,
                    u.username
                FROM sys_acl_actions a
                INNER JOIN sys_language l ON l.id = 647 AND l.deleted =0 AND l.active =0
                INNER JOIN sys_acl_modules sam ON sam.id = a.module_id AND sam.deleted = 0 AND sam.active = 0
                INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group= a.deleted AND sd.language_id = l.id AND sd.deleted = 0 AND sd.active = 0
                INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 16 AND sd1.first_group= a.active AND sd1.language_id = l.id AND sd1.deleted = 0 AND sd1.active = 0
                INNER JOIN info_users u ON u.id = a.op_user_id
                WHERE a.deleted =0 
                ORDER BY " . $sort . " "
                    . "" . $order . " "
                    . "LIMIT " . $pdo->quote($limit) . " "
                    . "OFFSET " . $pdo->quote($offset) . " ";
            $statement = $pdo->prepare($sql);
            $parameters = array(
                'sort' => $sort,
                'order' => $order,
                'limit' => $pdo->quote($limit),
                'offset' => $pdo->quote($offset),
            );
            //  echo debugPDO($sql, $parameters);
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
     * @author Okan CIRAN
     * @ Gridi doldurmak için sys_acl_actions tablosundan çekilen kayıtlarının kaç tane olduğunu döndürür   !!
     * @version v 1.0  26.07.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillGridRowTotalCount($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $whereSQL = ' WHERE a. deleted =0 ';
            $sql = "
                SELECT 
                    COUNT(a.id) AS COUNT
                FROM sys_acl_actions a                                
                INNER JOIN sys_language l ON l.id = 647 AND l.deleted =0 AND l.active =0                
                INNER JOIN sys_acl_modules sam ON sam.id = a.module_id AND sam.deleted = 0 AND sam.active = 0
                INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group= a.deleted AND sd.language_id = l.id AND sd.deleted = 0 AND sd.active = 0
                INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 16 AND sd1.first_group= a.active AND sd1.language_id = l.id AND sd1.deleted = 0 AND sd1.active = 0
                INNER JOIN info_users u ON u.id = a.op_user_id  
                " . $whereSQL . "
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
     * @author Okan CIRAN
     * @ combobox doldurmak için sys_acl_actions tablosundan tüm kayıtları döndürür !!
     * @version v 1.0  26.07.2016
     * @param array $params
     * @return array
     * @throws \PDOException
     */
    public function fillComboBoxFullAction($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $statement = $pdo->prepare("
                SELECT 
                    a.id,
                    a.name AS name,
                    'open' AS state_type,
                    a.active
                FROM sys_acl_actions a
                WHERE
                    a.deleted = 0
                ORDER BY name
                                 ");
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
     * @author Okan CIRAN
     * @ tree doldurmak için sys_acl_actions tablosundan tüm kayıtları döndürür !!
      * @version v 1.0  26.07.2016
     * @param array $params
     * @return array
     * @throws \PDOException
     */
    public function fillActionTree($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $id = 0;
            if (isset($params['id']) && $params['id'] != "") {
                $id = $params['id'];
            }
            $sql = " 
                SELECT
                    a.id,
                    a.name AS name,
                    'open' AS state_type,
                    a.active
                FROM sys_acl_actions a
                WHERE                    
                    a.deleted = 0
                ORDER BY name
                                 ";
            $statement = $pdo->prepare($sql);
            // echo debugPDO($sql, $params);
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
     * @author Okan CIRAN
     * @ sys_acl_actions bilgilerini döndürür !!
     * filterRules aktif 
     * @version v 1.0  26.07.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillActionList($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            if (isset($params['page']) && $params['page'] != "" && isset($params['rows']) && $params['rows'] != "") {
                $offset = ((intval($params['page']) - 1) * intval($params['rows']));
                $limit = intval($params['rows']);
            } else {
                $limit = 10;
                $offset = 0;
            }

            $sortArr = array();
            $orderArr = array();
            if (isset($params['sort']) && $params['sort'] != "") {
                $sort = trim($params['sort']);
                $sortArr = explode(",", $sort);
                if (count($sortArr) === 1)
                    $sort = trim($params['sort']);
            } else {
                $sort = " sam.name, a.name";
            }

            if (isset($params['order']) && $params['order'] != "") {
                $order = trim($params['order']);
                $orderArr = explode(",", $order);
                //print_r($orderArr);
                if (count($orderArr) === 1)
                    $order = trim($params['order']);
            } else {
                $order = "ASC";
            }

            $sorguStr = null;
            if ((isset($params['filterRules']) && $params['filterRules'] != "")) {
                $filterRules = trim($params['filterRules']);
                $jsonFilter = json_decode($filterRules, true);

                $sorguExpression = null;
                foreach ($jsonFilter as $std) {
                    if ($std['value'] != null) {
                        switch (trim($std['field'])) {
                            case 'name':
                                $sorguExpression = ' ILIKE \'%' . $std['value'] . '%\' ';
                                $sorguStr.=" AND a.name" . $sorguExpression . ' ';

                                break;
                            case 'description':
                                $sorguExpression = ' ILIKE \'%' . $std['value'] . '%\'  ';
                                $sorguStr.=" AND a.description" . $sorguExpression . ' ';

                                break;
                            case 'state_active':
                                $sorguExpression = ' ILIKE \'%' . $std['value'] . '%\'  ';
                                $sorguStr.=" AND sd1.description" . $sorguExpression . ' ';

                                break;
                            case 'module_name':
                                $sorguExpression = ' ILIKE \'%' . $std['value'] . '%\'  ';
                                $sorguStr.=" AND sam.name" . $sorguExpression . ' ';

                                break;
                            default:
                                break;
                        }
                    }
                }
            } else {
                $sorguStr = null;
                $filterRules = "";
            }
            $sorguStr = rtrim($sorguStr, "AND ");
            
            $sorguStr2 = null;
            if (isset($params['name']) && $params['name'] != "") {
                $sorguStr2 .= " AND a.name Like '%" . $params['name'] . "%'";
            }
            if (isset($params['description']) && $params['description'] != "") {
                $sorguStr2 .= " AND a.description Like '%" . $params['description'] . "%'";
            }
            if (isset($params['active']) && $params['active'] != "") {
                $sorguStr2 .= " AND a.active = " . $params['active'] ;
            }
            if (isset($params['module_id']) && $params['module_id'] != "") {
                $sorguStr2 .= " AND sam.id = " . $params['module_id'] ;
            }
            
            
            $sql = "                 
		SELECT 
                    a.id,
                    a.name AS name,   
                    sam.id AS module_id,
                    sam.name AS module_name,                     
                    a.c_date AS create_date,                        
                    a.deleted,
                    sd.description AS state_deleted,
                    a.active,
                    sd1.description AS state_active,
                    a.description,
                    a.op_user_id,
                    u.username AS op_user_name
                FROM sys_acl_actions a                                
                INNER JOIN sys_language l ON l.id = 647 AND l.deleted =0 AND l.active =0                
                INNER JOIN sys_acl_modules sam ON sam.id = a.module_id AND sam.deleted = 0 AND sam.active = 0
                INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group= a.deleted AND sd.language_id = l.id AND sd.deleted = 0 AND sd.active = 0
                INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 16 AND sd1.first_group= a.active AND sd1.language_id = l.id AND sd1.deleted = 0 AND sd1.active = 0
                INNER JOIN info_users u ON u.id = a.op_user_id                
              
                WHERE a.deleted =0 
                " . $sorguStr . "
                " . $sorguStr2 . "
                ORDER BY    " . $sort . " "
                    . "" . $order . " "
                    . "LIMIT " . $pdo->quote($limit) . " "
                    . "OFFSET " . $pdo->quote($offset) . " ";
            $statement = $pdo->prepare($sql);
            $parameters = array(
                'sort' => $sort,
                'order' => $order,
                'limit' => $pdo->quote($limit),
                'offset' => $pdo->quote($offset),
            );
            $statement = $pdo->prepare($sql);
            //echo debugPDO($sql, $params);
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
     * @author Okan CIRAN
     * @ sys_acl_actions bilgilerinin sayısını döndürür !!
     * filterRules aktif 
     * @version v 1.0  26.07.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillActionListRtc($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $sorguStr = null;
            if ((isset($params['filterRules']) && $params['filterRules'] != "")) {
                $filterRules = trim($params['filterRules']);
                $jsonFilter = json_decode($filterRules, true);

                $sorguExpression = null;
                foreach ($jsonFilter as $std) {
                    if ($std['value'] != null) {
                        switch (trim($std['field'])) {
                             case 'name':
                                $sorguExpression = ' ILIKE \'%' . $std['value'] . '%\' ';
                                $sorguStr.=" AND a.name" . $sorguExpression . ' ';

                                break;
                            case 'description':
                                $sorguExpression = ' ILIKE \'%' . $std['value'] . '%\'  ';
                                $sorguStr.=" AND a.description" . $sorguExpression . ' ';

                                break;
                            case 'state_active':
                                $sorguExpression = ' ILIKE \'%' . $std['value'] . '%\'  ';
                                $sorguStr.=" AND sd1.description" . $sorguExpression . ' ';

                                break;
                            case 'module_name':
                                $sorguExpression = ' ILIKE \'%' . $std['value'] . '%\'  ';
                                $sorguStr.=" AND sam.name" . $sorguExpression . ' ';

                                break;
                            default:
                                break;
                        }
                    }
                }
            } else {
                $sorguStr = null;
                $filterRules = "";
            }
            $sorguStr = rtrim($sorguStr, "AND ");
             $sorguStr2 = null;
            if (isset($params['name']) && $params['name'] != "") {
                $sorguStr2 .= " AND a.name Like '%" . $params['name'] . "%'";
            }
            if (isset($params['description']) && $params['description'] != "") {
                $sorguStr2 .= " AND a.description Like '%" . $params['description'] . "%'";
            }
            if (isset($params['active']) && $params['active'] != "") {
                $sorguStr2 .= " AND a.active = " . $params['active'] ;
            }
            if (isset($params['module_id']) && $params['module_id'] != "") {
                $sorguStr2 .= " AND sam.id = " . $params['module_id'] ;
            }
            $sql = "   
                SELECT COUNT(id) AS count 
                FROM (
                    SELECT id,name,deleted,active,description,state_deleted,state_active,module_id,module_name
                    FROM (
                        SELECT 
                            a.id,
                            a.name AS name,   
                            sam.id AS module_id,
                            sam.name AS module_name,                     
                            a.c_date AS create_date,                        
                            a.deleted,
                            sd.description AS state_deleted,
                            a.active,
                            sd1.description AS state_active,
                            a.description,
                            a.op_user_id,
                            u.username AS op_user_name
                        FROM sys_acl_actions a                                
                        INNER JOIN sys_language l ON l.id = 647 AND l.deleted =0 AND l.active =0                
                        INNER JOIN sys_acl_modules sam ON sam.id = a.module_id AND sam.deleted = 0 AND sam.active = 0
                        INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group= a.deleted AND sd.language_id = l.id AND sd.deleted = 0 AND sd.active = 0
                        INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 16 AND sd1.first_group= a.active AND sd1.language_id = l.id AND sd1.deleted = 0 AND sd1.active = 0
                        INNER JOIN info_users u ON u.id = a.op_user_id       
                        WHERE a.deleted =0 
                        " . $sorguStr . "
                        " . $sorguStr2 . "
                    ) as xtable      
                ) AS xxTable    
                ";

            $statement = $pdo->prepare($sql);
            // echo debugPDO($sql, $params);
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
     * @author Okan CIRAN
     * @ sys_acl_actions tablosundan parametre olarak  gelen id kaydın aktifliğini
     *  0(aktif) ise 1 , 1 (pasif) ise 0  yapar. !!
     * @version v 1.0  26.07.2016
     * @param type $params
     * @return array
     * @throws \PDOException
     */
    public function makeActiveOrPassive($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $opUserId = InfoUsers::getUserId(array('pk' => $params['pk']));
            if (\Utill\Dal\Helper::haveRecord($opUserId)) {
                $opUserIdValue = $opUserId ['resultSet'][0]['user_id'];
                if (isset($params['id']) && $params['id'] != "") {

                    $sql = "                 
                UPDATE sys_acl_actions
                SET active = (  SELECT   
                                CASE active
                                    WHEN 0 THEN 1
                                    ELSE 0
                                END activex
                                FROM sys_acl_actions
                                WHERE id = " . intval($params['id']) . "
                ),
                op_user_id = " . intval($opUserIdValue) . "
                WHERE id = " . intval($params['id']);
                    $statement = $pdo->prepare($sql);
                    //  echo debugPDO($sql, $params);
                    $update = $statement->execute();
                    $afterRows = $statement->rowCount();
                    $errorInfo = $statement->errorInfo();
                    if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                        throw new \PDOException($errorInfo[0]);
                }
                $pdo->commit();
                return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $afterRows);
            } else {
                $errorInfo = '23502';   // 23502  not_null_violation
                $errorInfoColumn = 'pk';
                $pdo->rollback();
                return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
            }
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * @author Okan CIRAN
     * @ sys_acl_actions tablosundan kayıtları döndürür !!
     * @version v 1.0  26.07.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException 
     */
    public function fillActionDdList($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $statement = $pdo->prepare("        
               SELECT                    
                    a.id, 	
                    a.name,  
                    a.description,                                    
                    a.active,
                    'open' AS state_type  
	         FROM sys_acl_actions a    
                 WHERE                    
                    a.deleted = 0                    
               ORDER BY a.name 
                                 ");
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
     * @author Okan CIRAN
     * @ sys_acl_menu_types_actions tablosunda action_id li menu_type_id daha önce kaydedilmiş mi ?  
     * @version v 1.0  26.07.2016
     * @param type $params
     * @return array
     * @throws \PDOException
     */
    public function haveMenuTypeRecords($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');                             
            $sql = " 
               SELECT  
                a.action_id AS name ,             
                a.action_id = " . $params['id'] . " AS control,
                'Bu Action Altında Menu Tipi Kaydı Bulunmakta. Lütfen Kontrol Ediniz !!!' AS message   
            FROM sys_acl_menu_types_actions  a                          
            WHERE a.action_id = ".$params['id']. "
                AND a.deleted =0    
            LIMIT 1                     
                               ";
            $statement = $pdo->prepare($sql);
           //echo debugPDO($sql, $params);
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
 
                             
                            
    
    
}
