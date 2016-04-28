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
                    UPDATE info_users 
                    SET deleted = 1, active =1,
                    user_id = " . $userIdValue . "                     
                    WHERE id = :id
                    ");
                $update = $statement->execute();
                $affectedRows = $statement->rowCount();
                $errorInfo = $statement->errorInfo();
                if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                    throw new \PDOException($errorInfo[0]);
                $pdo->commit();
                return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows);
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
     * @param array | null $args
     * @return type
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
                        ad.profile_public,                  
                        a.s_date, 
                        a.c_date, 
                        a.operation_type_id,                        
                        COALESCE(NULLIF(opx.operation_name, ''), op.operation_name_eng) AS operation_name,
                        ad.name, 
                        ad.surname, 
                        a.username, 
                        a.password, 
                        ad.auth_email,                   
                        ad.language_code, 
                        ad.language_id, 
                        l.language_eng as user_language,
			COALESCE(NULLIF(lx.id, NULL), 385) AS language_id,
		        COALESCE(NULLIF(lx.language, ''), 'en') AS language_name,                        
                        a.active,                         
                        COALESCE(NULLIF(sd16x.description, ''), sd16.description_eng) AS state_active, 
                        ad.deleted,
                        COALESCE(NULLIF(sd15x.description, ''), sd15.description_eng) AS state_deleted,  			
                        a.op_user_id,
                        u.username AS op_user_name,
                        ad.act_parent_id, 
                        ad.auth_allow_id,                         
                        COALESCE(NULLIF(sd13x.description, ''), sd13.description_eng) AS auth_alow, 
                        ad.cons_allow_id,                        
                        COALESCE(NULLIF(sd14x.description, ''), sd14.description_eng) AS cons_allow,                   
                        ad.root_id,
                        a.consultant_id,
                        cons.name AS cons_name, 
                        cons.surname AS cons_surname,			 
                        COALESCE(NULLIF(sd19x.description, ''), sd19.description_eng) AS state_profile_public                        
                    FROM info_users a    
                    INNER JOIN sys_language l ON l.id = a.language_id AND l.deleted =0 AND l.active =0                     
                    LEFT JOIN sys_language lx ON lx.id = ".intval($languageIdValue)." AND lx.deleted =0 AND lx.active =0                     
                    INNER JOIN info_users_detail ad ON ad.deleted =0 AND ad.active =0 AND ad.root_id = a.id AND ad.language_parent_id = 0 
                    LEFT JOIN sys_operation_types op ON op.id = a.operation_type_id AND op.deleted =0 AND op.active =0 AND op.language_parent_id =0
                    LEFT JOIN sys_operation_types opx ON (opx.id = a.operation_type_id OR opx.language_parent_id = a.operation_type_id) and opx.language_id =lx.id  AND opx.deleted =0 AND opx.active =0 
		    
		    INNER JOIN sys_specific_definitions sd13 ON sd13.main_group = 13 AND ad.auth_allow_id = sd13.first_group AND sd13.deleted =0 AND sd13.active =0 AND sd13.language_parent_id =0
		    INNER JOIN sys_specific_definitions sd14 ON sd14.main_group = 14 AND ad.cons_allow_id = sd14.first_group AND sd14.deleted =0 AND sd14.active =0 AND sd14.language_parent_id =0
		    INNER JOIN sys_specific_definitions sd15 ON sd15.main_group = 15 AND sd15.first_group= a.deleted AND sd15.deleted =0 AND sd15.active =0 AND sd15.language_parent_id =0
		    INNER JOIN sys_specific_definitions sd16 ON sd16.main_group = 16 AND sd16.first_group= a.active AND sd16.deleted = 0 AND sd16.active = 0 AND sd16.language_parent_id =0
		    INNER JOIN sys_specific_definitions sd19 ON sd19.main_group = 19 AND sd19.first_group= ad.profile_public AND sd19.deleted = 0 AND sd19.active = 0 AND sd19.language_parent_id =0

                    LEFT JOIN sys_specific_definitions sd13x ON sd13x.main_group = 13 AND sd13x.language_id = lx.id AND (sd13x.id = sd13.id OR sd13x.language_parent_id = sd13.id) AND sd13x.deleted =0 AND sd13x.active =0
                    LEFT JOIN sys_specific_definitions sd14x ON sd14x.main_group = 14 AND sd14x.language_id = lx.id AND (sd14x.id = sd14.id OR sd14x.language_parent_id = sd14.id) AND sd14x.deleted =0 AND sd14x.active =0
                    LEFT JOIN sys_specific_definitions sd15x ON sd15x.main_group = 15 AND sd15x.language_id =lx.id AND (sd15x.id = sd15.id OR sd15x.language_parent_id = sd15.id) AND sd15x.deleted =0 AND sd15x.active =0 
                    LEFT JOIN sys_specific_definitions sd16x ON sd16x.main_group = 16 AND sd16x.language_id = lx.id AND (sd16x.id = sd16.id OR sd16x.language_parent_id = sd16.id) AND sd16x.deleted = 0 AND sd16x.active = 0
                    LEFT JOIN sys_specific_definitions sd19x ON sd19x.main_group = 19 AND sd19x.language_id = lx.id AND (sd19x.id = sd19.id OR sd19x.language_parent_id = sd19.id) AND sd19x.deleted = 0 AND sd19x.active = 0
                    
                    INNER JOIN info_users u ON u.id = a.op_user_id                      
                    LEFT JOIN info_users_detail cons ON cons.root_id = a.consultant_id AND cons.cons_allow_id =1 
                
                    ORDER BY ad.name, ad.surname
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
     * @ info_users_details tablosundan parametre olarak  gelen id kaydını aktifliğini 1 = pasif yapar. !!
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
                UPDATE info_users
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
     * @ info_users tablosundan parametre olarak  gelen id kaydını aktifliğini 1 = pasif
     *  ve deleted 1 = silinmiş yapar. !!
     * @version v 1.0  09.02.2016
     * @param type $params
     * @return array
     * @throws \PDOException
     */
    public function makeUserDeleted($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');        
            $statement = $pdo->prepare(" 
                UPDATE info_users 
                SET                         
                    c_date =  timezone('Europe/Istanbul'::text, ('now'::text)::timestamp(0) with time zone) ,                     
                    active = 1 ,
                    deleted= 1 
                WHERE id = :id");
            $statement->bindValue(':id', $params['id'], \PDO::PARAM_INT);
            $update = $statement->execute();
            $afterRows = $statement->rowCount();
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);            
            return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $afterRows);
        } catch (\PDOException $e /* Exception $e */) {            
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
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
                username AS username , 
                '" . $params['username'] . "' AS value , 
                username ='" . $params['username'] . "' AS control,
                CONCAT(username , ' daha önce kayıt edilmiş. Lütfen Kontrol Ediniz !!!' ) AS message                             
            FROM info_users                
            WHERE   
                LOWER(username) = LOWER('" . $params['username'] . "') "
                    . $addSql . " 
               AND active =0         
               AND deleted =0   
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

    /**
     * @author Okan CIRAN
     * @ info_users tablosunda name sutununda daha önce oluşturulmuş mu? 
     * @version v 1.0 20.01.2016
     * @return array
     * @throws \PDOException
     */
    public function haveEmail($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $addSql = "";
            if (isset($params['id'])) {
                $addSql = " AND id != " . intval($params['id']) . " ";
            }
            $sql = " 
            SELECT  
                auth_email AS auth_email , 
                '" . $params['auth_email'] . "' AS value , 
                auth_email ='" . $params['auth_email'] . "' AS control,
                CONCAT(auth_email , ' daha önce kayıt edilmiş. Lütfen Kontrol Ediniz !!!' ) AS message                             
            FROM info_users_detail                
            WHERE   
                LOWER(auth_email) = LOWER('" . $params['auth_email'] . "') "
                    . $addSql . " 
               AND active =0         
               AND deleted =0   
                               ";
            $statement = $pdo->prepare($sql);
            //    echo debugPDO($sql, $params);
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
     * info_users tablosundaki kullanıcı kaydı oluşturur  !!
     * @author Okan CIRAN
     * @version v 1.0  26.01.2016
     * @param array | null $args
     * @return array
     * @throws PDOException
     */
    public function insert($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();            
            $kontrol = $this->haveRecords($params); // username kontrolu
            if (!\Utill\Dal\Helper::haveRecord($kontrol)) { 
                $userId = $this->getUserId(array('pk' => $params['pk']));// bı pk var mı  
                if (!\Utill\Dal\Helper::haveRecord($userId)) {
                    $userIdValue = $userId ['resultSet'][0]['user_id'];
                    /// languageid sini alalım 
                    $roleId = 5 ; 
                    $languageIdValue = 647;                    
                    if ((isset($params['preferred_language']) && $params['preferred_language'] != "")) {                                    
                        $languageIdValue = $params['preferred_language'];
                    }
                   //uzerinde az iş olan consultantı alalım.  
                   $getConsultant = SysOsbConsultants::getConsultantIdForUsers();              
                    if (\Utill\Dal\Helper::haveRecord($getConsultant)) {
                        $ConsultantId = $getConsultant ['resultSet'][0]['consultant_id'];
                    } else {
                        $ConsultantId = 1001;
                    } 
                    
                    $operationIdValue = -1;
                    $operationId = SysOperationTypes::getTypeIdToGoOperationId(
                                array('parent_id' => 3, 'main_group' => 3, 'sub_grup_id' => 43, 'type_id' => 1,));
                    if (\Utill\Dal\Helper::haveRecord($operationId)) {
                    $operationIdValue = $operationId ['resultSet'][0]['id'];
                    }
                    $sql = " 
                    INSERT INTO info_users(
                               operation_type_id, 
                               username, 
                               password, 
                               active,
                               language_id,
                               op_user_id,
                               role_id,
                               consultant_id
                                )
                    VALUES (  :operation_type_id, 
                              :username,
                              :password,
                              :active,
                              ".intval($languageIdValue).",
                              ".intval($userIdValue).",
                              :role_id,
                              ". intval($ConsultantId)."
                        )";

                    $statement = $pdo->prepare($sql);
                    $statement->bindValue(':operation_type_id', $operationIdValue, \PDO::PARAM_INT);
                    $statement->bindValue(':username', $params['username'], \PDO::PARAM_STR);
                    $statement->bindValue(':password', $params['password'], \PDO::PARAM_STR);
                    $statement->bindValue(':role_id', $roleId, \PDO::PARAM_INT);
                    // echo debugPDO($sql, $params);
                    $result = $statement->execute();
                    $insertID = $pdo->lastInsertId('info_users_id_seq');
                    $errorInfo = $statement->errorInfo();
                    if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                        throw new \PDOException($errorInfo[0]);


                    /*
                     * kullanıcı için gerekli olan private key ve value değerleri yaratılılacak.  
                     * kullanıcı için gerekli olan private key temp ve value temp değerleri yaratılılacak.  
                     */
                    $this->setPrivateKey(array('id' => $insertID));
                   
                    /*
                     * kullanıcı bilgileri info_users_detail tablosuna kayıt edilecek.   
                     */
                    $this->insertDetail(
                            array(
                                'id' => $insertID,
                                'op_user_id' => $userIdValue,
                                'role_id' => 5,
                                'active' => $params['active'],
                                'operation_type_id' => $params['operation_type_id'],
                                'language_id' => $params['preferred_language'],
                                'profile_public' => $params['profile_public'],
                                'f_check' => 0,
                                'name' => $params['name'],
                                'surname' => $params['surname'],
                                'auth_email' => $params['auth_email'],
                                'act_parent_id' => $params['act_parent_id'],
                                'auth_allow_id' => 0,
                                'cons_allow_id' => 0,
                                'root_id' => $insertID,
                                'consultant_id' => $ConsultantId,
                                'password' => $params['password'],
                    ));

                    $pdo->commit();
                    $logDbData = $this->getUsernamePrivateKey(array('id' => $insertID));
                    $this->insertLogUser(array('oid' => $insertID ,
                                               'username'=> $logDbData['resultSet'][0]['username'],  
                                               'sf_private_key_value'=> $logDbData['resultSet'][0]['sf_private_key_value'],  
                                               'sf_private_key_value_temp'=> $logDbData['resultSet'][0]['sf_private_key_value_temp']  
                            
                                                ));
                     
                    
                    return array("found" => true, "errorInfo" => $errorInfo, "lastInsertId" => $insertID);
                } else {
                    $errorInfo = '23502';   // 23502  not_null_violation
                    $errorInfoColumn = 'pk';
                    $pdo->rollback();
                    return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
                }
            } else {
                $errorInfo = '23505';   // 23505  unique_violation
                $errorInfoColumn = 'username';
                 $pdo->rollback();
                return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
            }
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * info_users tablosundaki kullanıcı kaydı oluşturur  !!
     * @author Okan CIRAN
     * @version v 1.0  26.01.2016
     * @param array | null $args
     * @return array
     * @throws PDOException
     */
    public function insertDetail($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');                  
            $kontrol = $this->haveRecords($params);
            if (\Utill\Dal\Helper::haveRecord($kontrol)) {
                $operationIdValue = -1;
                $operationId = SysOperationTypes::getTypeIdToGoOperationId(
                            array('parent_id' => 3, 'main_group' => 3, 'sub_grup_id' => 40, 'type_id' => 1,));
                if (\Utill\Dal\Helper::haveRecord($operationId)) {
                $operationIdValue = $operationId ['resultSet'][0]['id'];
                }
                $sql = " 
                INSERT INTO info_users_detail(                           
                            profile_public,                             
                            operation_type_id, 
                            name, 
                            surname, 
                            auth_email,                             
                            act_parent_id,                              
                            language_id,                             
                            root_id, 
                            op_user_id,
                            password,
                            consultant_id)
                VALUES (    :profile_public,                               
                            ". intval($operationIdValue).", 
                            :name, 
                            :surname, 
                            :auth_email,                             
                            (SELECT last_value FROM info_users_detail_id_seq),                              
                            :language_id,                             
                            :root_id, 
                            :op_user_id ,
                            :password,
                            ". intval($params['consultant_id'])."
                    )";
                $statement = $pdo->prepare($sql);
                $statement->bindValue(':profile_public', $params['profile_public'], \PDO::PARAM_INT);
                $statement->bindValue(':name', $params['name'], \PDO::PARAM_STR);
                $statement->bindValue(':surname', $params['surname'], \PDO::PARAM_STR);
                $statement->bindValue(':auth_email', $params['auth_email'], \PDO::PARAM_STR);                
                $statement->bindValue(':password', $params['password'], \PDO::PARAM_STR);
                $statement->bindValue(':language_id', $params['language_id'], \PDO::PARAM_INT);
                $statement->bindValue(':root_id', $params['root_id'], \PDO::PARAM_INT);
                $statement->bindValue(':op_user_id', $params['op_user_id'], \PDO::PARAM_INT);
           //   echo debugPDO($sql, $params);
                $result = $statement->execute();
                $insertID = $pdo->lastInsertId('info_users_detail_id_seq');
                $errorInfo = $statement->errorInfo();
                if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                    throw new \PDOException($errorInfo[0]);              
                return array("found" => true, "errorInfo" => $errorInfo, "lastInsertId" => $insertID);
            } else {
                $errorInfo = '23502';   // 23502 info_users tablosunda bulunamadı. not_null_violation   
                return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => '');
            }
        } catch (\PDOException $e /* Exception $e */) {         
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * Kullanıcı ilk kayıt ta "pk" sız olarak  cagırılacak servis.
     * Kullanıcıyı kaydeder. pk, pktemp, privatekey degerlerinin olusturur.  
     * @author Okan CIRAN
     * @version v 1.0 27.01.2016
     * @param array | null $args
     * @return array
     * @throws PDOException
     */
    public function insertTemp($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction(); 
                $kontrol = $this->haveRecords($params);
                if (!\Utill\Dal\Helper::haveRecord($kontrol)) {
                    $ConsultantId = 1001;
                    $getConsultant = SysOsbConsultants::getConsultantIdForUsers(array('category_id' => 0));              
                    if (\Utill\Dal\Helper::haveRecord($getConsultant)) {
                        $ConsultantId = $getConsultant ['resultSet'][0]['consultant_id'];
                    }
                    
                    $roleId = 5 ; 
                    $languageIdValue = 647;                    
                    if ((isset($params['preferred_language']) && $params['preferred_language'] != "")) {                                    
                        $languageIdValue = $params['preferred_language'];
                    }                   
                    
                    $operationIdValue = -1;
                    $operationId = SysOperationTypes::getTypeIdToGoOperationId(
                                array('parent_id' => 3, 'main_group' => 3, 'sub_grup_id' => 45, 'type_id' => 1,));
                    if (\Utill\Dal\Helper::haveRecord($operationId)) {
                    $operationIdValue = $operationId ['resultSet'][0]['id'];
                    }
                    
                    $sql = " 
                INSERT INTO info_users(                           
                        operation_type_id, 
                        username, 
                        password,                         
                        op_user_id,                            
                        language_id, 
                        role_id,
                        consultant_id
                              )      
                VALUES (".intval($operationIdValue).",
                        :username,
                        :password,
                        (SELECT last_value FROM info_users_id_seq),
                        ".intval($languageIdValue).", 
                        ".intval($roleId).",
                        ".intval($ConsultantId)."
                    )";
                    
                    $statement = $pdo->prepare($sql);
                    $statement->bindValue(':username', $params['username'], \PDO::PARAM_STR);
                    $statement->bindValue(':password', $params['password'], \PDO::PARAM_STR);                    
                  //echo debugPDO($sql, $params);
                    $result = $statement->execute();
                    $insertID = $pdo->lastInsertId('info_users_id_seq');
                    $errorInfo = $statement->errorInfo();
                    if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                        throw new \PDOException($errorInfo[0]);

                    /*
                     * kullanıcı için gerekli olan private key ve value değerleri yaratılılacak.  
                     * kullanıcı için gerekli olan private key temp ve value temp değerleri yaratılılacak.  
                     */
                    $this->setPrivateKey(array('id' => $insertID));
                    /*
                     * kullanıcının public key temp değeri alınacak..  
                     */
                    $publicKeyTemp = $this->getPublicKeyTemp(array('id' => $insertID));             
                    if (\Utill\Dal\Helper::haveRecord($publicKeyTemp)) {
                        $publicKeyTempValue = $publicKeyTemp ['resultSet'][0]['pk_temp'];
                    } else {
                        $publicKeyTempValue = NULL;
                    }
                
                    /*
                     * kullanıcı bilgileri info_users_detail tablosuna kayıt edilecek.   
                     */
                    $this->insertDetail(
                            array(
                                'op_user_id' => $insertID,
                                'role_id' => 5,                                
                                'language_id' => $params['preferred_language'],
                                'profile_public' => $params['profile_public'],                                
                                'name' => $params['name'],
                                'surname' => $params['surname'],
                                'username' => $params['username'],
                                'auth_email' => $params['auth_email'],                                                                 
                                'root_id' => $insertID,
                                'password' => $params['password'],
                                'consultant_id'=> $ConsultantId
                    ));

                    $pdo->commit();
                    $logDbData = $this->getUsernamePrivateKey(array('id' => $insertID));                    
                    $this->insertLogUser(array('oid' => $insertID ,
                                               'username'=> $logDbData['resultSet'][0]['username'],  
                                               'sf_private_key_value'=> $logDbData['resultSet'][0]['sf_private_key_value'],  
                                               'sf_private_key_value_temp'=> $logDbData['resultSet'][0]['sf_private_key_value_temp']  
                            
                                                ));
                    
                    return array("found" => true, "errorInfo" => $errorInfo, "lastInsertId" => $insertID, "pktemp" => $publicKeyTempValue);
                } else {
                    $errorInfo = '23505';   // 23505  unique_violation
                    $errorInfoColumn = 'username';
                     $pdo->rollback();
                    $result = $kontrol;
                    return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
                }             
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * parametre olarak gelen array deki 'id' li kaydın update ini yapar  !!
     * @author Okan CIRAN
     * @version v 1.0  26.01.2016     
     * @param array | null $args
     * @param type $params
     * @return array
     * @throws PDOException
     */
    public function update($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $userId = $this->getUserId(array('pk' => $params['pk'],));
            if (\Utill\Dal\Helper::haveRecord($userId)) {
                $userIdValue = $userId ['resultSet'][0]['user_id'];
                $kontrol = $this->haveRecords($params);
                if ( \Utill\Dal\Helper::haveRecord($kontrol)) {                    
                    $languageIdValue = 647;                    
                    if ((isset($params['preferred_language']) && $params['preferred_language'] != "")) {                                    
                        $languageIdValue = $params['preferred_language'];
                    } 
                    $operationIdValue = -2;
                    $operationId = SysOperationTypes::getTypeIdToGoOperationId(
                                array('parent_id' => 3, 'main_group' => 3, 'sub_grup_id' => 43, 'type_id' => 2,));
                    if (\Utill\Dal\Helper::haveRecord($operationId)) {
                    $operationIdValue = $operationId ['resultSet'][0]['id'];
                    }
                    /*
                     * parametre olarak gelen array deki 'id' li kaydın, info_users tablosundaki 
                     * alanlarını update eder !! username update edilmez.  
                     */
                    $this->updateInfoUsers(array('id' => $userIdValue,
                        'op_user_id' => $userIdValue,
                        'active' => $params['active'],
                        'language_id' => $languageIdValue,
                        'password' => $params['password'],
                        'operation_type_id' => $operationIdValue,
                    ));
                    /*
                     *  parametre olarak gelen array deki 'id' li kaydın, info_users_details tablosundaki 
                     * active = 0 ve deleted = 0 olan kaydın active alanını 1 yapar  !!
                     */
                    $this->setUserDetailsDisables(array('id' => $userIdValue));
                    $sql = " 
                    INSERT INTO info_users_detail(
                           profile_public,
                           operation_type_id,
                           name,
                           surname,
                           auth_email,                            
                           language_id,
                           op_user_id,      
                           root_id,
                           act_parent_id,
                           password,
                           auth_allow_id                           
                           ) 
                           SELECT 
                                " . intval($params['profile_public']) . " AS profile_public,
                                " . intval($operationIdValue) . " AS operation_type_id,
                                '" . $params['name'] . "' AS name, 
                                '" . $params['surname'] . "' AS surname,
                                '" . $params['auth_email'] . "' AS auth_email,   
                                " . intval($languageIdValue). " AS language_id,   
                                " . intval($userIdValue) . " AS user_id,
                                a.root_id AS root_id,
                                a.act_parent_id,
                                '" . $params['password'] . "' AS password ,
                                CASE
                                    (CASE 
                                        (SELECT (z.auth_email = '" . $params['auth_email'] . "') FROM info_users_detail z WHERE z.id = a.id)    
                                         WHEN true THEN 1
                                         ELSE 0  
                                         END ) 
                                     WHEN 1 THEN a.auth_allow_id
                                ELSE 0 END AS auth_allow_id
                            FROM info_users_detail a
                            WHERE a.root_id  =" . intval($params['id']) . " AND
                                a.active =1 AND a.deleted =0 AND 
                                a.c_date = (SELECT MAX(b.c_date)  
						FROM info_users_detail b WHERE b.root_id =a.root_id
						AND b.active =1 AND b.deleted =0)  
                    ";
                    $statementActInsert = $pdo->prepare($sql);
                    //   echo debugPDO($sql, $params);
                    $insertAct = $statementActInsert->execute();
                    $insertID = $pdo->lastInsertId('info_users_detail_id_seq');
                    $errorInfo = $statementActInsert->errorInfo();
                    if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                        throw new \PDOException($errorInfo[0]);
                    $pdo->commit();
                    return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows, "newId" => $insertID);
                } else {
                    $errorInfo = '23505';  // 23505  unique_violation 
                    $pdo->rollback();
                    $result = $kontrol;
                    return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '');
                }
            } else {
                $errorInfo = '23502';  /// 23502 user_id not_null_violation
                $pdo->rollback();
                $result = $kontrol;
                return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '');
            }
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * parametre olarak gelen array deki 'id' li kaydın update ini yapar  !!
     * @author Okan CIRAN
     * @version v 1.0  26.01.2016     
     * @param array | null $args
     * @param type $params
     * @return array
     * @throws PDOException
     */
    public function updateTemp($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $userId = $this->getUserIdTemp(array('pktemp' => $params['pktemp']));
            if (\Utill\Dal\Helper::haveRecord($userId)) {
                $userIdValue = $userId ['resultSet'][0]['user_id'];
                $kontrol = $this->haveRecords($params);
                if (\Utill\Dal\Helper::haveRecord($kontrol)) {
                    $active = 0;                 
                    if ((isset($params['active']) && $params['active'] != "")) {
                        $active= " " . intval($params['active']) ;                                             
                    }                    
                    
                    $languageIdValue = 647;                    
                    if ((isset($params['preferred_language']) && $params['preferred_language'] != "")) {                                    
                        $languageIdValue = $params['preferred_language'];
                    }                   
                    
                    $operationIdValue = -2;
                    $operationId = SysOperationTypes::getTypeIdToGoOperationId(
                                array('parent_id' => 3, 'main_group' => 3, 'sub_grup_id' => 45, 'type_id' => 2,));
                    if (\Utill\Dal\Helper::haveRecord($operationId)) {
                    $operationIdValue = $operationId ['resultSet'][0]['id'];
                    } 
                    
                    /*
                     * parametre olarak gelen array deki 'id' li kaydın, info_users tablosundaki 
                     * alanlarını update eder !! username update edilmez.  
                     */
                    $this->updateInfoUsers(array('id' => $userIdValue,
                        'op_user_id' => $userIdValue,                        
                        'active' => $active,
                        'operation_type_id' => $operationIdValue,
                        'language_id' => $languageIdValue,
                        'password' => $params['password'],                        
                    ));

                    /*
                     *  parametre olarak gelen array deki 'id' li kaydın, info_users_details tablosundaki 
                     * active = 0 ve deleted = 0 olan kaydın active alanını 1 yapar  !!
                     */
                    $this->setUserDetailsDisables(array('id' =>$userIdValue));

                    $sql = " 
                    INSERT INTO info_users_detail(
                           profile_public,  
                           operation_type_id,
                           active,
                           name,
                           surname,
                           auth_email,
                           language_id,
                           op_user_id,
                           root_id,
                           act_parent_id,
                           password 
                            ) 
                           SELECT 
                                " . intval($params['profile_public']) . " AS profile_public, 
                                " . intval($operationIdValue) . " AS operation_type_id,
                                " . intval($active) . " AS active,
                                '" . $params['name'] . "' AS name, 
                                '" . $params['surname'] . "' AS surname,
                                '" . $params['auth_email'] . "' AS auth_email,   
                                '" . $params['preferred_language'] . "' AS language_id,   
                                " . intval($userIdValue) . " AS user_id,
                                a.root_id,
                                a.act_parent_id,
                                '" . $params['password'] . "' AS password
                            FROM info_users_detail a
                            WHERE root_id  =" . intval($userIdValue) . "                               
                                AND active =1 AND deleted =0 and 
                                c_date = (SELECT MAX(c_date)  
						FROM info_users_detail WHERE root_id =a.root_id
						AND active =1 AND deleted =0) 
 
                    ";
                    $statementActInsert = $pdo->prepare($sql);
                  //  echo debugPDO($sql, $params);
                    $affectedRows1 = 1 ; 
                    $insertAct = $statementActInsert->execute();
                    $insertID = $pdo->lastInsertId('info_users_detail_id_seq');
                    $errorInfo = $statementActInsert->errorInfo();
                    if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                        throw new \PDOException($errorInfo[0]);
                    $pdo->commit();
                    return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows1);
                } else {
                    $errorInfo = '23505';  // 23505  unique_violation 
                    $pdo->rollback();
                    $result = $kontrol;
                    return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '');
                }
            } else {
                $errorInfo = '23502';  /// 23502 user_id not_null_violation
                $pdo->rollback();
                $result = $kontrol;
                return array("found" => false, "errorInfo" => $errorInfo, "resultSet" => '');
            }
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     *       
     * parametre olarak gelen array deki 'id' li kaydın, info_users_details tablosundaki 
     * active = 0 ve deleted = 0 olan kaydın active alanını 1 yapar  !!
     * @author Okan CIRAN
     * @version v 1.0  29.01.2016
     * @param array $params 
     * @return array
     * @throws \PDOException
     */
    public function setUserDetailsDisables($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            // $pdo->beginTransaction();           
            $sql = "
                UPDATE info_users_detail
                    SET
                        c_date =  timezone('Europe/Istanbul'::text, ('now'::text)::timestamp(0) with time zone) , 
                        active = 1 
                    WHERE root_id = :id AND active = 0 AND deleted = 0 
                    ";
             $statement = $pdo->prepare($sql); 
            $statement->bindValue(':id', $params['id'], \PDO::PARAM_INT);
          //  echo debugPDO($sql, $params);
            $update = $statement->execute();
            $affectedRows = $statement->rowCount();
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);           
            return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows);
        } catch (\PDOException $e /* Exception $e */) {         
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     *       
     * parametre olarak gelen array deki 'id' li kaydın, info_users tablosundaki 
     * alanlarını update eder !! username update edilmez. 
     * @author Okan CIRAN
     * @version v 1.0  29.01.2016
     * @param array $params 
     * @return array
     * @throws \PDOException
     */
    public function updateInfoUsers($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory'); 
            $statement = $pdo->prepare("
                UPDATE info_users
                    SET
                        c_date = timezone('Europe/Istanbul'::text, ('now'::text)::timestamp(0) with time zone) ,                         
                        operation_type_id = :operation_type_id,
                        password = :password, 
                        language_id = :language_id,                        
                        op_user_id = :op_user_id ,
                        active = :active
                    WHERE id = :id  
                    ");
            $statement->bindValue(':id', $params['id'], \PDO::PARAM_INT);            
            $statement->bindValue(':active', $params['active'], \PDO::PARAM_INT);                        
            $statement->bindValue(':operation_type_id', $params['operation_type_id'], \PDO::PARAM_INT);
            $statement->bindValue(':password', $params['password'], \PDO::PARAM_STR);
            $statement->bindValue(':language_id', $params['language_id'], \PDO::PARAM_INT);            
            $statement->bindValue(':op_user_id', $params['op_user_id'], \PDO::PARAM_INT);
            $update = $statement->execute();
            $affectedRows = $statement->rowCount();
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);         
            return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows);
        } catch (\PDOException $e /* Exception $e */) {         
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
        $whereSql = "" ;
        if (isset($args['sort']) && $args['sort'] != "") {
            $sort = trim($args['sort']);
            $sortArr = explode(",", $sort);
            if (count($sortArr) === 1)
                $sort = trim($args['sort']);
        } else {
            $sort = "ad.name, ad.surname";
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
        
        $languageId = NULL;
        $languageIdValue = 647;
        if ((isset($args['language_code']) && $args['language_code'] != "")) {                
            $languageId = SysLanguage::getLanguageId(array('language_code' => $args['language_code']));
            if (\Utill\Dal\Helper::haveRecord($languageId)) {
                $languageIdValue = $languageId ['resultSet'][0]['id'];                    
            }
        }  
         
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $sql = "    
                   SELECT
                        a.id, 
                        ad.profile_public,                  
                        a.s_date, 
                        a.c_date, 
                        a.operation_type_id,                        
                        COALESCE(NULLIF(opx.operation_name, ''), op.operation_name_eng) AS operation_name,
                        ad.name, 
                        ad.surname, 
                        a.username, 
                        a.password, 
                        ad.auth_email,                   
                        ad.language_code, 
                        ad.language_id, 
                        l.language_eng as user_language,
			COALESCE(NULLIF(lx.id, NULL), 385) AS language_id,
		        COALESCE(NULLIF(lx.language, ''), 'en') AS language_name,                        
                        a.active,                         
                        COALESCE(NULLIF(sd16x.description, ''), sd16.description_eng) AS state_active, 
                        ad.deleted,
                        COALESCE(NULLIF(sd15x.description, ''), sd15.description_eng) AS state_deleted,  			
                        a.op_user_id,
                        u.username AS op_user_name,
                        ad.act_parent_id, 
                        ad.auth_allow_id,                         
                        COALESCE(NULLIF(sd13x.description, ''), sd13.description_eng) AS auth_alow, 
                        ad.cons_allow_id,                        
                        COALESCE(NULLIF(sd14x.description, ''), sd14.description_eng) AS cons_allow,                   
                        ad.root_id,
                        a.consultant_id,
                        cons.name AS cons_name, 
                        cons.surname AS cons_surname,			 
                        COALESCE(NULLIF(sd19x.description, ''), sd19.description_eng) AS state_profile_public                        
                    FROM info_users a
                    INNER JOIN sys_language l ON l.id = a.language_id AND l.deleted =0 AND l.active =0                     
                    LEFT JOIN sys_language lx ON lx.id = ".intval($languageIdValue)." AND lx.deleted =0 AND lx.active =0                     
                    INNER JOIN info_users_detail ad ON ad.deleted =0 AND ad.active =0 AND ad.root_id = a.id AND ad.language_parent_id = 0 
                    LEFT JOIN sys_operation_types op ON op.id = a.operation_type_id AND op.deleted =0 AND op.active =0 AND op.language_parent_id =0
                    LEFT JOIN sys_operation_types opx ON (opx.id = a.operation_type_id OR opx.language_parent_id = a.operation_type_id) and opx.language_id =lx.id  AND opx.deleted =0 AND opx.active =0 
		    
		    INNER JOIN sys_specific_definitions sd13 ON sd13.main_group = 13 AND ad.auth_allow_id = sd13.first_group AND sd13.deleted =0 AND sd13.active =0 AND sd13.language_parent_id =0
		    INNER JOIN sys_specific_definitions sd14 ON sd14.main_group = 14 AND ad.cons_allow_id = sd14.first_group AND sd14.deleted =0 AND sd14.active =0 AND sd14.language_parent_id =0
		    INNER JOIN sys_specific_definitions sd15 ON sd15.main_group = 15 AND sd15.first_group= a.deleted AND sd15.deleted =0 AND sd15.active =0 AND sd15.language_parent_id =0
		    INNER JOIN sys_specific_definitions sd16 ON sd16.main_group = 16 AND sd16.first_group= a.active AND sd16.deleted = 0 AND sd16.active = 0 AND sd16.language_parent_id =0
		    INNER JOIN sys_specific_definitions sd19 ON sd19.main_group = 19 AND sd19.first_group= ad.profile_public AND sd19.deleted = 0 AND sd19.active = 0 AND sd19.language_parent_id =0

                    LEFT JOIN sys_specific_definitions sd13x ON sd13x.main_group = 13 AND sd13x.language_id = lx.id AND (sd13x.id = sd13.id OR sd13x.language_parent_id = sd13.id) AND sd13x.deleted =0 AND sd13x.active =0
                    LEFT JOIN sys_specific_definitions sd14x ON sd14x.main_group = 14 AND sd14x.language_id = lx.id AND (sd14x.id = sd14.id OR sd14x.language_parent_id = sd14.id) AND sd14x.deleted =0 AND sd14x.active =0
                    LEFT JOIN sys_specific_definitions sd15x ON sd15x.main_group = 15 AND sd15x.language_id =lx.id AND (sd15x.id = sd15.id OR sd15x.language_parent_id = sd15.id) AND sd15x.deleted =0 AND sd15x.active =0 
                    LEFT JOIN sys_specific_definitions sd16x ON sd16x.main_group = 16 AND sd16x.language_id = lx.id AND (sd16x.id = sd16.id OR sd16x.language_parent_id = sd16.id) AND sd16x.deleted = 0 AND sd16x.active = 0
                    LEFT JOIN sys_specific_definitions sd19x ON sd19x.main_group = 19 AND sd19x.language_id = lx.id AND (sd19x.id = sd19.id OR sd19x.language_parent_id = sd19.id) AND sd19x.deleted = 0 AND sd19x.active = 0
                    
                    INNER JOIN info_users u ON u.id = a.op_user_id                        
                    LEFT JOIN info_users_detail cons ON cons.root_id = a.consultant_id AND cons.cons_allow_id =1 
                 
                    WHERE a.deleted =0  
                    ".$whereSql."                   
                    ORDER BY  " . $sort . " "
                    . "" . $order . " "
                    . "LIMIT " . $pdo->quote($limit) . " "
                    . "OFFSET " . $pdo->quote($offset) . " ";
             
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
                        count(a.id) as count                                 
                    FROM info_users a
                    INNER JOIN sys_language l ON l.id = a.language_id AND l.deleted =0 AND l.active =0                                         
                    INNER JOIN info_users_detail ad ON ad.deleted =0 AND ad.active =0 AND ad.root_id = a.id AND ad.language_parent_id = 0 
                    INNER JOIN sys_specific_definitions sd13 ON sd13.main_group = 13 AND ad.auth_allow_id = sd13.first_group AND sd13.deleted =0 AND sd13.active =0 AND sd13.language_parent_id =0
		    INNER JOIN sys_specific_definitions sd14 ON sd14.main_group = 14 AND ad.cons_allow_id = sd14.first_group AND sd14.deleted =0 AND sd14.active =0 AND sd14.language_parent_id =0
		    INNER JOIN sys_specific_definitions sd15 ON sd15.main_group = 15 AND sd15.first_group= a.deleted AND sd15.deleted =0 AND sd15.active =0 AND sd15.language_parent_id =0
		    INNER JOIN sys_specific_definitions sd16 ON sd16.main_group = 16 AND sd16.first_group= a.active AND sd16.deleted = 0 AND sd16.active = 0 AND sd16.language_parent_id =0
		    INNER JOIN sys_specific_definitions sd19 ON sd19.main_group = 19 AND sd19.first_group= ad.profile_public AND sd19.deleted = 0 AND sd19.active = 0 AND sd19.language_parent_id =0
                    INNER JOIN info_users u ON u.id = a.op_user_id                      
                    WHERE a.deleted = 0 
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
     * @param type $id
     * @param type $params
     * @return array
     * @throws PDOException
     */
    public function deletedAct($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $userId = $this->getUserId(array('pk' => $params['pk']));
            if (\Utill\Dal\Helper::haveRecord($userId)) {
                $userIdValue = $userId ['resultSet'][0]['user_id']; 
                $this->setUserDetailsDisables(array('id' => $userIdValue));
                $this->makeUserDeleted(array('id' => $userIdValue));
                $sql = " 
                    INSERT INTO info_users_detail(
                           profile_public,   
                           f_check,
                           s_date,
                           c_date,
                           operation_type_id, 
                           name,
                           surname,                                                                        
                           auth_email,  
                           act_parent_id,
                           auth_allow_id,
                           cons_allow_id,
                           language_code,
                           language_id,
                           root_id,
                           op_user_id,
                           language_id,
                           password,                           
                           active,
                           deleted,
                            ) 
                           SELECT 
                                profile_public,                           
                                f_check,   
                                s_date,                              
                                timezone('Europe/Istanbul'::text, ('now'::text)::timestamp(0) with time zone) , 
                                3,
                                name,
                                surname,                                                                        
                                auth_email,  
                                act_parent_id,
                                auth_allow_id,
                                cons_allow_id,
                                language_code,
                                language_id,
                                root_id,                               
                                " . intval($userIdValue) . " AS op_user_id,
                                language_id,
                                password,   
                               1,
                               1
                            FROM info_users_detail 
                            WHERE root_id  =" . intval($userIdValue) . " 
                                AND active =0 AND deleted =0 
 
                    "; 
                $statement_act_insert = $pdo->prepare($sql);   
                $insert_act_insert = $statement_act_insert->execute();
                $affectedRows = $statement_act_insert->rowCount();
                $errorInfo = $statement_act_insert->errorInfo();
                if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                    throw new \PDOException($errorInfo[0]);
                $pdo->commit();
                return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows);
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
     *       
     * parametre olarak gelen array deki 'id' li kaydın, info_users tablosundaki private key ve value değerlerini oluşturur  !!
     * @author Okan CIRAN
     * @version v 1.0  26.01.2016
     * @param array $params 
     * @return array
     * @throws \PDOException
     */
    public function setPrivateKey($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');                    
            $statement = $pdo->prepare("
                UPDATE info_users
                SET              
                    sf_private_key = armor( pgp_sym_encrypt (username , oid, 'compress-algo=1, cipher-algo=bf')) ,
                    sf_private_key_temp = armor( pgp_sym_encrypt (username , oid_temp, 'compress-algo=1, cipher-algo=bf'))                   
                WHERE                   
                    id = :id");
            $statement->bindValue(':id', $params['id'], \PDO::PARAM_INT);
            $update = $statement->execute();
            $affectedRows = $statement->rowCount();
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            $statementValue = $pdo->prepare("
                UPDATE info_users
                SET              
                    sf_private_key_value = substring(sf_private_key,40,length( trim( sf_private_key))-140)   ,
                    sf_private_key_value_temp = substring(sf_private_key_temp,40,length( trim( sf_private_key_temp))-140)  
                WHERE                     
                    id = :id");
            $statementValue->bindValue(':id', $params['id'], \PDO::PARAM_INT);
            $updateValue = $statementValue->execute();
            $affectedRows = $statementValue->rowCount();
            $errorInfo = $statementValue->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);         
            return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows);
        } catch (\PDOException $e /* Exception $e */) {         
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * parametre olarak gelen array deki 'id' li kaydın, info_users tablosundaki  public key temp değerini döndürür !!
     * @author Okan CIRAN
     * @version v 1.0  26.01.2016
     * @param array $params 
     * @return array
     * @throws \PDOException
     */
    public function getPublicKeyTemp($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $sql = " 
                SELECT 
                    REPLACE(TRIM(SUBSTRING(crypt(sf_private_key_value_temp,gen_salt('xdes')),6,20)),'/','*') as pk_temp ,              
                    id =" .intval( $params['id']) . " AS control
                FROM info_users 
                WHERE 
                     id =" .intval( $params['id']) . "
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
     * parametre olarak gelen array deki 'pk' nın, info_users tablosundaki user_id si değerini döndürür !!
     * @author Okan CIRAN
     * @version v 1.0  26.01.2016
     * @param array $params 
     * @return array
     * @throws \PDOException
     */
    public function getUserId($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $sql = "  
                 SELECT id AS user_id, 1=1 AS control FROM (
                            SELECT id , 	
                                CRYPT(sf_private_key_value,CONCAT('_J9..',REPLACE('" . $params['pk'] . "','*','/'))) = CONCAT('_J9..',REPLACE('" . $params['pk'] . "','*','/')) as pkey                                
                            FROM info_users WHERE active =0 AND deleted =0) AS logintable
                        WHERE pkey = TRUE 
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
     * parametre olarak gelen array deki 'pk' yada 'pktemp' için info_users tablosundaki user_id si değerini döndürür !!
     * @author Okan CIRAN
     * @version v 1.0  27.01.2016
     * @param array $params 
     * @return array
     * @throws \PDOException
     */
    public function getUserIdTemp($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $sql = "  
                 SELECT id AS user_id , 1=1 AS control FROM (
                            SELECT id, 	                              
                                CRYPT(sf_private_key_value_Temp,CONCAT('_J9..',REPLACE('" . $params['pktemp'] . "','*','/'))) = CONCAT('_J9..',REPLACE('" . $params['pktemp'] . "','*','/')) AS pkeytemp                                    
                            FROM info_users WHERE active =0 AND deleted =0) AS logintable
                        WHERE pkeytemp = TRUE 
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
     *       
     * parametre olarak gelen array deki 'id' li kaydın, act_users_rrpmap tablosunda "New User" rolu verilerek kaydı oluşturulur. !!
     * insertTemp ve insert fonksiyonlarında kullanılacak.  
     * @author Okan CIRAN
     * @version v 1.0  27.01.2016
     * @param array $params 
     * @return array
     * @throws \PDOException
     */
    public function setNewUserRrpMap($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            // $pdo->beginTransaction();           
            $statement = $pdo->prepare("
                INSERT INTO act_users_rrpmap(
                    info_users_id, rrpmap_id, user_id)
                VALUES (
                    :id, 
                    8,
                    :user_id )
                    ");
            $statement->bindValue(':id', $params['id'], \PDO::PARAM_INT);
            if ($params['user_id'] == 0)
                $statement->bindValue(':user_id', $params['id'], \PDO::PARAM_INT);
            else
                $statement->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);
            $update = $statement->execute();
            $affectedRows = $statement->rowCount();
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);     
            return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows);
        } catch (\PDOException $e /* Exception $e */) {  
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     *       
     * parametre olarak gelen array deki 'id' li kaydın, act_users_rrpmap tablosunda "New User" rolu verilerek kaydı oluşturulur. !!
     * insertTemp ve insert fonksiyonlarında kullanılacak.  
     * @author Okan CIRAN
     * @version v 1.0  27.01.2016
     * @param array $params 
     * @return array
     * @throws \PDOException
     */
    public function updateNewUserRrpMap($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');                  
            $statement = $pdo->prepare("
                INSERT INTO act_users_rrpmap(
                    info_users_id, rrpmap_id, user_id)
                VALUES (
                    :id, 
                    8,
                    :user_id )
                    ");
            $statement->bindValue(':id', $params['id'], \PDO::PARAM_INT);
            if ($params['user_id'] == 0)
                $statement->bindValue(':user_id', $params['id'], \PDO::PARAM_INT);
            else
                $statement->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);
            $update = $statement->execute();
            $affectedRows = $statement->rowCount();
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows);
        } catch (\PDOException $e /* Exception $e */) {   
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }
 
    
   /**
     * log databasine yeni kullanıcı için kayıt ekler           
     * @author Okan CIRAN
     * @version v 1.0  09.03.2016
     * @param array $params 
     * @return array
     * @throws \PDOException
     */
    public function insertLogUser($params = array()) {
        try {         
            $pdoLog = $this->slimApp->getServiceManager()->get('pgConnectLogFactory');
            $pdoLog->beginTransaction();
                $sql = " 
                    INSERT INTO info_users(
                        username, 
                        sf_private_key_value, 
                        sf_private_key_value_temp,
                        oid
                        ) 
                    VALUES (
                        :username, 
                        :sf_private_key_value, 
                        :sf_private_key_value_temp,
                        :oid
                        )     
                    "; 
                $statement_log_insert = $pdoLog->prepare($sql);  
                $statement_log_insert->bindValue(':username', $params['username'], \PDO::PARAM_STR);
                $statement_log_insert->bindValue(':sf_private_key_value', $params['sf_private_key_value'], \PDO::PARAM_STR);
                $statement_log_insert->bindValue(':sf_private_key_value_temp', $params['sf_private_key_value_temp'], \PDO::PARAM_STR);
                $statement_log_insert->bindValue(':oid', $params['oid'], \PDO::PARAM_INT);
               //  echo debugPDO($sql, $params);
                $insert_log = $statement_log_insert->execute();
                $affectedRows = $statement_log_insert->rowCount();
                $errorInfo = $statement_log_insert->errorInfo();
                if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                    throw new \PDOException($errorInfo[0]);
                $pdoLog->commit();
                return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows);
           
        } catch (\PDOException $e /* Exception $e */) {
            $pdoLog->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    
    /**
     * parametre olarak gelen array deki 'id' li kaydın, info_users tablosundaki username ve private key değerlerini döndürür !!
     * @author Okan CIRAN
     * @version v 1.0  09.03.2016
     * @param array $params 
     * @return array
     * @throws \PDOException
     */
    public function getUsernamePrivateKey($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $sql = " 
                SELECT 
                    id,
                    username, 
                    sf_private_key_value, 
                    sf_private_key_value_temp
                FROM info_users 
                WHERE 
                     id =" .intval( $params['id']) . "
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
     * user interface fill operation    
     * @author Okan CIRAN
     * @ userin firm id sini döndürür döndürür !!
     * su an için sadece 1 firması varmış senaryosu için gecerli. 
     * @version v 1.0  29.02.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function getUserFirmId($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            if (isset($params['user_id'])) {
                $userIdValue = $params['user_id'];
                $sql = " 
                SELECT                    
                   ifu.firm_id,
                   1=1 control
                FROM info_firm_machine_tool a		
		INNER JOIN info_firm_users ifu ON ifu.user_id = " . intval($userIdValue) . " AND ifu.language_parent_id = 0 AND ifu.firm_id = a.firm_id                
                WHERE a.deleted =0 AND 
                      a.active =0 AND
                      a.language_parent_id =0 
                limit 1
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
    
}
