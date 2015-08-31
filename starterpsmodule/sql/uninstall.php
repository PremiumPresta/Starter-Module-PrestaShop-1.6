<?php
/**
 * Starter Module
 *
 *  @author    PremiumPresta <office@premiumpresta.com>
 *  @copyright 2015 PremiumPresta
 *  @license   http://creativecommons.org/licenses/by-nd/4.0/ CC BY-ND 4.0
 */

$sql = array();
$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'quotes`;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
