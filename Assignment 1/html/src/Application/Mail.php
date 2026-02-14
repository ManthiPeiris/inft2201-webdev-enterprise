<?php
namespace Application;

use PDO;

class Mail {
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // CREATE
    public function createMail($subject, $body) {
        $stmt = $this->pdo->prepare("INSERT INTO mail (subject, body) VALUES (?, ?) RETURNING id");
        $stmt->execute([$subject, $body]);

        return $stmt->fetchColumn();
    }

    // GET ONE
    public function getMailById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM mail WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
}
    // GET ALL
    public function listMail() {
    $stmt = $this->pdo->query("SELECT * FROM mail ORDER BY id");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

   //PDATE
    public function updateMail($id, $subject, $body) {
        $stmt = $this->pdo->prepare(
            "UPDATE mail SET subject = ?, body = ? WHERE id = ?"
        );

        $stmt->execute([$subject, $body, $id]);

        return $stmt->rowCount() > 0;
    }

    // DELETE
    public function deleteMail($id) {
        $stmt = $this->pdo->prepare(
            "DELETE FROM mail WHERE id = ?"
        );

        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    }
}