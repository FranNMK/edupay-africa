<?php

namespace EduPay;

class DemoRequest
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(array $payload): bool
    {
        $sql = "INSERT INTO demo_requests
            (institution_name, contact_name, email, phone, school_type, student_count, preferred_contact, message)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            $payload['institution_name'],
            $payload['contact_name'],
            $payload['email'],
            $payload['phone'],
            $payload['school_type'],
            $payload['student_count'],
            $payload['preferred_contact'],
            $payload['message'],
        ]);
    }

    public function getAll(): array
    {
        $sql = "SELECT id, institution_name, contact_name, email, phone, school_type, student_count,
                  preferred_contact, message, status, approval_status, institution_id, created_at
                FROM demo_requests
              WHERE (approval_status IS NULL OR approval_status <> 'approved')
                AND institution_id IS NULL
                ORDER BY created_at DESC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $sql = "UPDATE demo_requests SET status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$status, $id]);
    }

    public function getAllForApproval(): array
    {
        $sql = "SELECT id, institution_name, contact_name, email, phone, school_type, student_count,
                       status, approval_status, approval_notes, institution_id, onboarding_token, onboarding_expires_at,
                       approved_at, created_at
                FROM demo_requests
                ORDER BY created_at DESC";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    public function updateApproval(int $id, string $approvalStatus, ?int $approvedBy, string $notes = ''): bool
    {
        $this->pdo->beginTransaction();

        try {
            $selectSql = "SELECT id, institution_name, email, institution_id, onboarding_token
                          FROM demo_requests
                          WHERE id = ?
                          FOR UPDATE";
            $selectStmt = $this->pdo->prepare($selectSql);
            $selectStmt->execute([$id]);
            $request = $selectStmt->fetch();

            if (!$request) {
                $this->pdo->rollBack();
                return false;
            }

            $institutionId = $request['institution_id'] ? (int) $request['institution_id'] : null;
            $token = null;
            $expiresAt = null;

            if ($approvalStatus === 'approved') {
                if (!$institutionId) {
                    $institutionId = $this->getOrCreateInstitutionId(
                        (string) $request['institution_name'],
                        (string) $request['email']
                    );
                }

                $token = !empty($request['onboarding_token'])
                    ? (string) $request['onboarding_token']
                    : bin2hex(random_bytes(24));
                $expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));
            }

            $sql = "UPDATE demo_requests
                    SET approval_status = ?,
                        approval_notes = ?,
                        institution_id = ?,
                        approved_by = ?,
                        approved_at = CASE WHEN ? = 'approved' THEN NOW() ELSE NULL END,
                        onboarding_token = ?,
                        onboarding_expires_at = ?,
                        onboarding_email_sent_at = CASE WHEN ? = 'approved' THEN NOW() ELSE NULL END
                    WHERE id = ?";

            $stmt = $this->pdo->prepare($sql);
            $updated = $stmt->execute([
                $approvalStatus,
                $notes,
                $institutionId,
                $approvedBy,
                $approvalStatus,
                $token,
                $expiresAt,
                $approvalStatus,
                $id,
            ]);

            if (!$updated) {
                $this->pdo->rollBack();
                return false;
            }

            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function findByOnboardingToken(string $token): ?array
    {
        $sql = "SELECT id, institution_name, contact_name, email,
                       onboarding_token, onboarding_expires_at, approval_status
                FROM demo_requests
                WHERE onboarding_token = ?
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$token]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    private function getOrCreateInstitutionId(string $institutionName, string $email): int
    {
        $findSql = "SELECT id
                    FROM institutions
                    WHERE name = ? OR contact_email = ?
                    LIMIT 1";
        $findStmt = $this->pdo->prepare($findSql);
        $findStmt->execute([$institutionName, $email]);
        $existing = $findStmt->fetch();

        if ($existing) {
            return (int) $existing['id'];
        }

        $shortName = $this->generateUniqueShortName($institutionName);

        $insertSql = "INSERT INTO institutions (name, short_name, address, contact_email)
                      VALUES (?, ?, ?, ?)";
        $insertStmt = $this->pdo->prepare($insertSql);
        $insertStmt->execute([$institutionName, $shortName, null, $email]);

        return (int) $this->pdo->lastInsertId();
    }

    private function generateUniqueShortName(string $institutionName): string
    {
        $clean = preg_replace('/[^A-Za-z0-9 ]/', '', $institutionName);
        $words = preg_split('/\s+/', trim((string) $clean));
        $base = '';

        foreach ($words as $word) {
            if ($word !== '') {
                $base .= strtoupper(substr($word, 0, 1));
            }
        }

        if ($base === '') {
            $base = 'INST';
        }

        $base = substr($base, 0, 8);

        do {
            $suffix = str_pad((string) random_int(0, 999), 3, '0', STR_PAD_LEFT);
            $candidate = substr($base . '-' . $suffix, 0, 20);

            $sql = "SELECT id FROM institutions WHERE short_name = ? LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$candidate]);
            $exists = $stmt->fetch();
        } while ($exists);

        return $candidate;
    }
}
