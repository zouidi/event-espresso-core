<?php

namespace EventEspresso\core\domain\services\commands\price;

use EE_Price;
use EEM_Price;
use EventEspresso\core\domain\services\commands\EntityCommandHandler;
use InvalidArgumentException;

defined('EVENT_ESPRESSO_VERSION') || exit;



/**
 * Class PriceCommandHandler
 * abstract parent Service class for adding/updating/deleting Prices
 *
 * @package       Event Espresso
 * @author        Brent Christensen
 * @since         $VID:$
 */
abstract class PriceCommandHandler extends EntityCommandHandler
{


    /**
     * @var EEM_Price $price_model
     */
    protected $price_model;



    /**
     * PriceCommandHandler constructor.
     *
     * @param EEM_Price $price_model
     */
    public function __construct(EEM_Price $price_model)
    {
        $this->price_model = $price_model;
    }



    /**
     * simply ensures that all fields for the datetime model have values
     *
     * @param array    $price_data
     * @param EE_Price $price
     * @return array
     * @throws InvalidArgumentException
     */
    protected function validatePriceData(array $price_data, EE_Price $price)
    {
        //trim all values to ensure any excess whitespace is removed.
        $price_data = array_map(
            function ($price_data) {
                return is_array($price_data) ? $price_data : trim($price_data);
            },
            $price_data
        );
        if (empty($price_data['PRC_order'])) {
            throw new InvalidArgumentException(
                esc_html__('PRC_order is a required value and must be set.', 'event_espresso')
            );
        }
        return array(
            'PRC_ID'         => $this->validateArrayElement($price_data, 'PRC_ID'),
            'PRT_ID'         => $this->validateArrayElement(
                $price_data,
                'PRT_ID',
                $price instanceof EE_Price ? $price->type() : null
            ),
            'PRC_amount'     => $this->validateArrayElement(
                $price_data,
                'PRC_amount',
                $price instanceof EE_Price ? $price->amount() : 0
            ),
            'PRC_name'       => $this->validateArrayElement(
                $price_data,
                'PRC_name',
                $price instanceof EE_Price ? $price->name() : ''
            ),
            'PRC_desc'       => $this->validateArrayElement(
                $price_data,
                'PRC_desc',
                $price instanceof EE_Price ? $price->desc() : ''
            ),
            'PRC_is_default' => $this->validateArrayElement(
                $price_data,
                'PRC_is_default',
                $price instanceof EE_Price ? $price->is_default() : false
            ),
            'PRC_overrides'  => $this->validateArrayElement(
                $price_data,
                'PRC_overrides',
                $price instanceof EE_Price ? $price->overrides() : null
            ),
            'PRC_order'      => $this->validateArrayElement(
                $price_data,
                'PRC_order',
                $price instanceof EE_Price ? $price->order() : null,
                true
            ),
            'PRC_deleted'    => $this->validateArrayElement(
                $price_data,
                'PRC_deleted',
                $price instanceof EE_Price ? $price->deleted() : null
            ),
            'PRC_parent'     => $this->validateArrayElement(
                $price_data,
                'PRC_parent',
                $price instanceof EE_Price ? $price->parent() : null
            ),
            'PRC_wp_user'    => $this->validateArrayElement(
                $price_data,
                'PRC_deleted',
                $price instanceof EE_Price ? $price->wp_user() : null
            ),
        );
    }



}
// End of file PriceCommandHandler.php
// Location: core/domain/services/commands/price/PriceCommandHandler.php