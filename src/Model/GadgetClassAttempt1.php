<?php
	class gadget {
	        public $price;
		public $CPU;
		public $HD;
		public $RAM;
		public $GPU;
		public $features;
		public $reviews;
		public $drives;


		private $priceScore;
		private $CPUScore;
		private $HDScore;
		private $RAMScore;
		private $GPUScore;
		private $featuresScore;
		private $reviewsScore;
		private $drivesScore;
		private $score;

		function __construct() {
			        $this->price = 1;
				$this->CPU = 1;
				$this->HD = 1;
				$this->RAM = 1;
				$this->GPU = 1;
				$this->features = 1;
				$this->reviews = 1;
				$this->drives = 1;
		}

		public function factorInWeights($weights, $type)
		{
		  //this is a dummy just for show
		  if ($type == 'compy')
		    return $this->price + $this->CPU + $this->HD + $this->RAM + $this->GPU + $this->features + $this->reviews + $this->drives > 100;
		  else
		    return false;
		}
		public function CalcPrice($priceInput, $priceFromGadget){
			$tempPriceResult = 2 * ($priceInput - $priceFromGadget) / (($priceInput + $priceFromGadget) / 2);
			return $priceInput > $priceFromGadget ? 1 : 1 * (1 + $tempPriceResult);
		}
		
		
		// This calls the user input (which is stored in a gadget) and the weights class
		// Perhaps the weights class should be static or something
		public function CalcScore($inputGadget, $weights){
			
			$this->priceScore = CalcPrice($inputGadget->price, $this->price);
			$this->CPUScore = CalcComponent($inputGadget->CPU, $this->CPU);
			$this->HDScore = CalcComponent($inputGadget->HD, $this->HD);
			$this->RAMScore = CalcComponent($inputGadget->RAM, $this->RAM);
			$this->GPUScore = CalcComponent($inputGadget->GPU, $this->GPU);
			$this->featuresScore = CalcComponent($inputGadget->features, $this->features);
			$this->reviewsScore = CalcComponent($inputGadget->reviews, $this->reviews);
			$this->drivesScore = CalcComponent($inputGadget->drives, $this->drives);
			
		         $tempPriceScore = $weights->priceWeight * $this->priceScore;
			 $tempCPUScore = $weights->CPUWeight * $this->CPUScore;
			 $tempHDScore = $weights->HDWeight * $this->HDScore;
			 $tempRAMScore = $weights->RAMWeight * $this->RAMScore;
			 $tempGPUScore = $weights->GPUWeight * $this->GPUScore;
			 $tempFeaturesScore = $weights->featuresWeight * $this->featuresScore;
			 $tempReviewsScore = $weights->reviewsWeight * $this->reviewsScore;
			 $tempDrivesScore = $weights->drivesWeight * $this->drivesScore;
			
			$this->score = $tempPriceScore + $tempCPUScore + $tempHDScore + $tempRAMScore + $tempGPUScore + $tempFeaturesScore + $tempReviewsScore + $tempDrivesScore;
			return $this->score;
		}
		
	}

	/* CODE DUMP
	   private function CalcHD($HDInput){
			 $tempHDResult = 2 * ($HDInput - $HD) / (($HDInput + $HD) / 2);
			$this->HDScore = $HDInput > $HD ? 1 : 1 * (1 + $tempHDResult);
		}
		
		function CalcRAM($RAMInput){
			 $tempRAMResult = 2 * ($RAMInput - $RAM) / (($RAMInput + $RAM) / 2);
			$this->RAMScore = $RAMInput > $RAM ? 1 : 1 * (1 + $tempRAMResult);
		}
		
		function CalcGPU($GPUInput){
			 $tempGPUResult = 2 * ($GPUInput - $GPU) / (($GPUInput + $GPU) / 2);
			$this->GPUScore = $GPUInput > $GPU ? 1 : 1 * (1 + $tempGPUResult);
		}
		
		function CalcFeatures($featuresInput){
			 $tempFeaturesResult = 2 * ($featuresInput - $features) / (($featuresInput + $features) / 2);
			$this->featuresScore = $featuresInput > $features ? 1 : 1 * (1 + $tempFeaturesResult);
		}
		
		function CalcReviews($reviewsInput){
			 $tempReviewsResult = 2 * ($reviewsInput - $reviews) / (($reviewsInput + $reviews) / 2);
			$this->reviewsScore = $reviewsInput > $reviews ? 1 : 1 * (1 + $tempReviewsResult);
		}
		
		function CalcDrives($drivesInput){
			 $tempDrivesResult = 2 * ($drivesInput - $drives) / (($drivesInput + $drives) / 2);
			$this->reviewsScore = $scoreInput > $score ? 1 : 1 * (1 + $tempdrivesResult);
		}
	*/
	
