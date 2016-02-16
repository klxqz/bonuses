<?php

/**
 * @author wa-plugins.ru <support@wa-plugins.ru>
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
        $offset = !empty($options['offset']) ? $options['offset'] : 0;
        $limit = !empty($options['limit']) ? $options['limit'] : 30;

        $where = array();
        if (!empty($options['search_by']) && !empty($options['search'])) {
            switch ($options['search_by']) {
                case 'name':
                    $where[] = "`wc`.`id` IN (
                            SELECT c.id FROM wa_contact AS c 
                            WHERE c.name LIKE '" . $this->escape($options['search'], 'like') . "%')";
                    break;
                case 'email':
                    $where[] = "`wc`.`id` IN (
                            SELECT c.id
                            FROM wa_contact AS c
                            JOIN wa_contact_emails AS e ON e.contact_id=c.id
                            WHERE e.email LIKE '" . $this->escape($options['search'], 'like') . "%')";
                    break;
                case 'phone':
                    $dq = preg_replace("/[^\d]+/", '', $options['search']);
                    $where[] = "`wc`.`id` IN (
                            SELECT c.id
                            FROM wa_contact AS c
                            JOIN wa_contact_data AS d ON d.contact_id=c.id AND d.field='phone'
                            WHERE d.value LIKE '%" . $this->escape($dq, 'like') . "%')";
                    break;
                default:
                    break;
            }
        }

        $sql = "SELECT `sb`.*, `wc`.`name`,`wc`.`last_datetime` FROM `" . $this->table . "` as `sb`
                LEFT JOIN `wa_contact` as `wc` ON `sb`.`contact_id` = `wc`.`id`
                " . ($where ? "WHERE " . implode(" AND ", $where) : '') . "
                LIMIT $offset,$limit";
        $result = $this->query($sql)->fetchAll();
        return $result;
    }

    public function count($options = array()) {
        $where = array();
        if (!empty($options['search_by']) && !empty($options['search'])) {
            switch ($options['search_by']) {
                case 'name':
                    $where[] = "`wc`.`id` IN (
                            SELECT c.id FROM wa_contact AS c 
                            WHERE c.name LIKE '" . $this->escape($options['search'], 'like') . "%')";
                    break;
                case 'email':
                    $where[] = "`wc`.`id` IN (
                            SELECT c.id
                            FROM wa_contact AS c
                            JOIN wa_contact_emails AS e ON e.contact_id=c.id
                            WHERE e.email LIKE '" . $this->escape($options['search'], 'like') . "%')";
                    break;
                case 'phone':
                    $dq = preg_replace("/[^\d]+/", '', $options['search']);
                    $where[] = "`wc`.`id` IN (
                            SELECT c.id
                            FROM wa_contact AS c
                            JOIN wa_contact_data AS d ON d.contact_id=c.id AND d.field='phone'
                            WHERE d.value LIKE '%" . $this->escape($dq, 'like') . "%')";
                    break;
                default:
                    break;
            }
        }
        $sql = "SELECT COUNT(*) as `count` FROM `" . $this->table . "` as `wc` " . ($where ? "WHERE " . implode(" AND ", $where) : '');
        $result = $this->query($sql)->fetch();
        return $result['count'];
    }

}
