# Allpay Payment Plugin for JoomShopping

This is the **Allpay** payment plugin for the **JoomShopping** component (Joomla 5.x, JoomShopping 5.4.1+). It enables online payments via the Allpay.co.il payment system directly from your site's checkout.

## 📦 Installation

1. Copy the plugin folder `pm_allpay` into the following directory:

/components/com_jshopping/payments/


2. In your Joomla admin panel, go to:  
**JoomShopping → Settings → Payment Methods**

3. Click **New**, and choose payment method `pm_allpay`.

4. Set a title (e.g. **Credit Card via Allpay**) and save.

5. Open the newly created payment method and configure the settings:

## ⚙️ Plugin Settings

| Parameter                    | Description                                                            |
|-----------------------------|------------------------------------------------------------------------|
| `Login`                     | Your Allpay account login                                              |
| `API Key`                   | Your API key used for request signing                                  |
| `VAT`                       | Whether to include VAT in product prices (No VAT / VAT Included)       |
| `Installment max payments`  | Number of installment payments (0 = disabled)                          |
| `Installment min order`     | Minimum order amount required to enable installments                   |

## 🔄 Payment Flow

- When the customer proceeds to payment, the order is submitted to Allpay.
- After successful payment, the user is redirected back to your site.
- The order status is automatically updated to **Paid** upon receiving a notification from Allpay.

## 🔐 Signature

All requests are signed using SHA-256 based on the submitted parameters and your API key.

## 📡 Notification (Callback) URL

Allpay sends payment status notifications to:

https://YOUR_SITE/index.php?option=com_jshopping&controller=checkout&task=step7&act=notify&js_paymentclass=pm_allpay


## ✅ Compatibility

- Joomla 5.x
- JoomShopping 5.4.1+
- PHP 8.1+

## 🧑‍💻 Support

For integration support, please contact [Allpay](https://allpay.co.il) or your technical provider.

---

**Developer:**  
[Allpay.co.il](https://allpay.co.il)  
© 2025 Allpay Ltd. All rights reserved.

