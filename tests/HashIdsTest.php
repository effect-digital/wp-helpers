<?php

namespace Industrious\Tests\WpHelpers;

use Hashids\Hashids;

class HashIdsTest extends TestCase
{
    const ENCRYPTION_KEY = '23f027a3c353820679b0e23e0886ba1f';

    const TEST_STRING = 'test-phrase';
    const TEST_ARRAY = [
        'location' => 'Way of Life',
        'apartment' => 123,
    ];

    private $hashids;

    public function setUp()
    {
        $this->hashids = new Hashids(self::ENCRYPTION_KEY);
    }

    public function testEncryptString()
    {
        $testString = bin2hex(self::TEST_STRING);
        $encrypted = $this->hashids->encodeHex($testString);

        $this->assertEquals(
            $encrypted,
            'zYq7gpoNv9tv1Xebd4q'
        );
    }

    public function testEncryptArray()
    {
        $data = serialize(self::TEST_ARRAY);
        $testString = bin2hex($data);

        $encrypted = $this->hashids->encodeHex($testString);

        $this->assertEquals(
            $encrypted,
            'eg8X9qXy0xSLw6mXXDgNsmkGeDd9ppHEz4MvmJd8uwJ59Zo2DoizwBp9OOrLhgQygkOjpQfE5LjMqy5YTPOZGgpNNwTrEka6Jl0QCAjn'
        );
    }

    public function testDecryptString()
    {
        $decoded = $this->hashids->decodeHex('zYq7gpoNv9tv1Xebd4q');

        $this->assertEquals(
            hex2bin($decoded),
            self::TEST_STRING
        );
    }

    public function testDecryptArray()
    {
        $decoded = $this->hashids->decodeHex('eg8X9qXy0xSLw6mXXDgNsmkGeDd9ppHEz4MvmJd8uwJ59Zo2DoizwBp9OOrLhgQygkOjpQfE5LjMqy5YTPOZGgpNNwTrEka6Jl0QCAjn');

        $this->assertSame(
            unserialize(hex2bin($decoded)),
            self::TEST_ARRAY
        );
    }
}
