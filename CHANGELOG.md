
# Changelog
## Version 1.1.2 on 28 September 2020
- add "Klarna Slice It" and "Vipps" payment options.

## Version 1.1.1 on 21 September 2020
- Not delete Reepay session when payment success.

## Version 1.1.0 on 10 September 2020
- Implement surcharge fee
- Fixed invoice issue for Swish payment.

## Version 1.0.14 on 10 August 2020
- Add "Send order lines" option.
- Prevent capture amount more than authorized amount.

## Version 1.0.13 on 23 June 2020
- Add "Swish Bank", "ApplePay", "Paypal", "Klarna Pay Now", "Klarna Pay Later", "Resurs" and "Forbrugsforeningen" payment methods.

## Version 1.0.12 on 28 May 2020
- Fixed invoice issue and credit memo issue for multi currencies. 

## Version 1.0.11 on 27 February 2020
- Add "Resurs Bank" payment option.
- Fix checkout session for thank you page.

## Version 1.0.10 on 26 February 2020
- add payment method validation on cancel order observer.
- Fix checkout session for thank you page.

## Version 1.0.9 on 21 February 2020
- Add error handle for capture and refund from Magento
- Change logic to calculate "Other" line on order lines.
- Fixed integer parse issue

## Version 1.0.8 on 12 February 2020
- Fixed payment_link email template issue for other languages.

## Version 1.0.7 on 9 January 2020
- Save payment additional data when authorize, settled and refund 
- Fixed PHP Notice: Undefined index

## Version 1.0.6 on 18 September 2019
- Add new payment options (Apple Pay, Paypal)

## Version 1.0.5 on 3 July 2019
 - Add billing address and terms and conditions on payment methods in the checkout page.
	
## Version 1.0.4 on 21 June 2019
 - Implement payment link
 - Implement vat in order lines
 - Implement other line in the order lines
 - Implement partial capture/refund
 - Restore quote when cancel payment from payment window
 - Implement "invoice_authorized" webhook
 - Add Klarna and FFK payment options
 - Override send order confirmation email.

## Version 1.0.3 on 21 March 2019
 - Fixed text translation issue

## Version 1.0.2 on 5 March 2019
 - remove order_status_before_payment configuration (only allow pending status)

## Version 1.0.1 on 28 February 2019
 - fixed multi API keys issue for multi stores

## Version 1.0.0 on 22 February 2019
 - First release
