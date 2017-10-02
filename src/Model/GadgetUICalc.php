<?php
  class GadgetWeight {
  	private $priceWeight;
	private $CPUWeight;
	private $HDWeight;
	private $RAMWeight;
	private $GPUWeight;
	private $featuresWeight;
	private $reviewsWeight;
	private $drivesWeight;

	function setPrice($p) { $this->priceWeight = $p; }
	function priceWeight() { return $this->priceWeight; }
	function setCPU($p) { $this->CPUWeight = $p; }
	function CPUWeight() { return $this->CPUWeight; }
	function setHD($p) { $this->HDWeight = $p; }
	function HDWeight() { return $this->HDWeight; }
	function setRAM($p) { $this->RAMWeight = $p; }
	function RAMWeight() { return $this->RAMWeight; }
	function setGPU($p) { $this->GPUWeight = $p; }
	function GPUWeight() { return $this->GPUWeight; }
	function setFeatures($p) { $this->featuresWeight = $p; }
	function featuresWeight() { return $this->featuresWeight; }
	function setReviews($p) { $this->reviewsWeight = $p; }
	function reviewsWeight() { return $this->reviewsWeight; }
	function setDrives($p) { $this->drivesWeight = $p; }
	function drivesWeight() { return $this->drivesWeight; }
  }
  class GadgetUICalc {
  		private $input;
		private $gadget;
  		function __construct($ui, $gadget) {
			 $this->input = $ui;
			 $this->gadget = $gadget;
		}

		public function CalcPrice($priceInput, $priceFromGadget){
			$tempPriceResult = 2 * ($priceInput - $priceFromGadget) / (($priceInput + $priceFromGadget) / 2);
			return $priceInput > $priceFromGadget ? 1 : 1 * (1 + $tempPriceResult);
		}

   		function CalcComponent($componentInput, $componentFromGadget){
			 $tempComponentResult = ($componentInput - $componentFromGadget) / (($componentInput + $componentFromGadget) / 2);
			return $componentInput > $componentFromGadget ? 1 : 1 * (1 - $tempComponentResult);
		}
		function getScore () {
		       $gWeight = new GadgetWeight();
		       $gWeight->setPrice(0.125);
		       $gWeight->setCPU(0.125);
		       $gWeight->setHD(0.125);
		       $gWeight->setRAM(0.125);
		       $gWeight->setGPU(0.125);
		       $gWeight->setFeatures(0.125);
		       $gWeight->setReviews(0.125);
		       $gWeight->setDrives(0.125);
		       return $this->CalcScore($gWeight);
		}
		function CalcScore($weights){
			
			$priceScore = $this->CalcPrice($this->input->price(), $this->gadget->price);
			$CPUScore = $this->CalcComponent($this->input->CPU(), $this->gadget->CPU);
			$HDScore = $this->CalcComponent($this->input->HD(), $this->gadget->HD);
			$RAMScore = $this->CalcComponent($this->input->RAM(), $this->gadget->RAM);
			$GPUScore = $this->CalcComponent($this->input->GPU(), $this->gadget->GPU);
			$featuresScore = $this->CalcComponent($this->input->features(), $this->gadget->features);
			$reviewsScore = $this->CalcComponent($this->input->reviews(), $this->gadget->reviews);
			$drivesScore = $this->CalcComponent($this->input->drives(), $this->gadget->drives);
			
		  	 $tempPriceScore = $weights->priceWeight() * $priceScore;
			 $tempCPUScore = $weights->CPUWeight() * $CPUScore;
			 $tempHDScore = $weights->HDWeight() * $HDScore;
			 $tempRAMScore = $weights->RAMWeight() * $RAMScore;
			 $tempGPUScore = $weights->GPUWeight() * $GPUScore;
			 $tempFeaturesScore = $weights->featuresWeight() * $featuresScore;
			 $tempReviewsScore = $weights->reviewsWeight() * $reviewsScore;
			 $tempDrivesScore = $weights->drivesWeight() * $drivesScore;
			
			$this->score = $tempPriceScore + $tempCPUScore + $tempHDScore + $tempRAMScore + $tempGPUScore + $tempFeaturesScore + $tempReviewsScore + $tempDrivesScore;
			return $this->score;
		}
  }