-- Add judul column to jurnal table if it doesn't exist
ALTER TABLE jurnal ADD COLUMN IF NOT EXISTS judul VARCHAR(255) NOT NULL DEFAULT '' AFTER kategori;
 
-- Update existing records to have a default title if judul is empty
UPDATE jurnal SET judul = CONCAT('Jurnal ', kategori, ' - ', DATE_FORMAT(created_at, '%Y-%m-%d')) WHERE judul = '' OR judul IS NULL; 