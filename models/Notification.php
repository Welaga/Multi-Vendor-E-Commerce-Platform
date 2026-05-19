<?php
// models/Notification.php

class Notification {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    /** Get notifications for a user */
    public function getByUser(int $userId, int $limit = 15): array {
        $stmt = $this->db->prepare("
            SELECT * FROM notifications WHERE user_id = ?
            ORDER BY created_at DESC LIMIT $limit
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /** Count unread */
    public function countUnread(int $userId): int {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0
        ");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    /** Create a notification */
    public function create(int $userId, string $message, string $link = null): bool {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)
        ");
        return $stmt->execute([$userId, $message, $link]);
    }

    /** Mark all as read for user */
    public function markAllRead(int $userId): bool {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }

    /** Mark one as read */
    public function markRead(int $id): bool {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /** Delete old notifications (cleanup) */
    public function cleanup(int $userId, int $keepLast = 50): void {
        $stmt = $this->db->prepare("
            DELETE FROM notifications WHERE user_id = ?
            AND id NOT IN (
                SELECT id FROM (SELECT id FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?) t
            )
        ");
        $stmt->execute([$userId, $userId, $keepLast]);
    }
}
