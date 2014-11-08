<?php

namespace Rhumsaa\Uuid;

use Rhumsaa\Uuid\Codec\StringCodec;
use Rhumsaa\Uuid\Codec\GuidStringCodec;
use Rhumsaa\Uuid\Time\PhpTimeConverter;
use Rhumsaa\Uuid\Time\BigNumberTimeConverter;
use Rhumsaa\Uuid\Time\DegradedTimeConverter;
use Rhumsaa\Uuid\Time\SystemTimeProvider;
use Rhumsaa\Uuid\Node\FallbackNodeProvider;
use Rhumsaa\Uuid\Node\SystemNodeProvider;
use Rhumsaa\Uuid\Node\RandomNodeProvider;

class UuidFactory
{

    /**
     *
     * @var Codec
     */
    private $codec = null;

    /**
     *
     * @var NodeProvider
     */
    private $nodeProvider = null;

    /**
     *
     * @var BigNumberConverter
     */
    private $numberConverter = null;

    /**
     * @var RandomGenerator
     */
    private $randomGenerator = null;

    /**
     *
     * @var TimeConverter
     */
    private $timeConverter = null;

    /**
     *
     * @var TimeProvider
     */
    private $timeProvider = null;

    /**
     *
     * @var UuidBuilder
     */
    private $uuidBuilder = null;

    /**
     * Create a new a instance
     *
     */
    public function __construct(FeatureSet $features = null)
    {
        $features = $features ?: new FeatureSet();

        $this->codec = $features->getCodec();
        $this->nodeProvider = $features->getNodeProvider();
        $this->numberConverter = $features->getNumberConverter();
        $this->randomGenerator = $features->getRandomGenerator();
        $this->timeConverter = $features->getTimeConverter();
        $this->timeProvider = $features->getTimeProvider();
        $this->uuidBuilder = $features->getBuilder();
    }

    public function getCodec()
    {
        return $this->codec;
    }

    public function setTimeConverter(TimeConverter $converter)
    {
        $this->timeConverter = $converter;
    }

    public function setTimeProvider(TimeProvider $provider)
    {
        $this->timeProvider = $provider;
    }

    public function setRandomGenerator(RandomGenerator $generator)
    {
        $this->randomGenerator = $generator;
    }

    public function setNodeProvider(NodeProvider $provider)
    {
        $this->nodeProvider = $provider;
    }

    public function setNumberConverter(BigNumberConverter $converter)
    {
        $this->numberConverter = $converter;
    }

    public function setUuidBuilder(UuidBuilder $builder)
    {
        $this->uuidBuilder = $builder;
    }

    /**
     * Creates a UUID from a byte string.
     *
     * @param string $bytes
     * @return Uuid
     * @throws InvalidArgumentException If the $bytes string does not contain 16 characters
     */
    public function fromBytes($bytes)
    {
        return $this->codec->decodeBytes($bytes);
    }

    /**
     * Creates a UUID from the string standard representation as described
     * in the toString() method.
     *
     * @param string $name A string that specifies a UUID
     * @param bool $littleEndian A boolean specifying whether the time_low, time_mid, time_hi_and_version are encoded in little-endian format.
     * @return Uuid
     * @throws InvalidArgumentException If the $name isn't a valid UUID
     */
    public function fromString($name)
    {
        return $this->codec->decode($name);
    }

    public function fromInteger($integer)
    {
        $hex = $this->numberConverter->toHex($integer);
        $hex = str_pad($hex, 32, '0', STR_PAD_LEFT);

        return $this->fromString($hex);
    }

    /**
     * Generate a version 1 UUID from a host ID, sequence number, and the current time.
     * If $node is not given, we will attempt to obtain the local hardware
     * address. If $clockSeq is given, it is used as the sequence number;
     * otherwise a random 14-bit sequence number is chosen.
     *
     * @param int|string $node A 48-bit number representing the hardware
     *                         address. This number may be represented as
     *                         an integer or a hexadecimal string.
     * @param int $clockSeq A 14-bit number used to help avoid duplicates that
     *                      could arise when the clock is set backwards in time
     *                      or if the node ID changes.
     * @return Uuid
     * @throws InvalidArgumentException if the $node is invalid
     */
    public function uuid1($node = null, $clockSeq = null)
    {
        if ($node === null) {
            $node = $this->nodeProvider->getNode();
        }

        // Convert the node to hex, if it is still an integer
        if (is_int($node)) {
            $node = sprintf('%012x', $node);
        }

        if (! ctype_xdigit($node) || strlen($node) > 12) {
            throw new \InvalidArgumentException('Invalid node value');
        }

        $node = strtolower(sprintf('%012s', $node));

        if ($clockSeq === null) {
            // Not using "stable storage"; see RFC 4122, Section 4.2.1.1
            $clockSeq = mt_rand(0, 1 << 14);
        }

        // Create a 60-bit time value as a count of 100-nanosecond intervals
        // since 00:00:00.00, 15 October 1582
        $timeOfDay = $this->timeProvider->currentTime();
        $uuidTime = $this->timeConverter->calculateTime($timeOfDay['sec'], $timeOfDay['usec']);

        // Set the version number to 1
        $timeHi = hexdec($uuidTime['hi']) & 0x0fff;
        $timeHi &= ~(0xf000);
        $timeHi |= 1 << 12;

        // Set the variant to RFC 4122
        $clockSeqHi = ($clockSeq >> 8) & 0x3f;
        $clockSeqHi &= ~(0xc0);
        $clockSeqHi |= 0x80;

        $fields = array(
            'time_low' => $uuidTime['low'],
            'time_mid' => $uuidTime['mid'],
            'time_hi_and_version' => sprintf('%04x', $timeHi),
            'clock_seq_hi_and_reserved' => sprintf('%02x', $clockSeqHi),
            'clock_seq_low' => sprintf('%02x', $clockSeq & 0xff),
            'node' => $node,
        );

        return $this->uuid($fields);
    }


    /**
     * Generate a version 3 UUID based on the MD5 hash of a namespace identifier (which
     * is a UUID) and a name (which is a string).
     *
     * @param Uuid|string $ns The UUID namespace in which to create the named UUID
     * @param string $name The name to create a UUID for
     * @return Uuid
     */
    public function uuid3($ns, $name)
    {
        return $this->uuidFromNsAndName($ns, $name, 3, 'md5');
    }

    /**
     * Generate a version 4 (random) UUID.
     *
     * @return Uuid
     */
    public function uuid4()
    {
        $bytes = $this->randomGenerator->generate(16);

        // When converting the bytes to hex, it turns into a 32-character
        // hexadecimal string that looks a lot like an MD5 hash, so at this
        // point, we can just pass it to uuidFromHashedName.
        $hex = bin2hex($bytes);

        return $this->uuidFromHashedName($hex, 4);
    }

    /**
     * Generate a version 5 UUID based on the SHA-1 hash of a namespace identifier (which
     * is a UUID) and a name (which is a string).
     *
     * @param Uuid|string $ns The UUID namespace in which to create the named UUID
     * @param string $name The name to create a UUID for
     * @return Uuid
     */
    public function uuid5($ns, $name)
    {
        return $this->uuidFromNsAndName($ns, $name, 5, 'sha1');
    }

    public function uuid(array $fields)
    {
        return $this->uuidBuilder->build($this->codec, $fields);
    }

    protected function uuidFromNsAndName($ns, $name, $version, $hashFunction)
    {
        if (!($ns instanceof Uuid)) {
            $ns = $this->codec->decode($ns);
        }

        $hash = call_user_func($hashFunction, ($ns->getBytes() . $name));

        return $this->uuidFromHashedName($hash, $version);
    }

    /**
     * Returns a version 3 or 5 UUID based on the hash (md5 or sha1) of a
     * namespace identifier (which is a UUID) and a name (which is a string)
     *
     * @param string $hash The hash to use when creating the UUID
     * @param int $version The UUID version to be generated
     * @return Uuid
     */
    protected function uuidFromHashedName($hash, $version)
    {
        // Set the version number
        $timeHi = hexdec(substr($hash, 12, 4)) & 0x0fff;
        $timeHi &= ~(0xf000);
        $timeHi |= $version << 12;

        // Set the variant to RFC 4122
        $clockSeqHi = hexdec(substr($hash, 16, 2)) & 0x3f;
        $clockSeqHi &= ~(0xc0);
        $clockSeqHi |= 0x80;

        $fields = array(
            'time_low' => substr($hash, 0, 8),
            'time_mid' => substr($hash, 8, 4),
            'time_hi_and_version' => sprintf('%04x', $timeHi),
            'clock_seq_hi_and_reserved' => sprintf('%02x', $clockSeqHi),
            'clock_seq_low' => substr($hash, 18, 2),
            'node' => substr($hash, 20, 12),
        );

        return $this->uuid($fields);
    }
}
