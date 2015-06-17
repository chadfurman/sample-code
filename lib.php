<?php

App::uses('UnitsManager', 'Fuzz');

class BodyWeight {

  /**
   * The users height in Meters
   *
   * @var float
   */
  public $height;

  /**
   * The users weight in Kilograms
   *
   * @var float
   */
  public $weight;

  /**
   * Value is true if converting Imperial to Metric
   *
   * @var boolean
   */
  public $convert_units = true;

  /**
   * Lowest acceptable healthy BMI
   */
  const LOW_HEALTHY_BMI  = 18.5;

  /**
   * Highest acceptable healthy BMI
   */
  const HIGH_HEALTHY_BMI = 25.0;

  /**
   * Decimal points for rounding BMI and Weight
   */
  const PRECISION        = 1;

  /**
   * BodyWeight class constructor
   *
   * @param integer $height Users height in Centimeters or Inches
   * @param integer $weight Users weight in Kilograms or Pounds
   * @param string  $scale  UnitsManager constant for METRIC_SYSTEM or IMPERIAL_SYSTEM
   */
  public function __construct($height, $weight, $scale = UnitsManager::IMPERIAL_SYSTEM)
  {
    if ($scale === UnitsManager::IMPERIAL_SYSTEM) {
      $this->height = UnitsManager::inchesToCentimeters($height) / 100;
      $this->weight = UnitsManager::poundsToKilograms($weight);
    } else {
      $this->convert_units = false;

      $this->height = $height / 100;
      $this->weight = $weight;
    }
  }

  /**
   * Method to return the BMI for given weight in Kg
   *
   * @param float $weight Weight in Kb
   * @return float         BMI value
   */
  private function bmiForWeight($weight)
  {
    return round($weight / pow($this->height, 2), self::PRECISION);
  }

  /**
   * Method to return the BMI for the current weight
   *
   * @return float         BMI value
   */
  public function currentBmi()
  {
    return $this->bmiForWeight($this->weight);
  }

  /**
   * Method to test if current BMI/Weight is healthy
   *
   * @return boolean Test if healthy or not
   */
  public function isCurrentBmiHealthy()
  {
    $current_bmi = $this->currentBmi();

    return $current_bmi >= self::LOW_HEALTHY_BMI && $current_bmi <= self::HIGH_HEALTHY_BMI;
  }

  /**
   * Method to test if current BMI/Weight is healthy
   *
   * @return boolean Test if healthy or not
   */
  public function isCurrentWeightTooLow()
  {
    $current_bmi = $this->currentBmi();

    return $current_bmi <= self::LOW_HEALTHY_BMI;
  }

  /**
   * Method to test if target BMI/Weight is healthy
   *
   * @param float $target_weight Target weight in Kg or Pounds
   * @return boolean               Test if healthy or not
   */
  public function isTargetWeightHealthy($target_weight)
  {
    if ($this->convert_units) {
      $target_weight = UnitsManager::poundsToKilograms($target_weight);
    }

    $bmi = $this->bmiForWeight($target_weight);
    return $bmi >= self::LOW_HEALTHY_BMI && $bmi <= self::HIGH_HEALTHY_BMI;
  }

  /**
   * Returns a range of a healthy weight range based on BMI
   *
   * @return array Returns an array in kg or lb
   */
  public function currentHealthyTargetWeights()
  {
    $height_squared = pow($this->height, 2);

    $low  = self::LOW_HEALTHY_BMI * $height_squared;
    $high = self::HIGH_HEALTHY_BMI * $height_squared;

    if ($this->convert_units) {
      $low  = UnitsManager::kilogramsToPounds($low);
      $high = UnitsManager::kilogramsToPounds($high);
    }

    $low  = round($low,  self::PRECISION);
    $high = round($high, self::PRECISION);

    return compact('low', 'high');
  }
}
