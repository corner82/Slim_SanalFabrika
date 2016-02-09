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
class InfoFirmProfile extends \DAL\DalSlim {

    /**
     * @author Okan CIRAN
     * @ info_firm_profile tablosundan parametre olarak  gelen id kaydını siler. !!
     * @version v 1.0  06.01.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function delete($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $statement = $pdo->prepare(" 
                UPDATE info_firm_profile
                SET  deleted= 1, active = 1 , 
                  op_user_id =  " . intval($params['user_id']) . " 
                WHERE id = :id");
            $statement->bindValue(':id', $params['id'], \PDO::PARAM_INT);
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
     * @author Okan CIRAN
     * @ info_firm_profile tablosundaki tüm kayıtları getirir.  !!
     * @version v 1.0  06.01.2016   
     * @param array | null $args
     * @return array
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
                        a.firm_name, 
                        a.web_address,                     
                        a.tax_office, 
                        a.tax_no, 
                        a.sgk_sicil_no,
			a.bagkur_sicil_no,
			a.ownership_status_id,
                        sd4.description AS owner_ship,
			a.foundation_year,			
			a.act_parent_id,  
                        a.language_code, 
                        COALESCE(NULLIF(l.language_eng, ''), l.language) AS language_name,                        
                        a.active, 
                        sd3.description AS state_active,  
                        a.deleted,
			sd2.description AS state_deleted, 
                        a.op_user_id,
                        u.username,                    
                        a.auth_allow_id, 
                        sd.description AS auth_alow ,
                        a.cons_allow_id,
                        sd1.description AS cons_allow,
                        a.language_parent_id,               
                        a.firm_name_eng, 
                        a.firm_name_sort,
                        a.country_id, 
                        co.name AS tr_country_name 
                    FROM info_firm_profile a    
                    INNER JOIN sys_operation_types op ON op.id = a.operation_type_id and  op.language_code = a.language_code  AND op.deleted =0 AND op.active =0
                    INNER JOIN sys_specific_definitions sd ON sd.main_group = 13 AND sd.language_code = a.language_code AND a.auth_allow_id = sd.first_group  AND sd.deleted =0 AND sd.active =0
                    INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 14 AND  sd1.language_code = a.language_code AND a.cons_allow_id = sd1.first_group  AND sd1.deleted =0 AND sd1.active =0
                    INNER JOIN sys_specific_definitions sd2 ON sd2.main_group = 15 AND sd2.first_group= a.deleted AND sd2.language_code = a.language_code AND sd2.deleted =0 AND sd2.active =0 
                    INNER JOIN sys_specific_definitions sd3 ON sd3.main_group = 16 AND sd3.first_group= a.active AND sd3.language_code = a.language_code AND sd3.deleted = 0 AND sd3.active = 0
                    LEFT JOIN sys_specific_definitions sd4 ON sd4.main_group = 1 AND sd4.first_group= a.active AND sd4.language_code = a.language_code AND sd4.deleted = 0 AND sd4.active = 0
                    INNER JOIN sys_language l ON l.language_main_code = a.language_code AND l.deleted =0 AND l.active =0 
                    INNER JOIN info_users u ON u.id = a.op_user_id                      
                    LEFT JOIN sys_countrys co on co.id = a.country_id AND co.deleted = 0 AND co.active = 0 AND co.language_code = a.language_code                               
                    ORDER BY a.firm_name   
                          ");
            $statement->bindValue(':language_code', $params['language_code'], \PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetcAll(\PDO::FETCH_ASSOC);
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
     * @author Okan CIRAN
     * @ info_firm_profile tablosunda name sutununda daha önce oluşturulmuş mu? 
     * @version v 1.0 15.01.2016
     * @param array | null $args
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
                firm_name AS name , 
                '" . $params['tax_no'] . "' AS value , 
                tax_no ='" . $params['tax_no'] . "' AS control,
                CONCAT(firm_name , ' daha önce kayıt edilmiş. Lütfen Kontrol Ediniz !!!' ) AS message                             
            FROM info_firm_profile                
            WHERE tax_no = '" . $params['tax_no'] . "'"
                    . $addSql . " 
               AND deleted =0   
                               ";
            $statement = $pdo->prepare($sql);
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
     * @ info_firm_profile tablosundan parametre olarak  gelen id kaydını aktifliğini 1 = pasif yapar. !!
     * @version v 1.0  09.02.2016
     * @param type $params
     * @return array
     * @throws \PDOException
     */
    public function makePassive($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            //$pdo->beginTransaction();
            $statement = $pdo->prepare(" 
                UPDATE info_firm_profile
                SET                         
                    c_date =  timezone('Europe/Istanbul'::text, ('now'::text)::timestamp(0) with time zone) ,                     
                    active = 1                    
                WHERE id = :id");
            $statement->bindValue(':id', $params['id'], \PDO::PARAM_INT);
            $update = $statement->execute();
            $afterRows = $statement->rowCount();
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            //$pdo->commit();
            return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $afterRows);
        } catch (\PDOException $e /* Exception $e */) {
            //$pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    
    
    
    /**
     * @author Okan CIRAN
     * @ info_firm_profile tablosuna yeni bir kayıt oluşturur.  !!
     * @version v 1.0  06.01.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function insert($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();

            $opUserId = InfoUsers::getUserId(array('pk' => $params['pk']));
            if (!\Utill\Dal\Helper::haveRecord($opUserId)) {
                $opUserIdValue = $opUserId ['resultSet'][0]['user_id'];
                $kontrol = $this->haveRecords($params);
                if (!\Utill\Dal\Helper::haveRecord($kontrol)) {
                    $pdo->beginTransaction();
                    $addSql = " op_user_id, ";
                    $addSqlValue = " " . $opUserIdValue . ",";

                    $languageIdValue = 647;
                    $languageId = SysLanguage::getLanguageId(array('language_code' => $params['language_code']));
                    if (!\Utill\Dal\Helper::haveRecord($languageId)) {
                        $languageIdValue = $languageId ['resultSet'][0]['id'];
                    }
                    $addSql = " language_id, ";
                    $addSqlValue = " " . $languageIdValue . ",";

                    $statement = $pdo->prepare("
                INSERT INTO info_firm_profile(
                        profile_public, 
                        country_id,                    
                        firm_name, 
                        web_address, 
                        tax_office, 
                        tax_no, 
                        sgk_sicil_no, 
                        ownership_status_id, 
                        foundation_year, 
                        language_code, 
                        bagkur_sicil_no,                        
                         " . $addSql . "   
                        firm_name_eng, 
                        firm_name_sort,
                        act_parent_id
                        )
                VALUES (
                        :profile_public, 
                        :country_id,                     
                        :firm_name, 
                        :web_address, 
                        :tax_office, 
                        :tax_no, 
                        :sgk_sicil_no, 
                        :ownership_status_id, 
                        :foundation_year, 
                        :language_code, 
                        :bagkur_sicil_no,                        
                         " . $addSqlValue . " 
                        :firm_name_eng, 
                        :firm_name_sort,
                        (SELECT last_value FROM info_firm_profile_id_seq)
                                                ");
                    $statement->bindValue(':profile_public', $params['profile_public'], \PDO::PARAM_INT);
                    $statement->bindValue(':country_id', $params['country_id'], \PDO::PARAM_INT);
                    $statement->bindValue(':firm_name', $params['firm_name'], \PDO::PARAM_STR);
                    $statement->bindValue(':web_address', $params['web_address'], \PDO::PARAM_STR);
                    $statement->bindValue(':tax_office', $params['tax_office'], \PDO::PARAM_STR);
                    $statement->bindValue(':tax_no', $params['tax_no'], \PDO::PARAM_STR);
                    $statement->bindValue(':sgk_sicil_no', $params['sgk_sicil_no'], \PDO::PARAM_STR);
                    $statement->bindValue(':ownership_status_id', $params['ownership_status_id'], \PDO::PARAM_INT);
                    $statement->bindValue(':foundation_year', $params['foundation_year'], \PDO::PARAM_INT);
                    $statement->bindValue(':language_code', $params['language_code'], \PDO::PARAM_STR);
                    $statement->bindValue(':bagkur_sicil_no', $params['bagkur_sicil_no'], \PDO::PARAM_STR);
                    $statement->bindValue(':firm_name_eng', $params['firm_name_eng'], \PDO::PARAM_STR);
                    $statement->bindValue(':firm_name_sort', $params['firm_name_sort'], \PDO::PARAM_STR);
                    $result = $statement->execute();
                    $insertID = $pdo->lastInsertId('info_firm_profile_id_seq');
                    $errorInfo = $statement->errorInfo();
                    if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                        throw new \PDOException($errorInfo[0]);
                    $pdo->commit();

                    return array("found" => true, "errorInfo" => $errorInfo, "lastInsertId" => $insertID);
                } else {
                    // 23505  unique_violation
                    $errorInfo = '23505';
                    $pdo->commit();
                    $result = $kontrol;
                    return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => '');
                }
            } else {
                $errorInfo = '23502';   // 23502  not_null_violation
                $errorInfoColumn = 'pk';
                $pdo->commit();
                return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
            }
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * @author Okan CIRAN
     * info_firm_profile tablosuna parametre olarak gelen id deki kaydın bilgilerini günceller   !!
     * @version v 1.0  06.01.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function update($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $opUserId = InfoUsers::getUserId(array('pk' => $params['pk']));
            if (!\Utill\Dal\Helper::haveRecord($opUserId)) {
                $opUserIdValue = $opUserId ['resultSet'][0]['user_id'];

                $kontrol = $this->haveRecords($params);
                if (!\Utill\Dal\Helper::haveRecord($kontrol)) {

                    $this->makePassive(array('id' => $params['id']));

                    $languageIdValue = 647;
                    $languageId = SysLanguage::getLanguageId(array('language_code' => $params['language_code']));
                    if (!\Utill\Dal\Helper::haveRecord($languageId)) {
                        $languageIdValue = $languageId ['resultSet'][0]['id'];
                    }

                    $statement_act_insert = $pdo->prepare(" 
                 INSERT INTO info_firm_profile(
                        profile_public, 
                        country_id, 
                        op_user_id, 
                        firm_name, 
                        web_address, 
                        tax_office, 
                        tax_no, 
                        sgk_sicil_no, 
                        ownership_status_id, 
                        foundation_year, 
                        language_code, 
                        bagkur_sicil_no,                          
                        firm_name_eng, 
                        firm_name_sort,
                        act_parent_id,
                        consultant_id,
                        operation_type_id,
                        auth_allow_id,
                        language_id
                        )
                        SELECT  
                            " . intval($params['profile_public']) . " AS profile_public, 
                            " . intval($params['country_id']) . " AS country_id, 
                            " . intval($opUserIdValue) . " AS op_user_id, 
                            '" . $params['firm_name'] . "' AS firm_name, 
                            '" . $params['web_address'] . "' AS web_address, 
                            '" . $params['tax_office'] . "' AS tax_office, 
                            '" . $params['tax_no'] . "' AS tax_no, 
                            '" . $params['sgk_sicil_no'] . "' AS sgk_sicil_no, 
                            " . intval($params['ownership_status_id']) . " AS ownership_status_id, 
                            '" . $params['foundation_year'] . "' AS foundation_year, 
                            '" . $params['language_code'] . "' AS language_code, 
                            '" . $params['bagkur_sicil_no'] . "' AS bagkur_sicil_no,                             
                            '" . $params['firm_name_eng'] . "' AS firm_name_eng, 
                            '" . $params['firm_name_sort'] . "' AS firm_name_sort,
                            act_parent_id,
                            consultant_id,
                            2,
                            auth_allow_id,
                             " . intval($languageIdValue) . " AS language_id
                        FROM info_firm_profile 
                        WHERE id =  " . intval($params['id']) . " 
                        ");

                    $insert_act_insert = $statement_act_insert->execute();
                    $affectedRows = $statement_act_insert->rowCount();
                    $errorInfo = $statement_act_insert->errorInfo();
                    if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                        throw new \PDOException($errorInfo[0]);
                    $pdo->commit();
                    return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows);
                } else {
                    // 23505  unique_violation
                    $errorInfo = '23505';
                    $pdo->commit();
                    $result = $kontrol;
                    return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => '');
                }
            } else {
                $errorInfo = '23502';   // 23502  not_null_violation
                $errorInfoColumn = 'pk';
                $pdo->commit();
                return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
            }
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * Datagrid fill function used for testing
     * user interface datagrid fill operation   
     * @author Okan CIRAN
     * @ Gridi doldurmak için info_firm_profile tablosundan kayıtları döndürür !!
     * @version v 1.0  06.01.2016
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
            $sort = " a.firm_name";
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
        $languageIdValue = 647;
        $languageId = SysLanguage::getLanguageId(array('language_code' => $params['language_code']));
        if (!\Utill\Dal\Helper::haveRecord($languageId)) {
            $languageIdValue = $languageId ['resultSet'][0]['id'];
        }

        $whereSql = "WHERE a.language_id =   " . intval($languageIdValue);

        if (isset($args['search_name']) && $args['search_name'] != "") {
            $whereSql .= " AND a.firm_name LIKE '%" . trim($args['search_name']) . "%'";
        }

        if (isset($args['sgk_sicil_no']) && $args['sgk_sicil_no'] != "") {
            $whereSql .= " AND a.sgk_sicil_no LIKE '%" . trim($args['sgk_sicil_no']) . "%'";
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
                        a.firm_name, 
                        a.web_address,                     
                        a.tax_office, 
                        a.tax_no, 
                        a.sgk_sicil_no,
			a.bagkur_sicil_no,
			a.ownership_status_id,
                        sd4.description AS owner_ship,
			a.foundation_year,			
			a.act_parent_id,  
                        a.language_code, 
                        COALESCE(NULLIF(l.language_eng, ''), l.language) AS language_name,                        
                        a.active, 
                        sd3.description AS state_active,  
                        a.deleted,
			sd2.description AS state_deleted, 
                        a.op_user_id,
                        u.username,                    
                        a.auth_allow_id, 
                        sd.description AS auth_alow ,
                        a.cons_allow_id,
                        sd1.description AS cons_allow,
                        a.language_parent_id,               
                        a.firm_name_eng, 
                        a.firm_name_sort,
                        a.country_id, 
                        co.name AS tr_country_name 
                    FROM info_firm_profile a    
                    INNER JOIN sys_operation_types op ON op.id = a.operation_type_id and  op.language_id = a.language_id  AND op.deleted =0 AND op.active =0
                    INNER JOIN sys_specific_definitions sd ON sd.main_group = 13 AND sd.language_id = a.language_id AND a.auth_allow_id = sd.first_group  AND sd.deleted =0 AND sd.active =0
                    INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 14 AND  sd1.language_id = a.language_id AND a.cons_allow_id = sd1.first_group  AND sd1.deleted =0 AND sd1.active =0
                    INNER JOIN sys_specific_definitions sd2 ON sd2.main_group = 15 AND sd2.first_group= a.deleted AND sd2.language_id = a.language_id AND sd2.deleted =0 AND sd2.active =0 
                    INNER JOIN sys_specific_definitions sd3 ON sd3.main_group = 16 AND sd3.first_group= a.active AND sd3.language_id = a.language_id AND sd3.deleted = 0 AND sd3.active = 0
                    LEFT JOIN sys_specific_definitions sd4 ON sd4.main_group = 1 AND sd4.first_group= a.active AND sd4.language_id = a.language_id AND sd4.deleted = 0 AND sd4.active = 0
                    INNER JOIN sys_language l ON l.id = a.language_id AND l.deleted =0 AND l.active =0 
                    INNER JOIN info_users u ON u.id = a.op_user_id                      
                    LEFT JOIN sys_countrys co on co.id = a.country_id AND co.deleted = 0 AND co.active = 0 AND co.language_code = a.language_code                               
                    "
                    . $whereSql .
                    " ORDER BY    " . $sort . " "
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
     * @ Gridi doldurmak için info_firm_profile tablosundan çekilen kayıtlarının kaç tane olduğunu döndürür   !!
     * @version v 1.0  06.01.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillGridRowTotalCount($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');

            $languageIdValue = 647;
            $languageId = SysLanguage::getLanguageId(array('language_code' => $params['language_code']));
            if (!\Utill\Dal\Helper::haveRecord($languageId)) {
                $languageIdValue = $languageId ['resultSet'][0]['id'];
            }

            $whereSQL = " WHERE a.language_id =  " . intval($languageIdValue);
            $whereSQL1 = " WHERE a1.language_id =" . intval($languageIdValue) . " AND a2.deleted = 1 ";
            $whereSQL2 = " WHERE a2.language_id = " . intval($languageIdValue) . " AND a1.deleted = 0 ";

            if (isset($params['search_name']) && $params['search_name'] != "") {
                $whereSQL .= " AND a.name LIKE '%" . trim($params['search_name']) . "%' ";
                $whereSQL1 .= " AND a1.name LIKE '%" . trim($params['search_name']) . "%' ";
                $whereSQL2 .= " AND a2.name LIKE '%" . trim($params['search_name']) . "%' ";
            }
            if (isset($args['sgk_sicil_no']) && $args['sgk_sicil_no'] != "") {
                $whereSQL .= " AND a.sgk_sicil_no LIKE '%" . trim($params['sgk_sicil_no']) . "%'";
                $whereSQL1 .= " AND a1.sgk_sicil_no LIKE '%" . trim($params['sgk_sicil_no']) . "%'";
                $whereSQL2 .= " AND a2.sgk_sicil_no LIKE '%" . trim($params['sgk_sicil_no']) . "%'";
            }
            $sql = "
                SELECT 
                    COUNT(a.id) AS COUNT , 
                    (SELECT COUNT(a1.id) FROM info_firm_profile a1    
                     INNER JOIN sys_operation_types op1 ON op1.id = a1.operation_type_id and op1.language_id = a1.language_id AND op1.deleted =0 AND op1.active =0
                     INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 13 AND sd1.language_id = a1.language_id AND a1.auth_allow_id = sd1.first_group AND sd1.deleted =0 AND sd1.active =0
                     INNER JOIN sys_specific_definitions sd11 ON sd11.main_group = 14 AND sd11.language_id = a1.language_id AND a1.cons_allow_id = sd11.first_group AND sd11.deleted =0 AND sd11.active =0
                     INNER JOIN sys_specific_definitions sd21 ON sd21.main_group = 15 AND sd21.first_group= a1.deleted AND sd21.language_id = a1.language_id AND sd21.deleted =0 AND sd21.active =0 
                     INNER JOIN sys_specific_definitions sd31 ON sd31.main_group = 16 AND sd31.first_group= a1.active AND sd31.language_id = a1.language_id AND sd31.deleted = 0 AND sd31.active = 0                    
                     INNER JOIN sys_language l1 ON l1.id = a1.language_id AND l1.deleted =0 AND l1.active =0 
                     INNER JOIN info_users u1 ON u1.id = a1.op_user_id      
                      " . $whereSQL1 . ") AS undeleted_count,
                    (SELECT COUNT(a2.id) FROM info_firm_profile a2    
                     INNER JOIN sys_operation_types op2 ON op2.id = a2.operation_type_id and op2.language_id = a2.language_id AND op2.deleted =0 AND op2.active =0
                     INNER JOIN sys_specific_definitions sd2 ON sd2.main_group = 13 AND sd2.language_id = a2.language_id AND a2.auth_allow_id = sd2.first_group AND sd2.deleted =0 AND sd2.active =0
                     INNER JOIN sys_specific_definitions sd12 ON sd12.main_group = 14 AND sd12.language_id = a2.language_id AND a2.cons_allow_id = sd12.first_group AND sd12.deleted =0 AND sd12.active =0
                     INNER JOIN sys_specific_definitions sd22 ON sd22.main_group = 15 AND sd22.first_group= a2.deleted AND sd22.language_id = a2.language_id AND sd22.deleted =0 AND sd22.active =0 
                     INNER JOIN sys_specific_definitions sd32 ON sd32.main_group = 16 AND sd32.first_group= a2.active AND sd32.language_id = a2.language_id AND sd32.deleted = 0 AND sd32.active = 0                    
                     INNER JOIN sys_language l2 ON l2.id = a2.language_id AND l2.deleted =0 AND l2.active =0 
                     INNER JOIN info_users u2 ON u2.id = a2.op_user_id  
                      " . $whereSQL2 . " ) AS deleted_count 
                FROM info_firm_profile a    
                INNER JOIN sys_operation_types op ON op.id = a.operation_type_id and  op.language_id = a.language_id  AND op.deleted =0 AND op.active =0
                INNER JOIN sys_specific_definitions sd ON sd.main_group = 13 AND sd.language_id = a.language_id AND a.auth_allow_id = sd.first_group  AND sd.deleted =0 AND sd.active =0
                INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 14 AND  sd1.language_id = a.language_id AND a.cons_allow_id = sd1.first_group  AND sd1.deleted =0 AND sd1.active =0
                INNER JOIN sys_specific_definitions sd2 ON sd2.main_group = 15 AND sd2.first_group= a.deleted AND sd2.language_id = a.language_id AND sd2.deleted =0 AND sd2.active =0 
                INNER JOIN sys_specific_definitions sd3 ON sd3.main_group = 16 AND sd3.first_group= a.active AND sd3.language_id = a.language_id AND sd3.deleted = 0 AND sd3.active = 0
                INNER JOIN sys_language l ON l.id = a.language_id AND l.deleted =0 AND l.active =0 
                INNER JOIN info_users u ON u.id = a.op_user_id  
                 " . $whereSQL . "'
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
     *  
     * @author Okan CIRAN
     * @ seçilmiş olan user_id nin sahip oldugu firmaları combobox a doldurmak için kayıtları döndürür   !!
     * @version v 1.0  06.01.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillComboBox($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $opUserId = InfoUsers::getUserId(array('pk' => $params['pk']));
            if (!\Utill\Dal\Helper::haveRecord($opUserId)) {
                $opUserIdValue = $opUserId ['resultSet'][0]['user_id'];
                $languageIdValue = 647;
                $languageId = SysLanguage::getLanguageId(array('language_code' => $params['language_code']));
                if (!\Utill\Dal\Helper::haveRecord($languageId)) {
                    $languageIdValue = $languageId ['resultSet'][0]['id'];
                }
                $sql = "            
                SELECT 
                    a.id,                     
                    COALESCE(NULLIF(a.firm_name, ''), a.firm_name_eng) AS name
                FROM info_firm_profile  a               
                WHERE 
                    a.active =0 AND 
                    a.deleted = 0 AND 
                    a.language_id = " . intval($languageIdValue) . " AND 
                    a.owner_user_id = " . intval($opUserIdValue) . "             
                ORDER BY  name                
                                 ";
                $statement = $pdo->prepare($sql);
                $statement->execute();
                $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
                $errorInfo = $statement->errorInfo();
                if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                    throw new \PDOException($errorInfo[0]);
                return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
            } else {
                $errorInfo = '23502';   // 23502  not_null_violation
                $errorInfoColumn = 'pk';
                $pdo->commit();
                return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
            }
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * usage     
     * @author Okan CIRAN
     * @ info_firm_profile tablosuna aktif olan diller için ,tek bir kaydın tabloda olmayan diğer dillerdeki kayıtlarını oluşturur   !!
     * @version v 1.0  06.01.2016
     * @return array
     * @throws \PDOException
     */
    public function insertLanguageTemplate($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $statement = $pdo->prepare("                 
                    
                    INSERT INTO info_firm_profile(
                        language_parent_id, firm_name,firm_name_eng, 
			profile_public, f_check, s_date, active, country_id, 
			operation_type_id,  web_address, tax_office, 
			tax_no, sgk_sicil_no, ownership_status_id, foundation_year,  
			act_parent_id, bagkur_sicil_no, deleted, 
			auth_allow_id, owner_user_id, firm_name_sort ,op_user_id,   language_code)  
                    SELECT                          
			language_parent_id,  
                        firm_name,
                        firm_name_eng, 
			profile_public, 
                        f_check, 
                        s_date,                         
                        active, 
                        country_id, 
			operation_type_id,  
                        web_address, 
                        tax_office, 
			tax_no, 
                        sgk_sicil_no, 
                        ownership_status_id, 
                        foundation_year,  
			act_parent_id, 
                        bagkur_sicil_no, 
                        deleted, 
			auth_allow_id,  
                        owner_user_id, 
                        firm_name_sort ,
                        op_user_id, 
                        language_main_code 
                    FROM ( 
                            SELECT 
				c.id AS language_parent_id,                                
				'' AS firm_name, 
                                c.firm_name_eng, 
                                c.profile_public, 
                                0 AS f_check, 
                                c.s_date,                                 
                                0 AS active, 
                                c.country_id, 
				1 AS operation_type_id,  
                                c.web_address, 
                                c.tax_office, 
				c.tax_no, 
                                c.sgk_sicil_no, 
                                c.ownership_status_id, 
                                c.foundation_year,  
				0 AS act_parent_id, 
                                c.bagkur_sicil_no, 
                                0 AS deleted, 
				c.auth_allow_id,  
                                c.owner_user_id, 
                                c.firm_name_sort ,					 
                                c.op_user_id, 		                               
                                l.language_main_code
                            FROM info_firm_profile c
                            LEFT JOIN sys_language l ON l.deleted =0 AND l.active =0 
                            WHERE c.id = " . intval($params['id']) . "
                    ) AS xy  
                    WHERE xy.language_main_code NOT IN 
                        (SELECT 
                            DISTINCT language_code 
                         FROM info_firm_profile cx 
                         WHERE (cx.language_parent_id = " . intval($params['id']) . "
						OR cx.id = " . intval($params['id']) . "
					) AND cx.deleted =0 AND cx.active =0)

                            ");

            //   $statement->bindValue(':id', $params['id'], \PDO::PARAM_INT);

            $result = $statement->execute();
            $insertID = $pdo->lastInsertId('info_firm_profile_id_seq');
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
     * 
     * @author Okan CIRAN
     * @ text alanları doldurmak için info_firm_profile tablosundan tek kayıt döndürür !! 
     * insertLanguageTemplate fonksiyonu ile oluşturulmuş kayıtları 
     * combobox dan çağırmak için hazırlandı.
     * @version v 1.0  06.01.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillTextLanguageTemplate($args = array()) {

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
                        a.firm_name, 
                        a.web_address,                     
                        a.tax_office, 
                        a.tax_no, 
                        a.sgk_sicil_no,
			a.bagkur_sicil_no,
			a.ownership_status_id,
                        sd4.description AS owner_ship,
			a.foundation_year,			
			a.act_parent_id,  
                        a.language_code, 
                        COALESCE(NULLIF(l.language_eng, ''), l.language) AS language_name,                        
                        a.active, 
                        sd3.description AS state_active,  
                        a.deleted,
			sd2.description AS state_deleted, 
                        a.op_user_id,
                        u.username,                    
                        a.auth_allow_id, 
                        sd.description AS auth_alow ,
                        a.cons_allow_id,
                        sd1.description AS cons_allow,
                        a.language_parent_id,
                        a.owner_user_id,
                        u1.name as firm_owner_name,
                        u1.surname as firm_owner_surname,
                        a.firm_name_eng, 
                        a.firm_name_sort
                    FROM info_firm_profile a    
                    INNER JOIN sys_operation_types op ON op.id = a.operation_type_id and  op.language_code = a.language_code  AND op.deleted =0 AND op.active =0
                    INNER JOIN sys_specific_definitions sd ON sd.main_group = 13 AND sd.language_code = a.language_code AND a.auth_allow_id = sd.first_group  AND sd.deleted =0 AND sd.active =0
                    INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 14 AND  sd1.language_code = a.language_code AND a.cons_allow_id = sd1.first_group  AND sd1.deleted =0 AND sd1.active =0
                    INNER JOIN sys_specific_definitions sd2 ON sd2.main_group = 15 AND sd2.first_group= a.deleted AND sd2.language_code = a.language_code AND sd2.deleted =0 AND sd2.active =0 
                    INNER JOIN sys_specific_definitions sd3 ON sd3.main_group = 16 AND sd3.first_group= a.active AND sd3.language_code = a.language_code AND sd3.deleted = 0 AND sd3.active = 0
                    LEFT JOIN sys_specific_definitions sd4 ON sd4.main_group = 1 AND sd4.first_group= a.active AND sd4.language_code = a.language_code AND sd4.deleted = 0 AND sd4.active = 0
                    INNER JOIN sys_language l ON l.language_main_code = a.language_code AND l.deleted =0 AND l.active =0 
                    INNER JOIN info_users u ON u.id = a.op_user_id  
                    LEFT JOIN info_users u1 ON u1.id = a.owner_user_id  
                    WHERE 
                        a.language_code = :language_code AND 
                        a.language_parent_id = :language_parent_id AND
                        a.active = 0 AND 
                        a.deleted = 0

                    ";

            $statement = $pdo->prepare($sql);
            /**
             * For debug purposes PDO statement sql
             * uses 'Panique' library located in vendor directory
             */
            $statement->bindValue(':language_code', $args['language_code'], \PDO::PARAM_STR);
            $statement->bindValue(':language_parent_id', $args['id'], \PDO::PARAM_STR);


            //    echo debugPDO($sql, $parameters);

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
     * delete olayında önce kaydın active özelliğini pasif e olarak değiştiriyoruz. 
     * daha sonra deleted= 1 ve active = 1 olan kaydı oluşturuyor. 
     * böylece tablo içerisinde loglama mekanizması için gerekli olan kayıt oluşuyor.
     * @version 06.01.2016 
     * @param type $id
     * @param type $params
     * @return array
     * @throws PDOException
     */
    public function deletedAct($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $opUserId = InfoUsers::getUserId(array('pk' => $params['pk']));
            if (!\Utill\Dal\Helper::haveRecord($opUserId)) {
                $opUserIdValue = $opUserId ['resultSet'][0]['user_id'];
                
             $this->makePassive(array('id' => $params['id']));
            
             $languageIdValue =647;
             $languageId = SysLanguage::getLanguageId(array('language_code' => $params['language_code']));
             if (!\Utill\Dal\Helper::haveRecord($languageId)) {
             $languageIdValue = $languageId ['resultSet'][0]['id'];
             }            
            
            $statement_act_insert = $pdo->prepare(" 
               INSERT INTO info_firm_profile(
                        profile_public, 
                        country_id, 
                        op_user_id, 
                        firm_name, 
                        web_address, 
                        tax_office, 
                        tax_no, 
                        sgk_sicil_no, 
                        ownership_status_id, 
                        foundation_year, 
                        language_code, 
                        bagkur_sicil_no,                          
                        firm_name_eng, 
                        firm_name_sort,
                        act_parent_id,
                        consultant_id,
                        operation_type_id,
                        auth_allow_id,
                        language_id,
                        active,
                        deleted
                        )
                        SELECT  
                            profile_public, 
                            country_id, 
                            " . intval($opUserIdValue) . " AS op_user_id, 
                            firm_name, 
                            web_address, 
                            tax_office, 
                            tax_no, 
                            sgk_sicil_no, 
                            ownership_status_id, 
                            foundation_year, 
                            language_code, 
                            bagkur_sicil_no,                             
                            firm_name_eng, 
                            firm_name_sort,
                            act_parent_id,
                            consultant_id,
                            " . intval($params['operation_type_id']) . " AS operation_type_id,
                            auth_allow_id,
                            language_id
                            ,1
                            ,1
                        FROM info_firm_profile 
                        WHERE id =  " . intval($params['id']) . "                         
                        ");                                               
            
            $insert_act_insert = $statement_act_insert->execute();
            $affectedRows = $statement_act_insert->rowCount();
            $errorInfo = $statement_act_insert->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            $pdo->commit();
            return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows);
            } else {
                $errorInfo = '23502';   // 23502  not_null_violation
                $errorInfoColumn = 'pk';
                $pdo->commit();
                return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
            }
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

}
