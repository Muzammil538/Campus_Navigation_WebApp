-- Sample departments
INSERT INTO departments (name, code, description) VALUES
('Computer Science', 'CSE', 'Department of Computer Science & Engineering'),
('Electronics', 'ECE', 'Department of Electronics & Communication'),
('Mechanical', 'MECH', 'Department of Mechanical Engineering');

-- Sample labs
INSERT INTO labs (department_id, name, room_no, description) VALUES
(1, 'AI & ML Lab', 'CSE-101', 'Artificial Intelligence research lab'),
(1, 'Database Lab', 'CSE-102', 'Database systems and DBMS lab'),
(2, 'Digital Electronics Lab', 'ECE-201', 'Digital circuit design lab'),
(2, 'Embedded Systems Lab', 'ECE-202', 'Microcontroller programming lab');