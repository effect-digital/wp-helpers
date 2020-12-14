<?php

namespace Industrious\Tests\WpHelpers;

use Industrious\WpHelpers\Encryption;

class EncryptionTest extends TestCase
{
    const ENCRYPTION_KEY = '23f027a3c353820679b0e23e0886ba1f';
    const ENCRYPTION_IV = 'b9b09b42e42bb2e75fddfe460cdc5989';

    /**
     * @var Encryption
     */
    private $encryptor;

    public function setUp()
    {
        $this->encryptor = new Encryption(
            self::ENCRYPTION_KEY,
            self::ENCRYPTION_IV
        );
    }

    public function testEncryptString()
    {
        $testString = 'test-phrase';
        $encrypted = $this->encryptor->encrypt($testString);

        $this->assertEquals(
            $encrypted,
            'OtrQSWgqS/xEeyorvWGJPA=='
        );
    }

    public function testEncryptStringTwo()
    {
        $testString = 'wayoflife';
        $encrypted = $this->encryptor->encrypt($testString);

        $this->assertEquals(
            $encrypted,
            'J2FHEKI84P5cVQHPkAYXJQ=='
        );
    }

    public function testEncryptArray()
    {
        $testArray = serialize([
            'location' => 'The Lansdowne',
            'apartment' => 123,
        ]);

        $encrypted = $this->encryptor->encrypt($testArray);

        $this->assertEquals(
            $encrypted,
            'pVI/yuVrI6EHH6Fgyklwj0MdpPfdCMymWpNGxgE+d58Z1Nk8p0/LAl+EAtxdaWpNkN6xY/YD2Z54NjrJkwPAWzX36z/GBpMm7mXCh19tvRU='
        );
    }

    public function testDecryptString()
    {
        $testString = 'test-phrase';
        $encrypted = $this->encryptor->encrypt($testString);

        $this->assertEquals(
            $testString,
            $this->encryptor->decrypt($encrypted)
        );
    }

    public function testDecryptArray()
    {
        $testArray = serialize([
            'location' => 'The Lansdowne',
            'apartment' => 123,
        ]);

        // 'pVI/yuVrI6EHH6Fgyklwj0MdpPfdCMymWpNGxgE+d58Z1Nk8p0/LAl+EAtxdaWpNkN6xY/YD2Z54NjrJkwPAWzX36z/GBpMm7mXCh19tvRU='
        $encrypted = $this->encryptor->encrypt($testArray);

        $decrypted = $this->encryptor->decrypt($encrypted);

        $this->assertEquals(
            $decrypted,
            $testArray
        );
    }
}
