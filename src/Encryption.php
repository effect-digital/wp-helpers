<?php
namespace Industrious\WpHelpers;

class Encryption
{
    const CIPHER = 'AES-128-CBC';
    const OPENSSL_OPTIONS = 0;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $initializationVector;

    /**
     * Encryption constructor.
     *
     * @param string $key
     * @param string $initializationVector
     */
    public function __construct(string $key, string $initializationVector)
    {
        $this->key = $key;
        $this->initializationVector = $initializationVector;
    }

    public function encrypt(string $string): ?string
    {
        return base64_encode(
            openssl_encrypt(
                $string,
                self::CIPHER,
                $this->getKey(),
                self::OPENSSL_OPTIONS,
                $this->getInitializationVector()
            )
        );
    }

    public function decrypt($data): ?string
    {
        return openssl_decrypt(
            base64_decode($data),
            self::CIPHER,
            $this->getKey(),
            self::OPENSSL_OPTIONS,
            $this->getInitializationVector()
        );
    }

    private function getKey(): string
    {
        return hash('sha256', $this->key);
    }

    private function getInitializationVector(): string
    {
        return substr(hash('sha256', $this->initializationVector), 0, 16);
    }
}
