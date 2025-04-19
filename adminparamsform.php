<?php
defined('_JEXEC') or die();

$params['login'] = $params['login'] ?? '';
$params['api_key'] = $params['api_key'] ?? '';
$params['vat'] = $params['vat'] ?? '0';
$params['installment_n'] = $params['installment_n'] ?? '0';
$params['installment_min_order'] = $params['installment_min_order'] ?? '0';

?>
<table class="admintable">
    <tr>
        <td class="key">
            <label for="login">Login</label>
        </td>
        <td>
            <input type="text" name="pm_params[login]" class="inputbox" value="<?php echo htmlspecialchars($params['login'], ENT_QUOTES); ?>" />
        </td>
    </tr>
    <tr>
        <td class="key">
            <label for="api_key">API Key</label>
        </td>
        <td>
            <input type="text" name="pm_params[api_key]" class="inputbox" value="<?php echo htmlspecialchars($params['api_key'], ENT_QUOTES); ?>" />
        </td>
    </tr>
    <tr>
        <td class="key">
            <label for="vat">VAT</label>
        </td>
        <td>
            <select name="pm_params[vat]">
                <option value="0"<?php if ($params['vat'] == 0) echo ' selected'; ?>>No VAT</option>
                <option value="1"<?php if ($params['vat'] == 1) echo ' selected'; ?>>VAT Included</option>
            </select>
        </td>
    </tr>
    <tr>
        <td class="key">
            <label for="installment_n">Installment max payments</label>
        </td>
        <td>
            <input type="text" name="pm_params[installment_n]" class="inputbox" value="<?php echo htmlspecialchars($params['installment_n'], ENT_QUOTES); ?>" />
        </td>
    </tr>
    <tr>
        <td class="key">
            <label for="installment_min_order">Installment min order amount</label>
        </td>
        <td>
            <input type="text" name="pm_params[installment_min_order]" class="inputbox" value="<?php echo htmlspecialchars($params['installment_min_order'], ENT_QUOTES); ?>" />
        </td>
    </tr>
</table>
