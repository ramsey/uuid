.. _customize.calculators:

=========================
Using a Custom Calculator
=========================

By default, ramsey/uuid uses `brick/math`_  as its internal calculator. However, you may change the calculator, if your
needs require something else.

To swap the default calculator with your custom one, first make an adapter that wraps your custom calculator and
implements :php:interface:`Ramsey\\Uuid\\Math\\CalculatorInterface`. This might look something like this:

.. code-block:: php
    :caption: Create a custom calculator wrapper that implements CalculatorInterface
    :name: customize.calculators.wrapper-example

    namespace MyProject;

    use Other\OtherCalculator;
    use Ramsey\Uuid\Math\CalculatorInterface;
    use Ramsey\Uuid\Type\Integer as IntegerObject;
    use Ramsey\Uuid\Type\NumberInterface;

    class MyUuidCalculator implements CalculatorInterface
    {
        private $internalCalculator;

        public function __construct(OtherCalculator $customCalculator)
        {
            $this->internalCalculator = $customCalculator;
        }

        public function add(NumberInterface $augend, NumberInterface ...$addends): NumberInterface
        {
            $value = $augend->toString();

            foreach ($addends as $addend) {
                $value = $this->internalCalculator->plus($value, $addend->toString());
            }

            return new IntegerObject($value);
        }

        /* ... Class truncated for brevity ... */

    }

The easiest way to use your custom calculator wrapper is to instantiate a new FeatureSet, set the calculator on it, and
pass the FeatureSet into a new UuidFactory. Using the factory, you may then generate and work with UUIDs, using your
custom calculator.

.. code-block:: php
    :caption: Use your custom calculator wrapper when working with UUIDs
    :name: customize.calculators.use-wrapper-example

    use MyProject\MyUuidCalculator;
    use Other\OtherCalculator;
    use Ramsey\Uuid\FeatureSet;
    use Ramsey\Uuid\UuidFactory;

    $otherCalculator = new OtherCalculator();
    $myUuidCalculator = new MyUuidCalculator($otherCalculator);

    $featureSet = new FeatureSet();
    $featureSet->setCalculator($myUuidCalculator);

    $factory = new UuidFactory($featureSet);

    $uuid = $factory->uuid1();

.. _brick/math: https://github.com/brick/math
