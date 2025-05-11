<?php

use Daigox\StringManager\StringManager;
use PHPUnit\Framework\TestCase;

final class StringManagerTest extends TestCase
{
    public function testSanitizeUsername(): void
    {
        $this->assertSame('ali_123', StringManager::sanitizeUsername('علی ۱۲۳'));
    }

    public function testUuidValidation(): void
    {
        $this->assertTrue(StringManager::validateUuid('123e4567-e89b-12d3-a456-426614174000'));
        $this->assertFalse(StringManager::validateUuid('invalid-uuid'));
    }
}
