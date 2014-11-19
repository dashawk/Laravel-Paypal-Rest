<?php namespace Panugaling\LaravelPaypal;

use Config;
use Panugaling\LaravelPaypal\Functions;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PPConnectionException as PEx;
	
class Paypal extends Functions {
	
	// API Context
	public static function context() {
		if(!defined('PP_CONFIG_PATH')) {
			define('PP_CONFIG_PATH', __DIR__);
		}
		
		$key = Config::get('laravel-paypal::clientID');
		$secret = Config::get('laravel-paypal::clientSecret');
		
		$context = new ApiContext(new OAuthTokenCredential($key, $secret), 'Payment_' . time());
		
		$context->setConfig([
			'mode' => Config::get('laravel-paypal::mode'),
			'http.ConnectionTimeOut' => 30,
			'log.LogEnabled' => true,
			'log.FileName' => __DIR__.'/../PayPal.log',
			'log.LogLevel' => 'FINE'
		]);
		
		return $context;
	}
	
	// Create Credit Card ID
	public static function createCard(array $creditcard) {
		$card = self::CreditCard();
		
		try {
			$card->setNumber($creditcard['number'])
				->setType($creditcard['type'])
				->setExpireMonth($creditcard['expire_month'])
				->setExpireYear($creditcard['expire_year'])
				->setCvv2($creditcard['cvv2'])
				->setFirstName($creditcard['firstname'])
				->setLastName($creditcard['lastname'])
				->setBillingAddress(self::setAddress($creditcard['address']));

		} catch(PEx $e) {
			return $e;
		}
		
		return $card->create(self::context());
	}
	
	// Create payment using credit card
	public static function payWithCreditCard($creditcard, $items, $currency, $total, $description) {
		$payer = self::setPayer('credit_card', self::setCreditCard($creditcard));
		$amount = self::setAmount($currency, $total);
		$transaction = self::setTransaction($amount, $description, $items);
		
		$payment = self::setPayment($payer, $transaction);
		
		$payment->create(self::context());
		return $payment;
	}
	
	// Create payment using paypal funds
	public static function payWithPaypal($items, $currency, $total, $description, $returnURL, $cancelURL) {
		$payer = self::setPayer('paypal');
		$amount = self::setAmount($currency, $total);
		$transaction = self::setTransaction($amount, $description, $items);
		$urls = self::setRedirects($returnURL, $cancelURL);
		$payment = self::setPayment($payer, $transaction, $urls);
	
		try {
			$payment->create(self::context());
		} catch (PayPal\Exception\PPConnectionException $e) {
			return $e;
		}
		return $payment;
	}
	
	// Set payer's address
	protected static function setAddress(array $details) {
		$address = self::Address();
		
		try {
			$address->setLine1( isset($details['address1']) ? $details['address1'] : '' );
			$address->setLine2( isset($details['address2']) ? $details['address2'] : '' );
			$address->setCity( isset($details['city']) ? $details['city'] : '' );
			$address->setState( isset($details['state']) ? $details['state'] : '' );
			$address->setPostalCode( isset($details['postalCode']) ? $details['postalCode'] : '' );
			$address->setCountryCode( isset($details['countryCode']) ? $details['countryCode'] : '' );
			$address->setPhone( isset($details['phone']) ? $details['phone'] : '' );
		} catch (PEx $e) {
			return $e;
		}
		
		return $address;
	}
	
	// Set Credit Card Information
	protected static function setCreditCard(array $creditcard) {
		if(isset($creditcard['cardID'])) {
			$token = self::CreditCardToken();
			$token->setCreditCardId($creditcard['cardID']);
			
			$fi = self::FundingInstrument();
			$fi->setCreditCardToken($token);
			
			return $fi;
		} else {
			$card = self::CreditCard();
			try {
				$card->setNumber($creditcard['number'])
					->setType($creditcard['type'])
					->setExpireMonth($creditcard['expire_month'])
					->setExpireYear($creditcard['expire_year'])
					->setCvv2($creditcard['cvv2'])
					->setFirstName($creditcard['firstname'])
					->setLastName($creditcard['lastname'])
					->setBillingAddress(self::setAddress($creditcard['address']));

				$fi = self::FundingInstrument();
				$fi->setCredit_card($card);
			} catch(PEx $e) {
				return $e;
			}
			
			return $fi;
		}
	}

	// Set Payer
	protected static function setPayer($method, $fundingInstrument = null) {
		$payer = self::Payer();
		
		try {
			$payer->setPayment_method($method);

			if(!is_null($fundingInstrument)) {
				$payer->setFundingInstruments([$fundingInstrument]);
			}
		} catch (PEx $e) {
			return $e;
		}
		
		return $payer;
	}
	
	// Set Total Amount
	protected static function setAmount($currency = 'USD', $total = 0) {
		$amount = self::Amount();
		
		try {
			$amount->setCurrency($currency);
			$amount->setTotal($total);
		} catch (PEx $e) {
			return $e;
		}
		
		return $amount;
	}
	
	// Set Transaction
	protected static function setTransaction($amount, $description = '', $itemList = null) {
		$transaction = self::Transaction();
		
		try {
			$transaction->setAmount($amount);

			if(!is_null($itemList)) {
				$transaction->setItemList($itemList);
			}

			$transaction->setDescription($description);
		} catch (PEx $e) {
			return $e;
		}
		
		return $transaction;
	}
	
	// Set Payment Details
	protected static function setPayment($payer, $transaction, $redirects = null) {
		$payment = self::Payment();
		
		try {
			$payment->setIntent('sale');
			$payment->setPayer($payer);

			if(!is_null($redirects)) {
				$payment->setRedirectUrls($redirects);
			}

			$payment->setTransactions([$transaction]);
		} catch (PEx $e) {
			return $e;
		}
		
		return $payment;
	}
	
	// Set Redirect Urls
	protected static function setRedirects($return, $cancel) {
		$urls = self::RedirectUrls();
		
		try {
			$urls->setReturnUrl($return);
			$urls->setCancelUrl($cancel);
		} catch (PEx $e) {
			return $e;
		}
		
		return $urls;
	}
	
	// Add a new Item
	public static function addItem(array $details) {
		$item = self::Item();
		
		try {
			$item->setName( isset($details['name']) ? $details['name'] : '' );
			$item->setCurrency( isset($details['currency']) ? $details['currency'] : '' );
			$item->setQuantity( isset($details['qty']) ? $details['qty'] : '' );
			$item->setPrice( isset($details['price']) ? $details['price'] : '' );
		} catch (PEx $e) {
			return $e;
		}
		
		return $item;
	}
	
	// Return Item List
	public static function setItemList($item) {
		$list = self::ItemList();
		
		try {
			$list->setItems($item);
		} catch (PEx $e) {
			return $e;
		}
		
		return $list;
	}
}