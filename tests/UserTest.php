<?php

declare(strict_types=1);

namespace EduPay\Tests;

use EduPay\User;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the User domain class.
 *
 * Uses an in-memory SQLite database so no external MySQL server is required.
 */
class UserTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Ensure a session is active for tests that exercise session handling.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->pdo->exec(<<<SQL
            CREATE TABLE users (
                id              INTEGER PRIMARY KEY AUTOINCREMENT,
                full_name       TEXT    NOT NULL,
                email           TEXT    NOT NULL UNIQUE,
                password_hash   TEXT    NOT NULL,
                role            TEXT    NOT NULL DEFAULT 'parent',
                institution_id  INTEGER
            )
        SQL);

        $this->pdo->exec(<<<SQL
            CREATE TABLE parent_student_link (
                parent_id  INTEGER NOT NULL,
                student_id INTEGER NOT NULL,
                PRIMARY KEY (parent_id, student_id)
            )
        SQL);
    }

    // -------------------------------------------------------------------------
    // register()
    // -------------------------------------------------------------------------

    public function testRegisterCreatesUserAndHashesPassword(): void
    {
        $user = new User($this->pdo);
        $result = $user->register('Alice Wambui', 'alice@example.com', 'secret123', 'parent');

        self::assertTrue($result);

        $row = $this->pdo->query("SELECT * FROM users WHERE email = 'alice@example.com'")->fetch();
        self::assertNotFalse($row);
        self::assertSame('Alice Wambui', $row['full_name']);
        self::assertSame('parent', $row['role']);
        // The stored hash must NOT equal the plain-text password.
        self::assertNotSame('secret123', $row['password_hash']);
        // The stored hash must verify correctly.
        self::assertTrue(password_verify('secret123', $row['password_hash']));
    }

    public function testRegisterReturnsFalseOnDuplicateEmail(): void
    {
        $user = new User($this->pdo);
        $user->register('Alice Wambui', 'alice@example.com', 'secret123', 'parent');

        $result = $user->register('Alice Duplicate', 'alice@example.com', 'other123', 'parent');
        self::assertFalse($result);
    }

    // -------------------------------------------------------------------------
    // login()
    // -------------------------------------------------------------------------

    public function testLoginStartsSessionAndReturnsTrueWithCorrectCredentials(): void
    {
        // Manually insert a known-hash row to avoid coupling to register().
        $hash = password_hash('correctpassword', PASSWORD_BCRYPT);
        $this->pdo->exec(
            "INSERT INTO users (full_name, email, password_hash, role) VALUES ('Bob', 'bob@example.com', '$hash', 'admin')"
        );

        $user = new User($this->pdo);

        $result = $user->login('bob@example.com', 'correctpassword');
        self::assertTrue($result);
        self::assertSame('Bob', $_SESSION['user_name']);
        self::assertSame('admin', $_SESSION['role']);
    }

    public function testLoginReturnsFalseWithWrongPassword(): void
    {
        $hash = password_hash('rightpassword', PASSWORD_BCRYPT);
        $this->pdo->exec(
            "INSERT INTO users (full_name, email, password_hash, role) VALUES ('Carol', 'carol@example.com', '$hash', 'parent')"
        );

        $user = new User($this->pdo);

        $result = $user->login('carol@example.com', 'wrongpassword');
        self::assertFalse($result);
    }

    public function testLoginReturnsFalseForUnknownEmail(): void
    {
        $user = new User($this->pdo);

        $result = $user->login('nobody@example.com', 'anypassword');
        self::assertFalse($result);
    }

    // -------------------------------------------------------------------------
    // getChildren()
    // -------------------------------------------------------------------------

    public function testGetChildrenReturnsLinkedStudents(): void
    {
        $this->pdo->exec(
            "INSERT INTO users (id, full_name, email, password_hash, role) VALUES
             (1, 'Parent One',  'parent@example.com',  'x', 'parent'),
             (2, 'Student One', 'student1@example.com', 'x', 'student'),
             (3, 'Student Two', 'student2@example.com', 'x', 'student')"
        );
        $this->pdo->exec(
            "INSERT INTO parent_student_link (parent_id, student_id) VALUES (1, 2), (1, 3)"
        );

        $user     = new User($this->pdo);
        $children = $user->getChildren(1);

        self::assertCount(2, $children);
        $names = array_column($children, 'full_name');
        self::assertContains('Student One', $names);
        self::assertContains('Student Two', $names);
    }

    public function testGetChildrenReturnsEmptyForParentWithNoLinks(): void
    {
        $this->pdo->exec(
            "INSERT INTO users (id, full_name, email, password_hash, role) VALUES
             (10, 'Lonely Parent', 'lonely@example.com', 'x', 'parent')"
        );

        $user     = new User($this->pdo);
        $children = $user->getChildren(10);

        self::assertCount(0, $children);
    }
}
