<?php
/**
 * Oggetto Web checkout extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Oggetto OnepageCheckout module to newer versions in the future.
 * If you wish to customize the Oggetto OnepageCheckout module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_OnepageCheckout
 * @copyright  Copyright (C) 2014 Oggetto Web ltd (http://oggettoweb.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Exclude dates control block
 *
 * @category   Oggetto
 * @package    Oggetto_OnepageCheckout
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_OnepageCheckout_Block_Adminhtml_Config_ExcludeDates
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_months = [
        1   => 'January',
        2   => 'February',
        3   => 'March',
        4   => 'April',
        5   => 'May',
        6   => 'June',
        7   => 'July',
        8   => 'August',
        9   => 'September',
        10  => 'October',
        11  => 'November',
        12  => 'December'
    ];
    protected $_days = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26,
        27, 28, 29, 30, 31];
    protected $_years = [
        0 => 'Yearly'
    ];

    /**
     * Class constructor
     */
    public function __construct()
    {

        for ($i = (int)date('Y'); $i <= (int)date('Y') + 5; $i++) {
            $this->_years[$i] = $i;
        }
        $this->addColumn('date', [
            'label' => $this->helper('onepageCheckout')->__('Date')
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = $this->helper('onepageCheckout')->__('Add');


        parent::__construct();

        $this->setTemplate('onepage/config/form/field/excludeDates.phtml');
    }

    /**
     * Render cell template
     *
     * @param string $columnName Column name
     * @return string Cell html code
     * @throws Exception Wrong column name
     */
    protected function _renderCellTemplate($columnName)
    {
        if (empty($this->_columns[$columnName])) {
            throw new Exception('Wrong column name specified.');
        }
        $inputName = $this->getElement()->getName() . '[#{_id}][' . $columnName . ']';

        $rendered = "<select class=\"#{_id}-{$columnName}-day\" name=\"{$inputName}[day]\">";
        foreach ($this->_days as $day) {
            $rendered .= '<option value="' . $day . '">' . $this->__($day) . '</option>';
        }
        $rendered .= '</select>';

        $rendered .= "<select class=\"#{_id}-{$columnName}-month\" name=\"{$inputName}[month]\">";
        foreach ($this->_months as $key => $value) {
            $rendered .= '<option value="' . $key . '">' . $this->__($value) . '</option>';
        }
        $rendered .= '</select>';


        $rendered .= "<select class=\"#{_id}-{$columnName}-year\" name=\"{$inputName}[year]\">";
        foreach ($this->_years as $key => $value) {
            $rendered .= '<option value="' . $key . '">' . $this->__($value) . '</option>';
        }
        $rendered .= '</select>';

        return $rendered;
    }
}
