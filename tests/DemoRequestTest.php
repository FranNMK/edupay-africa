<?php

declare(strict_types=1);

namespace EduPay\Tests;

use EduPay\DemoRequest;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the DemoRequest domain class.
 */
class DemoRequestTest extends TestCase
{
    private PDO $pdo;

    /** @var array<string,mixed> */
    private array $validPayload = [
        'institution_name'  => 'Sunrise Academy',
        'contact_name'      => 'Jane Doe',
        'email'             => 'jane@sunrise.ac.ke',
        'phone'             => '+254712345678',
        'school_type'       => 'Secondary',
        'student_count'     => 800,
        'preferred_contact' => 'Email',
        'message'           => 'We are interested in EduPay.',
    ];

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $this->pdo->exec(<<<SQL
            CREATE TABLE institutions (
                id            INTEGER PRIMARY KEY AUTOINCREMENT,
                name          TEXT NOT NULL,
                short_name    TEXT NOT NULL UNIQUE,
                address       TEXT,
                contact_email TEXT
            )
        SQL);

        $this->pdo->exec(<<<SQL
            CREATE TABLE demo_requests (
                id                      INTEGER PRIMARY KEY AUTOINCREMENT,
                institution_name        TEXT,
                contact_name            TEXT,
                email                   TEXT,
                phone                   TEXT,
                school_type             TEXT,
                student_count           INTEGER,
                preferred_contact       TEXT,
                message                 TEXT,
                status                  TEXT    DEFAULT 'new',
                approval_status         TEXT    DEFAULT 'pending',
                institution_id          INTEGER,
                approved_by             INTEGER,
                approval_notes          TEXT,
                onboarding_token        TEXT,
                onboarding_expires_at   TEXT,
                onboarding_email_sent_at TEXT,
                approved_at             TEXT,
                created_at              TEXT    DEFAULT (datetime('now'))
            )
        SQL);
    }

    // -------------------------------------------------------------------------
    // create()
    // -------------------------------------------------------------------------

    public function testCreateInsertsRowAndReturnsTrue(): void
    {
        $demo   = new DemoRequest($this->pdo);
        $result = $demo->create($this->validPayload);

        self::assertTrue($result);

        $count = (int) $this->pdo->query("SELECT COUNT(*) FROM demo_requests")->fetchColumn();
        self::assertSame(1, $count);
    }

    public function testCreateStoresCorrectValues(): void
    {
        $demo = new DemoRequest($this->pdo);
        $demo->create($this->validPayload);

        $row = $this->pdo->query("SELECT * FROM demo_requests LIMIT 1")->fetch();
        self::assertSame('Sunrise Academy', $row['institution_name']);
        self::assertSame('jane@sunrise.ac.ke', $row['email']);
        self::assertSame(800, (int) $row['student_count']);
    }

    // -------------------------------------------------------------------------
    // getAll()
    // -------------------------------------------------------------------------

    public function testGetAllExcludesApprovedAndLinkedRows(): void
    {
        $demo = new DemoRequest($this->pdo);

        // Insert three rows: one pending, one approved (should be excluded), one with institution_id set.
        $demo->create($this->validPayload);  // visible

        $this->pdo->exec(
            "INSERT INTO demo_requests (institution_name, contact_name, email, phone, school_type, student_count, preferred_contact, message, approval_status)
             VALUES ('School B', 'Bob', 'bob@school.com', '+254', 'Primary', 200, 'Phone', '', 'approved')"
        );

        $this->pdo->exec(
            "INSERT INTO demo_requests (institution_name, contact_name, email, phone, school_type, student_count, preferred_contact, message, institution_id)
             VALUES ('School C', 'Carol', 'carol@school.com', '+254', 'College', 300, 'Email', '', 5)"
        );

        $rows = $demo->getAll();
        self::assertCount(1, $rows);
        self::assertSame('Sunrise Academy', $rows[0]['institution_name']);
    }

    // -------------------------------------------------------------------------
    // updateStatus()
    // -------------------------------------------------------------------------

    public function testUpdateStatusChangesStatusField(): void
    {
        $demo = new DemoRequest($this->pdo);
        $demo->create($this->validPayload);
        $id = (int) $this->pdo->lastInsertId();

        $result = $demo->updateStatus($id, 'contacted');

        self::assertTrue($result);
        $row = $this->pdo->query("SELECT status FROM demo_requests WHERE id = $id")->fetch();
        self::assertSame('contacted', $row['status']);
    }

    // -------------------------------------------------------------------------
    // findByOnboardingToken()
    // -------------------------------------------------------------------------

    public function testFindByOnboardingTokenReturnsMatchingRow(): void
    {
        $token = bin2hex(random_bytes(16));
        $this->pdo->exec(
            "INSERT INTO demo_requests (institution_name, contact_name, email, phone, school_type, student_count, preferred_contact, message, approval_status, onboarding_token)
             VALUES ('Token School', 'Eve', 'eve@school.com', '+254', 'University', 1000, 'WhatsApp', '', 'approved', '$token')"
        );

        $demo   = new DemoRequest($this->pdo);
        $result = $demo->findByOnboardingToken($token);

        self::assertNotNull($result);
        self::assertSame('Token School', $result['institution_name']);
    }

    public function testFindByOnboardingTokenReturnsNullForUnknownToken(): void
    {
        $demo   = new DemoRequest($this->pdo);
        $result = $demo->findByOnboardingToken('does-not-exist');

        self::assertNull($result);
    }
}
