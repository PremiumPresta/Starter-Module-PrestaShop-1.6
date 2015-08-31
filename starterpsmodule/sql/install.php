<?php
/**
 * Starter Module
 *
 *  @author    PremiumPresta <office@premiumpresta.com>
 *  @copyright 2015 PremiumPresta
 *  @license   http://creativecommons.org/licenses/by-nd/4.0/ CC BY-ND 4.0
 */

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'quotes` (
        `id_quote` int(11) NOT NULL AUTO_INCREMENT,
        PRIMARY KEY  (`id_quote`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
