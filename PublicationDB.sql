
USE publicationDB;
-- Tạo bảng USERS
CREATE TABLE USERS (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    status VARCHAR(10) NOT NULL,
    user_type VARCHAR(10) NOT NULL
);

-- Tạo bảng PAPERS
CREATE TABLE PAPERS (
    paper_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author_string_list VARCHAR(255) NOT NULL,
    abstract VARCHAR(1000) NOT NULL,
    conference_id INT NOT NULL,
    topic_id INT NOT NULL,
    user_id INT NOT NULL
);

-- Tạo bảng AUTHORS
CREATE TABLE AUTHORS (
    user_id INT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    website VARCHAR(255),
    profile_json_text VARCHAR(4000),
    image_path VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES USERS(user_id)
);

-- Tạo bảng PARTICIPATION
CREATE TABLE PARTICIPATION (
    author_id INT NOT NULL,
    paper_id INT NOT NULL,
    role VARCHAR(30) NOT NULL,
    date_added DATETIME NOT NULL,
    status VARCHAR(10) NOT NULL,
    PRIMARY KEY (author_id, paper_id)
);

-- Tạo bảng CONFERENCES
CREATE TABLE CONFERENCES (
    conference_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    abbreviation VARCHAR(10) NOT NULL,
    `rank` VARCHAR(1) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    type VARCHAR(50) NOT NULL
);

-- Tạo bảng TOPICS
CREATE TABLE TOPICS (
    topic_id INT AUTO_INCREMENT PRIMARY KEY,
    topic_name VARCHAR(255) NOT NULL
);

-- Tạo khóa ngoại
ALTER TABLE PAPERS
ADD CONSTRAINT fk_papers_conference
FOREIGN KEY (conference_id) REFERENCES CONFERENCES(conference_id);

ALTER TABLE PAPERS
ADD CONSTRAINT fk_papers_topic
FOREIGN KEY (topic_id) REFERENCES TOPICS(topic_id);

ALTER TABLE PAPERS
ADD CONSTRAINT fk_papers_user
FOREIGN KEY (user_id) REFERENCES USERS(user_id);

ALTER TABLE PARTICIPATION
ADD CONSTRAINT fk_participation_author
FOREIGN KEY (author_id) REFERENCES AUTHORS(user_id);

ALTER TABLE PARTICIPATION
ADD CONSTRAINT fk_participation_paper
FOREIGN KEY (paper_id) REFERENCES PAPERS(paper_id);


-- Chèn dữ liệu
-- Insert data into USERS table
INSERT INTO USERS (username, email, password, status, user_type) VALUES
('johnsmith', 'john.smith@example.com', 'password123', 'active', 'admin'),
('janedoe', 'jane.doe@example.com', 'password123', 'active', 'member'),
('michaelbrown', 'michael.brown@example.com', 'password123', 'active', 'member'),
('emilydavis', 'emily.davis@example.com', 'password123', 'active', 'member'),
('davidharris', 'david.harris@example.com', 'password123', 'active', 'member'),
('sarahmartin', 'sarah.martin@example.com', 'password123', 'active', 'member'),
('jameslee', 'james.lee@example.com', 'password123', 'active', 'member'),
('amandawilson', 'amanda.wilson@example.com', 'password123', 'active', 'member'),
('robertmoore', 'robert.moore@example.com', 'password123', 'active', 'member'),
('lindathomas', 'linda.thomas@example.com', 'password123', 'active', 'member');

-- Insert data into AUTHORS table
INSERT INTO AUTHORS (user_id, full_name, website, profile_json_text, image_path) VALUES
(1, 'John Smith', 'http://johnsmith.com', '{"bio": "John is a researcher in computer science.", "interests": ["AI", "Machine Learning"]}', '/images/johnsmith.png'),
(2, 'Jane Doe', 'http://janedoe.com', '{"bio": "Jane is a data scientist with expertise in NLP.", "interests": ["NLP", "Data Mining"]}', '/images/janedoe.png'),
(3, 'Michael Brown', 'http://michaelbrown.com', '{"bio": "Michael works in the field of robotics.", "interests": ["Robotics", "AI"]}', '/images/michaelbrown.png'),
(4, 'Emily Davis', 'http://emilydavis.com', '{"bio": "Emily is a software engineer.", "interests": ["Software Engineering", "Distributed Systems"]}', '/images/emilydavis.png'),
(5, 'David Harris', 'http://davidharris.com', '{"bio": "David specializes in cybersecurity.", "interests": ["Cybersecurity", "Networks"]}', '/images/davidharris.png'),
(6, 'Sarah Martin', 'http://sarahmartin.com', '{"bio": "Sarah is a professor of computer science.", "interests": ["Computer Science Education", "Algorithms"]}', '/images/sarahmartin.png'),
(7, 'James Lee', 'http://jameslee.com', '{"bio": "James researches bioinformatics.", "interests": ["Bioinformatics", "Genomics"]}', '/images/jameslee.png'),
(8, 'Amanda Wilson', 'http://amandawilson.com', '{"bio": "Amanda is a machine learning engineer.", "interests": ["Machine Learning", "Data Science"]}', '/images/amandawilson.png'),
(9, 'Robert Moore', 'http://robertmoore.com', '{"bio": "Robert develops software for embedded systems.", "interests": ["Embedded Systems", "IoT"]}', '/images/robertmoore.png'),
(10, 'Linda Thomas', 'http://lindathomas.com', '{"bio": "Linda is a researcher in cloud computing.", "interests": ["Cloud Computing", "Big Data"]}', '/images/lindathomas.png');


-- Insert data into CONFERENCES table
INSERT INTO CONFERENCES (name, abbreviation, `rank`, start_date, end_date, type) VALUES
('International Conference on Artificial Intelligence', 'ICAI', 'A', '2024-07-10', '2024-07-12', 'Academic'),
('Global Symposium on Data Science', 'GSDS', 'B', '2024-08-05', '2024-08-07', 'Academic'),
('World Congress on Machine Learning', 'WCML', 'A', '2024-09-15', '2024-09-17', 'Academic'),
('Conference on Computational Linguistics', 'CCL', 'B', '2024-10-20', '2024-10-22', 'Academic'),
('Symposium on Cyber Security', 'SCS', 'C', '2024-11-10', '2024-11-12', 'Industry'),
('International Workshop on Big Data', 'IWBD', 'B', '2024-12-05', '2024-12-07', 'Academic'),
('Annual Meeting on Robotics', 'AMR', 'A', '2024-01-15', '2024-01-17', 'Academic'),
('Forum on Internet of Things', 'FIT', 'B', '2024-02-20', '2024-02-22', 'Industry'),
('Summit on Cloud Computing', 'SCC', 'C', '2024-03-10', '2024-03-12', 'Industry'),
('Workshop on Bioinformatics', 'WB', 'A', '2024-04-25', '2024-04-27', 'Academic');

-- Insert data into TOPICS table
INSERT INTO TOPICS (topic_name) VALUES
('Artificial Intelligence'),
('Machine Learning'),
('Natural Language Processing'),
('Cyber Security'),
('Cloud Computing');


-- Insert data into PAPERS table
INSERT INTO PAPERS (title, author_string_list, abstract, conference_id, topic_id, user_id) VALUES
('Advancements in Artificial Intelligence', 'John Smith, Jane Doe', 'An in-depth study on recent advancements in AI.', 1, 1, 1),
('Data Science Applications in Healthcare', 'Michael Brown, Emily Davis, David Harris', 'Exploring the impact of data science in healthcare.', 2, 2, 3),
('Machine Learning Algorithms for Big Data', 'Sarah Martin, James Lee', 'A comprehensive review of ML algorithms for big data.', 3, 2, 6),
('Natural Language Processing Techniques', 'Amanda Wilson, Robert Moore, Linda Thomas', 'A study on various NLP techniques and their applications.', 4, 3, 8),
('Cyber Security Trends in 2024', 'John Smith, David Harris', 'An analysis of emerging trends in cyber security.', 5, 4, 1),
('Big Data Challenges and Solutions', 'Jane Doe, Sarah Martin', 'Addressing the challenges faced in big data and proposing solutions.', 6, 2, 2),
('Robotics Innovations in the 21st Century', 'Michael Brown, James Lee, Linda Thomas', 'An overview of the latest innovations in robotics.', 7, 1, 3),
('IoT Security Issues', 'Emily Davis, Robert Moore', 'Investigating security issues in IoT and possible mitigations.', 8, 4, 4),
('Cloud Computing for Enterprises', 'Amanda Wilson, John Smith', 'Benefits and challenges of cloud computing in enterprise environments.', 9, 5, 8),
('Bioinformatics: A New Era', 'Linda Thomas, Michael Brown', 'The role of bioinformatics in modern biology.', 10, 2, 10);


-- Insert data into PARTICIPATION table
INSERT INTO PARTICIPATION (author_id, paper_id, role, date_added, status) VALUES
(1, 1, 'first_author', '2024-06-01', 'show'),
(2, 1, 'member', '2024-06-02', 'show'),
(3, 2, 'first_author', '2024-06-03', 'show'),
(4, 2, 'member', '2024-06-04', 'show'),
(5, 2, 'member', '2024-06-05', 'show'),
(6, 3, 'first_author', '2024-06-06', 'show'),
(7, 3, 'member', '2024-06-07', 'show'),
(8, 4, 'first_author', '2024-06-08', 'show'),
(9, 4, 'member', '2024-06-09', 'show'),
(10, 4, 'member', '2024-06-10', 'show'),
(1, 5, 'first_author', '2024-06-11', 'show'),
(5, 5, 'member', '2024-06-12', 'show'),
(2, 6, 'first_author', '2024-06-13', 'show'),
(6, 6, 'member', '2024-06-14', 'show'),
(3, 7, 'first_author', '2024-06-15', 'show'),
(7, 7, 'member', '2024-06-16', 'show'),
(10, 7, 'member', '2024-06-17', 'show'),
(4, 8, 'first_author', '2024-06-18', 'show'),
(9, 8, 'member', '2024-06-19', 'show'),
(8, 9, 'first_author', '2024-06-20', 'show'),
(1, 9, 'member', '2024-06-21', 'show'),
(10, 10, 'first_author', '2024-06-22', 'show'),
(3, 10, 'member', '2024-06-23', 'show');

