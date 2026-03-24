<?php

declare(strict_types=1);

namespace EduPay\Tests;

use EduPay\Institution;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the Institution domain class.
 */
class InstitutionTest extends TestCase
{
    private PDO $pdo;

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
    }

    // -------------------------------------------------------------------------
    // create()
    // -------------------------------------------------------------------------

    public function testCreateInsertsInstitutionAndReturnsTrue(): void
    {
        $inst   = new Institution($this->pdo);
        $result = $inst->create('Greenfields Academy', 'GFA-001', '123 School Rd', 'admin@gfa.ac.ke');

        self::assertTrue($result);

        $row = $this->pdo->query("SELECT * FROM institutions WHERE short_name = 'GFA-001'")->fetch();
        self::assertNotFalse($row);
        self::assertSame('Greenfields Academy', $row['name']);
        self::assertSame('admin@gfa.ac.ke', $row['contact_email']);
    }

    public function testCreateReturnsFalseOnDuplicateShortName(): void
    {
        $inst = new Institution($this->pdo);
        $inst->create('School A', 'DUPE-001', null, 'a@school.com');

        $result = $inst->create('School B', 'DUPE-001', null, 'b@school.com');
        self::assertFalse($result);
    }

    // -------------------------------------------------------------------------
    // getAll()
    // -------------------------------------------------------------------------

    public function testGetAllReturnsAllInstitutionsOrderedByName(): void
    {
        $inst = new Institution($this->pdo);
        $inst->create('Zebra School',  'ZEB-001', null, null);
        $inst->create('Alpha School',  'ALP-001', null, null);
        $inst->create('Middle School', 'MID-001', null, null);

        $rows  = $inst->getAll();
        $names = array_column($rows, 'name');

        self::assertCount(3, $rows);
        self::assertSame(['Alpha School', 'Middle School', 'Zebra School'], $names);
    }

    public function testGetAllReturnsEmptyArrayWhenNoInstitutions(): void
    {
        $inst = new Institution($this->pdo);
        self::assertSame([], $inst->getAll());
    }
}
