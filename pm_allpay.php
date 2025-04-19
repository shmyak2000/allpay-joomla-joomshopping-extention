<?php
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Helper;

class pm_allpay extends PaymentRoot
{
    function showAdminFormParams($params)
    {
        $defaults = [
            'login' => '',
            'api_key' => '',
            'vat' => '0',
            'installment_n' => '0',
            'installment_min_order' => '0'
        ];
        foreach ($defaults as $key => $val) {
            if (!isset($params[$key])) $params[$key] = $val;
        }
        include(dirname(__FILE__) . '/adminparamsform.php');
    }

    function getPaymentDefaultParams()
    {
        return [
            'login' => '',
            'api_key' => '',
            'vat' => '0',
            'installment_n' => '0',
            'installment_min_order' => '0',
            'payment_type' => 'htmlform'
        ];
    }

    function showPaymentForm($params, $pmconfigs)
    {
        return '';
    }    

    function showEndForm($pmconfigs, $order)
    {
        $lang = explode('-', Factory::getLanguage()->getTag())[0];
        $items = [];

        foreach ($order->getAllItems() as $product) {
            $items[] = [
                'name' => $product->product_name,
                'qty' => $product->product_quantity,
                'price' => number_format($product->product_item_price, 2, '.', ''),
                'vat' => (int)($pmconfigs['vat'] ?? 0)
            ];
        }

        $params = [
            'login' => $pmconfigs['login'],
            'order_id' => $order->order_id,
            'items' => $items,
            'currency' => $order->currency_code_iso,
            'lang' => strtoupper($lang),
            'client_name' => trim($order->f_name . ' ' . $order->l_name),
            'client_email' => $order->email,
            'client_phone' => $order->phone ?? '',
            'notifications_url' => Uri::root() . 'index.php?option=com_jshopping&controller=checkout&task=step7&act=notify&js_paymentclass=pm_allpay',
            'success_url' => Uri::root() . 'index.php?option=com_jshopping&controller=checkout&task=step7&act=return&js_paymentclass=pm_allpay',
            'backlink_url' => Uri::root()
        ];

        if ((int)$pmconfigs['installment_n'] > 0 && ((int)$pmconfigs['installment_min_order'] == 0 || $pmconfigs['installment_min_order'] <= $order->order_total)) {
            $params['inst'] = (int)$pmconfigs['installment_n'];
        }

        $params['sign'] = $this->generateSignature($params, $pmconfigs['api_key']);

        $ch = curl_init('https://allpay.to/app/?show=getpayment&mode=api7');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        if (!empty($data['payment_url'])) {

            $orderTable = JSFactory::getTable('order');
            $orderTable->load($order->order_id);
            $orderTable->order_created = 1;
            $orderTable->store();            

            echo '<script>window.location.href="' . $data['payment_url'] . '";</script>';
            return;
        } else {
            echo '<p>Payment gateway error: ' . $data['error_msg'] . '.</p><p><a href="/">Return</a></p>';
        }               
        return '';
    }

    function checkTransaction($pmconfigs, $order, $act)
    {
        $data = count($_POST) ? $_POST : json_decode(file_get_contents('php://input'), true);

        if (!$data || empty($data['sign'])) {
            return [0, 'No data or signature'];
        }
    
        $expectedSign = $this->generateSignature($data, $pmconfigs['api_key']);
        if ($data['sign'] !== $expectedSign) {
            return [0, 'Invalid signature'];
        }
    
        if ((int)$data['status'] !== 1) {
            return [2, 'Payment not confirmed']; 
        }
    
        $transaction = $data['payment_id'] ?? '';
        $transactiondata = $data;

        $orderTable = JSFactory::getTable('order');
        $orderTable->load($order->order_id);
        $orderTable->order_status = 6; // Paid
        $orderTable->store();

        echo 'OK';
    
        return [1, '', $transaction, $transactiondata];
    }

    function getUrlParams($pmconfigs)
    {
        $data = count($_POST) ? $_POST : json_decode(file_get_contents('php://input'), true);

        return [
            'order_id' => (int)($data['order_id'] ?? 0),
            'hash' => '',
            'checkHash' => 0,
            'checkReturnParams' => 0,
        ];
    }

    private function generateSignature($params, $api_key)
    {
        ksort($params);
        $chunks = [];
        foreach ($params as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $item) {
                    if (is_array($item)) {
                        ksort($item);
                        foreach ($item as $val) {
                            if (trim((string)$val) !== '') {
                                $chunks[] = $val;
                            }
                        }
                    }
                }
            } elseif (trim((string)$v) !== '' && $k !== 'sign') {
                $chunks[] = $v;
            }
        }
        $signature = implode(':', $chunks) . ':' . $api_key;
        return hash('sha256', $signature);
    }

    private function verifySignature($params, $api_key)
    {
        $expected = $this->generateSignature($params, $api_key);
        return $params['sign'] === $expected;
    }
}
