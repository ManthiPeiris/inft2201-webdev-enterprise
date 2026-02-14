<?php
use PHPUnit\Framework\TestCase;
use Application\Mail;

class MailTest extends TestCase {
    protected PDO $pdo;

    protected function setUp(): void
    {
        $dsn = "pgsql:host=" . getenv('DB_TEST_HOST') . ";dbname=" . getenv('DB_TEST_NAME');
        $this->pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'));
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // reinitialize the table
        $this->pdo->exec("DROP TABLE IF EXISTS mail;");
        $this->pdo->exec("
            CREATE TABLE mail (
                id SERIAL PRIMARY KEY,
                subject TEXT NOT NULL,
                body TEXT NOT NULL
            );
        ");
    }

    public function testCreateMail() {
        $mail = new Mail($this->pdo);
        $id = $mail->createMail("Alice", "Hello world");

        $this->assertIsInt($id);
        $this->assertEquals(1, $id);
    }

    public function testGetMailById() {
        $mail = new Mail($this->pdo);

        $id = $mail->createMail("Bob", "Test message");

        $result = $mail->getMailById($id);

        $this->assertEquals("Bob", $result['subject']);
        $this->assertEquals("Test message", $result['body']);
    }

    public function testListMail() {
        $mail = new Mail($this->pdo);

        $mail->createMail("A", "Msg1");
        $mail->createMail("B", "Msg2");

        $list = $mail->listMail();

        $this->assertCount(2, $list);
    }

    public function testUpdateMail() {
        $mail = new Mail($this->pdo);

        $id = $mail->createMail("Old", "Body");

        $updated = $mail->updateMail($id, "New", "NewBody");

        $this->assertTrue($updated);

        $result = $mail->getMailById($id);
        $this->assertEquals("New", $result['subject']);
    }

    public function testDeleteMail() {
        $mail = new Mail($this->pdo);

        $id = $mail->createMail("Delete", "Me");

        $deleted = $mail->deleteMail($id);

        $this->assertTrue($deleted);

        $result = $mail->getMailById($id);
        $this->assertFalse($result);
    }
}
