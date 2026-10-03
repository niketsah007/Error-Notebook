CREATE TABLE mistakes (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    subject VARCHAR(100) NOT NULL,
    question TEXT,
    error_type VARCHAR(50) NOT NULL,
    solution TEXT NOT NULL,
    interval_days INT(11) DEFAULT 1,
    next_review DATE NOT NULL,
    image_path VARCHAR(255)
);