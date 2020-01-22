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

namespace Ramsey\Uuid\Generator;

use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Exception\InvalidArgumentException;
use Ramsey\Uuid\Provider\DceSecurityProviderInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\IntegerValue;
use Ramsey\Uuid\Uuid;

/**
 * DceSecurityGenerator generates strings of binary data based on a local
 * domain, local identifier, node ID, clock sequence, and the current time
 */
class DceSecurityGenerator implements DceSecurityGeneratorInterface
{
    private const DOMAINS = [
        Uuid::DCE_DOMAIN_PERSON,
        Uuid::DCE_DOMAIN_GROUP,
        Uuid::DCE_DOMAIN_ORG,
    ];

    /**
     * @var NumberConverterInterface
     */
    private $numberConverter;

    /**
     * @var TimeGeneratorInterface
     */
    private $timeGenerator;

    /**
     * @var DceSecurityProviderInterface
     */
    private $dceSecurityProvider;

    public function __construct(
        NumberConverterInterface $numberConverter,
        TimeGeneratorInterface $timeGenerator,
        DceSecurityProviderInterface $dceSecurityProvider
    ) {
        $this->numberConverter = $numberConverter;
        $this->timeGenerator = $timeGenerator;
        $this->dceSecurityProvider = $dceSecurityProvider;
    }

    public function generate(
        int $localDomain,
        ?IntegerValue $localIdentifier = null,
        ?Hexadecimal $node = null,
        ?int $clockSeq = null
    ): string {
        if (!in_array($localDomain, self::DOMAINS)) {
            throw new InvalidArgumentException(
                'Local domain must be a valid DCE Security domain'
            );
        }

        switch ($localDomain) {
            case Uuid::DCE_DOMAIN_ORG:
                if ($localIdentifier === null) {
                    throw new InvalidArgumentException(
                        'A local identifier must be provided for the org domain'
                    );
                }

                break;
            case Uuid::DCE_DOMAIN_PERSON:
                if ($localIdentifier === null) {
                    $localIdentifier = $this->dceSecurityProvider->getUid();
                }

                break;
            case Uuid::DCE_DOMAIN_GROUP:
            default:
                if ($localIdentifier === null) {
                    $localIdentifier = $this->dceSecurityProvider->getGid();
                }

                break;
        }

        $domainByte = pack('n', $localDomain)[1];
        $identifierBytes = pack('N*', $localIdentifier->toString());

        if ($node instanceof Hexadecimal) {
            $node = $node->toString();
        }

        /** @var string $bytes */
        $bytes = $this->timeGenerator->generate($node, $clockSeq);

        // Replace bytes in the time-based UUID with DCE Security values.
        $bytes = substr_replace($bytes, $identifierBytes, 0, 4);
        $bytes = substr_replace($bytes, $domainByte, 9, 1);

        return $bytes;
    }
}
