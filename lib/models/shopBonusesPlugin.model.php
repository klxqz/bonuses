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

    public function getBonusesList() {
        $sql = "SELECT `sb`.*, `wc`.`name`,`wc`.`last_datetime`,`wce`.`email` FROM `" . $this->table . "` as `sb`"
                . " LEFT JOIN `wa_contact` as `wc` ON `sb`.`contact_id` = `wc`.`id`"
                . " LEFT JOIN `wa_contact_emails` as `wce` ON `wc`.`id` = `wce`.`contact_id`";
        $result = $this->query($sql)->fetchAll();
        return $result;
    }

    public function deleteIds($ids = array()) {
        if (!$ids) {
            return false;
        }
        foreach ($ids as &$id) {
            $id = $this->escape($id);
        }
        $sql = "DELETE FROM {$this->table} WHERE id IN(" . implode(',', $ids) . ")";
        return $this->query($sql);
    }

}
