<?php

namespace IntelligentSpark\Model\Payment;

use Isotope\Model\Payment;
use Isotope\Interfaces\IsotopePayment;
use Isotope\Interfaces\IsotopeProductCollection;

class PurchaseOrder extends Payment implements IsotopePayment
{

    protected $strTemplate = 'iso_payment_purchaseorder';

    /**
     * Proceed to complete step?
     *
     * @access protected
     * @var boolean
     */
    protected $blnProceed = false;

    /**
     * Form ID
     *
     * @access protected
     * @var string
     */
    protected $strFormId = 'payment_purchase_order';

    /**
     * Generate payment authorization form and AUTH or CAPTURE
     *
     * @access 	public
     * @param 	object
     * @return	mixed
     */
    public function checkoutForm(IsotopeProductCollection $objOrder, \Module $objModule)
    {
        $objTemplate = new \FrontendTemplate($this->strTemplate);

        $objTemplate->setData($this->arrData);
        $objTemplate->id = $this->id;
        $objTemplate->po_input = $this->generatePaymentWidget($objOrder, $objModule);
        $objTemplate->slabel = $GLOBALS['TL_LANG']['MSC']['submitLabel'];
        $objTemplate->action = \Environment::get('request');
        $objTemplate->headline = specialchars($GLOBALS['TL_LANG']['MODEL']['tl_iso_payment']['purchaseorder'][0]);

        return $objTemplate->parse();
    }


    /**
     * Process payment on checkout page.
     * @param   IsotopeProductCollection    The order being places
     * @param   Module                      The checkout module instance
     * @return  mixed
     */
    public function processPayment(IsotopeProductCollection $objOrder, \Module $objModule)
    {
        $objOrder->checkout();
        $objOrder->updateOrderStatus($this->new_order_status);

        return true;
    }

    protected function generatePaymentWidget($objOrder,$objModule) {


        $arrFields = array
        (
            'purchase_order_id'	=> array
            (
                'label'				=> &$GLOBALS['TL_LANG']['MSC']['purchase_order_id'],
                'inputType'			=> 'text',
                'eval'				=> array('mandatory'=>true, 'tableless'=>true),
            )
        );

        $arrParsed = array();
        $blnSubmit = true;

        foreach ($arrFields as $field => $arrData )
        {
            $strClass = $GLOBALS['TL_FFL'][$arrData['inputType']];
            // Continue if the class is not defined
            if (!class_exists($strClass))
            {
                continue;
            }

            $objWidget = new $strClass($strClass::getAttributesFromDca($arrData, $field));
            if($arrData['value'])
            {
                $objWidget->value = $arrData['value'];
            }
            $objWidget->tableless = $this->tableless;

            //Handle form submit
            if( \Input::post('FORM_SUBMIT') == $this->strFormId."_".$this->id)
            {
                $objWidget->validate();
                if($objWidget->hasErrors())
                {
                    $blnSubmit = false;
                    $objModule->doNotSubmit = true;
                }else{
                    $objOrder->purchase_order_id = $objModule->value;
                    $objOrder->save();
                }
            }

            // Give the template plenty of ways to output the fields
            $strParsed = $objWidget->parse();
            #$strBuffer .= $strParsed;
            #$arrParsed[$field] = $strParsed;
            return $strParsed;
        }
    }
}

