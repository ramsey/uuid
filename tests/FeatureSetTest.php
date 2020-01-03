<?php

declare(strict_types=1);

namespace Ramsey\Uuid\Test;

use Mockery;
use Ramsey\Uuid\Builder\FallbackBuilder;
use Ramsey\Uuid\FeatureSet;
use Ramsey\Uuid\Guid\DegradedGuidBuilder;
use Ramsey\Uuid\Validator\ValidatorInterface;

class FeatureSetTest extends TestCase
{
    public function testDegradedGuidBuilderIsSelectedOn32BitSystem(): void
    {
        $featureSet = new FeatureSet(true, true);

        $this->assertInstanceOf(DegradedGuidBuilder::class, $featureSet->getBuilder());
    }

    public function testFallbackBuilderIsSelectedOn32BitSystem(): void
    {
        $featureSet = new FeatureSet(false, true);

        $this->assertInstanceOf(FallbackBuilder::class, $featureSet->getBuilder());
    }

    public function testSetValidatorSetsTheProvidedValidator(): void
    {
        $validator = Mockery::mock(ValidatorInterface::class);

        $featureSet = new FeatureSet();
        $featureSet->setValidator($validator);

        $this->assertSame($validator, $featureSet->getValidator());
    }
}
