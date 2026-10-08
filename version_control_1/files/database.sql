-- Import in phpMyAdmin: Import tab > choose this file > Go
CREATE DATABASE IF NOT EXISTS reading_log CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE reading_log;

-- Every book the reader owns / is reading / has finished
CREATE TABLE books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  author VARCHAR(150),
  total_pages INT NOT NULL,
  pages_read INT NOT NULL DEFAULT 0,
  pages_left INT GENERATED ALWAYS AS (total_pages - pages_read) STORED,
  status ENUM('to_read','reading','finished') NOT NULL DEFAULT 'to_read',
  finished_on DATE NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- One row per reading session (daily reading time)
CREATE TABLE reading_sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  book_id INT NOT NULL,
  session_date DATE NOT NULL,
  minutes_read INT NOT NULL,
  pages_read INT NOT NULL DEFAULT 0,
  FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

-- Suggested books for kids
CREATE TABLE kids_books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  author VARCHAR(150),
  age_group ENUM('3-5','6-8','9-12') NOT NULL,
  pages INT NOT NULL
);

INSERT INTO kids_books (title, author, age_group, pages) VALUES
('The Very Hungry Caterpillar','Eric Carle','3-5',32),
('Goodnight Moon','Margaret Wise Brown','3-5',32),
('Brown Bear, Brown Bear, What Do You See?','Bill Martin Jr.','3-5',28),
('Where the Wild Things Are','Maurice Sendak','3-5',40),
('Llama Llama Red Pajama','Anna Dewdney','3-5',40),
('Charlotte''s Web','E.B. White','6-8',184),
('Matilda','Roald Dahl','6-8',240),
('Dinosaurs Before Dark (Magic Tree House)','Mary Pope Osborne','6-8',96),
('Frog and Toad Are Friends','Arnold Lobel','6-8',64),
('Diary of a Wimpy Kid','Jeff Kinney','6-8',217),
('Harry Potter and the Sorcerer''s Stone','J.K. Rowling','9-12',309),
('Wonder','R.J. Palacio','9-12',315),
('The Lightning Thief','Rick Riordan','9-12',377),
('Holes','Louis Sachar','9-12',233),
('The Wild Robot','Peter Brown','9-12',277);
