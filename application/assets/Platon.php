<?php
/**
 * Payment method
 */
class Platon
{
  protected $_supportedCurrencies = array('EUR','UAH','USD', 'RUR');

  private $_url;
  private $_merchant_id;
  private $_merchant_password;


  /**
   * Constructor
   *
   * @param string $url
   * @param string $public_key
   * @param string $private_key
   */
  public function __construct($url, $merchant_id, $merchant_password) { // {{{
    if (empty($url)) {
      throw new Exception('url is empty');
    }

    if (empty($merchant_id)) {
      throw new Exception('public_key is empty');
    }

    if (empty($merchant_password)) {
      throw new Exception('private_key is empty');
    }

    $this->_url = $url;
    $this->_merchant_id = $merchant_id;
    $this->_merchant_password = $merchant_password;
  } // }}}


  /**
   * Get form
   *
   * @param array $params
   *
   * @return string
   */
  public function getForm($params)
  {
    $payment = 'CC';

    $data = base64_encode(
      json_encode(
        array(
          $params['order_id'] => [
            'amount' => number_format((float)$params['amount'], 2, '.', ''),
            'currency' => 'UAH',
            'description' => 'Order on Avtoclassika'],
        )
      )
    );

    $sign = md5(strtoupper(
      strrev($this->_merchant_id) . strrev($payment) . strrev($data) .
      strrev($params['result_url']) . strrev($this->_merchant_password)
    ));

    $language = 'en';
    if (isset($params['language']) && $params['language'] == 'ru') {
      $language = 'ru';
    }

    $result = [
      'key' => $this->_merchant_id,
      'lang' => $language,
      'payment' => $payment,
      'ext1' => number_format((float)$params['amount_original'], 2, '.', '') . ' ' . $params['currency'],
      'order' => $params['order_id'],
      'data' => $data,
      'url' => $params['result_url'],
      'sign' => $sign,
    ];

    $inputs = array();
    foreach ($result as $key => $value) {
      $inputs[] = sprintf('<input type="hidden" name="%s" value="%s" />', $key, $value);
    }

    return sprintf('
      <form method="post" action="' . $this->_url . '" accept-charset="utf-8">
        %s
      </form>', join("\r\n", $inputs)
    );
  } // }}}
}
