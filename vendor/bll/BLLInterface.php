<?php
/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */

namespace BLL;

/**
 * CRUD operations intarefce for common usage
 */
interface DalInterface {
    public function getAll();
    public function update($id = null);
    public function delete ($id = null);
    public function insert($params = array());
}

