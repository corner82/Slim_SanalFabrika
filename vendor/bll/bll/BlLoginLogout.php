<?php
/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */

namespace BLL\BLL;

/**
 * Business Layer class for report Configuration entity
 */
class BlLoginLogout extends \BLL\BLLSlim{
    
    /**
     * constructor
     */
    public function __construct() {
        //parent::__construct();
    }
    
    /**
     * DAta insert function
     * @param array | null $params
     * @return array
     */
    public function insert($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('blLoginLogoutPDO');
        return $DAL->insert($params);
    }
    
    /**
     * Data update function
     * @param integer $id
     * @param array | null $params
     * @return array
     */
    public function update($id = null, $params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('blLoginLogoutPDO');
        return $DAL->update($id, $params);
    }
    
    /**
     * Data delete function
     * @param integer $id
     * @return array
     */
    public function delete($id = null) {
        $DAL = $this->slimApp->getDALManager()->get('blLoginLogoutPDO');
        return $DAL->delete($id);
    }

    /**
     * get all data
     * @return array
     */
    public function getAll() {
        $DAL = $this->slimApp->getDALManager()->get('blLoginLogoutPDO');
        return $DAL->getAll();
    }
    
    
    /**
     *  
     * @param array | null $params
     * @return array
     */
    public function pkControl($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('blLoginLogoutPDO');
        $resultSet = $DAL->pkControl($params);  
        return $resultSet['resultSet'];
    }

    
    public function pkLoginControl($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('blLoginLogoutPDO');
        $resultSet = $DAL->pkLoginControl($params);  
        return $resultSet['resultSet'];
    }

    public function getPK($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('blLoginLogoutPDO');
        $resultSet = $DAL->getPK($params);  
        return $resultSet['resultSet'];
    }

       
    public function pkSessionControl($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('blLoginLogoutPDO');
        $resultSet = $DAL->pkSessionControl($params);  
        return $resultSet['resultSet'];
    }

     /**
     *  
     * @param array | null $params
     * @return array
     */
    public function pkIsThere($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('blLoginLogoutPDO');
        $resultSet = $DAL->pkIsThere($params);  
        return $resultSet['resultSet'];
    }  
    
     /**
     *  
     * @param array | null $params
     * @return array
     */
    public function pkAllPkGeneratedFromPrivate($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('blLoginLogoutPDO');
        $resultSet = $DAL->pkAllPkGeneratedFromPrivate($params);  
        return $resultSet['resultSet'];
    }   
    
    
    
    
    
    
}

