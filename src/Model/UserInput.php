<?php

	class UserInput {
		private $price;
		private $CPU;
		private $HD;
		private $RAM;
		private $GPU;
		private $features;
		private $reviews;
		private $drives;

		public function __construct($valuemap) {
		  $this->price = $valuemap['price'];
		  $this->CPU = $valuemap['cpu'];
		  $this->HD = $valuemap['hd'];
		  $this->RAM = $valuemap['ram'];
		  $this->GPU = $valuemap['gpu'];
		  $this->features = $valuemap['features'];
		  $this->reviews = $valuemap['reviews'];
		  $this->drives = $valuemap['drives'];
		}

		public function price() {
		       return $this->price;
		}
		public function CPU() {
		       return $this->CPU;
		}
		public function HD() {
		       return $this->HD;
		}
		public function RAM() {
		       return $this->RAM;
		}
		public function GPU() {
		       return $this->GPU;
		}
		public function features() {
		       return $this->features;
		}
		public function reviews() {
		       return $this->reviews;
		}
		public function drives() {
		       return $this->drives;
		}
	}