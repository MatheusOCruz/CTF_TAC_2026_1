CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    role VARCHAR(50) NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS patients (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    record_code VARCHAR(20) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    status VARCHAR(50) NOT NULL,
    room VARCHAR(20) NULL,
    diagnosis VARCHAR(150) NOT NULL,
    physician VARCHAR(100) NOT NULL,
    notes TEXT NOT NULL,
    is_restricted TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS staff (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    extension VARCHAR(10) NOT NULL,
    status VARCHAR(30) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS system_notices (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    body TEXT NOT NULL,
    severity ENUM('info', 'warning', 'critical') NOT NULL DEFAULT 'info',
    published_at DATETIME NOT NULL
) ENGINE=InnoDB;

INSERT INTO users (username, password_hash, display_name, role, active)
VALUES (
    'archives',
    '$2y$12$0Is/LmYOEXMtw5ukyqaFMeskoDwA4wuFOTnZClpHCMJ1RzaxK.nW6',
    'Archive Maintenance',
    'Archive Manager',
    1
)
ON DUPLICATE KEY UPDATE
    password_hash = VALUES(password_hash),
    display_name = VALUES(display_name),
    role = VALUES(role),
    active = VALUES(active);

INSERT INTO patients
(record_code, full_name, status, room, diagnosis, physician, notes, is_restricted)
VALUES
('BH-001', 'Maria', 'Discharged', NULL, 'Memory disorder', 'Dr. Michael Kaufmann', 'Patient responded positively to treatment. File archived.', 0),
('BH-002', 'Angela Orosco', 'Under observation', 'C-204', 'Acute psychological distress', 'Dr. Leonard Wolf', 'Contact with family remains restricted.', 0),
('BH-003', 'Eddie Dombrowski', 'Under observation', 'B-117', 'Behavioral disorder', 'Dr. Michael Kaufmann', 'Patient demonstrates hostile behavior during interviews.', 0),
('BH-004', 'Laura', 'Visitor', NULL, 'No diagnosis', 'N/A', 'No medical history. Visitor record only.', 0),
('BH-312', 'Mary Sunderland', 'RESTRICTED', '312', 'Record sealed', 'REDACTED', 'Access requires archive authorization.', 1)
ON DUPLICATE KEY UPDATE
    full_name = VALUES(full_name),
    status = VALUES(status),
    room = VALUES(room),
    diagnosis = VALUES(diagnosis),
    physician = VALUES(physician),
    notes = VALUES(notes),
    is_restricted = VALUES(is_restricted);

INSERT INTO staff (id, full_name, department, extension, status)
VALUES
(1, 'Dr. Michael Kaufmann', 'Psychiatry', '114', 'On duty'),
(2, 'Lisa Garland', 'Nursing', '203', 'Unavailable'),
(3, 'Rachel Simmons', 'Emergency', '119', 'On duty'),
(4, 'Leonard Wolf', 'Psychiatry', '221', 'On leave')
ON DUPLICATE KEY UPDATE
    full_name = VALUES(full_name),
    department = VALUES(department),
    extension = VALUES(extension),
    status = VALUES(status);

INSERT INTO system_notices (id, title, body, severity, published_at)
VALUES
(1, 'Archive migration', 'Legacy patient records are being transferred to the internal archive system.', 'info', '1992-11-02 08:00:00'),
(2, 'Room 312', 'Records associated with room 312 remain sealed until further notice.', 'warning', '1992-11-04 19:30:00'),
(3, 'Radiology maintenance', 'The imaging terminal will be unavailable during the night shift.', 'info', '1992-11-06 16:15:00')
ON DUPLICATE KEY UPDATE
    title = VALUES(title),
    body = VALUES(body),
    severity = VALUES(severity),
    published_at = VALUES(published_at);
