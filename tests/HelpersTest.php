<?php

declare(strict_types=1);

namespace EduPay\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the global helper functions in src/helpers.php.
 */
class HelpersTest extends TestCase
{
    protected function setUp(): void
    {
        // Ensure a session is active for all CSRF-related tests.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // -------------------------------------------------------------------------
    // h()
    // -------------------------------------------------------------------------

    public function testHEscapesHtmlSpecialChars(): void
    {
        self::assertSame('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', h('<script>alert("xss")</script>'));
    }

    public function testHEscapesSingleQuotes(): void
    {
        self::assertSame('it&#039;s fine', h("it's fine"));
    }

    public function testHCastsNonStringInput(): void
    {
        self::assertSame('42', h(42));
        self::assertSame('', h(null));
    }

    // -------------------------------------------------------------------------
    // csrf_token() / csrf_field() / csrf_verify()
    // -------------------------------------------------------------------------

    public function testCsrfTokenGeneratesAndStoresToken(): void
    {
        unset($_SESSION['csrf_token']);

        $token = csrf_token();
        self::assertNotEmpty($token);
        self::assertSame($token, $_SESSION['csrf_token']);
    }

    public function testCsrfTokenReturnsSameTokenOnSubsequentCalls(): void
    {
        $first  = csrf_token();
        $second = csrf_token();
        self::assertSame($first, $second);
    }

    public function testCsrfFieldRendersHiddenInput(): void
    {
        $token = csrf_token();
        $field = csrf_field();

        self::assertStringContainsString('type="hidden"', $field);
        self::assertStringContainsString('name="csrf_token"', $field);
        self::assertStringContainsString($token, $field);
    }

    public function testCsrfVerifyReturnsTrueWhenTokenMatches(): void
    {
        $token               = csrf_token();
        $_POST['csrf_token'] = $token;

        self::assertTrue(csrf_verify());
    }

    public function testCsrfVerifyReturnsFalseWhenTokenMismatch(): void
    {
        csrf_token(); // ensure session token is set
        $_POST['csrf_token'] = 'invalid-token';

        self::assertFalse(csrf_verify());
    }

    public function testCsrfVerifyReturnsFalseWhenPostTokenMissing(): void
    {
        csrf_token();
        unset($_POST['csrf_token']);

        self::assertFalse(csrf_verify());
    }
}
