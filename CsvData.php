<?php

class CsvData {

    public function getData($ids) {
        $retval = array();


        if ((int) Configuration::get("ULOZENKA_CSV_REF"))
            $reffile = 'reference';
        else
            $reffile = 'id_order';
        foreach ($ids as $id) {
            $sql = 'SELECT 
             o.' . $reffile . ' as objednavka,
             c.firstname as jmeno, 
             c.lastname as  prijmeni,
             c.email,
             COALESCE(ad.phone_mobile, ai.phone_mobile,  ad.phone, ai.phone)  as telefon,
             IF(u.dobirka > 0, o.total_paid, 0) as dobirka,
             "" as heslo,
             u.pobocka as vyzvednuti
            
            FROM ' . _DB_PREFIX_ . 'orders o
             LEFT JOIN   ' . _DB_PREFIX_ . 'customer c ON
             o.id_customer =c.id_customer
             LEFT  JOIN   ' . _DB_PREFIX_ . 'address ad ON
             o.id_address_delivery =ad.id_address
             LEFT  JOIN   ' . _DB_PREFIX_ . 'address ai ON
             o.id_address_invoice =ai.id_address
            LEFT  JOIN   ' . _DB_PREFIX_ . 'ulozenka u ON
             o.id_order =u.id_order 
             WHERE
            o.id_order=' . (int) $id;
            $data = Db::getInstance()->getRow($sql);
            if ($data && is_array($data))
                $retval[] = $data;
        }
        return $retval;
    }

}

?>
