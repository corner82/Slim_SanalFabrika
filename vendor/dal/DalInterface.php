<?php
/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */

namespace DAL;

/**
 * interface to used for CRUD operations on DAl layer
 * @author Mustafa Zeynel Dağlı
 */
interface DalInterface {
    public function getAll_sysNavigationLeft();
    public function update_sysNavigationLeft($id = null, $params = array());
    public function delete_sysNavigationLeft ($id = null);
    public function insert_sysNavigationLeft($params = array());
}

