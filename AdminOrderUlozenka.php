<?php

/*
 * 2007-2013 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2013 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class AdminOrderUlozenka extends AdminTab {

    public $toolbar_title;
    protected $boxes;

    public function __construct() {
        global $cookie;
        
        $this->bootstrap = true;
        $this->table = 'order';
        $this->className = 'Order';
        $this->lang = false;
        $this->noLink = true;
        //	$this->explicitSelect = true;
        //	$this->allow_export = false;
        $this->deleted = false;
        $this->deleted = false;

        $this->bulk_actions = array('exportU' => array('text' => $this->l('export Ulozenka')));

        $this->_select = '
        a.id_currency,
        a.id_order AS id_pdf,
        CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
     
        u.`dobirka`,  u.`id_ulozenka`,u.`exported`,  u.`pobocka_name`, u.`date_exp`,
        IF((SELECT COUNT(so.id_order) FROM `' . _DB_PREFIX_ . 'orders` so WHERE so.id_customer = a.id_customer) > 1, 0, 1) as new';

        $this->_join = '
        LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`)
            LEFT JOIN `' . _DB_PREFIX_ . 'ulozenka` u ON (u.`id_order` = a.`id_order`)
        
        ';

        $this->_where .='AND u.`id_order` > 0';

        $this->_orderBy = 'id_order';
        $this->_orderWay = 'DESC';

        $statuses_array = array();
        $statuses = OrderState::getOrderStates((int) $cookie->id_lang);

        foreach ($statuses as $status)
            $statuses_array[$status['id_order_state']] = $status['name'];


        $this->fieldsDisplay = array(
            'id_order' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 25
            ),
            'id_ulozenka' => array(
                'title' => $this->l('id uloženka'),
                'align' => 'center',
                'width' => 25
            ),
            'customer' => array(
                'title' => $this->l('Zákazník'),
                'havingFilter' => true,
            ),
            'total_paid' => array(
                'title' => $this->l('Celkem'),
                'width' => 25,
                'align' => 'right',
                'prefix' => '<b>',
                'suffix' => '</b>',
                'type' => 'price',
                'currency' => true
            ),
            'payment' => array(
                'title' => $this->l('Platba: '),
                'width' => 80
            ),
            'osname' => array(
                'title' => $this->l('Stav'),
                'color' => 'color',
                'width' => 50,
                'type' => 'select',
                'list' => $statuses_array,
                'search' => false,
                'orderby' => false,
            ),
            'pobocka_name' => array(
                'title' => $this->l('Pobočka'),
                'width' => 100,
                'align' => 'right',
                'filter_key' => 'pobocka_name'
            ),
            'date_add' => array(
                'title' => $this->l('Datum objednávky'),
                'width' => 60,
                'align' => 'right',
                'type' => 'datetime',
                'filter_key' => 'a!date_add'
            ),
            'date_exp' => array(
                'title' => $this->l('Datum exportu'),
                'width' => 60,
                'align' => 'right',
                'type' => 'datetime',
                'filter_key' => 'date_exp'
            ),
            'exported' => array(
                'title' => $this->l('Exportováno'),
                'width' => 70,
                'align' => 'center',
                'type' => 'bool',
                'active' => 'exported',
                'filter_key' => 'exported'
            ),
            'dobirka' => array(
                'title' => $this->l('Dobírka'),
                'width' => 70,
                'align' => 'center',
                'type' => 'bool',
                'active' => 'dobirka',
                'filter_key' => 'dobirka'
            ),
        );

        $this->shopLinkType = 'shop';



        parent::__construct();
    }

    public function processDobirka() {
        $sql = 'SELECT dobirka FROM ' . _DB_PREFIX_ . 'ulozenka WHERE id_order=' . (int) Tools::getValue('id_order');
        $dobirka = Db::getInstance()->getValue($sql);
        if (!($dobirka === false)) {
            // $dobirka=!(int)$dobirka;
            if ((int) $dobirka == 1)
                $sql = 'UPDATE   ' . _DB_PREFIX_ . 'ulozenka SET dobirka=0  WHERE id_order=' . (int) Tools::getValue('id_order');
            else
                $sql = 'UPDATE   ' . _DB_PREFIX_ . 'ulozenka SET dobirka=1  WHERE id_order=' . (int) Tools::getValue('id_order');
            Db::getInstance()->execute($sql);
            Tools::redirect($_SERVER['HTTP_REFERER']);
            return true;
        }
    }

    public function processExported() {
        $sql = 'SELECT exported FROM ' . _DB_PREFIX_ . 'ulozenka WHERE id_order=' . (int) Tools::getValue('id_order');
        $exported = Db::getInstance()->getValue($sql);
        if (!($exported === false)) {
            // $dobirka=!(int)$dobirka;
            if ((int) $exported == 1)
                $sql = 'UPDATE   ' . _DB_PREFIX_ . 'ulozenka SET exported=0  WHERE id_order=' . (int) Tools::getValue('id_order');
            else
                $sql = 'UPDATE   ' . _DB_PREFIX_ . 'ulozenka SET exported=1  WHERE id_order=' . (int) Tools::getValue('id_order');
            Db::getInstance()->execute($sql);
            // $url='./index.php?tab=AdminOrderUlozenka&token='.Tools::getAdminTokenLite('AdminOrderUlozenka');
            Tools::redirect($_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    protected function processBulkExportU() {
        $this->boxes = Tools::getValue('orderBox');
        if (is_array($this->boxes) && !empty($this->boxes)) {
            $exportu = Module::getInstanceByName('ulozenka');
            $exportu->exportOrders($this->boxes);
        } else
            $this->errors[] = Tools::displayError('You must select at least one element to export.');
    }

    public function displayListFooter($token = NULL) {
        echo '</table>';

        echo '<p><input type="submit" class="button" name="submitExportU" value="' . $this->l('Export') . '"  /></p>';
        echo '
                </td>
            </tr>
        </table>
        <input type="hidden" name="token" value="' . ($token ? $token : $this->token) . '" />
        </form>';
        if (isset($this->_includeTab) AND sizeof($this->_includeTab))
            echo '<br /><br />';
    }

    public function getList($id_lang, $orderBy = NULL, $orderWay = NULL, $start = 0, $limit = NULL) {
        parent::getList($id_lang, $orderBy, $orderWay, $start, $limit);
        foreach ($this->_list as &$l) {
            $sql = 'SELECT osl.`name` AS `osname` FROM
          `' . _DB_PREFIX_ . 'order_history` oh 
        LEFT JOIN `' . _DB_PREFIX_ . 'order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
        LEFT JOIN `' . _DB_PREFIX_ . 'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = ' . (int) (Configuration::get('PS_LANG_DEFAULT')) . ') WHERE oh.id_order=' . (int) $l['id_order'] . ' ORDER BY oh.id_order_history DESC
        ';
            $l['osname'] = Db::getInstance()->getValue($sql);
        }
    }

    public function initToolbar() {
        if ($this->display == 'view') {
            $order = new Order((int) Tools::getValue('id_order'));
        }
        $res = parent::initToolbar();
        if (Context::getContext()->shop->getContext() != Shop::CONTEXT_SHOP && isset($this->toolbar_btn['new']) && Shop::isFeatureActive())
            unset($this->toolbar_btn['new']);
        return $res;
    }

    public function setMedia() {
        parent::setMedia();
        $this->addJqueryUI('ui.datepicker');
        if ($this->tabAccess['edit'] == 1 && $this->display == 'view') {
            $this->addJS(_PS_JS_DIR_ . 'admin_order.js');
            $this->addJS(_PS_JS_DIR_ . 'tools.js');
            $this->addJqueryPlugin('autocomplete');
        }
    }

    public function display() {
        parent::display();
    }

    public function postProcess() {
        if (Tools::isSubmit('submitExportU')) {
            $this->processBulkExportU();
        }
        if (isset($_GET['dobirkaorder']) && Tools::getValue('id_order') > 0) {
            return $this->processDobirka();
        }
        if (isset($_GET['exportedorder']) && Tools::getValue('id_order') > 0) {
            return $this->processExported();
        }

        parent::postProcess();
    }

    public function ajaxProcessSearchCustomers() {
        if ($customers = Customer::searchByName(pSQL(Tools::getValue('customer_search'))))
            $to_return = array('customers' => $customers,
                'found' => true);
        else
            $to_return = array('found' => false);
        $this->content = Tools::jsonEncode($to_return);
    }

    public function displayListContent($token = NULL) {
        /* Display results in a table
         *
         * align  : determine value alignment
         * prefix : displayed before value
         * suffix : displayed after value
         * image  : object image
         * icon   : icon determined by values
         * active : allow to toggle status
         */

        global $currentIndex, $cookie;
        $currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));

        $id_category = 1; // default categ

        $irow = 0;
        if ($this->_list AND isset($this->fieldsDisplay['position'])) {
            $positions = array_map(create_function('$elem', 'return (int)($elem[\'position\']);'), $this->_list);
            sort($positions);
        }
        if ($this->_list) {
            $isCms = false;
            if (preg_match('/cms/Ui', $this->identifier))
                $isCms = true;
            $keyToGet = 'id_' . ($isCms ? 'cms_' : '') . 'category' . (in_array($this->identifier, array('id_category', 'id_cms_category')) ? '_parent' : '');
            foreach ($this->_list AS $tr) {
                $id = $tr[$this->identifier];
                echo '<tr' . (array_key_exists($this->identifier, $this->identifiersDnd) ? ' id="tr_' . (($id_category = (int) (Tools::getValue('id_' . ($isCms ? 'cms_' : '') . 'category', '1'))) ? $id_category : '') . '_' . $id . '_' . $tr['position'] . '"' : '') . ($irow++ % 2 ? ' class="alt_row"' : '') . ' ' . ((isset($tr['color']) AND $this->colorOnBackground) ? 'style="background-color: ' . $tr['color'] . '"' : '') . '>
                            <td class="center">';

                echo '<input type="checkbox" name="' . $this->table . 'Box[]" value="' . $id . '" class="noborder" />';
                echo '</td>';
                foreach ($this->fieldsDisplay AS $key => $params) {
                    $tmp = explode('!', $key);
                    $key = isset($tmp[1]) ? $tmp[1] : $tmp[0];
                    echo '
                    <td ' . (isset($params['position']) ? ' id="td_' . (isset($id_category) AND $id_category ? $id_category : 0) . '_' . $id . '"' : '') . ' class="' . ((!isset($this->noLink) OR ! $this->noLink) ? 'pointer' : '') . ((isset($params['position']) AND $this->_orderBy == 'position') ? ' dragHandle' : '') . (isset($params['align']) ? ' ' . $params['align'] : '') . '" ';
                    if (!isset($params['position']) AND ( !isset($this->noLink) OR ! $this->noLink))
                        echo ' onclick="document.location = \'' . $currentIndex . '&' . $this->identifier . '=' . $id . ($this->view ? '&view' : '&update') . $this->table . '&token=' . ($token != NULL ? $token : $this->token) . '\'">' . (isset($params['prefix']) ? $params['prefix'] : '');
                    else
                        echo '>';
                    if (isset($params['active']) AND isset($tr[$key]))
                        $this->_displayEnableLink($token, $id, $tr[$key], $params['active'], Tools::getValue('id_category'), Tools::getValue('id_product'));
                    elseif (isset($params['activeVisu']) AND isset($tr[$key]))
                        echo '<img src="../img/admin/' . ($tr[$key] ? 'enabled.gif' : 'disabled.gif') . '"
                        alt="' . ($tr[$key] ? $this->l('Enabled') : $this->l('Disabled')) . '" title="' . ($tr[$key] ? $this->l('Enabled') : $this->l('Disabled')) . '" />';
                    elseif (isset($params['position'])) {
                        if ($this->_orderBy == 'position' AND $this->_orderWay != 'DESC') {
                            echo '<a' . (!($tr[$key] != $positions[sizeof($positions) - 1]) ? ' style="display: none;"' : '') . ' href="' . $currentIndex .
                            '&' . $keyToGet . '=' . (int) ($id_category) . '&' . $this->identifiersDnd[$this->identifier] . '=' . $id . '
                                    &way=1&position=' . (int) ($tr['position'] + 1) . '&token=' . ($token != NULL ? $token : $this->token) . '">
                                    <img src="../img/admin/' . ($this->_orderWay == 'ASC' ? 'down' : 'up') . '.gif"
                                    alt="' . $this->l('Down') . '" title="' . $this->l('Down') . '" /></a>';

                            echo '<a' . (!($tr[$key] != $positions[0]) ? ' style="display: none;"' : '') . ' href="' . $currentIndex .
                            '&' . $keyToGet . '=' . (int) ($id_category) . '&' . $this->identifiersDnd[$this->identifier] . '=' . $id . '
                                    &way=0&position=' . (int) ($tr['position'] - 1) . '&token=' . ($token != NULL ? $token : $this->token) . '">
                                    <img src="../img/admin/' . ($this->_orderWay == 'ASC' ? 'up' : 'down') . '.gif"
                                    alt="' . $this->l('Up') . '" title="' . $this->l('Up') . '" /></a>';
                        } else
                            echo (int) ($tr[$key] + 1);
                    }
                    elseif (isset($params['image'])) {
                        // item_id is the product id in a product image context, else it is the image id.
                        $item_id = isset($params['image_id']) ? $tr[$params['image_id']] : $id;
                        // If it's a product image
                        if (isset($tr['id_image'])) {
                            $image = new Image((int) $tr['id_image']);
                            $path_to_image = _PS_IMG_DIR_ . $params['image'] . '/' . $image->getExistingImgPath() . '.' . $this->imageType;
                        } else
                            $path_to_image = _PS_IMG_DIR_ . $params['image'] . '/' . $item_id . (isset($tr['id_image']) ? '-' . (int) ($tr['id_image']) : '') . '.' . $this->imageType;

                        echo cacheImage($path_to_image, $this->table . '_mini_' . $item_id . '.' . $this->imageType, 45, $this->imageType);
                    }
                    elseif (isset($params['icon']) AND ( isset($params['icon'][$tr[$key]]) OR isset($params['icon']['default'])))
                        echo '<img src="../img/admin/' . (isset($params['icon'][$tr[$key]]) ? $params['icon'][$tr[$key]] : $params['icon']['default'] . '" alt="' . $tr[$key]) . '" title="' . $tr[$key] . '" />';
                    elseif (isset($params['price']))
                        echo Tools::displayPrice($tr[$key], (isset($params['currency']) ? Currency::getCurrencyInstance((int) ($tr['id_currency'])) : $currency), false);
                    elseif (isset($params['float']))
                        echo rtrim(rtrim($tr[$key], '0'), '.');
                    elseif (isset($params['type']) AND $params['type'] == 'date')
                        echo Tools::displayDate($tr[$key], (int) $cookie->id_lang);
                    elseif (isset($params['type']) AND $params['type'] == 'datetime')
                        echo Tools::displayDate($tr[$key], (int) $cookie->id_lang, true);
                    elseif (isset($tr[$key])) {
                        $echo = ($key == 'price' ? round($tr[$key], 2) : isset($params['maxlength']) ? Tools::substr($tr[$key], 0, $params['maxlength']) . '...' : $tr[$key]);
                        echo isset($params['callback']) ? call_user_func_array(array($this->className, $params['callback']), array($echo, $tr)) : $echo;
                    } else
                        echo '--';

                    echo (isset($params['suffix']) ? $params['suffix'] : '') .
                    '</td>';
                }

                if ($this->edit OR $this->delete OR ( $this->view AND $this->view !== 'noActionColumn')) {
                    echo '<td class="center" style="white-space: nowrap;">';
                    if ($this->view)
                        $this->_displayViewLink($token, $id);
                    if ($this->edit)
                        $this->_displayEditLink($token, $id);
                    if ($this->delete AND ( !isset($this->_listSkipDelete) OR ! in_array($id, $this->_listSkipDelete)))
                        $this->_displayDeleteLink($token, $id);
                    if ($this->duplicate)
                        $this->_displayDuplicate($token, $id);
                    echo '</td>';
                }
                echo '</tr>';
            }
        }
    }

}
