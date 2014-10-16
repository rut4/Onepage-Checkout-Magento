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
 * Time ranges control block
 *
 * @category   Oggetto
 * @package    Oggetto_OnepageCheckout
 * @subpackage Block
 * @author     Eduard Paliy <epaliy@oggettoweb.com>
 */
class Oggetto_OnepageCheckout_Block_Adminhtml_Config_TimeRanges
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_hours;
    protected $_minutes;
    protected $_seconds;

    /**
     * Class constructor
     */
    public function __construct()
    {
        for ($i = 0; $i < 60; $i++) {
            $this->_minutes[] = $i;
        }
        for ($i = 0; $i < 24; $i++) {
            $this->_hours[] = $i;
        }
        $this->addColumn('from', [
            'label' => $this->helper('onepageCheckout')->__('From'),
        ]);
        $this->addColumn('to', [
            'label' => $this->helper('onepageCheckout')->__('To')
        ]);
        $this->_addAfter = false;
        $this->_addButtonLabel = $this->helper('onepageCheckout')->__('Add');


        parent::__construct();

        $this->setTemplate('onepage/config/form/field/timeRanges.phtml');
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

        $rendered = "<select class=\"#{_id}-{$columnName}-hour\" name=\"{$inputName}[hour]\" style=\"width: 45px\">";
        foreach ($this->_hours as $hour) {
            $rendered .= '<option value="' . $hour . '">' . $hour . '</option>';
        }
        $rendered .= '</select><span> : </span>';

        $rendered .= "<select class=\"#{_id}-{$columnName}-minute\" name=\"{$inputName}[minute]\" style=\"width: 45px\">";
        foreach ($this->_minutes as $minute) {
            $rendered .= '<option value="' . $minute . '">' . $minute . '</option>';
        }
        $rendered .= '</select>';

        return $rendered;
    }
}
