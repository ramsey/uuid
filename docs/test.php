<?php

require_once '../vendor/autoload.php';

use Ramsey\Uuid\FeatureSet;
use Ramsey\Uuid\Provider\Node\StaticNodeProvider;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;

$nodeProvider = new StaticNodeProvider(new Hexadecimal('1234567890ab'));

$featureSet = new FeatureSet();
$featureSet->setNodeProvider($nodeProvider);

$factory = new UuidFactory($featureSet);

Uuid::setFactory($factory);

$uuid = Uuid::uuid1();

echo $uuid->toString() . "\n";
echo $uuid->getDateTime()->format('r') . "\n";
echo $uuid->getFields()->getNode()->toString() . "\n";
