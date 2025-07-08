<?php

namespace AK\SMARTAUTH\Http\Request;

defined('ABSPATH') || exit; // Prevent direct access

/**
 * Class Request
 *
 * Represents an HTTP request.
 */
class Request
{
    /**
     * Get specific request data.
     *
     * @param array $keys The keys to retrieve from the request.
     * @return array The filtered request data.
     */
    public function only(array $keys)
    {
        $data = [];

        foreach ($keys as $key) {
            //Nonce verification is not required here as the request is made using rest api and nonce already verified there
            if (isset($_REQUEST[$key])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
                $data[$key] = $_REQUEST[$key]; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
            }
        }
        return $data;
    }

    /**
     * Get a specific request parameter by key.
     *
     * @param string $key      The key of the request parameter.
     * @param mixed  $default  The default value to return if the key is not found.
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (!isset($_REQUEST[$key])) {
            return $default;
        }

        return $_REQUEST[$key];
    }

    /**
     * Magic method to get a specific request parameter by key.
     *
     * @param string $key The key of the request parameter.
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Get a specific request parameter safely by key.
     *
     * @param string $key The key of the request parameter.
     *
     * @return mixed|null
     */
    public function getSafe($key)
    {
        if (!isset($_REQUEST[$key])) {
            return null;
        }

        return $_REQUEST[$key];
    }

    /**
     * Validate request data using specified rules and custom error messages.
     *
     * @param array $data     The data to validate.
     * @param array $rules    The validation rules.
     * @param array $messages Custom error messages.
     *
     * @return array
     */
    public function validate($data, $rules, $messages)
    {
        $validator = new Validator($data, $rules, $messages);

        return $validator->validate();
    }
}