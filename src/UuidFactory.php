<?php

namespace Rhumsaa\Uuid;

use Rhumsaa\Uuid\Codec\StringCodec;
use Rhumsaa\Uuid\Codec\GuidStringCodec;

class UuidFactory
{

    /**
     * For testing, 64-bit system override; if true, treat the system as 32-bit
     *
     * @var bool
     */
    public static $force32Bit = false;

    /**
     * For testing, Moontoast\Math\BigNumber override; if true, treat as if
     * BigNumber is not available
     *
     * @var bool
     */
    public static $forceNoBigNumber = false;

    /**
     * For testing, system override to ignore generating node from hardware
     *
     * @var bool
     */
    public static $ignoreSystemNode = false;

    /**
     * For testing, sets time of day to a static, known value
     *
     * @var array
     */
    public static $timeOfDayTest;

    /**
     * @var RandomGenerator
     */
    private static $prng = null;

    /**
     * Calculates the UUID time fields from a UNIX timestamp
     *
     * UUID time is a 60-bit time value as a count of 100-nanosecond intervals
     * since 00:00:00.00, 15 October 1582.
     *
     * @param int $sec Seconds since the Unix Epoch
     * @param int $usec Microseconds
     * @return array
     * @throws Exception\UnsatisfiedDependencyException if called on a 32-bit system and Moontoast\Math\BigNumber is not present
     */
    protected static function calculateUuidTime($sec, $usec)
    {
        if (self::is64BitSystem()) {

            // 0x01b21dd213814000 is the number of 100-ns intervals between the
            // UUID epoch 1582-10-15 00:00:00 and the Unix epoch 1970-01-01 00:00:00.
            $uuidTime = ($sec * 10000000) + ($usec * 10) + 0x01b21dd213814000;

            return array(
                'low' => sprintf('%08x', $uuidTime & 0xffffffff),
                'mid' => sprintf('%04x', ($uuidTime >> 32) & 0xffff),
                'hi' => sprintf('%04x', ($uuidTime >> 48) & 0x0fff),
            );
        }

        if (self::hasBigNumber()) {

            $uuidTime = new \Moontoast\Math\BigNumber('0');

            $sec = new \Moontoast\Math\BigNumber($sec);
            $sec->multiply('10000000');

            $usec = new \Moontoast\Math\BigNumber($usec);
            $usec->multiply('10');

            $uuidTime->add($sec)
            ->add($usec)
            ->add('122192928000000000');

            $uuidTimeHex = sprintf('%016s', $uuidTime->convertToBase(16));

            return array(
                'low' => substr($uuidTimeHex, 8),
                'mid' => substr($uuidTimeHex, 4, 4),
                'hi' => substr($uuidTimeHex, 0, 4),
            );
        }

        throw new Exception\UnsatisfiedDependencyException(
            'When calling ' . __METHOD__ . ' on a 32-bit system, '
            . 'Moontoast\Math\BigNumber must be present in order '
            . 'to generate version 1 UUIDs'
        );
    }

    /**
     * Get the hardware address as a 48-bit positive integer. If all attempts to
     * obtain the hardware address fail, we choose a random 48-bit number with
     * its eighth bit set to 1 as recommended in RFC 4122. "Hardware address"
     * means the MAC address of a network interface, and on a machine with
     * multiple network interfaces the MAC address of any one of them may be
     * returned.
     *
     * @return string
     */
    protected static function getNodeFromSystem()
    {
        $node = null;
        $pattern = '/[^:]([0-9A-Fa-f]{2}([:-])[0-9A-Fa-f]{2}(\2[0-9A-Fa-f]{2}){4})[^:]/';
        $matches = array();

        // Search the ifconfig output for all MAC addresses and return
        // the first one found
        if (preg_match_all($pattern, self::getIfconfig(), $matches, PREG_PATTERN_ORDER)) {
            $node = $matches[1][0];
            $node = str_replace(':', '', $node);
            $node = str_replace('-', '', $node);
        }

        return $node;
    }

    /**
     * Returns the network interface configuration for the system
     *
     * @todo Needs evaluation and possibly modification to ensure this works
     *       well across multiple platforms.
     * @codeCoverageIgnore
     */
    protected static function getIfconfig()
    {
        switch (strtoupper(substr(php_uname('a'), 0, 3))) {
            case 'WIN':
                $ifconfig = `ipconfig /all 2>&1`;
                break;
            case 'DAR':
                $ifconfig = `ifconfig 2>&1`;
                break;
            case 'LIN':
            default:
                $ifconfig = `netstat -ie 2>&1`;
                break;
        }

        return $ifconfig;
    }

    /**
     * Returns true if the system has Moontoast\Math\BigNumber
     *
     * @return bool
     */
    protected static function hasBigNumber()
    {
        return (class_exists('Moontoast\Math\BigNumber') && !self::$forceNoBigNumber);
    }

    /**
     * Returns true if the system is 64-bit, false otherwise
     *
     * @return bool
     */
    protected static function is64BitSystem()
    {
        return (PHP_INT_SIZE == 8 && !self::$force32Bit);
    }


    /**
     * Generates random bytes for use in version 4 UUIDs
     *
     * @param int $length
     * @return string
     */
    private static function generateBytes($length)
    {
        if (! self::$prng) {
            self::$prng = (new RandomGeneratorFactory())->getGenerator();
        }

        return self::$prng->generate($length);
    }

    private $codec = null;

    public function __construct(Codec $uuidCodec = null, Codec $guidCodec = null)
    {
        $this->codec = $uuidCodec ?: new StringCodec($this);
        $this->guidCodec = $guidCodec ?: new GuidStringCodec($this);
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
        return $this->codec->decodeBytes($this->getConverter(), $bytes);
    }

    public function fromGuidBytes($bytes)
    {
        return $this->guidCodec->decodeBytes($this->getConverter(), $bytes);
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
        return $this->codec->decode($this->getConverter(), $name);
    }

    public function fromGuidString($name)
    {
        return $this->guidCodec->decode($this->getConverter(), $name);
    }

    public function getConverter()
    {
        $converter = new BigNumberConverter();

        if (! self::hasBigNumber()) {
            $converter = new UnsatisfiedNumberConverter();
        }

        return $converter;
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
        if ($node === null && !self::$ignoreSystemNode) {
            $node = self::getNodeFromSystem();
        }

        // if $node is still null (couldn't get from system), randomly generate
        // a node value, according to RFC 4122, Section 4.5
        if ($node === null) {
            $node = sprintf('%06x%06x', mt_rand(0, 1 << 24), mt_rand(0, 1 << 24));
        }

        // Convert the node to hex, if it is still an integer
        if (is_int($node)) {
            $node = sprintf('%012x', $node);
        }

        if (ctype_xdigit($node) && strlen($node) <= 12) {
            $node = strtolower(sprintf('%012s', $node));
        } else {
            throw new \InvalidArgumentException('Invalid node value');
        }

        if ($clockSeq === null) {
            // Not using "stable storage"; see RFC 4122, Section 4.2.1.1
            $clockSeq = mt_rand(0, 1 << 14);
        }

        // Create a 60-bit time value as a count of 100-nanosecond intervals
        // since 00:00:00.00, 15 October 1582
        if (self::$timeOfDayTest === null) {
            $timeOfDay = gettimeofday();
        } else {
            $timeOfDay = self::$timeOfDayTest;
        }

        $uuidTime = self::calculateUuidTime($timeOfDay['sec'], $timeOfDay['usec']);

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
        if (!($ns instanceof UuidInterface)) {
            $ns = $this->codec->decode($this->getConverter(), $ns);
        }

        $hash = md5($ns->getBytes() . $name);

        return $this->uuidFromHashedName($hash, 3);
    }

    /**
     * Generate a version 4 (random) UUID.
     *
     * @return Uuid
     */
    public function uuid4()
    {
        $bytes = self::generateBytes(16);

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
        if (!($ns instanceof Uuid)) {
            $ns = $this->codec->decode($this->getConverter(), $ns);
        }

        $hash = sha1($ns->getBytes() . $name);

        return $this->uuidFromHashedName($hash, 5);
    }

    public function uuid(array $fields, Codec $codec = null)
    {
        $codec = $codec ?: $this->codec;

        if (! self::is64BitSystem()) {
            return new SmallIntUuid($fields, $this->getConverter(), $codec);
        }

        return new Uuid($fields, $this->getConverter(), $codec);
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
