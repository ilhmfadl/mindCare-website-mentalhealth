CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,           -- user_id, 1 (admin), atau 0 (agent)
    receiver_id INT NOT NULL,         -- user_id atau 1 (admin)
    sender_role ENUM('user', 'admin', 'agent') NOT NULL,
    message TEXT,
    file_url VARCHAR(255),
    file_type VARCHAR(50),
    file_name VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) DEFAULT 0
); 