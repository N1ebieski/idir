<?php

namespace N1ebieski\IDir\Utils\Payment\Interfaces;

interface TransferUtilStrategy
{
    /**
     * Undocumented function
     *
     * @param string $amount
     * @return self
     */
    public function setAmount(string $amount);

    /**
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    public function setDesc(string $desc);
    
    /**
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    public function setUuid(string $uuid);
    
    /**
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    public function setRedirect(string $redirect);
    
    /**
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    public function setCancelUrl(string $cancelUrl);
    
    /**
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    public function setReturnUrl(string $returnUrl);
    
    /**
     * Undocumented function
     *
     * @param string $desc
     * @return self
     */
    public function setNotifyUrl(string $notifyUrl);

    /**
     * Undocumented function
     *
     * @return void
     */
    public function purchase() : void;

    /**
     * Undocumented function
     *
     * @param array $attributes
     * @return void
     */
    public function complete(array $attributes) : void;

    /**
     * [authorize description]
     * @param  array  $attributes [description]
     * @return void               [description]
     */
    public function authorize(array $attributes) : void;
    
    /**
     * Undocumented function
     *
     * @return string
     */
    public function getUrlToPayment() : string;
}
