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
            $userId = $this->getUserId(array('pk' => $params['pk']));
            if (\Utill\Dal\Helper::haveRecord($userId)) {
                $userIdValue = $userId ['resultSet'][0]['user_id'];
                $statement = $pdo->prepare(" 
                UPDATE info_firm_profile
                SET  deleted= 1 , active = 1 ,
                     op_user_id = " . $userIdValue . "     
                WHERE id = :id");               
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
     * @ info_firm_profile tablosundaki tüm kayıtları getirir.  !!
     * @version v 1.0  06.01.2016   
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function getAll($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $languageId = NULL;
            $languageIdValue = 647;
            if ((isset($params['language_code']) && $params['language_code'] != "")) {                
                $languageId = SysLanguage::getLanguageId(array('language_code' => $params['language_code']));
                if (\Utill\Dal\Helper::haveRecord($languageId)) {
                    $languageIdValue = $languageId ['resultSet'][0]['id'];                    
                }
            }   
         
            $statement = $pdo->prepare("
            SELECT 
                    a.id, 
                    a.profile_public, 
                    a.f_check, 
                    a.s_date, 
                    a.c_date, 
                    a.operation_type_id,                   
                    COALESCE(NULLIF(opx.operation_name, ''), op.operation_name_eng) AS operation_names,                   
                    COALESCE(NULLIF(COALESCE(NULLIF(ax.firm_name, ''), a.firm_name_eng), ''), a.firm_name) AS firm_names,   
                    a.web_address,                     
                    a.tax_office, 
                    a.tax_no, 
                    a.sgk_sicil_no,                   
                    a.ownership_status_id,             
                    COALESCE(NULLIF(sd1x.description, ''), sd1.description_eng) AS owner_ships,   
                    a.foundation_year,			
                    a.act_parent_id,  
                    a.language_code, 
                    a.language_id, 
                    COALESCE(NULLIF(lx.language, ''), l.language_eng) AS language_names,                        
                    a.active,                
                    COALESCE(NULLIF(sd16x.description, ''), sd16.description_eng) AS state_actives,    
                    a.deleted,                  
                    COALESCE(NULLIF(sd15x.description, ''), sd15.description_eng) AS state_deleteds,    
                    a.op_user_id,
                    u.username,                    
                    a.auth_allow_id,                    
                    COALESCE(NULLIF(sd13x.description, ''), sd13.description_eng) AS auth_alows,    
                    a.cons_allow_id,                   
                    COALESCE(NULLIF(sd14x.description, ''), sd14.description_eng) AS cons_allows,    
                    a.language_parent_id, 
                    a.firm_name_short,
                    a.country_id,                   
		    COALESCE(NULLIF(cox.name, ''), co.name_eng) AS country_names,                     
                    COALESCE(NULLIF(COALESCE(NULLIF(ax.description, ''), a.description_eng), ''), a.description) AS descriptions,   
                    a.duns_number,
                    a.owner_user_id,
                    own.username AS owner_username ,
                    ifk.network_key,
                    a.logo
                FROM info_firm_profile a   
                LEFT JOIN info_firm_keys ifk on ifk.firm_id =  a.act_parent_id AND a.deleted = 0 AND a.active =0 
                INNER JOIN sys_language l ON l.id = a.language_id AND l.deleted =0 AND l.active =0 
                LEFT JOIN sys_language lx ON lx.id = ". intval($languageIdValue)." AND l.deleted =0 AND l.active =0 
                INNER JOIN sys_operation_types op ON op.id = a.operation_type_id AND op.language_id = a.language_id AND op.deleted =0 AND op.active =0
                INNER JOIN sys_specific_definitions sd13 ON sd13.main_group = 13 AND sd13.language_id = a.language_id AND a.auth_allow_id = sd13.first_group AND sd13.deleted =0 AND sd13.active =0
                INNER JOIN sys_specific_definitions sd14 ON sd14.main_group = 14 AND sd14.language_id = a.language_id AND a.cons_allow_id = sd14.first_group AND sd14.deleted =0 AND sd14.active =0
                INNER JOIN sys_specific_definitions sd15 ON sd15.main_group = 15 AND sd15.first_group= a.deleted AND sd15.language_id = a.language_id AND sd15.deleted =0 AND sd15.active =0 
                INNER JOIN sys_specific_definitions sd16 ON sd16.main_group = 16 AND sd16.first_group= a.active AND sd16.language_id = a.language_id AND sd16.deleted = 0 AND sd16.active = 0
                LEFT JOIN sys_specific_definitions sd1 ON sd1.main_group = 1 AND sd1.first_group= a.ownership_status_id AND sd1.language_id = a.language_id AND sd1.deleted = 0 AND sd1.active = 0
                
                LEFT JOIN info_users u ON u.id = a.op_user_id                      
                LEFT JOIN info_users own ON own.id = a.owner_user_id                                      
                LEFT JOIN sys_countrys co ON co.id = a.country_id AND co.deleted = 0 AND co.active = 0 AND co.language_id = a.language_id  
                
                LEFT JOIN sys_countrys cox ON (cox.id = a.country_id OR cox.language_parent_id = a.country_id) AND cox.deleted = 0 AND cox.active = 0 AND cox.language_id = lx.id                
		LEFT JOIN sys_operation_types opx ON opx.id = a.operation_type_id AND opx.language_id = lx.id AND opx.deleted =0 AND opx.active =0
                LEFT JOIN sys_specific_definitions sd13x ON sd13x.main_group = 13 AND sd13x.language_id = lx.id  AND a.auth_allow_id = sd13x.first_group AND sd13x.deleted =0 AND sd13x.active =0
                LEFT JOIN sys_specific_definitions sd14x ON sd14x.main_group = 14 AND  sd14x.language_id = lx.id  AND a.cons_allow_id = sd14x.first_group AND sd14x.deleted =0 AND sd14x.active =0
                LEFT JOIN sys_specific_definitions sd15x ON sd15x.main_group = 15 AND sd15x.first_group= a.deleted AND sd15x.language_id =lx.id  AND sd15x.deleted =0 AND sd15x.active =0 
                LEFT JOIN sys_specific_definitions sd16x ON sd16x.main_group = 16 AND sd16x.first_group= a.active AND sd16x.language_id = lx.id  AND sd16x.deleted = 0 AND sd16x.active = 0
                LEFT JOIN sys_specific_definitions sd1x ON sd1x.main_group = 1 AND sd1x.first_group= a.ownership_status_id AND sd1x.language_id =lx.id  AND sd1x.deleted = 0 AND sd1x.active = 0
		LEFT JOIN info_firm_profile ax on ax.language_parent_id = a.id AND ax.language_id = lx.id AND ax.active =0 AND ax.deleted =0 
                
                WHERE a.deleted =0 AND a.active =0 AND a.language_parent_id =0       
                ORDER BY a.firm_name   
                          ");
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
                '" . $params['firm_name'] . "' AS value , 
                firm_name ='" . $params['firm_name'] . "' AS control,
                CONCAT(firm_name , ' daha önce kayıt edilmiş. Lütfen Kontrol Ediniz !!!' ) AS message                             
            FROM info_firm_profile                
            WHERE firm_name = '" . $params['firm_name'] . "'"
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
            if (\Utill\Dal\Helper::haveRecord($opUserId)) {
                $opUserIdValue = $opUserId ['resultSet'][0]['user_id'];
                $kontrol = $this->haveRecords($params);
                if (!\Utill\Dal\Helper::haveRecord($kontrol)) {
                    $addSql = " op_user_id, ";
                    $addSqlValue = " " . $opUserIdValue . ",";

                    $addSql .= " owner_user_id,  ";
                    if ((isset($params['owner_user_id']) && $params['owner_user_id'] != "")) {
                        $ownerUserId = $params['owner_user_id'];
                    } else {
                        $ownerUserId = $opUserIdValue;
                    }
                    $addSqlValue .= " " . $ownerUserId . ",";

                    $addSql .= " operation_type_id,  ";
                    if ((isset($params['operation_type_id']) && $params['operation_type_id'] != "")) {
                        $addSqlValue .= " " . intval($params['operation_type_id']) . ",";
                    } ELSE {
                        $addSqlValue .= " 1,";
                    }

                    $getConsultant = SysOsbConsultants::getConsultantIdForCompany(array('category_id' => 1));
                    if (\Utill\Dal\Helper::haveRecord($getConsultant)) {
                        $ConsultantId = $getConsultant ['resultSet'][0]['consultant_id'];
                    } else {
                        $ConsultantId = 1001;
                    }
                    $addSql .= " consultant_id,  ";
                    $addSqlValue .= " " . intval($ConsultantId) . ",";

                    if ((isset($params['foundation_year']) && $params['foundation_year'] != "")) {
                        $foundationYear = $params['foundation_year'];
                        $addSql .= " foundation_year,  ";
                        $addSqlValue .= " " . $FoundationYear . ",";
                    }
                    if (isset($params['auth_allow_id'])) {
                        $addSql .= " auth_allow_id, ";
                        $addSqlValue .= intval($params['auth_allow_id']) . ", ";
                    }
                    if (isset($params['cons_allow_id'])) {
                        $addSql .= " cons_allow_id, ";
                        $addSqlValue .= intval($params['cons_allow_id']) . ", ";
                    }


                    $languageId = NULL;
                    $languageIdValue = 647;
                    if ((isset($params['language_code']) && $params['language_code'] != "")) {                
                        $languageId = SysLanguage::getLanguageId(array('language_code' => $params['language_code']));
                        if (\Utill\Dal\Helper::haveRecord($languageId)) {
                            $languageIdValue = $languageId ['resultSet'][0]['id'];                    
                            }
                    }   
                    $addSql .= " language_id, ";
                    $addSqlValue .= " " . $languageIdValue . ",";
                  

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
                         " . $addSql . "   
                        firm_name_eng, 
                        firm_name_short,
                        act_parent_id,                   
                        description,
                        description_eng,
                        duns_number,
                        logo
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
                         " . $addSqlValue . " 
                        :firm_name_eng, 
                        :firm_name_short,
                        (SELECT last_value FROM info_firm_profile_id_seq),                       
                        :description,
                        :description_eng,
                        :duns_number,
                        :logo
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
                    $statement->bindValue(':firm_name_eng', $params['firm_name_eng'], \PDO::PARAM_STR);
                    $statement->bindValue(':firm_name_short', $params['firm_name_short'], \PDO::PARAM_STR);
                    $statement->bindValue(':description', $params['description'], \PDO::PARAM_STR);
                    $statement->bindValue(':description_eng', $params['description_eng'], \PDO::PARAM_STR);
                    $statement->bindValue(':duns_number', $params['duns_number'], \PDO::PARAM_STR);
                    $statement->bindValue(':logo', $params['logo'], \PDO::PARAM_STR);
                    $result = $statement->execute();
                    $insertID = $pdo->lastInsertId('info_firm_profile_id_seq');
                    $errorInfo = $statement->errorInfo();
                    if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                        throw new \PDOException($errorInfo[0]);
                                
                    InfoFirmKeys::insert(array('firm_id' => $insertID, 
                                              'country_id' => $params['country_id']));
                   
                    $pdo->commit();
                    return array("found" => true, "errorInfo" => $errorInfo, "lastInsertId" => $insertID);
                } else {
                    // 23505  unique_violation
                    $errorInfo = '23505';
                    $errorInfoColumn = 'firm_name';
                    $pdo->rollback();
                    // $result = $kontrol;
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
            if (\Utill\Dal\Helper::haveRecord($opUserId)) {
                $opUserIdValue = $opUserId ['resultSet'][0]['user_id'];

                $kontrol = $this->haveRecords($params);
                if (\Utill\Dal\Helper::haveRecord($kontrol)) {

                    $this->makePassive(array('id' => $params['id']));
                    $addSql = " op_user_id, ";
                    $addSqlValue = " " . intval($opUserIdValue) . ",";
                    $addSql .= " owner_user_id, ";
                    $addSqlValue .= " " . intval($params['owner_user_id']) . ",";

                    $addSql .= " operation_type_id,  ";
                    if ((isset($params['operation_type_id']) && $params['operation_type_id'] != "")) {
                        $addSqlValue .= " " . intval($params['operation_type_id']) . ",";
                    } ELSE {
                        $addSqlValue .= " 2,";
                    }
                   
                    if ((isset($params['active']) && $params['active'] != "")) {
                        $addSql .= " active,  ";
                        $addSqlValue .= " " . intval($params['active']) . ",";
                    }  

                    $languageId = NULL;
                    $languageIdValue = 647;
                    if ((isset($params['language_code']) && $params['language_code'] != "")) {                
                        $languageId = SysLanguage::getLanguageId(array('language_code' => $params['language_code']));
                        if (\Utill\Dal\Helper::haveRecord($languageId)) {
                            $languageIdValue = $languageId ['resultSet'][0]['id'];                    
                            }
                    }   

                    if ((isset($params['consultant_id']) && $params['consultant_id'] != "")) {
                        $addSql .= " consultant_id,  ";
                        $addSqlValue .= " " . intval($params['consultant_id']) . ",";
                        if ((isset($params['consultant_confirm_type_id']) && $params['consultant_confirm_type_id'] != "")) {
                            $addSql .= " consultant_confirm_type_id,  ";
                            $addSqlValue .= " " . intval($params['consultant_confirm_type_id']) . ",";
                        }
                        if ((isset($params['confirm_id']) && $params['confirm_id'] != "")) {
                            $addSql .= " confirm_id,  ";
                            $addSqlValue .= " " . intval($params['confirm_id']) . ",";                            
                        }
                    } 
                    else{
                         $addSql .= " consultant_id,  ";
                         $addSqlValue .= " consultant_id, ";
                    } 
                         

                    $statement_act_insert = $pdo->prepare(" 
                 INSERT INTO info_firm_profile(
                        profile_public, 
                        ".$addSql."
                        country_id,                        
                        firm_name, 
                        web_address, 
                        tax_office, 
                        tax_no, 
                        sgk_sicil_no, 
                        ownership_status_id, 
                        foundation_year, 
                        language_code,                         
                        firm_name_eng, 
                        firm_name_short,
                        act_parent_id, 
                        auth_allow_id,
                        language_id,
                        description,
                        description_eng,
                        duns_number,
                        logo
                        )
                        SELECT  
                            " . intval($params['profile_public']) . " AS profile_public, 
                            ".$addSqlValue."
                            " . intval($params['country_id']) . " AS country_id,                             
                            '" . $params['firm_name'] . "' AS firm_name, 
                            '" . $params['web_address'] . "' AS web_address, 
                            '" . $params['tax_office'] . "' AS tax_office, 
                            '" . $params['tax_no'] . "' AS tax_no, 
                            '" . $params['sgk_sicil_no'] . "' AS sgk_sicil_no, 
                            " . intval($params['ownership_status_id']) . " AS ownership_status_id, 
                            '" . $params['foundation_year'] . "' AS foundation_year, 
                            '" . $params['language_code'] . "' AS language_code,                             
                            '" . $params['firm_name_eng'] . "' AS firm_name_eng, 
                            '" . $params['firm_name_short'] . "' AS firm_name_short,
                            act_parent_id,  
                            auth_allow_id,
                             " . intval($languageIdValue) . " AS language_id,
                            '" . $params['description'] . "' AS description, 
                            '" . $params['description_eng'] . "' AS description_eng, 
                            '" . $params['duns_number'] . "' AS duns_number ,
                            '" . $params['logo'] . "' AS logo       
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
                    $pdo->rollback();
                    $result = $kontrol;
                    return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '');
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
     * Datagrid fill function used for testing
     * user interface datagrid fill operation   
     * @author Okan CIRAN
     * @ Gridi doldurmak için info_firm_profile tablosundan kayıtları döndürür !!
     * @version v 1.0  06.01.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillGrid($params = array()) {
        if (isset($params['page']) && $params['page'] != "" && isset($params['rows']) && $params['rows'] != "") {
            $offset = ((intval($params['page']) - 1) * intval($params['rows']));
            $limit = intval($params['rows']);
        } else {
            $limit = 10;
            $offset = 0;
        }

        $sortArr = array();
        $orderArr = array();
        $whereSql = "";
        if (isset($params['sort']) && $params['sort'] != "") {
            $sort = trim($params['sort']);
            $sortArr = explode(",", $sort);
            if (count($sortArr) === 1)
                $sort = trim($params['sort']);
        } else {
            $sort = " a.firm_name";
        }

        if (isset($params['order']) && $params['order'] != "") {
            $order = trim($params['order']);
            $orderArr = explode(",", $order); 
            if (count($orderArr) === 1)
                $order = trim($params['order']);
        } else {
            $order = "ASC";
        }
        
        $languageId = NULL;
        $languageIdValue = 647;
        if ((isset($params['language_code']) && $params['language_code'] != "")) {                
            $languageId = SysLanguage::getLanguageId(array('language_code' => $params['language_code']));
            if (\Utill\Dal\Helper::haveRecord($languageId)) {
                $languageIdValue = $languageId ['resultSet'][0]['id'];                    
                }
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
                    COALESCE(NULLIF(opx.operation_name, ''), op.operation_name_eng) AS operation_names,                   
                    COALESCE(NULLIF(COALESCE(NULLIF(ax.firm_name, ''), a.firm_name_eng), ''), a.firm_name) AS firm_names,   
                    a.web_address,                     
                    a.tax_office, 
                    a.tax_no, 
                    a.sgk_sicil_no,
                    a.ownership_status_id,             
                    COALESCE(NULLIF(sd1x.description, ''), sd1.description_eng) AS owner_ships,   
                    a.foundation_year,			
                    a.act_parent_id,  
                    a.language_code, 
                    a.language_id, 
                    COALESCE(NULLIF(lx.language, ''), l.language_eng) AS language_names,                        
                    a.active,                
                    COALESCE(NULLIF(sd16x.description, ''), sd16.description_eng) AS state_actives,    
                    a.deleted,                  
                    COALESCE(NULLIF(sd15x.description, ''), sd15.description_eng) AS state_deleteds,    
                    a.op_user_id,
                    u.username,                    
                    a.auth_allow_id,                    
                    COALESCE(NULLIF(sd13x.description, ''), sd13.description_eng) AS auth_alows,    
                    a.cons_allow_id,                   
                    COALESCE(NULLIF(sd14x.description, ''), sd14.description_eng) AS cons_allows,    
                    a.language_parent_id,   
                    a.firm_name_short,
                    a.country_id,                   
		    COALESCE(NULLIF(cox.name, ''), co.name_eng) AS country_names,                     
                    COALESCE(NULLIF(COALESCE(NULLIF(ax.description, ''), a.description_eng), ''), a.description) AS descriptions,   
                    a.duns_number,
                    a.owner_user_id,
                    own.username AS owner_username ,
                    ifk.network_key,
                    a.logo
                FROM info_firm_profile a   
                LEFT JOIN info_firm_keys ifk on ifk.firm_id =  a.act_parent_id AND a.deleted = 0 AND a.active =0 
                INNER JOIN sys_language l ON l.id = a.language_id AND l.deleted =0 AND l.active =0 
                LEFT JOIN sys_language lx ON lx.id = ". intval($languageIdValue)." AND l.deleted =0 AND l.active =0 
                INNER JOIN sys_operation_types op ON op.id = a.operation_type_id AND op.language_id = a.language_id AND op.deleted =0 AND op.active =0
                INNER JOIN sys_specific_definitions sd13 ON sd13.main_group = 13 AND sd13.language_id = a.language_id AND a.auth_allow_id = sd13.first_group AND sd13.deleted =0 AND sd13.active =0
                INNER JOIN sys_specific_definitions sd14 ON sd14.main_group = 14 AND sd14.language_id = a.language_id AND a.cons_allow_id = sd14.first_group AND sd14.deleted =0 AND sd14.active =0
                INNER JOIN sys_specific_definitions sd15 ON sd15.main_group = 15 AND sd15.first_group= a.deleted AND sd15.language_id = a.language_id AND sd15.deleted =0 AND sd15.active =0 
                INNER JOIN sys_specific_definitions sd16 ON sd16.main_group = 16 AND sd16.first_group= a.active AND sd16.language_id = a.language_id AND sd16.deleted = 0 AND sd16.active = 0
                LEFT JOIN sys_specific_definitions sd1 ON sd1.main_group = 1 AND sd1.first_group= a.ownership_status_id AND sd1.language_id = a.language_id AND sd1.deleted = 0 AND sd1.active = 0
                
                LEFT JOIN info_users u ON u.id = a.op_user_id                      
                LEFT JOIN info_users own ON own.id = a.owner_user_id                                      
                LEFT JOIN sys_countrys co ON co.id = a.country_id AND co.deleted = 0 AND co.active = 0 AND co.language_id = a.language_id  
                
                LEFT JOIN sys_countrys cox ON (cox.id = a.country_id OR cox.language_parent_id = a.country_id) AND cox.deleted = 0 AND cox.active = 0 AND cox.language_id = lx.id                
		LEFT JOIN sys_operation_types opx ON opx.id = a.operation_type_id AND opx.language_id = lx.id AND opx.deleted =0 AND opx.active =0
                LEFT JOIN sys_specific_definitions sd13x ON sd13x.main_group = 13 AND sd13x.language_id = lx.id  AND a.auth_allow_id = sd13x.first_group AND sd13x.deleted =0 AND sd13x.active =0
                LEFT JOIN sys_specific_definitions sd14x ON sd14x.main_group = 14 AND  sd14x.language_id = lx.id  AND a.cons_allow_id = sd14x.first_group AND sd14x.deleted =0 AND sd14x.active =0
                LEFT JOIN sys_specific_definitions sd15x ON sd15x.main_group = 15 AND sd15x.first_group= a.deleted AND sd15x.language_id =lx.id  AND sd15x.deleted =0 AND sd15x.active =0 
                LEFT JOIN sys_specific_definitions sd16x ON sd16x.main_group = 16 AND sd16x.first_group= a.active AND sd16x.language_id = lx.id  AND sd16x.deleted = 0 AND sd16x.active = 0
                LEFT JOIN sys_specific_definitions sd1x ON sd1x.main_group = 1 AND sd1x.first_group= a.ownership_status_id AND sd1x.language_id =lx.id  AND sd1x.deleted = 0 AND sd1x.active = 0
		LEFT JOIN info_firm_profile ax on ax.language_parent_id = a.id AND ax.language_id = lx.id AND ax.active =0 AND ax.deleted =0 
                
                WHERE a.deleted =0 AND a.active =0 AND a.language_parent_id =0 
              
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

            $languageId = NULL;
            $languageIdValue = 647;
            if ((isset($params['language_code']) && $params['language_code'] != "")) {                
                $languageId = SysLanguage::getLanguageId(array('language_code' => $params['language_code']));
                if (\Utill\Dal\Helper::haveRecord($languageId)) {
                    $languageIdValue = $languageId ['resultSet'][0]['id'];                    
                    }
            }   
            $whereSQL = " WHERE a.language_id =  " . intval($languageIdValue);
            $whereSQL1 = " WHERE a1.language_id =" . intval($languageIdValue) . " AND a2.deleted = 1 ";
            $whereSQL2 = " WHERE a2.language_id = " . intval($languageIdValue) . " AND a1.deleted = 0 ";

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
                      " . $whereSQL1 . ") AS undeleted_count,
                    (SELECT COUNT(a2.id) FROM info_firm_profile a2    
                     INNER JOIN sys_operation_types op2 ON op2.id = a2.operation_type_id and op2.language_id = a2.language_id AND op2.deleted =0 AND op2.active =0
                     INNER JOIN sys_specific_definitions sd2 ON sd2.main_group = 13 AND sd2.language_id = a2.language_id AND a2.auth_allow_id = sd2.first_group AND sd2.deleted =0 AND sd2.active =0
                     INNER JOIN sys_specific_definitions sd12 ON sd12.main_group = 14 AND sd12.language_id = a2.language_id AND a2.cons_allow_id = sd12.first_group AND sd12.deleted =0 AND sd12.active =0
                     INNER JOIN sys_specific_definitions sd22 ON sd22.main_group = 15 AND sd22.first_group= a2.deleted AND sd22.language_id = a2.language_id AND sd22.deleted =0 AND sd22.active =0 
                     INNER JOIN sys_specific_definitions sd32 ON sd32.main_group = 16 AND sd32.first_group= a2.active AND sd32.language_id = a2.language_id AND sd32.deleted = 0 AND sd32.active = 0                    
                     INNER JOIN sys_language l2 ON l2.id = a2.language_id AND l2.deleted =0 AND l2.active =0                      
                      " . $whereSQL2 . " ) AS deleted_count 
                FROM info_firm_profile a    
                INNER JOIN sys_operation_types op ON op.id = a.operation_type_id and  op.language_id = a.language_id  AND op.deleted =0 AND op.active =0
                INNER JOIN sys_specific_definitions sd ON sd.main_group = 13 AND sd.language_id = a.language_id AND a.auth_allow_id = sd.first_group  AND sd.deleted =0 AND sd.active =0
                INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 14 AND  sd1.language_id = a.language_id AND a.cons_allow_id = sd1.first_group  AND sd1.deleted =0 AND sd1.active =0
                INNER JOIN sys_specific_definitions sd2 ON sd2.main_group = 15 AND sd2.first_group= a.deleted AND sd2.language_id = a.language_id AND sd2.deleted =0 AND sd2.active =0 
                INNER JOIN sys_specific_definitions sd3 ON sd3.main_group = 16 AND sd3.first_group= a.active AND sd3.language_id = a.language_id AND sd3.deleted = 0 AND sd3.active = 0
                INNER JOIN sys_language l ON l.id = a.language_id AND l.deleted =0 AND l.active =0                 
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
                $languageId = NULL;
                $languageIdValue = 647;
                if ((isset($params['language_code']) && $params['language_code'] != "")) {                
                    $languageId = SysLanguage::getLanguageId(array('language_code' => $params['language_code']));
                    if (\Utill\Dal\Helper::haveRecord($languageId)) {
                        $languageIdValue = $languageId ['resultSet'][0]['id'];                    
                    }
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
                 $pdo->rollback();
                return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
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
			auth_allow_id, owner_user_id, firm_name_short ,op_user_id,   language_code)  
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
                        firm_name_short ,
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
                                c.firm_name_short ,					 
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
                        a.firm_name_short
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

                $addSql = " op_user_id, ";
                $addSqlValue = " " . intval($opUserIdValue) . ",";
                $addSql .= " owner_user_id, ";
                $addSqlValue .= " owner_user_id,";
                $addSql .= " active,  ";
                $addSqlValue .= " 1,";
                $addSql .= " deleted,  ";
                $addSqlValue .= " 1,";
                $addSql .= " consultant_id,  ";
                $addSqlValue .= " consultant_id, ";
                $addSql .= " consultant_confirm_type_id,  ";
                $addSqlValue .= " consultant_confirm_type_id,  ";
                $addSql .= " confirm_id,  ";
                $addSqlValue .= " confirm_id,";


                $addSql .= " operation_type_id,  ";
                if ((isset($params['operation_type_id']) && $params['operation_type_id'] != "")) {
                    $addSqlValue .= " " . intval($params['operation_type_id']) . ",";
                } ELSE {
                    $addSqlValue .= " 3,";
                }


                $statement_act_insert = $pdo->prepare(" 
                 INSERT INTO info_firm_profile(
                        profile_public, 
                        ".$addSql."
                        country_id,                        
                        firm_name, 
                        web_address, 
                        tax_office, 
                        tax_no, 
                        sgk_sicil_no, 
                        ownership_status_id, 
                        foundation_year, 
                        language_code,                         
                        firm_name_eng, 
                        firm_name_short,
                        act_parent_id, 
                        auth_allow_id,
                        language_id,
                        description,
                        description_eng,
                        duns_number,
                        logo
                        )
                        SELECT  
                            profile_public, 
                            ".$addSqlValue."
                            country_id,                             
                            firm_name, 
                            web_address, 
                            tax_office, 
                            tax_no, 
                            sgk_sicil_no, 
                            ownership_status_id, 
                            foundation_year, 
                            language_code,                             
                            firm_name_eng, 
                            firm_name_short,
                            act_parent_id,  
                            auth_allow_id,
                            language_id,
                            description, 
                            description_eng, 
                            duns_number,
                            logo
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
                $pdo->rollback();
                return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
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
     * @version v 1.0  09.02.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillSingular($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $userId = InfoUsers::getUserId(array('pk' => $params['pk']));
            if (\Utill\Dal\Helper::haveRecord($userId)) {
                $whereSql = " AND a.owner_user_id = " . $userId ['resultSet'][0]['user_id'];

                $languageId = NULL;
                $languageIdValue = 647;
                if ((isset($params['language_code']) && $params['language_code'] != "")) {                
                    $languageId = SysLanguage::getLanguageId(array('language_code' => $params['language_code']));
                    if (\Utill\Dal\Helper::haveRecord($languageId)) {
                        $languageIdValue = $languageId ['resultSet'][0]['id'];                    
                    }
                }                   

                $sql = "
                 SELECT 
                    a.id, 
                    a.profile_public, 
                    a.f_check, 
                    a.s_date, 
                    a.c_date, 
                    a.operation_type_id,                   
                    COALESCE(NULLIF(opx.operation_name, ''), op.operation_name_eng) AS operation_names,                   
                    COALESCE(NULLIF(COALESCE(NULLIF(ax.firm_name, ''), a.firm_name_eng), ''), a.firm_name) AS firm_names,   
                    a.web_address,                     
                    a.tax_office, 
                    a.tax_no, 
                    a.sgk_sicil_no,                   
                    a.ownership_status_id,             
                    COALESCE(NULLIF(sd1x.description, ''), sd1.description_eng) AS owner_ships,   
                    a.foundation_year,			
                    a.act_parent_id,  
                    a.language_code, 
                    a.language_id, 
                    COALESCE(NULLIF(lx.language, ''), l.language_eng) AS language_names,                        
                    a.active,                
                    COALESCE(NULLIF(sd16x.description, ''), sd16.description_eng) AS state_actives,    
                    a.deleted,                  
                    COALESCE(NULLIF(sd15x.description, ''), sd15.description_eng) AS state_deleteds,    
                    a.op_user_id,
                    u.username,                    
                    a.auth_allow_id,                    
                    COALESCE(NULLIF(sd13x.description, ''), sd13.description_eng) AS auth_alows,    
                    a.cons_allow_id,                   
                    COALESCE(NULLIF(sd14x.description, ''), sd14.description_eng) AS cons_allows,    
                    a.language_parent_id,  
                    a.firm_name_short,
                    a.country_id,                   
		    COALESCE(NULLIF(cox.name, ''), co.name_eng) AS country_names,                     
                    COALESCE(NULLIF(COALESCE(NULLIF(ax.description, ''), a.description_eng), ''), a.description) AS descriptions,   
                    a.duns_number,
                    a.owner_user_id,
                    own.username AS owner_username ,
                    ifk.network_key,
                    a.logo
                FROM info_firm_profile a   
                LEFT JOIN info_firm_keys ifk on ifk.firm_id =  a.act_parent_id AND a.deleted = 0 AND a.active =0 
                INNER JOIN sys_language l ON l.id = a.language_id AND l.deleted =0 AND l.active =0 
                LEFT JOIN sys_language lx ON lx.id = ". intval($languageIdValue)." AND l.deleted =0 AND l.active =0 
                INNER JOIN sys_operation_types op ON op.id = a.operation_type_id AND op.language_id = a.language_id AND op.deleted =0 AND op.active =0
                INNER JOIN sys_specific_definitions sd13 ON sd13.main_group = 13 AND sd13.language_id = a.language_id AND a.auth_allow_id = sd13.first_group AND sd13.deleted =0 AND sd13.active =0
                INNER JOIN sys_specific_definitions sd14 ON sd14.main_group = 14 AND sd14.language_id = a.language_id AND a.cons_allow_id = sd14.first_group AND sd14.deleted =0 AND sd14.active =0
                INNER JOIN sys_specific_definitions sd15 ON sd15.main_group = 15 AND sd15.first_group= a.deleted AND sd15.language_id = a.language_id AND sd15.deleted =0 AND sd15.active =0 
                INNER JOIN sys_specific_definitions sd16 ON sd16.main_group = 16 AND sd16.first_group= a.active AND sd16.language_id = a.language_id AND sd16.deleted = 0 AND sd16.active = 0
                LEFT JOIN sys_specific_definitions sd1 ON sd1.main_group = 1 AND sd1.first_group= a.ownership_status_id AND sd1.language_id = a.language_id AND sd1.deleted = 0 AND sd1.active = 0
                
                LEFT JOIN info_users u ON u.id = a.op_user_id                      
                LEFT JOIN info_users own ON own.id = a.owner_user_id                                      
                LEFT JOIN sys_countrys co ON co.id = a.country_id AND co.deleted = 0 AND co.active = 0 AND co.language_id = a.language_id  
                
                LEFT JOIN sys_countrys cox ON (cox.id = a.country_id OR cox.language_parent_id = a.country_id) AND cox.deleted = 0 AND cox.active = 0 AND cox.language_id = lx.id                
		LEFT JOIN sys_operation_types opx ON opx.id = a.operation_type_id AND opx.language_id = lx.id AND opx.deleted =0 AND opx.active =0
                LEFT JOIN sys_specific_definitions sd13x ON sd13x.main_group = 13 AND sd13x.language_id = lx.id  AND a.auth_allow_id = sd13x.first_group AND sd13x.deleted =0 AND sd13x.active =0
                LEFT JOIN sys_specific_definitions sd14x ON sd14x.main_group = 14 AND  sd14x.language_id = lx.id  AND a.cons_allow_id = sd14x.first_group AND sd14x.deleted =0 AND sd14x.active =0
                LEFT JOIN sys_specific_definitions sd15x ON sd15x.main_group = 15 AND sd15x.first_group= a.deleted AND sd15x.language_id =lx.id  AND sd15x.deleted =0 AND sd15x.active =0 
                LEFT JOIN sys_specific_definitions sd16x ON sd16x.main_group = 16 AND sd16x.first_group= a.active AND sd16x.language_id = lx.id  AND sd16x.deleted = 0 AND sd16x.active = 0
                LEFT JOIN sys_specific_definitions sd1x ON sd1x.main_group = 1 AND sd1x.first_group= a.ownership_status_id AND sd1x.language_id =lx.id  AND sd1x.deleted = 0 AND sd1x.active = 0
		LEFT JOIN info_firm_profile ax on ax.language_parent_id = a.id AND ax.language_id = lx.id AND ax.active =0 AND ax.deleted =0 
                
                WHERE a.deleted =0 AND a.active =0 AND a.language_parent_id =0 
                
                ORDER BY sd6.first_group 
                ";
                $statement = $pdo->prepare($sql);
                //  echo debugPDO($sql, $parameters);                
                $statement->execute();
                $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
                $errorInfo = $statement->errorInfo();
                if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                    throw new \PDOException($errorInfo[0]);
                return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
            } else {
                $errorInfo = '23502';   // 23502  user_id not_null_violation
                $errorInfoColumn = 'user_id';                
                return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
            }
        } catch (\PDOException $e /* Exception $e */) {
            //$debugSQLParams = $statement->debugDumpParams();
            return array("found" => false, "errorInfo" => $e->getMessage()/* , 'debug' => $debugSQLParams */);
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
    public function insertTemp($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();

            $opUserId = InfoUsers::getUserIdTemp(array('pktemp' => $params['pktemp']));
            if (\Utill\Dal\Helper::haveRecord($opUserId)) {
                $opUserIdValue = $opUserId ['resultSet'][0]['user_id'];
                $kontrol = $this->haveRecords($params);
                if (!\Utill\Dal\Helper::haveRecord($kontrol)) {
                    $addSql = " op_user_id, ";
                    $addSqlValue = " " . $opUserIdValue . ",";
                    $addSql .= " operation_type_id,  ";
                    $addSqlValue .= " 1,";
                    $addSql .= " owner_user_id,  ";
                    $addSqlValue .= " " . $opUserIdValue . ",";

                    $getConsultant = SysOsbConsultants::getConsultantIdForCompany(array('category_id' => 1));
                    if (\Utill\Dal\Helper::haveRecord($getConsultant)) {
                        $ConsultantId = $getConsultant ['resultSet'][0]['consultant_id'];
                    } else {
                        $ConsultantId = 1001;
                    }
                    $addSql .= " consultant_id,  ";
                    $addSqlValue .= " " . intval($ConsultantId) . ",";

                    if ((isset($params['foundation_year']) && $params['foundation_year'] != "")) {
                        $foundationYear = $params['foundation_year'];
                        $addSql .= " foundation_year,  ";
                        $addSqlValue .= " '" . $foundationYear . "',";
                    }

                    $languageId = NULL;
                    $languageIdValue = 647;
                    if ((isset($params['language_code']) && $params['language_code'] != "")) {                
                        $languageId = SysLanguage::getLanguageId(array('language_code' => $params['language_code']));
                        if (\Utill\Dal\Helper::haveRecord($languageId)) {
                            $languageIdValue = $languageId ['resultSet'][0]['id'];                    
                        }
                    }   
                    $addSql .= " language_id, ";
                    $addSqlValue .= " " . intval($languageIdValue) . ",";

                    $sql = " 
                   INSERT INTO info_firm_profile(
                        profile_public, 
                        country_id,                    
                        firm_name, 
                        web_address, 
                        tax_office, 
                        tax_no, 
                        sgk_sicil_no, 
                        ownership_status_id,                         
                        language_code,                         
                         " . $addSql . "                     
                        firm_name_short,
                        act_parent_id,                      
                        description,
                        description_eng,
                        duns_number,
                        logo
                        )
                VALUES (
                        " . intval($params['profile_public']) . ", 
                        " . intval($params['country_id']) . ",                     
                        :firm_name, 
                        :web_address, 
                        :tax_office, 
                        :tax_no, 
                        :sgk_sicil_no, 
                        " . intval($params['ownership_status_id']) . ",                         
                        :language_code,                         
                         " . $addSqlValue . "                     
                        :firm_name_short,
                        (SELECT last_value FROM info_firm_profile_id_seq),                   
                        :description,
                        :description_eng,
                        :duns_number,
                        :logo
                                            )    ";
                    $statementInsert = $pdo->prepare($sql);
                    $statementInsert->bindValue(':firm_name', $params['firm_name'], \PDO::PARAM_STR);
                    $statementInsert->bindValue(':web_address', $params['web_address'], \PDO::PARAM_STR);
                    $statementInsert->bindValue(':tax_office', $params['tax_office'], \PDO::PARAM_STR);
                    $statementInsert->bindValue(':tax_no', $params['tax_no'], \PDO::PARAM_STR);
                    $statementInsert->bindValue(':sgk_sicil_no', $params['sgk_sicil_no'], \PDO::PARAM_STR);
                    $statementInsert->bindValue(':language_code', $params['language_code'], \PDO::PARAM_STR);
                    $statementInsert->bindValue(':firm_name_short', $params['firm_name_short'], \PDO::PARAM_STR);
                    $statementInsert->bindValue(':description', $params['description'], \PDO::PARAM_STR);
                    $statementInsert->bindValue(':description_eng', $params['description_eng'], \PDO::PARAM_STR);
                    $statementInsert->bindValue(':duns_number', $params['duns_number'], \PDO::PARAM_STR);
                    $statementInsert->bindValue(':logo', $params['logo'], \PDO::PARAM_STR);
                   // echo debugPDO($sql, $params);     
                    $result = $statementInsert->execute();
                    $insertID = $pdo->lastInsertId('info_firm_profile_id_seq');
                    $errorInfo = $statementInsert->errorInfo();
                    if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                        throw new \PDOException($errorInfo[0]);
                     
                    
                    InfoFirmKeys::insert(array('firm_id' => $insertID, 
                                              'country_id' => $params['country_id']));
                    $pdo->commit();
                    return array("found" => true, "errorInfo" => $errorInfo, "lastInsertId" => $insertID);
                } else {
                    // 23505  unique_violation
                    $errorInfo = '23505';
                    $errorInfoColumn = 'firm_name';
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
     * user interface fill operation   
     * @author Okan CIRAN
     * @ userin firm id sini döndürür  !!
     * su an için sadece 1 firması varmış senaryosu için gecerli. 
     * @version v 1.0  29.02.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function getUserFirmIds($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            if (isset($params['user_id'])) {
                $ownerUser = $params['user_id'];                

                $sql = " 
                SELECT id AS firm_id, 1=1 AS control FROM (
                            SELECT ifp.id 
                            FROM info_users a
                            INNER JOIN info_firm_users ifu ON ifu.user_id = " . intval($ownerUser) . " AND ifu.language_parent_id =0 AND a.id = ifu.user_id                            
			    INNER JOIN info_firm_profile ifp ON ifp.active =0 AND ifp.deleted =0 AND ifp.language_parent_id =0 AND ifu.firm_id = ifp.id     
                            WHERE a.active =0 AND a.deleted =0 AND a.language_parent_id =0 
                ) AS xtable limit 1                             
                                 ";
                $statement = $pdo->prepare($sql);
                // echo debugPDO($sql, $params);
                $statement->execute();
                $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
                $errorInfo = $statement->errorInfo();
                if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                    throw new \PDOException($errorInfo[0]);
                return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
            } else {
                $errorInfo = '23502';   // 23502  user_id not_null_violation
                $errorInfoColumn = 'pk';
                return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
            }
        } catch (\PDOException $e /* Exception $e */) {
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    
    
    
     /**
   
     * @author Okan CIRAN
     * @ quest kullanıcısı için,   info_firm_profile tablosundan kayıtları döndürür !!
     * @version v 1.0  21.03.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillCompanyListsGuest($params = array()) {
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
                $whereSql = "";
                if (isset($params['sort']) && $params['sort'] != "") {
                    $sort = trim($params['sort']);
                    $sortArr = explode(",", $sort);
                    if (count($sortArr) === 1)
                        $sort = trim($params['sort']);
                } else {
                    $sort = " firm_names";
                }

                if (isset($params['order']) && $params['order'] != "") {
                    $order = trim($params['order']);
                    $orderArr = explode(",", $order); 
                    if (count($orderArr) === 1)
                        $order = trim($params['order']);
                } else {
                    $order = "ASC";
                }

                $languageId = NULL;
                $languageIdValue = 647;
                if ((isset($params['language_code']) && $params['language_code'] != "")) {                
                    $languageId = SysLanguage::getLanguageId(array('language_code' => $params['language_code']));
                    if (\Utill\Dal\Helper::haveRecord($languageId)) {
                        $languageIdValue = $languageId ['resultSet'][0]['id'];                    
                    }
                }                                 

                $sql = "
                 
		SELECT 
                    k.network_key AS pk ,
                    LOWER(COALESCE(NULLIF(COALESCE(NULLIF(ax.firm_name, ''), a.firm_name_eng), ''), a.firm_name)) AS firm_names,   
                    LOWER(a.web_address) AS web_address ,
                    LOWER(a.firm_name_short) AS firm_name_short,
                    a.country_id,                   
		    LOWER(COALESCE(NULLIF(cox.name, ''), co.name_eng)) AS country_names,                     
                    LOWER(COALESCE(NULLIF(COALESCE(NULLIF(ax.description, ''), a.description_eng), ''), a.description)) AS descriptions,
                    COALESCE(NULLIF(a.logo, ''), 'image_not_found.png') AS logo
                FROM info_firm_profile a                   
                INNER JOIN info_firm_keys k on a.id = k.firm_id                   
                INNER JOIN sys_language l ON l.id = a.language_id AND l.deleted =0 AND l.active =0 
                LEFT JOIN sys_language lx ON lx.id = ". intval($languageIdValue)." AND l.deleted =0 AND l.active =0 
                LEFT JOIN sys_countrys co ON co.id = a.country_id AND co.deleted = 0 AND co.active = 0 AND co.language_id = a.language_id                  
                LEFT JOIN sys_countrys cox ON (cox.id = a.country_id OR cox.language_parent_id = a.country_id) AND cox.deleted = 0 AND cox.active = 0 AND cox.language_id = lx.id                
		LEFT JOIN info_firm_profile ax on ax.act_parent_id = a.id AND ax.language_id = lx.id AND ax.active =0 AND ax.deleted =0 AND ax.profile_public =0                 
                WHERE a.deleted =0 AND a.active =0 AND a.language_parent_id =0 AND a.profile_public =0 
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
     * @ quest kullanıcısı için,   info_firm_profile tablosundan kayıtları döndürür !!
     * @version v 1.0  21.03.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillCompanyListsGuestRtc($params = array()) {
        try {
                $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');                           

                $languageId = NULL;
                $languageIdValue = 647;
                if ((isset($params['language_code']) && $params['language_code'] != "")) {                
                    $languageId = SysLanguage::getLanguageId(array('language_code' => $params['language_code']));
                    if (\Utill\Dal\Helper::haveRecord($languageId)) {
                        $languageIdValue = $languageId ['resultSet'][0]['id'];                    
                    }
                }                                 

                $sql = "                 
		SELECT 
                    count(a.id) as count  
                FROM info_firm_profile a                   
                INNER JOIN sys_language l ON l.id = a.language_id AND l.deleted =0 AND l.active =0 
                LEFT JOIN sys_language lx ON lx.id = ". intval($languageIdValue)." AND l.deleted =0 AND l.active =0 
                LEFT JOIN sys_countrys co ON co.id = a.country_id AND co.deleted = 0 AND co.active = 0 AND co.language_id = a.language_id                  
                LEFT JOIN sys_countrys cox ON (cox.id = a.country_id OR cox.language_parent_id = a.country_id) AND cox.deleted = 0 AND cox.active = 0 AND cox.language_id = lx.id                
		LEFT JOIN info_firm_profile ax on ax.language_parent_id = a.id AND ax.language_id = lx.id AND ax.active =0 AND ax.deleted =0 AND ax.profile_public =0                 
                WHERE a.deleted =0 AND a.active =0 AND a.language_parent_id =0 AND a.profile_public =0 
                 ";
                $statement = $pdo->prepare($sql);
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
     * @ quest kullanıcısı için,   info_firm_profile tablosundan kayıtları döndürür !!
     * @version v 1.0  21.03.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillCompanyInfoEmployeesGuest($params = array()) {
        try {
                $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');            
                 
                $languageId = NULL;
                $languageIdValue = 647;
                if ((isset($params['language_code']) && $params['language_code'] != "")) {                
                    $languageId = SysLanguage::getLanguageId(array('language_code' => $params['language_code']));
                    if (\Utill\Dal\Helper::haveRecord($languageId)) {
                        $languageIdValue = $languageId ['resultSet'][0]['id'];                    
                    }
                }                                 
             
                $sql = "
                SELECT 
                    a.id,      
                    COALESCE(NULLIF(COALESCE(NULLIF(ax.firm_name, ''), a.firm_name_eng), ''), a.firm_name) AS firm_names,   
                    a.web_address,                                         
                    a.firm_name_short,
                    a.country_id,                   
		    COALESCE(NULLIF(cox.name, ''), co.name_eng) AS country_names,                     
                    COALESCE(NULLIF(COALESCE(NULLIF(ax.description, ''), a.description_eng), ''), a.description) AS descriptions,
                    COALESCE(NULLIF(a.logo, ''), 'image_not_found.png') AS logo,
                    ifpi.number_of_employees, 
                    ifpi.number_of_worker, 
                    ifpi.number_of_technician, 
                    ifpi.number_of_engineer, 
                    ifpi.number_of_administrative_staff, 
                    ifpi.number_of_sales_staff, 
                    ifpi.number_of_foreign_trade_staff
                FROM info_firm_profile a    
                INNER JOIN info_firm_keys fk ON fk.firm_id = a.id
                INNER JOIN sys_language l ON l.id = a.language_id AND l.deleted =0 AND l.active =0 
                LEFT JOIN info_firm_personnel_info ifpi on ifpi.firm_id = a.id AND ifpi.profile_public =0
                LEFT JOIN sys_language lx ON lx.id = ". intval($languageIdValue)." AND l.deleted =0 AND l.active =0 
                LEFT JOIN sys_countrys co ON co.id = a.country_id AND co.deleted = 0 AND co.active = 0 AND co.language_id = a.language_id                  
                LEFT JOIN sys_countrys cox ON (cox.id = a.country_id OR cox.language_parent_id = a.country_id) AND cox.deleted = 0 AND cox.active = 0 AND cox.language_id = lx.id                
		LEFT JOIN info_firm_profile ax ON ax.act_parent_id = a.id AND ax.language_id = lx.id AND ax.active =0 AND ax.deleted =0 AND ax.profile_public =0                 
                WHERE 
                    a.deleted =0 AND 
                    a.active =0 AND 
                    a.language_parent_id =0 AND 
                    a.profile_public =0 AND 
                    fk.network_key = '".$params['network_key']."' 
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
            //$debugSQLParams = $statement->debugDumpParams();
            return array("found" => false, "errorInfo" => $e->getMessage()/* , 'debug' => $debugSQLParams */);
        }
    }

             /**
   
     * @author Okan CIRAN
     * @ quest kullanıcısı için,   info_firm_profile tablosundan kayıtları döndürür !!
     * @version v 1.0  23.03.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillCompanyInfoReferencesGuest($params = array()) {
        try {
                $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');            
                 
                $languageId = NULL;
                $languageIdValue = 647;
                if ((isset($params['language_code']) && $params['language_code'] != "")) {                
                    $languageId = SysLanguage::getLanguageId(array('language_code' => $params['language_code']));
                    if (\Utill\Dal\Helper::haveRecord($languageId)) {
                        $languageIdValue = $languageId ['resultSet'][0]['id'];                    
                    }
                }                                 

                $sql = "
                SELECT                
		   distinct COALESCE(NULLIF(COALESCE(NULLIF(ifrx.ref_name, ''), ifr.ref_name_eng), ''), ifr.ref_name) AS ref_name
                FROM info_firm_profile a    
                INNER JOIN info_firm_keys fk on fk.firm_id = a.id
                INNER JOIN info_firm_references ifr on ifr.firm_id = a.id AND   ifr.deleted =0 AND ifr.active =0 
                INNER JOIN sys_language l ON l.id = a.language_id AND l.deleted =0 AND l.active =0                 
                LEFT JOIN sys_language lx ON lx.id = ". intval($languageIdValue)." AND l.deleted =0 AND l.active =0                 
		LEFT JOIN info_firm_references ifrx on (ifrx.id = ifr.id OR ifrx.language_parent_id = ifr.id) AND ifrx.language_id = lx.id AND ifrx.active =0 AND ifrx.deleted =0                                    
                WHERE 
                    a.deleted =0 AND 
                    a.active =0 AND 
                    a.language_parent_id =0 AND 
                    a.profile_public =0 AND 
                    fk.network_key = '".$params['network_key']."' 
                ORDER BY ref_name
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
            //$debugSQLParams = $statement->debugDumpParams();
            return array("found" => false, "errorInfo" => $e->getMessage()/* , 'debug' => $debugSQLParams */);
        }
    }

     /*  
     * @author Okan CIRAN
     * @ quest kullanıcısı için,   info_firm_profile tablosundan kayıtları döndürür !!
     * @version v 1.0  23.03.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillCompanyInfoSocialediaGuest($params = array()) {
        try {
                $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');            
                 
                $languageId = NULL;
                $languageIdValue = 647;
                if ((isset($params['language_code']) && $params['language_code'] != "")) {                
                    $languageId = SysLanguage::getLanguageId(array('language_code' => $params['language_code']));
                    if (\Utill\Dal\Helper::haveRecord($languageId)) {
                        $languageIdValue = $languageId ['resultSet'][0]['id'];                    
                    }
                }                                 

                $sql = "
                SELECT                
		    COALESCE(NULLIF(COALESCE(NULLIF(smx.name, ''), sm.name_eng), ''), sm.name) AS socialmedia,                   
                    fsm.firm_link                   
                FROM info_firm_profile a    
                INNER JOIN info_firm_keys fk on fk.firm_id = a.id
                INNER JOIN info_firm_socialmedia fsm on fsm.firm_id = a.id AND   fsm.deleted =0 AND fsm.active =0 AND fsm.profile_public =0
                INNER JOIN sys_socialmedia sm on sm.id = fsm.sys_socialmedia_id AND sm.deleted =0 AND sm.active =0 
                INNER JOIN sys_language l ON l.id = a.language_id AND l.deleted =0 AND l.active =0                 
                LEFT JOIN sys_language lx ON lx.id = ". intval($languageIdValue)." AND l.deleted =0 AND l.active =0                 
		LEFT JOIN sys_socialmedia smx on smx.language_parent_id = fsm.sys_socialmedia_id AND smx.language_id = lx.id AND smx.active =0 AND smx.deleted =0                   
                WHERE 
                    a.deleted =0 AND 
                    a.active =0 AND 
                    a.language_parent_id =0 AND 
                    a.profile_public =0 AND 
                    fk.network_key = '".$params['network_key']."' 
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
            //$debugSQLParams = $statement->debugDumpParams();
            return array("found" => false, "errorInfo" => $e->getMessage()/* , 'debug' => $debugSQLParams */);
        }
    }

       /*  
     * @author Okan CIRAN
     * @ quest kullanıcısı için,   info_firm_profile tablosundan kayıtları döndürür !!
     * @version v 1.0  23.03.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillCompanyInfoCustomersGuest($params = array()) {
        try {
                $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');            
                 
                $languageId = NULL;
                $languageIdValue = 647;
                if ((isset($params['language_code']) && $params['language_code'] != "")) {                
                    $languageId = SysLanguage::getLanguageId(array('language_code' => $params['language_code']));
                    if (\Utill\Dal\Helper::haveRecord($languageId)) {
                        $languageIdValue = $languageId ['resultSet'][0]['id'];                    
                    }
                }                                 

                $sql = "
                SELECT                
		    COALESCE(NULLIF(COALESCE(NULLIF(ifcx.customer_name, ''), ifc.customer_name_eng), ''), ifc.customer_name) AS customer_names
                FROM info_firm_profile a    
                INNER JOIN info_firm_keys fk on fk.firm_id = a.id
                INNER JOIN info_firm_customers ifc on ifc.firm_id = a.id AND ifc.deleted =0 AND ifc.active =0  AND ifc.profile_public =0                
                INNER JOIN sys_language l ON l.id = a.language_id AND l.deleted =0 AND l.active =0                 
                LEFT JOIN sys_language lx ON lx.id = ". intval($languageIdValue)." AND l.deleted =0 AND l.active =0                 
		LEFT JOIN info_firm_customers ifcx on (ifcx.id = ifc.id OR ifcx.language_parent_id = ifc.id) AND ifcx.language_id = lx.id AND ifcx.active =0 AND ifcx.deleted =0                   
                WHERE 
                    a.deleted =0 AND 
                    a.active =0 AND 
                    a.language_parent_id =0 AND 
                    a.profile_public =0 AND 
                    fk.network_key = '".$params['network_key']."' 
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
            //$debugSQLParams = $statement->debugDumpParams();
            return array("found" => false, "errorInfo" => $e->getMessage()/* , 'debug' => $debugSQLParams */);
        }
    }

     
}
