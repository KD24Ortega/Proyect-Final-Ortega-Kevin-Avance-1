
CREATE OR REPLACE TABLE author(
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    orcid VARCHAR(20) UNIQUE,
    afiliation VARCHAR(50) NOT NULL
);

CREATE TABLE publication(
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description VARCHAR(100) NOT NULL,
    publication_date DATE NOT NULL,
    author_id INT NOT NULL,
    type ENUM('book','article') NOT NULL,
    FOREIGN KEY (author_id) REFERENCES author(id) ON DELETE CASCADE
);

CREATE TABLE book(
    publication_id INT AUTO_INCREMENT PRIMARY KEY,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    genre VARCHAR(50) NOT NULL,
    edition INT NOT NULL,
    FOREIGN KEY (publication_id) REFERENCES publication(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE article(
    publication_id INT AUTO_INCREMENT PRIMARY KEY,
    doi VARCHAR(20) NOT NULL UNIQUE,
    abstract VARCHAR(255) NOT NULL,
    keywords VARCHAR(255) NOT NULL,
    indexation VARCHAR(50) NOT NULL,
    magazine VARCHAR(100) NOT NULL,
    area VARCHAR(100) NOT NULL,
    FOREIGN KEY (publication_id) REFERENCES publication(id) ON DELETE CASCADE ON UPDATE CASCADE
);