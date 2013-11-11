<?php

class shopBonusesPluginModel extends waModel
{
    protected $table = 'shop_bonuses';
    
    public function getBonusByContactId($contact_id)
    {
        $result = $this->getByField('contact_id',$contact_id);
        if($result) {
            return $result['bonus'];
        } else {
            return 0;
        }
        
    }

}
