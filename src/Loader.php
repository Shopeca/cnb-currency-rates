<?php

namespace Shopeca\CNBCurrencyRates;

class Loader {

	private static $url = 'http://www.cnb.cz/cs/financni_trhy/devizovy_trh/kurzy_devizoveho_trhu/denni_kurz.txt?date=';

	public static function getRates (\DateTime $date = null) {
		if ($date === null) {
			$date = new \DateTime();
		}

		$url = self::$url . $date->format('d.m.Y');

		$r = [];
		$rates = self::download($url);

		if ($rates !== null) {
			$r = self::formatResponse($rates);
		}

		return $r;
	}

	private static function formatResponse ($rates) {
		$r = [];
		$lines = explode(PHP_EOL, $rates);
		if (count($lines) > 2) { // First two lines are description
			for ($i = 2; $i < count($lines); $i++) {
				$items = explode('|', $lines[$i]);
				if (count($items) == 5) {
					$r[$items[3]] = [
						'country' => $items[0],
						'currency' => $items[1],
						'amount' => $items[2],
						'code' => $items[3],
						'rate' => $items[4],
					];
				}
			}
		}
		return $r;
	}

	private static function download ($url) {
		$rates = null;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if ($data = curl_exec($ch)) {
			$rates = $data;
		}
		curl_close($ch);
		return $rates;
	}

}
