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
              SELECT 
		    a.id, 		   
		    COALESCE(NULLIF(a.menu_name, ''), a.menu_name_eng) AS menu_name, 
		    a.menu_name_eng,  
		    a.url, 
		    a.parent, 
		    a.icon_class, 
		    a.page_state, 
		    a.collapse, 
		    sd.description as state_deleted,                 
                    a.active, 
		    sd1.description as state_active, 
		    a.language_code, 
		    COALESCE(NULLIF(l.language_eng, ''), l.language) AS language_name,  		        
		    a.warning, 
		    a.warning_type, 
		    a.hint, 
		    a.z_index, 
		    a.language_parent_id, 
		    a.hint_eng, 
		    a.warning_class,
                    a.user_id,
                    u.username,
                    a.acl_type,
                     (select COALESCE(NULLIF(max(ax.active), 0),0)+COALESCE(NULLIF(max(bx.active), 0),0)+COALESCE(NULLIF(max(cx.active), 0),0)+
			COALESCE(NULLIF(max(dx.active), 0),0) +COALESCE(NULLIF(max(ex.active), 0),0)+ COALESCE(NULLIF(max(fx.active), 0),0)+
			COALESCE(NULLIF(max(gx.active), 0),0) 
			from sys_navigation_left ax 
			left join sys_navigation_left bx on ax.parent = bx.id
			left join sys_navigation_left cx on bx.parent = cx.id 
			left join sys_navigation_left dx on cx.parent = dx.id
			left join sys_navigation_left ex on dx.parent = ex.id
			left join sys_navigation_left fx on ex.parent = fx.id
			left join sys_navigation_left gx on fx.parent = gx.id
			where ax.id = a.id ) as active_control
		FROM sys_navigation_left a                 
		INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group= a.deleted AND sd.language_code = a.language_code AND sd.deleted = 0 AND sd.active = 0
		INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 16 AND sd1.first_group= a.active AND sd1.language_code = a.language_code AND sd1.deleted = 0 AND sd1.active = 0		
		INNER JOIN sys_language l ON l.id = a.language_code AND l.deleted =0 AND l.active = 0 
		INNER JOIN info_users u ON u.id = a.user_id  
                ORDER BY a.parent, a.z_index
                
                             
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
                    language_code, 
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
                    warning_class,
                    user_id,                    
                    acl_type)    
                VALUES (
                        :menu_name, 
                        :language_code, 
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
                        :warning_class,
                        :user_id,
                        :acl_type)
                                                ");
            $statement->bindValue(':menu_name', $params['menu_name'], \PDO::PARAM_STR);
            $statement->bindValue(':language_code', $params['language_code'], \PDO::PARAM_STR);
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
            $statement->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);
            $statement->bindValue(':acl_type', $params['acl_type'], \PDO::PARAM_INT);

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
                    language_code = :language_code, 
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
                    warning_class = :warning_class ,
                    user_id = :user_id,
                    acl_type = :acl_type
                WHERE id = :id");
            //Bind our value to the parameter :id.

            $statement->bindValue(':id', $id, \PDO::PARAM_INT);
            //Bind our :model parameter.
            $statement->bindValue(':menu_name', $params['menu_name'], \PDO::PARAM_STR);
            $statement->bindValue(':language_code', $params['language_code'], \PDO::PARAM_STR);
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
            $statement->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);
            $statement->bindValue(':acl_type', $params['acl_type'], \PDO::PARAM_INT); 
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
 /// su  an aktif  kullanılmıyor. language code a göre değiştirilecek oki..

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
		    COALESCE(NULLIF(a.menu_name, ''), a.menu_name_eng) AS menu_name, 
		    a.menu_name_eng,  
		    a.url, 
		    a.parent, 
		    a.icon_class, 
		    a.page_state, 
		    a.collapse, 
		    sd.description as state_deleted,                 
                    a.active, 
		    sd1.description as state_active, 
		    a.language_code, 
		    COALESCE(NULLIF(l.language_eng, ''), l.language) AS language_name,  		        
		    a.warning, 
		    a.warning_type, 
		    a.hint, 
		    a.z_index, 
		    a.language_parent_id, 
		    a.hint_eng, 
		    a.warning_class,
                    a.user_id,
                    u.username,
                    a.acl_type,
                     (select COALESCE(NULLIF(max(ax.active), 0),0)+COALESCE(NULLIF(max(bx.active), 0),0)+COALESCE(NULLIF(max(cx.active), 0),0)+
			COALESCE(NULLIF(max(dx.active), 0),0) +COALESCE(NULLIF(max(ex.active), 0),0)+ COALESCE(NULLIF(max(fx.active), 0),0)+
			COALESCE(NULLIF(max(gx.active), 0),0) 
			from sys_navigation_left ax 
			left join sys_navigation_left bx on ax.parent = bx.id
			left join sys_navigation_left cx on bx.parent = cx.id 
			left join sys_navigation_left dx on cx.parent = dx.id
			left join sys_navigation_left ex on dx.parent = ex.id
			left join sys_navigation_left fx on ex.parent = fx.id
			left join sys_navigation_left gx on fx.parent = gx.id
			where ax.id = a.id ) as active_control
		FROM sys_navigation_left a                 
		INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group= a.deleted AND sd.language_code = a.language_code AND sd.deleted = 0 AND sd.active = 0
		INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 16 AND sd1.first_group= a.active AND sd1.language_code = a.language_code AND sd1.deleted = 0 AND sd1.active = 0		
		INNER JOIN sys_language l ON l.id = a.language_code AND l.deleted =0 AND l.active = 0 
		INNER JOIN info_users u ON u.id = a.user_id  
                where a.language_code = :language_code 
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
     * @ Gridi doldurmak için sys_navigation_left tablosundan çekilen kayıtlarının kaç tane olduğunu döndürür   !!
     * @version v 1.0  14.12.2015
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillGridRowTotalCount($params = array()) {
        // su an kullanılmıyor. sql  language code gore ayarlanacak.. oki.. 
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $sql = "
                    SELECT 
			COUNT(a.id) AS COUNT , 
			(SELECT COUNT(a1.id) FROM sys_navigation_left a1 
			INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 15 AND sd1.first_group= a1.deleted AND sd1.language_code = a1.language_code AND sd1.deleted = 0 AND sd1.active = 0
			INNER JOIN sys_specific_definitions sd11 ON sd11.main_group = 16 AND sd11.first_group= a1.active AND sd11.language_code = a1.language_code AND sd11.deleted = 0 AND sd11.active = 0		
			INNER JOIN sys_language l1 ON l1.id = a1.language_code AND l1.deleted =0 AND l1.active = 0 
			INNER JOIN info_users u1 ON u1.id = a1.user_id  
			WHERE a1.language_code = :language_code AND a1.deleted =0) AS undeleted_count, 
			(SELECT COUNT(a2.id)
			FROM sys_navigation_left a2                 
			INNER JOIN sys_specific_definitions sd2 ON sd2.main_group = 15 AND sd2.first_group= a2.deleted AND sd2.language_code = a2.language_code AND sd2.deleted = 0 AND sd2.active = 0
			INNER JOIN sys_specific_definitions sd12 ON sd12.main_group = 16 AND sd12.first_group= a2.active AND sd12.language_code = a2.language_code AND sd12.deleted = 0 AND sd12.active = 0		
			INNER JOIN sys_language l2 ON l2.id = a2.language_code AND l2.deleted =0 AND l2.active = 0 
			INNER JOIN info_users u2 ON u2.id = a2.user_id  
			WHERE a2.language_code = :language_code AND a2.deleted =1) AS deleted_count  
		FROM sys_navigation_left a                 
		INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group= a.deleted AND sd.language_code = a.language_code AND sd.deleted = 0 AND sd.active = 0
		INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 16 AND sd1.first_group= a.active AND sd1.language_code = a.language_code AND sd1.deleted = 0 AND sd1.active = 0		
		INNER JOIN sys_language l ON l.id = a.language_code AND l.deleted =0 AND l.active = 0 
		INNER JOIN info_users u ON u.id = a.user_id  		 
                WHERE a.language_code = '".$params['language_code']."'  
                    ";
            $statement = $pdo->prepare($sql);
          //  $statement->bindValue(':language_code', $args['language_code'], \PDO::PARAM_INT);
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
    public function pkGetLeftMenu($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            /**
             * table names and column names will be changed for specific use
             */
            
            $sql = "
                SELECT a.id, 
                    COALESCE(NULLIF(a.menu_name, ''), a.menu_name_eng) AS menu_name, 
                    a.language_id, 
                    a.menu_name_eng, 
                    a.url, 
                    a.parent, 
                    a.icon_class, 
                    a.page_state, 
                    a.collapse, 
                    a.active, 
                    a.deleted, 
                    CASE 
                        WHEN a.deleted = 0 THEN 'Aktif' 
                        WHEN a.deleted = 1 THEN 'Silinmiş' 
                    END AS state,    
                    a.warning, 
                    a.warning_type, 
                    COALESCE(NULLIF(hint, ''), hint_eng) AS hint, 
                    a.z_index, 
                    a.language_parent_id, 
                    a.hint_eng, 
                    a.warning_class,
                    a.acl_type,
                    a.language_code,
                    (   SELECT COALESCE(NULLIF(max(ax.active), 0),0)+COALESCE(NULLIF(max(bx.active), 0),0)+COALESCE(NULLIF(max(cx.active), 0),0)+
                            COALESCE(NULLIF(max(dx.active), 0),0) +COALESCE(NULLIF(max(ex.active), 0),0)+ COALESCE(NULLIF(max(fx.active), 0),0)+
                            COALESCE(NULLIF(max(gx.active), 0),0) 
                        FROM sys_navigation_left ax 
			LEFT JOIN sys_navigation_left bx ON ax.parent = bx.id
			LEFT JOIN sys_navigation_left cx ON bx.parent = cx.id 
			LEFT JOIN sys_navigation_left dx ON cx.parent = dx.id
			LEFT JOIN sys_navigation_left ex ON dx.parent = ex.id
			LEFT JOIN sys_navigation_left fx ON ex.parent = fx.id
			LEFT JOIN sys_navigation_left gx ON fx.parent = gx.id
			WHERE ax.id = a.id ) AS active_control,
			a.menu_type			
                FROM sys_navigation_left a 
                INNER JOIN info_users iu ON iu.active =0 AND iu.deleted =0	     	
                INNER JOIN act_session ssx ON CRYPT(iu.sf_private_key_value,CONCAT('_J9..',REPLACE(ssx.public_key,'*','/'))) = CONCAT('_J9..',REPLACE(ssx.public_key,'*','/'))  
                WHERE a.language_code = 
                    (SELECT COALESCE(NULLIF((SELECT language_main_code FROM sys_language lz WHERE lz.language_main_code = '".$params['language_code']."' AND lz.deleted =0 AND lz.active =0 ),''),'en')) AND                        
                    acl_type = 0 AND 
                    a.active = 0 AND 
                    a.deleted = 0 AND 
                    a.parent = ".intval($params['parent'])." AND                    
                    a.menu_type = CAST(
                      (SELECT                               
                          COALESCE(NULLIF( 
                         (SELECT CAST(MIN(bz.parent) AS varchar(5))
                                           FROM sys_acl_roles az 
                                           LEFT JOIN sys_acl_roles bz ON bz.parent = az.id   
                                           WHERE az.id= sarmapv.role_id),''), CAST(sarv.parent AS varchar(5))) AS Menu_type  
                         FROM info_users av
                         INNER JOIN act_users_rrpmap usrv ON usrv.info_users_id = av.id AND usrv.active = 0 AND usrv.deleted = 0 
                         INNER JOIN sys_acl_rrpmap sarmapv ON sarmapv.id = usrv.rrpmap_id AND sarmapv.active=0 AND sarmapv.deleted =0 
                         INNER JOIN sys_acl_roles sarv ON sarv.id = sarmapv.role_id AND sarv.active=0 AND sarv.deleted=0 
                         INNER JOIN act_session sszv ON CRYPT(av.sf_private_key_value,CONCAT('_J9..',REPLACE(sszv.public_key,'*','/'))) = CONCAT('_J9..',REPLACE(sszv.public_key,'*','/'))  
                         WHERE av.active =0 and av.deleted =0 AND sszv.public_key = ssx.public_key 
                      ) as integer) AND
                      ssx.public_key = '".$params['pk']."'                     

                ORDER BY a.parent, a.z_index

                                 ";           
            $statement = $pdo->prepare($sql);
          //  $statement->bindValue(':parent',  $params['parent'], \PDO::PARAM_INT);
         
         //   $statement->bindValue(':language_code', $params['language_code'], \PDO::PARAM_STR);
        //    $statement->bindValue(':public_key', $params['pk'], \PDO::PARAM_STR);
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
     * 
     * @return type
     * @version bu  fonksiyon kullanılmıyor.
     */
    public function getLeftMenuFull() {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            /**
             * table names and column names will be changed for specific use
             */
            $sql = "SELECT a.id, 
                    COALESCE(NULLIF(a.menu_name, ''), a.menu_name_eng) AS menu_name, 
                    a.language_code, 
                    a.menu_name_eng, 
                    a.url, 
                    a.parent, 
                    a.icon_class, 
                    a.page_state, 
                    a.collapse, 
                    a.active, 
                    a.deleted, 
                    CASE 
                            WHEN a.deleted = 0 THEN 'Aktif' 
                            WHEN a.deleted = 1 THEN 'Silinmiş' 
                    END AS state,    
                    a.warning, 
                    a.warning_type, 
                    COALESCE(NULLIF(hint, ''), hint_eng) AS hint, 
                    a.z_index, 
                    a.language_parent_id, 
                    a.hint_eng, 
                    a.warning_class,
                    a.acl_type,
                     (select COALESCE(NULLIF(max(ax.active), 0),0)+COALESCE(NULLIF(max(bx.active), 0),0)+COALESCE(NULLIF(max(cx.active), 0),0)+
			COALESCE(NULLIF(max(dx.active), 0),0) +COALESCE(NULLIF(max(ex.active), 0),0)+ COALESCE(NULLIF(max(fx.active), 0),0)+
			COALESCE(NULLIF(max(gx.active), 0),0) 
			from sys_navigation_left ax 
			left join sys_navigation_left bx on ax.parent = bx.id
			left join sys_navigation_left cx on bx.parent = cx.id 
			left join sys_navigation_left dx on cx.parent = dx.id
			left join sys_navigation_left ex on dx.parent = ex.id
			left join sys_navigation_left fx on ex.parent = fx.id
			left join sys_navigation_left gx on fx.parent = gx.id
			where ax.id = a.id ) as active_control
              FROM sys_navigation_left a 
              WHERE a.language_code = 647
              AND acl_type = 0                
              ORDER BY a.parent, a.z_index 
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

}
