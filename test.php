<?php
App::uses('BodyWeight', 'Fuzz');
App::uses('UnitsManager', 'Fuzz');

class FuzzBodyWeightTest extends CakeTestCase
{
  const HEIGHT_INCHES = 69; // 5 feet 9 inches
  const WEIGHT_POUNDS = 150; // 300 lbs

  public function setup()
  {
    $this->body_weight_imperial = new BodyWeight(
      self::HEIGHT_INCHES,
      self::WEIGHT_POUNDS,
      UnitsManager::IMPERIAL_SYSTEM
    );

    $this->body_weight_metric = new BodyWeight(
      UnitsManager::inchesToCentimeters(self::HEIGHT_INCHES),
      UnitsManager::poundsToKilograms(self::WEIGHT_POUNDS),
      UnitsManager::METRIC_SYSTEM
    );
  }

  /**
   * Test Init
   *
   * Ensure that the height and weight are what we expect after initializing our BodyWeight library.
   */
  public function testInit()
  {
    $this->assertEquals(UnitsManager::inchesToCentimeters(self::HEIGHT_INCHES) / 100, $this->body_weight_imperial->height, 'Height Imperial');
    $this->assertEquals(UnitsManager::poundsToKilograms(self::WEIGHT_POUNDS), $this->body_weight_imperial->weight, 'Weight Imperial');

    $this->assertEquals(UnitsManager::inchesToCentimeters(self::HEIGHT_INCHES) / 100, $this->body_weight_metric->height, 'Height Metric');
    $this->assertEquals(UnitsManager::poundsToKilograms(self::WEIGHT_POUNDS), $this->body_weight_metric->weight, 'Weight Metric');
  }

  /**
   * Test Component Methods On Body Weight Imperial
   *
   * Verifies that imperial measurements are as expected.
   */
  public function testComponentMethodsOnBodyWeightImperial()
  {
    $body_weight = $this->body_weight_imperial;
    $this->assertEquals(22.2, $body_weight->currentBmi(), 'Healthy BMI matches');
    $this->assertEquals(false, $body_weight->isCurrentWeightTooLow(), 'Weight is not too low');
    $this->assertEquals(true, $body_weight->isCurrentBmiHealthy(), 'Healthy BMI check');
    $this->assertEquals(['low' => 125.3, 'high' => 169.3], $body_weight->currentHealthyTargetWeights(), 'Healthy target weights');

    $body_weight->weight = 300; // 300 lbs
    $this->assertEquals(97.7, $body_weight->currentBmi(), 'Unhealthy BMI matches');
    $this->assertEquals(false, $body_weight->isCurrentBmiHealthy(), 'Unhealthy BMI check');

    $body_weight->weight = 50;
    $this->assertEquals(true, $body_weight->isCurrentWeightTooLow(), 'Weight is too low');

    $this->assertEquals(true, $body_weight->isTargetWeightHealthy(150), 'Target weight is healthy');
    $this->assertEquals(false, $body_weight->isTargetWeightHealthy(500), 'Target weight is unhealthy');

    // unchanged when weight changes
    $this->assertEquals(['low' => 125.3, 'high' => 169.3], $body_weight->currentHealthyTargetWeights(), 'Healthy target weights');
  }

  /**
   * Test Component Methods On Body Weight Metric
   *
   * Verifies that imperial measurements are as expected.
   */
  public function testComponentMethodsOnBodyWeightMetric()
  {
    $body_weight = $this->body_weight_metric;
    $this->assertEquals(22.2, $body_weight->currentBmi(), 'Healthy BMI matches');
    $this->assertEquals(false, $body_weight->isCurrentWeightTooLow(), 'Weight is not too low');
    $this->assertEquals(true, $body_weight->isCurrentBmiHealthy(), 'Healthy BMI check');
    $this->assertEquals(['low' => 56.8, 'high' => 76.8], $body_weight->currentHealthyTargetWeights(), 'Healthy target weights');

    $body_weight->weight = 300; // 300 lbs
    $this->assertEquals(97.7, $body_weight->currentBmi(), 'Unhealthy BMI matches');
    $this->assertEquals(false, $body_weight->isCurrentBmiHealthy(), 'Unhealthy BMI check');

    $body_weight->weight = 50;
    $this->assertEquals(true, $body_weight->isCurrentWeightTooLow(), 'Weight is too low');

    $this->assertEquals(true, $body_weight->isTargetWeightHealthy(UnitsManager::poundsToKilograms(self::WEIGHT_POUNDS)), 'Target weight is healthy');
    $this->assertEquals(false, $body_weight->isTargetWeightHealthy(200), 'Target weight is unhealthy');

    // unchanged when weight changes
    $this->assertEquals(['low' => 56.8, 'high' => 76.8], $body_weight->currentHealthyTargetWeights(), 'Healthy target weights');
  }
}
