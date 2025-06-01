.. _reference.calculators:

===========
Calculators
===========

.. php:namespace:: Ramsey\Uuid\Math

.. php:interface:: CalculatorInterface

    Provides functionality for performing mathematical calculations.

    .. php:method:: add($augend, ...$addends)

        :param Ramsey\\Uuid\\Type\\NumberInterface $augend: The first addend (the integer being added to)
        :param Ramsey\\Uuid\\Type\\NumberInterface ...$addends: The additional integers to a add to the augend
        :returns: The sum of all the parameters
        :returntype: Ramsey\\Uuid\\Type\\NumberInterface

    .. php:method:: subtract($minuend, ...$subtrahends)

        :param Ramsey\\Uuid\\Type\\NumberInterface $minuend: The integer being subtracted from
        :param Ramsey\\Uuid\\Type\\NumberInterface ...$subtrahends: The integers to subtract from the minuend
        :returns: The difference after subtracting all parameters
        :returntype: Ramsey\\Uuid\\Type\\NumberInterface

    .. php:method:: multiply($multiplicand, ...$multipliers)

        :param Ramsey\\Uuid\\Type\\NumberInterface $multiplicand: The integer to be multiplied
        :param Ramsey\\Uuid\\Type\\NumberInterface ...$multipliers: The factors by which to multiply the multiplicand
        :returns: The product of multiplying all the provided parameters
        :returntype: Ramsey\\Uuid\\Type\\NumberInterface

    .. php:method:: divide($roundingMode, $scale, $dividend, ...$divisors)

        :param int $roundingMode: The strategy for rounding the quotient; one of the :php:class:`Ramsey\\Uuid\\Math\\RoundingMode` constants
        :param int $scale: The scale to use for the operation
        :param Ramsey\\Uuid\\Type\\NumberInterface $dividend: The integer to be divided
        :param Ramsey\\Uuid\\Type\\NumberInterface ...$divisors: The integers to divide ``$dividend`` by, in the order in which the division operations should take place (left-to-right)
        :returns: The quotient of dividing the provided parameters left-to-right
        :returntype: Ramsey\\Uuid\\Type\\NumberInterface


    .. php:method:: fromBase($value, $base)

        Converts a value from an arbitrary base to a base-10 integer value.

        :param string $value: The value to convert
        :param int $base: The base to convert from (i.e., 2, 16, 32, etc.)
        :returns: The base-10 integer value of the converted value
        :returntype: Ramsey\\Uuid\\Type\\Integer

    .. php:method:: toBase($value, $base)

        Converts a base-10 integer value to an arbitrary base.

        :param Ramsey\\Uuid\\Type\\Integer $value: The integer value to convert
        :param int $base: The base to convert to (i.e., 2, 16, 32, etc.)
        :returns: The value represented in the specified base
        :returntype: ``string``

    .. php:method:: toHexadecimal($value)

        Converts an Integer instance to a Hexadecimal instance.

        :param Ramsey\\Uuid\\Type\\Integer $value: The Integer to convert to Hexadecimal
        :returntype: Ramsey\\Uuid\\Type\\Hexadecimal

    .. php:method:: toInteger($value)

        Converts a Hexadecimal instance to an Integer instance.

        :param Ramsey\\Uuid\\Type\\Hexadecimal $value: The Hexadecimal to convert to Integer
        :returntype: Ramsey\\Uuid\\Type\\Integer


.. php:class:: RoundingMode

    .. php:const:: UNNECESSARY

        Asserts that the requested operation has an exact result, hence no rounding is necessary.

    .. php:const:: UP

        Rounds away from zero.

        Always increments the digit prior to a nonzero discarded fraction. Note that this rounding mode never decreases
        the magnitude of the calculated value.

    .. php:const:: DOWN

        Rounds towards zero.

        Never increments the digit prior to a discarded fraction (i.e., truncates). Note that this rounding mode never
        increases the magnitude of the calculated value.

    .. php:const:: CEILING

        Rounds towards positive infinity.

        If the result is positive, behaves as for :php:const:`UP <Ramsey\\Uuid\\Math\\RoundingMode::UP>`; if negative,
        behaves as for :php:const:`DOWN <Ramsey\\Uuid\\Math\\RoundingMode::DOWN>`. Note that this rounding mode never
        decreases the calculated value.

    .. php:const:: FLOOR

        Rounds towards negative infinity.

        If the result is positive, behave as for :php:const:`DOWN <Ramsey\\Uuid\\Math\\RoundingMode::DOWN>`; if negative,
        behave as for :php:const:`UP <Ramsey\\Uuid\\Math\\RoundingMode::UP>`. Note that this rounding mode never
        increases the calculated value.

    .. php:const:: HALF_UP

        Rounds towards "nearest neighbor" unless both neighbors are equidistant, in which case round up.

        Behaves as for :php:const:`UP <Ramsey\\Uuid\\Math\\RoundingMode::UP>` if the discarded fraction is >= 0.5;
        otherwise, behaves as for :php:const:`DOWN <Ramsey\\Uuid\\Math\\RoundingMode::DOWN>`. Note that this is the
        rounding mode commonly taught at school.

    .. php:const:: HALF_DOWN

        Rounds towards "nearest neighbor" unless both neighbors are equidistant, in which case round down.

        Behaves as for :php:const:`UP <Ramsey\\Uuid\\Math\\RoundingMode::UP>` if the discarded fraction is > 0.5;
        otherwise, behaves as for :php:const:`DOWN <Ramsey\\Uuid\\Math\\RoundingMode::DOWN>`.

    .. php:const:: HALF_CEILING

        Rounds towards "nearest neighbor" unless both neighbors are equidistant, in which case round towards positive
        infinity.

        If the result is positive, behaves as for :php:const:`HALF_UP <Ramsey\\Uuid\\Math\\RoundingMode::HALF_UP>`; if
        negative, behaves as for :php:const:`HALF_DOWN <Ramsey\\Uuid\\Math\\RoundingMode::HALF_DOWN>`.

    .. php:const:: HALF_FLOOR

        Rounds towards "nearest neighbor" unless both neighbors are equidistant, in which case round towards negative
        infinity.

        If the result is positive, behaves as for :php:const:`HALF_DOWN <Ramsey\\Uuid\\Math\\RoundingMode::HALF_DOWN>`;
        if negative, behaves as for :php:const:`HALF_UP <Ramsey\\Uuid\\Math\\RoundingMode::HALF_UP>`.

    .. php:const:: HALF_EVEN

        Rounds towards the "nearest neighbor" unless both neighbors are equidistant, in which case rounds towards the
        even neighbor.

        Behaves as for :php:const:`HALF_UP <Ramsey\\Uuid\\Math\\RoundingMode::HALF_UP>` if the digit to the left of the
        discarded fraction is odd; behaves as for :php:const:`HALF_DOWN <Ramsey\\Uuid\\Math\\RoundingMode::HALF_DOWN>`
        if it's even.

        Note that this is the rounding mode that statistically minimizes cumulative error when applied repeatedly over a
        sequence of calculations. It is sometimes known as "Banker's rounding", and is chiefly used in the USA.
