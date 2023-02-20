-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2016 at 04:06 PM
-- Server version: 10.1.16-MariaDB
-- PHP Version: 7.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbms_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `C_ID` int(5) NOT NULL,
  `C_NAME` varchar(30) NOT NULL,
  `FIELD` varchar(30) NOT NULL,
  `C_URL` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`C_ID`, `C_NAME`, `FIELD`, `C_URL`) VALUES
(14001, 'Ashok Leyland', 'Automobile Manufacturing', 'ashokleyland.com/apply-job'),
(14002, 'BEL', 'Advanced Electronic Products', 'bel-india.com/'),
(14003, 'Essar', 'Power Communication', 'essar.com/'),
(14004, 'Mahindra and Mahindra', 'Automobile Manufacturing', 'mahindra.com/Careers'),
(15001, 'Essar', 'Power Communication', 'essar.com/'),
(15002, 'Microsoft', 'OS Developer', 'careers.microsoft.com'),
(15003, 'Mahindra and Mahindra', 'Automobile Manufacturing', 'mahindra.com/Careers'),
(15004, 'SAIL', 'Steel Manufacturing', 'sail.shine.com/jobs/'),
(16001, 'TATA Motors', 'Automobile', 'careers.tatamotors.com/'),
(16002, 'TCS', 'IT', 'careers.tcs.com/'),
(16003, 'United Health Group', 'Project Management', 'careers.unitedhealthgroup.com'),
(16004, 'Coal India Ltd', 'Project Management', 'coalindia.in/career/en-us/home.aspx'),
(17001, 'Microsoft Research', 'Software Engineering', 'careers.research.microsoft.com/'),
(17002, 'Amazon', 'Data Warehouseing', 'amazon.jobs'),
(17003, 'United Health Groups', 'Medical Care', 'careers.unitedhealthgroup.com/'),
(17004, 'Works Applications', 'ERP', 'career.worksap.com/'),
(17005, 'Smartprix', 'IT', 'smartprix.com/about/jobs'),
(17006, 'Tesco', 'commerce', 'tesco-careers.com/'),
(17007, 'IBM Watson Research', 'Artificail Intelligence APIs', 'research.ibm.com/careers/');

-- --------------------------------------------------------

--
-- Table structure for table `comp_reg`
--

CREATE TABLE `comp_reg` (
  `C_ID` int(6) NOT NULL,
  `C_DATE` date NOT NULL,
  `VENUE` varchar(30) NOT NULL,
  `MIN_CGPA` decimal(4,2) NOT NULL DEFAULT '5.00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `comp_reg`
--

INSERT INTO `comp_reg` (`C_ID`, `C_DATE`, `VENUE`, `MIN_CGPA`) VALUES
(17001, '2016-11-14', 'SES', '7.50'),
(17001, '2016-12-06', 'SES', '7.50'),
(17002, '2016-11-15', 'SES', '7.80'),
(17003, '2016-12-07', 'SES', '7.00'),
(17004, '2016-12-08', 'SES', '7.00'),
(17005, '2016-12-09', 'SES', '6.50'),
(17006, '2016-12-10', 'SES', '8.00'),
(17007, '2016-12-12', 'SES', '8.50');

--
-- Triggers `comp_reg`
--
DELIMITER $$
CREATE TRIGGER `COMP_REG_CHECK` BEFORE INSERT ON `comp_reg` FOR EACH ROW BEGIN
declare msg varchar(30);
declare n integer;
declare md date;
declare mind date;

set n = 2000+NEW.c_id/1000;
set md= concat(n,'-06-30');
set mind =concat((n-1),'-07-01');
if(mind<date(sysdate())) then
	set mind = date(sysdate());
end if;
if(NEW.c_date > md or NEW.c_date < mind) then
	set msg = 'invalid date';
 signal sqlstate '45000' set message_text = msg;
end if;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `c_login`
--

CREATE TABLE `c_login` (
  `USERNAME` int(5) NOT NULL,
  `PASSWORD` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `c_login`
--

INSERT INTO `c_login` (`USERNAME`, `PASSWORD`) VALUES
(14001, '1'),
(14002, '1'),
(14003, '1'),
(14004, '1'),
(15001, '1'),
(15002, '1'),
(15003, '1'),
(15004, '1'),
(16001, '1'),
(16002, '1'),
(16003, '1'),
(16004, '1'),
(17001, '1'),
(17002, '1'),
(17003, '1'),
(17004, '1'),
(17005, '1'),
(17006, '1'),
(17007, '1');

--
-- Triggers `c_login`
--
DELIMITER $$
CREATE TRIGGER `comp_id_check` BEFORE INSERT ON `c_login` FOR EACH ROW BEGIN
declare n integer;
declare m integer;
declare y integer;
declare msg varchar(30);
set n = 2000 + floor(New.username/1000);
set y = extract(year from date(sysdate()));
set m = extract(month from date(sysdate()));
if(m>6) then
	set y=y+1;
end if;
if(y!=n) THEN
	set msg = "year mismatch";
    signal sqlstate '45000' set message_text = msg;
end if;
end
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `USERNAME` varchar(30) NOT NULL,
  `PASSWORD` varchar(30) NOT NULL,
  `ACC_RIGHT` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`USERNAME`, `PASSWORD`, `ACC_RIGHT`) VALUES
('admin@gmail.com', 'admin', 1),
('ag13@gmail.com', '1', 0),
('aj12@gmail.com', '1', 0),
('aj85@gmail.com', '1', 0),
('ak29@gmail.com', '1', 0),
('an64@gmail.com', '1', 0),
('as36@gmail.com', '1', 0),
('av43@gmail.com', '1', 0),
('ba96@gmail.com', '1', 0),
('de68@gmail.com', '1', 0),
('di06@gmail.com', '1', 0),
('go09@gmail.com', '1', 0),
('ka69@gmail.com', '1', 0),
('ma22@gmail.com', '1', 0),
('ma25@gmail.com', '1', 0),
('mc19@gmail.com', '1', 0),
('mo24@gmail.com', '1', 0),
('pr01@gmail.com', '1', 0),
('pr91@gmail.com', '1', 0),
('ri28@gmail.com', '1', 0),
('ru77@gmail.com', '1', 0),
('se00@gmail.com', '1', 0),
('se40@gmail.com', '1', 0),
('sh68@gmail.com', '1', 0),
('sh91@gmail.com', '1', 0),
('su48@gmail.com', '1', 0),
('vk90@gmail.com', '1', 0);

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `S_ID` char(9) NOT NULL,
  `C_ID` int(6) NOT NULL,
  `p_offered` int(8) NOT NULL,
  `offer_acc` int(1) DEFAULT '0',
  `R_DATE` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`S_ID`, `C_ID`, `p_offered`, `offer_acc`, `R_DATE`) VALUES
('10CS01001', 14001, 1000000, 1, NULL),
('10CS01002', 14002, 700000, 1, '2013-12-11'),
('10EE01001', 14002, 800000, 1, '2013-12-12'),
('10EE01002', 14003, 850000, 0, '2014-01-05'),
('10ME01001', 14003, 1000000, 1, NULL),
('11CS01001', 15001, 1200000, 1, NULL),
('11CS01002', 15002, 1800000, 1, '2014-12-15'),
('11EE01001', 15003, 800000, 0, NULL),
('11EE01002', 15003, 1200000, 0, '2014-12-19'),
('11ME01001', 15004, 2000000, 1, '2014-12-10'),
('11ME01002', 15004, 500000, 1, '2014-12-23'),
('12CS01001', 16001, 750000, 0, '2016-01-09'),
('12CS01002', 16001, 1000000, 1, '2016-01-10'),
('12EE01001', 16002, 900000, 0, '2015-12-09'),
('12EE01002', 16003, 600000, 0, NULL),
('12ME01001', 16004, 1000000, 1, NULL),
('12ME01002', 16004, 1000000, 1, '2016-01-07'),
('13CS01001', 17001, 1500000, 1, '2016-11-14'),
('13CS01005', 17001, 700000, 0, '2016-11-14');

--
-- Triggers `results`
--
DELIMITER $$
CREATE TRIGGER `offer_acc_check` BEFORE UPDATE ON `results` FOR EACH ROW BEGIN
declare msg varchar(30);
declare n integer;
declare md date;
declare mind date;
DECLARE PYR INTEGER;
DECLARE SYR INTEGER;

set PYR = NEW.c_id/1000;
set SYR = (Select P_YEAR from student where S_id = NEW.s_id);
if(PYR!=SYR) then
	set msg = "year mismatch";
    signal sqlstate '45000' set message_text = msg;
end if;

set n = 2000+NEW.c_id/1000;
set md= concat(n,'-06-30');
set mind =concat((n-1),'-07-01');
if(mind<date(sysdate())) then
	set mind = date(sysdate());
end if;
if(date(sysdate()) > md or date(sysdate()) < mind) then
	set msg = 'invalid date';
 signal sqlstate '45000' set message_text = msg;
end if;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `offer_check` BEFORE INSERT ON `results` FOR EACH ROW begin
declare sd date;
declare msg varchar(30);
DECLARE PYR INTEGER;
DECLARE SYR INTEGER;
declare n integer;
declare md date;
declare mind date;

set PYR = NEW.c_id/1000;
set SYR = (Select P_YEAR from student where S_id = NEW.s_id);
if(PYR!=SYR) then
	set msg = "year mismatch";
    signal sqlstate '45000' set message_text = msg;
end if;

if(NEW.r_date is not NULL) then
    set sd = (select s_date from schedule where c_id = NEW.c_id and s_id = NEW.s_id);
    if(NEW.r_date < sd) then
        set msg = "Trying to offer before interview date!";
        signal sqlstate '45000' set message_text = msg;
     end if;
end if;

set n = 2000+NEW.c_id/1000;
set md= concat(n,'-06-30');
set mind =concat((n-1),'-07-01');
if(mind<date(sysdate())) then
	set mind = date(sysdate());
end if;

if(date(sysdate()) > md or date(sysdate()) < mind) then
	set msg = 'invalid date';
 signal sqlstate '45000' set message_text = msg;
end if;

end
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `res_acc_check` AFTER UPDATE ON `results` FOR EACH ROW BEGIN

IF NEW.OFFER_ACC = 1 THEN
	DELETE FROM SCHEDULE WHERE S_ID = NEW.S_ID;
END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `S_ID` char(9) NOT NULL,
  `C_ID` int(6) NOT NULL,
  `S_DATE` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`S_ID`, `C_ID`, `S_DATE`) VALUES
('13CS01005', 17001, '2016-11-14'),
('13CS01019', 17001, '2016-11-14'),
('13CS01004', 17001, '2016-12-06'),
('13CS01006', 17001, '2016-12-06'),
('13CS01003', 17002, '2016-11-15'),
('13CS01019', 17002, '2016-11-15'),
('13CS01004', 17003, '2016-12-07'),
('13CS01006', 17003, '2016-12-07'),
('13CS01003', 17004, '2016-12-08'),
('13CS01019', 17004, '2016-12-08'),
('13CS01002', 17005, '2016-12-09'),
('13CS01003', 17005, '2016-12-09'),
('13CS01004', 17005, '2016-12-09'),
('13CS01005', 17005, '2016-12-09'),
('13CS01006', 17005, '2016-12-09'),
('13CS01003', 17006, '2016-12-10'),
('13CS01004', 17006, '2016-12-10'),
('13CS01005', 17006, '2016-12-10');

--
-- Triggers `schedule`
--
DELIMITER $$
CREATE TRIGGER `SC_CHECK` BEFORE INSERT ON `schedule` FOR EACH ROW BEGIN
declare sg decimal;
declare rg decimal;
declare msg varchar(30);
DECLARE PYR INTEGER;
DECLARE SYR INTEGER;
declare cnt integer;
declare msg2 varchar(30);
set cnt = (select count(*) from results where s_id=NEW.s_id and offer_acc=1);
if(cnt >0) then
	set msg = "already accpted";
    signal sqlstate '45000' set message_text = msg;
end if;

set sg = (select cgpa from student where s_id = NEW.s_id);
set rg = (select min_cgpa from comp_reg where c_id = NEW.c_id and c_date=NEW.s_date);
if(sg<rg) then
 set msg = 'MyTriggerError:Low grade';
 signal sqlstate '45000' set message_text = msg;
end if;
set PYR = NEW.c_id/1000;
set SYR = (Select P_YEAR from student where S_id = NEW.s_id);
if(PYR!=SYR) then
	set msg = "year mismatch";
    signal sqlstate '45000' set message_text = msg;
end if;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `S_ID` char(9) NOT NULL,
  `S_NAME` varchar(30) NOT NULL,
  `BRANCH` varchar(30) NOT NULL,
  `PNO` bigint(10) NOT NULL,
  `E_ID` varchar(30) NOT NULL,
  `CGPA` decimal(4,2) NOT NULL,
  `P_YEAR` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`S_ID`, `S_NAME`, `BRANCH`, `PNO`, `E_ID`, `CGPA`, `P_YEAR`) VALUES
('10CS01001', 'KALINDA', 'COMPUTER SCIENCE', 7477945947, 'ka69@gmail.com', '6.59', 19),
('10CS01002', 'SEETA', 'COMPUTER SCIENCE', 8519403494, 'se40@gmail.com', '7.43', 19),
('10EE01001', 'VIKRAM', 'ELECTRICAL ENGINEERING', 7382254252, 'vk90@gmail.com', '8.58', 19),
('10EE01002', 'GOPI', 'ELECTRICAL ENGINEERING', 9912667646, 'go09@gmail.com', '7.08', 19,
('10ME01001', 'MAHESHA', 'MECHANICAL ENGINEERING', 7889129657, 'ma22@gmail.com', '7.28', 19),
('11CS01001', 'RISHI', 'COMPUTER SCIENCE', 7628131221, 'ri28@gmail.com', '7.16', 20),
('11CS01002', 'PRATIBHA', 'COMPUTER SCIENCE', 7511062610, 'pr91@gmail.com', '6.63', 20),
('11EE01001', 'AGNI', 'ELECTRICAL ENGINEERING', 9984647863, 'ag13@gmail.com', '9.97', 20),
('11EE01002', 'PRAMOD', 'ELECTRICAL ENGINEERING', 9596734369, 'pr01@gmail.com', '8.05', 20),
('11ME01001', 'AJITH', 'MECHANICAL ENGINEERING', 7449758567, 'aj12@gmail.com', '8.70', 20),
('11ME01002', 'DINESH', 'MECHANICAL ENGINEERING', 9299004846, 'di06@gmail.com', '7.10', 20),
('12CS01001', 'MANJU', 'COMPUTER SCIENCE', 8857398522, 'ma25@gmail.com', '6.86', 21),
('12CS01002', 'DEVDAS', 'COMPUTER SCIENCE', 9079559871, 'de68@gmail.com', '7.16', 21),
('12EE01001', 'SEETA', 'ELECTRICAL ENGINEERING', 7597740682, 'se00@gmail.com', '9.05', 21),
('12EE01002', 'SHIVALI', 'ELECTRICAL ENGINEERING', 8705646867, 'sh91@gmail.com', '8.58', 21),
('12ME01001', 'MOHAN', 'MECHANICAL ENGINEERING', 7111792990, 'mo24@gmail.com', '8.20', 21),
('12ME01002', 'BALDEV', 'MECHANICAL ENGINEERING', 9583733221, 'ba96@gmail.com', '8.94', 21),
('13CS01001', 'SUMANTRA', 'COMPUTER SCIENCE', 7211800874, 'su48@gmail.com', '9.10', 19),
('13CS01002', 'ANIMA', 'COMPUTER SCIENCE', 9204714677, 'an64@gmail.com', '6.72', 18),
('13CS01003', 'RUSHIL', 'COMPUTER SCIENCE', 8119259569, 'ru77@gmail.com', '9.35', 18),
('13CS01004', 'AVANTI', 'COMPUTER SCIENCE', 7552675088, 'av43@gmail.com', '8.35', 19),
('13CS01005', 'AJAY', 'COMPUTER SCIENCE', 7786979338, 'aj85@gmail.com', '8.91', 20),
('13CS01006', 'SHARMILA', 'COMPUTER SCIENCE', 9083470874, 'sh68@gmail.com', '7.81', 21),
('13CS01019', 'Mohinish Chatterjee', 'COMPUTER SCIENCE', 7834440152, 'mc19@gmail.com', '7.89', 21),
('14CS01018', 'K Ananth', 'CSE', 7077100978, 'ak29@gmail.com', '8.40', 20),
('14CS01039', 'Avinash Swargam', 'CSE', 9437379768, 'as36@gmail.com', '7.80', 20);

--
-- Triggers `student`
--
DELIMITER $$
CREATE TRIGGER `del_yr_chg` AFTER UPDATE ON `student` FOR EACH ROW begin
if(NEW.p_year!=OLD.p_year) then
	delete from schedule where s_id = NEW.S_id;
    delete from results where s_id = NEW.S_id;
end if;
end
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`C_ID`);

--
-- Indexes for table `comp_reg`
--
ALTER TABLE `comp_reg`
  ADD PRIMARY KEY (`C_ID`,`C_DATE`);

--
-- Indexes for table `c_login`
--
ALTER TABLE `c_login`
  ADD PRIMARY KEY (`USERNAME`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`USERNAME`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`S_ID`,`C_ID`),
  ADD KEY `fk_re_cid` (`C_ID`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`S_ID`,`C_ID`),
  ADD KEY `fk_sch_cid_sdate` (`C_ID`,`S_DATE`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`S_ID`),
  ADD KEY `fk_stu_eid` (`E_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `C_ID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17008;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `company`
--
ALTER TABLE `company`
  ADD CONSTRAINT `fk_com_cid` FOREIGN KEY (`C_ID`) REFERENCES `c_login` (`USERNAME`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `comp_reg`
--
ALTER TABLE `comp_reg`
  ADD CONSTRAINT `fk_cr_cid` FOREIGN KEY (`C_ID`) REFERENCES `company` (`C_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `fk_re_cid` FOREIGN KEY (`C_ID`) REFERENCES `company` (`C_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_re_sid` FOREIGN KEY (`S_ID`) REFERENCES `student` (`S_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `fk_sch_cid_sdate` FOREIGN KEY (`C_ID`,`S_DATE`) REFERENCES `comp_reg` (`C_ID`, `C_DATE`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sch_sid` FOREIGN KEY (`S_ID`) REFERENCES `student` (`S_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `fk_stu_eid` FOREIGN KEY (`E_ID`) REFERENCES `login` (`USERNAME`) ON DELETE CASCADE ON UPDATE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `c_deleter` ON SCHEDULE EVERY 1 YEAR STARTS '2016-07-01 00:00:00' ON COMPLETION NOT PRESERVE ENABLE COMMENT 'delteing company_reg after every year' DO delete from comp_reg where 1=1$$

DELIMITER ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
