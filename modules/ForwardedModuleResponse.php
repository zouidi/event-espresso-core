<?php
namespace EventEspresso\modules;

defined('EVENT_ESPRESSO_VERSION') || exit();



/**
 * Class ForwardedModuleResponse
 * Simple container to be used as a DTO (Data Transfer Object)
 * Uses an internal array for holding data added/retrieved by get() and set()
 * Uses a corresponding data_map array for defining the keys to be used in the data array,
 * as well as
 *
 * @package       Event Espresso
 * @subpackage    core
 * @author        Brent Christensen
 * @since         $VID:$
 */
class ForwardedModuleResponse
{

    /**
     * for use in ForwardedModuleResponse objects to indicate
     * that the response DTO is NOT valid and should NOT be forwarded
     *
     * @see ForwardedModuleResponse::getForwardClassConstants()
     */
    const NO_FORWARD = 0;

    /**
     * for use in ForwardedModuleResponse objects to indicate
     * that the response DTO is valid and can be forwarded
     *
     * @see ForwardedModuleResponse::getForwardClassConstants()
     */
    const PROCESS_FORWARD = 1;

    /**
     * @var int $status
     */
    protected $status = ForwardedModuleResponse::NO_FORWARD;

    /**
     * @var array $data
     */
    protected $data = array();

    /**
     * keys = name of data parameter
     * values = array of validators applied during data setting,
     * where validator values are the validation callbacks OR a specific value to be tested against,
     * and the keys can be used to define specific validation types like 'instanceof'
     * but are usually empty for primitive data types that use simple validation methods
     *      for example:
     *          array(
     *              'an-integer'  => array( 'absint' ), // no key
     *              'some-string' => array( 'sanitize_text_field' ), // no key
     *              'my-object'   => array( 'instanceof' => 'class_name' ),
     *          )
     *
     * @var array $data_map
     */
    protected $data_map = array();



    /**
     * ForwardedModuleResponse constructor.
     *
     * @throws InvalidModuleResponseDataException
     */
    public function __construct()
    {
        if (empty($this->data_map)) {
            throw new InvalidModuleResponseDataException(
                __(
                    'The data map can not be empty. Please ensure it is set before constructing an instance of \EventEspresso\modules\ForwardedModuleResponse',
                    'event_espresso'
                )
            );
        }
    }




    /**
     * get_forward_constants
     *
     * @access    public
     * @return    array
     */
    public static function getForwardClassConstants()
    {
        return array(
            ForwardedModuleResponse::NO_FORWARD,
            ForwardedModuleResponse::PROCESS_FORWARD,
        );
    }



    /**
     * ensures that critical parameters are set before forwarding this object
     * should be overridden for more customized module responses
     *
     * @return bool
     */
    public function valid()
    {
        return ! empty($this->data);
    }



    /**
     * @param array $data_map
     */
    public function setDataMap(array $data_map)
    {
        $this->data_map = $data_map;
    }



    /**
     * @param int $status
     * @throws \EE_Error
     */
    public function setStatus($status = ForwardedModuleResponse::NO_FORWARD)
    {
        $forward_class_constants = ForwardedModuleResponse::getForwardClassConstants();
        if ( ! in_array($status, $forward_class_constants, true)) {
            throw new InvalidModuleResponseDataException(
                sprintf(
                    __('"%1$s" is an invalid module forwarding status.', 'event_espresso'),
                    $status
                )
            );
        }
        $this->status = $status;
    }



    /**
     * @return int
     */
    public function status()
    {
        return $this->status;
    }



    /**
     * whether or not the data parameter key exists in the data_map
     * does NOT report whether a value has been set for that data parameter
     *
     * @param  string $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this->data_map[$key]) ? true : false;
    }



    /**
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }



    /**
     * @param  string $key
     * @param  null   $value
     * @throws \EE_Error
     */
    public function set($key, $value)
    {
        $this->data[$key] = $this->applySetterValidation($key, $value);
    }



    /**
     * @return array
     */
    public function getAll()
    {
        return $this->data;
    }



    /**
     * @param array $data
     * @throws \EE_Error
     */
    public function setAll(array $data)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }



    /**
     * @param string $key
     * @param mixed  $value
     * @return mixed|null
     * @throws \EE_Error
     */
    protected function applySetterValidation($key, $value)
    {
        if ( ! $this->has($key)) {
            throw new InvalidModuleResponseDataException(
                sprintf(
                    __('"%1$s" is not a valid data parameter for the "%2$s" class', 'event_espresso'),
                    $key,
                    __CLASS__
                )
            );
        }
        foreach ($this->data_map[$key] as $type => $validator) {
            if ((string)$type === 'instanceof') {
                if ( ! $value instanceof $validator) {
                    throw new InvalidModuleResponseDataException(
                        sprintf(
                            __('"%1$s" is not a valid instance of "%2$s"', 'event_espresso'),
                            $key,
                            $validator
                        )
                    );
                }
            } else {
                $value = $validator($value);
            }
        }
        return $value;
    }



}
// End of file ForwardedModuleResponse.php
// Location: /ForwardedModuleResponse.php