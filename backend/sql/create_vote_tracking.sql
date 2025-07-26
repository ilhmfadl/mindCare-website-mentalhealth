-- Create table to track user votes on responses
CREATE TABLE forum_response_votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    response_id INT NOT NULL,
    user_id INT NOT NULL,
    vote_type ENUM('upvote', 'downvote') NOT NULL DEFAULT 'upvote',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_response (user_id, response_id),
    FOREIGN KEY (response_id) REFERENCES forum_responses(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Add index for better performance
CREATE INDEX idx_response_votes_response_id ON forum_response_votes(response_id);
CREATE INDEX idx_response_votes_user_id ON forum_response_votes(user_id); 