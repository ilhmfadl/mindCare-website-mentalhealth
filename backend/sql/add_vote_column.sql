-- Add vote column to forum_responses table
ALTER TABLE forum_responses ADD COLUMN vote INT DEFAULT 0;

-- Add index for better performance when sorting by vote
CREATE INDEX idx_forum_responses_vote ON forum_responses(vote DESC, created_at ASC);

-- Optional: Add comment to explain the column
ALTER TABLE forum_responses MODIFY COLUMN vote INT DEFAULT 0 COMMENT 'Number of upvotes for this response'; 