<?php namespace Panugaling\LaravelPaypal;

use PayPal\Api\Address;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Authorization;
use PayPal\Api\Capture;
use PayPal\Api\CreditCard;
use PayPal\Api\CreditCardToken;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Link;
use PayPal\Api\Payee;
use PayPal\Api\Payer;
use PayPal\Api\PayerInfo;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\PaymentHistory;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Refund;
use PayPal\Api\Resource;
use PayPal\Api\Sale;
use PayPal\Api\ShippingAddress;
use PayPal\Api\SubTransaction;
use PayPal\Api\Transaction;

class Functions {
	public static function Address() {
		return new Address();
	}
	
	public static function Amount() {
		return new Amount();
	}
	
	public static function AmountDetails() {
		return new Details();
	}
	
	public static function Authorization() {
		return new Authorization();
	}
	
	public static function Capture() {
		return new Capture();
	}
	
	public static function CreditCard() {
		return new CreditCard();
	}
	
	public static function CreditCardToken() {
		return new CreditCardToken();
	}
	
	public static function FundingInstrument() {
		return new FundingInstrument();
	}
	
	public static function Item() {
		return new Item();
	}
	
	public static function ItemList() {
		return new ItemList();
	}
	
	public static function Link() {
		return new Link();
	}
	
	public static function Payee() {
		return new Payee();
	}
	
	public static function Payer() {
		return new Payer();
	}
	
	public static function PayerInfo() {
		return new PayerInfo();
	}
	
	public static function Payment() {
		return new Payment();
	}
	
	public static function PaymentExecution() {
		return new PaymentExecution();
	}
	
	public static function PaymentHistory() {
		return new PaymentHistory();
	}
	
	public static function RedirectUrls() {
		return new RedirectUrls();
	}
	
	public static function Refund() {
		return new Refund();
	}
	
	public static function Resource() {
		return new Resource();
	}
	
	public static function Sale() {
		return new Sale();
	}
	
	public static function ShippingAddress() {
		return new ShippingAddress();
	}
	
	public static function SubTransaction() {
		return new SubTransaction();
	}
	
	public static function Transaction() {
		return new Transaction();
	}
}