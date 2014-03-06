<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopBonusesPluginModel extends waModel {

    protected $table = 'shop_bonuses';

    public function getBonusByContactId($contact_id) {
        $result = $this->getByField('contact_id', $contact_id);
        if ($result) {
            return $result['bonus'];
        } else {
            return 0;
        }
    }

    public function getBonusesList($options = array()) {
        $offset = isset($options['offset']) ? $options['offset'] : 0;
        $limit = isset($options['limit']) ? $options['limit'] : 30;
        $sql = "SELECT `sb`.*, `wc`.`name`,`wc`.`last_datetime` FROM `" . $this->table . "` as `sb`
                LEFT JOIN `wa_contact` as `wc` ON `sb`.`contact_id` = `wc`.`id`
                LIMIT $offset,$limit";
        $result = $this->query($sql)->fetchAll();
        return $result;
    }
    
    public function count() {
        $sql = "SELECT COUNT(*) as `count` FROM `" . $this->table . "`";
        $result = $this->query($sql)->fetch();
        return $result['count'];
    }

}
