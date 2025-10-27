<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table         = 'notifications';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';

    // Columns exactly as in your migration
    protected $allowedFields = ['user_id', 'message', 'is_read', 'created_at'];

    // Auto-manage created_at only (migration has no updated_at)
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = ''; // disable updated_at

    public function getUnreadCount(int $userId): int
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->countAllResults();
    }

    public function getNotificationsForUser(int $userId, int $limit = 5): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll($limit);
    }

    public function markAsRead(int $notificationId): bool
    {
        return (bool) $this->where('id', $notificationId)
                           ->set('is_read', 1)
                           ->update();
    }
}