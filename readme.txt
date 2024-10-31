=== DD QR Payment Gateway Interface ===
Contributors: ddtickets
Tags: payment gateway, QR Payment, woocommerce extension, other payment, instant payment, payment option, IPS QR instant payment
Requires at least: 4.0
Tested up to: 5.7
Stable tag: 1.0.0
Requires PHP: 5.6
WC requires at least: 3.0
WC tested up to: 4.8
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Upgrade your webshop with the QR Instant Payment Method which allows your customers to pay using the m-banking application on their phone - option IPS QR scan.

== Description ==

DD QR Payment Gateway Interface is a plugin that add a new payment method to the WooCommerce payments tab.

It allows all customers in Serbia who have an m-banking application on their mobile phone to make an easy and secure instant payment on your webshop by scanning a QR code displayed on the screen after the checkout page.

You need to do the following steps in order to implement the ISP QR Instant Payment on your webshop:

1. Open an account at the Raiffeisen Bank in Serbia (in case that you don't have it already).
2. Ask **Raiffeisen bank** for a contract for implementing the IPS Instant Payment for your e-commerce webshop.
3. Ask **DD Ticketing Solutions** for a contract for using a QR Payment Gateway.

The contact mail for Raiffeisen Bank is: <pos@raiffeisenbank.rs>
The contact mail for DD Ticketing Solutions is: <ecommerce@ddtickets.rs>

After getting the access parameters from the Raiffeisen Bank, DD Ticketing Solutions will setup your account for:
1. The  QR Payment Gateway for your Webshop
2. Your QR Payment Gateway Backoffice

Then you can configure your new QR payment method in your WooCommerce Payments tab, and you are ready to go!

So, what are the main benefits of the QR Instant Payment?

= Benefits for your customer =
* **PRACTICAL** - because only a mobile phone is needed for payment.
* **SECURE** - because no personal or financial data are required from the customer.
* **QUICK** - because there is no need for typing (no mistakes) and everything is done in less than 10 sec.
* **EASY** - because it all comes only to scanning a QR code followed by a PIN confirmation.
* **SAFE** - because it is done without physical contact, under top technological standards.
* **FREE** - because no fee is charged to the customer.

= Benefits for you =
* **FEE** - The lowest fee among all the online payment methods.
* **LIQUIDITY** - Money is on your account instantly after the payment.
* **TECHNOLOGY** - The payment process in full automated and in real-time.
* **LOOK & FEEL** - The QR Payment Gateway can be fully customized in the backoffice, so it can looks like a integrated part of your webshop.
* **RESPONSIVE** - Work on all devices (desktop, mobile, tablet) with automated or manual adaptation.
* **SUPPORT** - The backoffice module allows you a full administration of your QR transactions, with full search and reporting functionality.

== Installation ==

* Go to the Plugins -> Add New section in your WordPress admin area.
* In the search field type "dd qr payment gateway interface"
* Find the plugin **DD QR Payment Gateway Interface** *by DD Ticketing Solutions*
* Click on Install Now and then activate the plugin.
* Now, go to the WooCommerce Settings menu, tab Payments and manage the options.

== Frequently Asked Questions ==

= Can I have an account in a bank other than Raiffeisen bank? =

At this moment, DD Ticketing Solutions has an certified solution for the QR Payment Gateway only in Raiffeisen Bank. The next one will be Komencijalna banka, and other banks will hopefully follow in the near future.

= Is this payment option available only for customer who have their accounts in Raiffeisen bank? =

No. All customers who have an installed m-banking application with the option ISP scan can make an online QR Instant Payment, regardless of in witch bank they have their account.

= Why do I need the DD QR Payment Gateway? Can't I integrate it directly with the bank? =

You don't need it, but it is very recommended. Because otherwise, you need a very solid knowledge in programming, REST API protocols, follow a strict development procedure, pass a lot time in testing until you get an approved solution by the bank, and after that, you need to develop a full backoffice for the administration.

= The steps for implementations seams too complicated to me. Can you do all the stuff for me? =

Yes. Please contact us on <ecommerce@ddtickets.rs> and we will do the rest for you.

= In case of need for refund, how is it solved? Do I need to contact the bank or use the e-banking application?

No. You can do everything directly from your QR Payment Gateway Backoffice. You can quicky and easy make a refund for the whole or partial amount.

= If I have no transaction during a month, how much will I be charged? =

The bank will make no charge (except for keeping your account active if there is such fee). DD Ticketing Solutions will charge you some small amount defined in the contract. It depends on the contract options, but it is usually approx. 5-10 euro per month.

== Changelog ==

= 1.0.0 =
* First commit of the plugin