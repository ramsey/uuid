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

namespace Ramsey\Uuid\Benchmark;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

use function array_map;

final class UuidStringConversionBench
{
    private const TINY_UUID = '00000000-0000-0000-0000-000000000001';
    private const HUGE_UUID = 'ffffffff-ffff-ffff-ffff-ffffffffffff';
    private const UUIDS_TO_BE_SHORTENED = [
        '0ae0cac5-2a40-465c-99ed-3d331b7cf72a',
        '5759b9ce-07b5-4e89-b33a-f864317a2951',
        '20c8664e-81a8-498d-9e98-444973ef3122',
        '16fcbcf3-bb47-4227-90bd-3485d60510c3',
        'fa83ae94-38e0-4903-bc6a-0a3eca6e9ef5',
        '51c9e011-0429-4d77-a753-702bd67dcd84',
        '1bd8857a-d6d7-4bd6-8734-b3dfedbcda7b',
        '7aa38b71-37c3-4561-9b2e-ca227f1c9c55',
        'e6b8854c-435c-4bb1-b6ad-1800b5d3e6bb',
        '4e2b0031-8b09-46e2-8244-3814c46a2f53',
        'bedd0850-da1a-4808-95c4-25fef0abbaa7',
        '516b9052-d6fb-4828-bfc1-dffdef2d56d2',
        '5d60a7e7-9139-4779-9f28-e6316b9fe3b7',
        '65aa3d74-c1fb-4bdd-9a00-ce88a5270c57',
        '27c2e339-74ed-49a7-a3c4-1a0172e9f945',
        'e89b7727-4847-41ab-98d7-4148216eea8c',
        'd79efaf3-b5dc-43a0-b3a5-c492155a7e0d',
        'ee9ee6e7-5b7d-4e18-ab88-ce03d569305f',
        'fe90c911-c13b-4103-bf33-16757aa87ff5',
        '4d7ff67a-0074-4195-95d7-cf8b84eba079',
        'abe5d378-d021-4905-93f4-0e76a7848365',
        '19d21907-d121-4d85-8a34-a65d04ce8977',
        'c421b8ad-33a4-42aa-b0cc-8f5f94b2cff7',
        'f3dbbe55-3c80-453e-ab39-a6fe5001a7fc',
        'f48d3eb2-6060-458f-809f-b5e887f9a17f',
        'd189e406-de29-4889-8470-7bfa0d020c0c',
        '71627018-9f21-4034-aafe-4c8b17151217',
        '0c6a9278-0963-4460-9cae-6dc6f5420f4f',
        'c833ac35-cce0-4315-8df3-3ed76656a548',
        '78e94126-1d0a-472a-9b99-37840784318f',
        '6e684707-ce4b-42df-8a77-71e57b54b581',
        '811df139-e7a3-4cd8-b778-c81494d239ee',
        'c263c5d8-c166-4599-9219-3e975e506f45',
        'b31e7c5d-95ba-41d4-bc29-e6357c96f005',
        '16ae2983-7f8f-4eee-9afb-6d4617836a01',
        'ecbbfac7-f92a-4b41-996e-3e4724aa0e23',
        '2c6b3db9-a5ee-4425-a837-8880a86faaa0',
        '3d67a99a-b39a-4295-b7f8-0bf71ead5b2d',
        'ca421bb7-ad73-41ea-9648-70073862ad5a',
        '5ba156fa-853d-460f-a884-ca8dd3a27314',
        '42a4359a-1df2-4086-b454-7477dbb726ff',
        '7db9517b-f6ba-4bcf-ae26-6a88a7dbb034',
        'bc758bd6-eb50-425b-ada1-07e6bb312032',
        '254cf6d0-696d-4ff0-b579-ac3b633f03c0',
        'f8f34b37-4c71-4177-bac5-6b99bb1929af',
        'b0cc6179-f2b1-4ddf-8fe2-2251c3d935a3',
        '333ad834-fa3b-4cf4-b9ba-fdb1c481c497',
        '011fc3bc-a97d-4535-8cb0-81766e361e78',
        'acf2262b-4ccf-4f1d-b5c1-5e44641884c6',
        '6bf661b1-2f85-4277-8dba-6552141e7e42',
        'a76df66b-8c50-488f-b4e7-4f4d3c05afff',
        'b5c5df47-f939-4536-a340-442bf00bd70d',
        'd4914d41-0011-49fb-a1c2-fe69108e4983',
        'efd5fa37-b0de-43b0-9fe7-1b7a7a6523f8',
        '6048f863-7faa-43f2-8202-4b349ae34810',
        '659a0024-fa05-4068-aed0-e61239554b6d',
        '6ec80af3-0415-429e-91e9-8491ab5745c0',
        '0e6f754c-0533-4336-b4f0-e2e35518efa1',
        '47469672-7e55-4316-b5d4-c458e43d2404',
        '0c5ad756-a823-4a3f-8449-840fac080f45',
        '8f8345da-1dd9-499b-bda5-57100bb305d5',
        '4a31d059-e375-4571-9d28-ea0de51740e7',
        'ed7fb50c-1b3a-4594-920b-9a461abce57c',
        '3d8fe6f6-e603-44c0-b550-3568523c3224',
        '809259bc-7912-427a-a975-7298ee5626db',
        'ec88d77e-5612-466c-b269-ad146abd70d0',
        'bd308a10-8073-45ae-9bfb-9a663ad5dd10',
        '83a6a4cc-3079-46d8-9263-8f57af4fd4c7',
        '557f0041-7e7f-447c-988c-eafa6e396915',
        '6ad0fa1c-7425-41e9-9b74-19c4935750a2',
        'a9193e21-e529-43cf-9421-6ed09b59d86e',
        '2a09f6e6-4fb2-4da0-97bf-6f32858ba977',
        'd66e0940-087f-4e71-8292-fc38e306d9f7',
        '0dfc58b3-d591-40be-803d-e17a52e5d262',
        'a46c6902-de10-45cc-8dac-600d68860532',
        '5200f9dc-b967-4d1e-ab01-51c726c152ba',
        'acd8498b-ee8b-4d58-b0ef-c353fb1b5a45',
        '36adf355-cccc-406f-a814-6333ec4e31bf',
        'd6d64c6f-8388-4de3-9db1-de07f02071b6',
        'daf3fde9-41d0-422f-a0e3-8c7a93a77091',
        '160f4fac-a229-4169-893e-4e9e6864c098',
        '170c4be9-1fe6-4838-8a77-dee364ae9a95',
        '2864fed0-868c-4bd1-a3fa-ae3bb3de20f4',
        '8ea6639c-36dc-463c-8299-8f9a12b10898',
        '626bef95-2f24-47c2-a792-f06e8f13a11e',
        'ede75c44-5a1d-484c-942d-87407f27db23',
        '966ec42b-0bf7-4923-9672-7a41fee377bc',
        '399d7ce6-b28f-4751-ac50-73e31b079f22',
        'ab2b4086-e181-4f02-aee1-a94afed40b50',
        '3cfc33a6-73f7-49f7-9c01-fbcf84e604d0',
        '40cf06c6-74ca-4016-b388-17dc0334770d',
        '58f9ecd3-14ab-4100-b32a-cc2622f06c81',
        'a5c35e34-5d05-4724-bb6c-613b5d306a18',
        '5133ae3e-e38b-47fa-a3dc-965c738be792',
        '594acd2f-7100-4b2b-8b8a-6097cb1cec3d',
        '08b3da92-6b32-43d8-9fdd-53eaa996d649',
        '93dcdc27-ab2c-4828-9074-4876ee7ab257',
        '8260a154-23cc-4510-a5df-cc5119f457fb',
        '732a6571-9729-4935-92be-1a74b3242636',
        'c15f5581-e047-45b7-a36f-dfef4e7ba4bb',
    ];
    /** @var UuidInterface */
    private $tinyUuid;
    /** @var UuidInterface */
    private $hugeUuid;
    /** @var UuidInterface */
    private $uuid;
    /**
     * @var non-empty-list<UuidInterface>
     */
    private $promiscuousUuids;
    /**
     * @var non-empty-string
     */
    private $tinyUuidBytes;
    /**
     * @var non-empty-string
     */
    private $hugeUuidBytes;
    /**
     * @var non-empty-string
     */
    private $uuidBytes;
    /**
     * @var non-empty-list<non-empty-string>
     */
    private $promiscuousUuidsBytes;

    public function __construct()
    {
        $this->tinyUuid = Uuid::fromString(self::TINY_UUID);
        $this->hugeUuid = Uuid::fromString(self::HUGE_UUID);
        $this->uuid = Uuid::fromString(self::UUIDS_TO_BE_SHORTENED[0]);
        $this->promiscuousUuids = array_map([Uuid::class, 'fromString'], self::UUIDS_TO_BE_SHORTENED);
        $this->tinyUuidBytes = $this->tinyUuid->getBytes();
        $this->hugeUuidBytes = $this->hugeUuid->getBytes();
        $this->uuidBytes = $this->uuid->getBytes();
        $this->promiscuousUuidsBytes = array_map(static function (UuidInterface $uuid): string {
            return $uuid->getBytes();
        }, $this->promiscuousUuids);
    }

    public function benchCreationOfTinyUuidFromString(): void
    {
        Uuid::fromString(self::TINY_UUID);
    }

    public function benchCreationOfHugeUuidFromString(): void
    {
        Uuid::fromString(self::HUGE_UUID);
    }

    public function benchCreationOfUuidFromString(): void
    {
        Uuid::fromString(self::UUIDS_TO_BE_SHORTENED[0]);
    }

    public function benchCreationOfPromiscuousUuidsFromString(): void
    {
        array_map([Uuid::class, 'fromString'], self::UUIDS_TO_BE_SHORTENED);
    }

    public function benchCreationOfTinyUuidFromBytes(): void
    {
        Uuid::fromBytes($this->tinyUuidBytes);
    }

    public function benchCreationOfHugeUuidFromBytes(): void
    {
        Uuid::fromBytes($this->hugeUuidBytes);
    }

    public function benchCreationOfUuidFromBytes(): void
    {
        Uuid::fromBytes($this->uuidBytes);
    }

    public function benchCreationOfPromiscuousUuidsFromBytes(): void
    {
        array_map([Uuid::class, 'fromBytes'], $this->promiscuousUuidsBytes);
    }

    public function benchStringConversionOfTinyUuid(): void
    {
        $this->tinyUuid->toString();
    }

    public function benchStringConversionOfHugeUuid(): void
    {
        $this->hugeUuid->toString();
    }

    public function benchStringConversionOfUuid(): void
    {
        $this->uuid->toString();
    }

    public function benchStringConversionOfPromiscuousUuids(): void
    {
        array_map(static function (UuidInterface $uuid): string {
            return $uuid->toString();
        }, $this->promiscuousUuids);
    }

    public function benchBytesConversionOfTinyUuid(): void
    {
        $this->tinyUuid->getBytes();
    }

    public function benchBytesConversionOfHugeUuid(): void
    {
        $this->hugeUuid->getBytes();
    }

    public function benchBytesConversionOfUuid(): void
    {
        $this->uuid->getBytes();
    }

    public function benchBytesConversionOfPromiscuousUuids(): void
    {
        array_map(static function (UuidInterface $uuid): string {
            return $uuid->getBytes();
        }, $this->promiscuousUuids);
    }
}
