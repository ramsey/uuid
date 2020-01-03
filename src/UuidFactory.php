<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Ramsey\Uuid;

use Ramsey\Uuid\Builder\UuidBuilderInterface;
use Ramsey\Uuid\Codec\CodecInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Generator\RandomGeneratorInterface;
use Ramsey\Uuid\Generator\TimeGeneratorInterface;
use Ramsey\Uuid\Provider\NodeProviderInterface;
use Ramsey\Uuid\Validator\ValidatorInterface;

class UuidFactory implements UuidFactoryInterface
{
    /**
     * @var CodecInterface
     */
    private $codec;

    /**
     * @var NodeProviderInterface
     */
    private $nodeProvider;

    /**
     * @var NumberConverterInterface
     */
    private $numberConverter;

    /**
     * @var RandomGeneratorInterface
     */
    private $randomGenerator;

    /**
     * @var TimeGeneratorInterface
     */
    private $timeGenerator;

    /**
     * @var UuidBuilderInterface
     */
    private $uuidBuilder;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param FeatureSet $features A set of available features in the current environment
     */
    public function __construct(?FeatureSet $features = null)
    {
        $features = $features ?: new FeatureSet();

        $this->codec = $features->getCodec();
        $this->nodeProvider = $features->getNodeProvider();
        $this->numberConverter = $features->getNumberConverter();
        $this->randomGenerator = $features->getRandomGenerator();
        $this->timeGenerator = $features->getTimeGenerator();
        $this->uuidBuilder = $features->getBuilder();
        $this->validator = $features->getValidator();
    }

    /**
     * Returns the codec used by this factory
     */
    public function getCodec(): CodecInterface
    {
        return $this->codec;
    }

    /**
     * Sets the codec to use for this factory
     *
     * @param CodecInterface $codec A UUID encoder-decoder
     */
    public function setCodec(CodecInterface $codec): void
    {
        $this->codec = $codec;
    }

    /**
     * Returns the node provider used by this factory
     */
    public function getNodeProvider(): NodeProviderInterface
    {
        return $this->nodeProvider;
    }

    /**
     * Returns the random generator used by this factory
     */
    public function getRandomGenerator(): RandomGeneratorInterface
    {
        return $this->randomGenerator;
    }

    /**
     * Returns the time generator used by this factory
     */
    public function getTimeGenerator(): TimeGeneratorInterface
    {
        return $this->timeGenerator;
    }

    /**
     * Sets the time generator to use for this factory
     *
     * @param TimeGeneratorInterface $generator A generator to generate binary
     *     data, based on the time
     */
    public function setTimeGenerator(TimeGeneratorInterface $generator): void
    {
        $this->timeGenerator = $generator;
    }

    /**
     * Returns the number converter used by this factory
     */
    public function getNumberConverter(): NumberConverterInterface
    {
        return $this->numberConverter;
    }

    /**
     * Sets the random generator to use for this factory
     *
     * @param RandomGeneratorInterface $generator A generator to generate binary
     *     data, based on some random input
     */
    public function setRandomGenerator(RandomGeneratorInterface $generator): void
    {
        $this->randomGenerator = $generator;
    }

    /**
     * Sets the number converter to use for this factory
     *
     * @param NumberConverterInterface $converter A converter to use for working
     *     with large integers (i.e. integers greater than PHP_INT_MAX)
     */
    public function setNumberConverter(NumberConverterInterface $converter): void
    {
        $this->numberConverter = $converter;
    }

    /**
     * Returns the UUID builder used by this factory
     */
    public function getUuidBuilder(): UuidBuilderInterface
    {
        return $this->uuidBuilder;
    }

    /**
     * Sets the UUID builder to use for this factory
     *
     * @param UuidBuilderInterface $builder A builder for constructing instances
     *     of UuidInterface
     */
    public function setUuidBuilder(UuidBuilderInterface $builder): void
    {
        $this->uuidBuilder = $builder;
    }

    /**
     * @psalm-mutation-free
     */
    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }

    /**
     * Sets the validator to use for this factory
     *
     * @param ValidatorInterface $validator A validator to use for validating
     *     whether a string is a valid UUID
     */
    public function setValidator(ValidatorInterface $validator): void
    {
        $this->validator = $validator;
    }

    /**
     * @psalm-pure
     */
    public function fromBytes(string $bytes): UuidInterface
    {
        return $this->codec->decodeBytes($bytes);
    }

    /**
     * @psalm-pure
     */
    public function fromString(string $uuid): UuidInterface
    {
        $uuid = strtolower($uuid);

        return $this->codec->decode($uuid);
    }

    /**
     * @psalm-pure
     */
    public function fromInteger(string $integer): UuidInterface
    {
        $hex = $this->numberConverter->toHex($integer);
        $hex = str_pad($hex, 32, '0', STR_PAD_LEFT);

        return $this->fromString($hex);
    }

    /**
     * @inheritDoc
     */
    public function uuid1($node = null, ?int $clockSeq = null): UuidInterface
    {
        $bytes = $this->timeGenerator->generate($node, $clockSeq);
        $hex = bin2hex($bytes);

        return $this->uuidFromHashedName($hex, 1);
    }

    /**
     * @inheritDoc
     */
    public function uuid3($ns, string $name): UuidInterface
    {
        return $this->uuidFromNsAndName($ns, $name, 3, 'md5');
    }

    public function uuid4(): UuidInterface
    {
        $bytes = $this->randomGenerator->generate(16);

        // When converting the bytes to hex, it turns into a 32-character
        // hexadecimal string that looks a lot like an MD5 hash, so at this
        // point, we can just pass it to uuidFromHashedName.
        $hex = bin2hex((string) $bytes);

        return $this->uuidFromHashedName($hex, 4);
    }

    /**
     * @inheritDoc
     */
    public function uuid5($ns, string $name): UuidInterface
    {
        return $this->uuidFromNsAndName($ns, $name, 5, 'sha1');
    }

    /**
     * Returns a Uuid created from the provided fields
     *
     * Uses the configured builder and codec and the provided array of
     * hexadecimal-value UUID fields to construct a Uuid object.
     *
     * @param string[] $fields An array of fields from which to construct a UUID;
     *     see {@see \Ramsey\Uuid\UuidInterface::getFieldsHex()} for array structure
     *
     * @return UuidInterface An instance of UuidInterface, created from the
     *     provided fields
     */
    public function uuid(array $fields): UuidInterface
    {
        return $this->uuidBuilder->build($this->codec, $fields);
    }

    /**
     * Returns a version 3 or 5 namespaced Uuid
     *
     * @param string|UuidInterface $ns The namespace (must be a valid UUID)
     * @param string $name The name to hash together with the namespace
     * @param int $version The version of UUID to create (3 or 5)
     * @param callable $hashFunction The hash function to use when hashing together
     *     the namespace and name
     *
     * @return UuidInterface An instance of UuidInterface, created by hashing
     *     together the provided namespace and name
     */
    private function uuidFromNsAndName($ns, string $name, int $version, callable $hashFunction): UuidInterface
    {
        if (!($ns instanceof UuidInterface)) {
            $ns = $this->codec->decode($ns);
        }

        $hash = (string) call_user_func($hashFunction, $ns->getBytes() . $name);

        return $this->uuidFromHashedName($hash, $version);
    }

    /**
     * Returns an RFC 4122 variant Uuid, created from the provided hash and version
     *
     * @param string $hash The hashed string to convert to a UUID
     * @param int $version The RFC 4122 version to apply to the UUID
     *
     * @return UuidInterface An instance of UuidInterface, created from the
     *     hashed string and version
     */
    private function uuidFromHashedName(string $hash, int $version): UuidInterface
    {
        $timeHi = BinaryUtils::applyVersion(substr($hash, 12, 4), $version);
        $clockSeqHi = BinaryUtils::applyVariant((int) hexdec(substr($hash, 16, 2)));

        $fields = [
            'time_low' => substr($hash, 0, 8),
            'time_mid' => substr($hash, 8, 4),
            'time_hi_and_version' => str_pad(dechex($timeHi), 4, '0', STR_PAD_LEFT),
            'clock_seq_hi_and_reserved' => str_pad(dechex($clockSeqHi), 2, '0', STR_PAD_LEFT),
            'clock_seq_low' => substr($hash, 18, 2),
            'node' => substr($hash, 20, 12),
        ];

        return $this->uuid($fields);
    }
}
