-- Practical 11: Student Management CRUD with Search, Filter, and MySQLi Statements
-- Database: studenthub
CREATE DATABASE IF NOT EXISTS `studenthub` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `studenthub`;


CREATE TABLE IF NOT EXISTS `students` (
  `student_id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `course` VARCHAR(100) NOT NULL,
  `semester` INT NOT NULL DEFAULT 1,
  `phone` VARCHAR(15) NOT NULL DEFAULT '',
  `status` ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed minimum 20 students as per practical requirements
INSERT INTO `students` (`student_id`, `name`, `email`, `course`, `semester`, `phone`, `status`) VALUES
(1, 'Aarav Patel', 'aarav.patel@charusat.edu.in', 'Computer Engineering', 3, '9825011223', 'Active'),
(2, 'Diya Shah', 'diya.shah@charusat.edu.in', 'Information Technology', 3, '9825022334', 'Active'),
(3, 'Krish Mehta', 'krish.mehta@charusat.edu.in', 'Computer Engineering', 4, '9825033445', 'Active'),
(4, 'Riya Desai', 'riya.desai@charusat.edu.in', 'Information Technology', 3, '9825044556', 'Active'),
(5, 'Vivaan Joshi', 'vivaan.joshi@charusat.edu.in', 'Computer Engineering', 5, '9825055667', 'Active'),
(6, 'Ananya Sharma', 'ananya.sharma@charusat.edu.in', 'Mechanical Engineering', 2, '9825066778', 'Inactive'),
(7, 'Rohan Verma', 'rohan.verma@charusat.edu.in', 'Civil Engineering', 4, '9825077889', 'Active'),
(8, 'Tanvi Trivedi', 'tanvi.trivedi@charusat.edu.in', 'Information Technology', 5, '9825088990', 'Active'),
(9, 'Yash Dave', 'yash.dave@charusat.edu.in', 'Electronics & Comm', 3, '9825099001', 'Active'),
(10, 'Ishaan Bhatt', 'ishaan.bhatt@charusat.edu.in', 'Computer Engineering', 2, '9825100112', 'Active'),
(11, 'Sneha Pandya', 'sneha.pandya@charusat.edu.in', 'Civil Engineering', 6, '9825111223', 'Inactive'),
(12, 'Parth Soni', 'parth.soni@charusat.edu.in', 'Information Technology', 4, '9825122334', 'Active'),
(13, 'Meera Rajput', 'meera.rajput@charusat.edu.in', 'Mechanical Engineering', 3, '9825133445', 'Active'),
(14, 'Devang Barot', 'devang.barot@charusat.edu.in', 'Computer Engineering', 5, '9825144556', 'Active'),
(15, 'Khushi Vaghela', 'khushi.vaghela@charusat.edu.in', 'Electronics & Comm', 4, '9825155667', 'Active'),
(16, 'Harshil Panchal', 'harshil.panchal@charusat.edu.in', 'Information Technology', 3, '9825166778', 'Active'),
(17, 'Nidhi Rawal', 'nidhi.rawal@charusat.edu.in', 'Civil Engineering', 2, '9825177889', 'Active'),
(18, 'Manan Shukla', 'manan.shukla@charusat.edu.in', 'Computer Engineering', 6, '9825188990', 'Active'),
(19, 'Pooja Solanki', 'pooja.solanki@charusat.edu.in', 'Mechanical Engineering', 4, '9825199001', 'Inactive'),
(20, 'Siddharth Prajapati', 'siddharth.p@charusat.edu.in', 'Electronics & Comm', 5, '9825200112', 'Active'),
(21, 'Komal Parmar', 'komal.parmar@charusat.edu.in', 'Information Technology', 2, '9825211223', 'Active'),
(22, 'Karan Zala', 'karan.zala@charusat.edu.in', 'Computer Engineering', 3, '9825222334', 'Active')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `course` = VALUES(`course`), `semester` = VALUES(`semester`);
