SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `trainee_reports`
--

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `report_id` int(11) NOT NULL,
  `report_number` int(11) NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `status` tinyint(6) NOT NULL DEFAULT 0 COMMENT '0 = default, 1 = exported'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `report_weekday`
--

CREATE TABLE `report_weekday` (
  `report_weekday_id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `weekday_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `report_weekday_row`
--

CREATE TABLE `report_weekday_row` (
  `report_weekday_row_id` int(11) NOT NULL,
  `report_weekday_id` int(11) NOT NULL,
  `text` varchar(200) NOT NULL,
  `hours` decimal(10,1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `weekday`
--

CREATE TABLE `weekday` (
  `ID_weekday` int(11) NOT NULL,
  `weekday_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `weekday`
--

INSERT INTO `weekday` (`ID_weekday`, `weekday_name`) VALUES
(0, 'Montag'),
(1, 'Dienstag'),
(2, 'Mittwoch'),
(3, 'Donnerstag'),
(4, 'Freitag'),
(5, 'Samstag'),
(6, 'Sonntag');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`report_id`),
  ADD UNIQUE KEY `report_number` (`report_number`);

--
-- Indexes for table `report_weekday`
--
ALTER TABLE `report_weekday`
  ADD PRIMARY KEY (`report_weekday_id`),
  ADD KEY `report_id` (`report_id`),
  ADD KEY `weekday_id` (`weekday_id`);

--
-- Indexes for table `report_weekday_row`
--
ALTER TABLE `report_weekday_row`
  ADD PRIMARY KEY (`report_weekday_row_id`),
  ADD KEY `report_weekday_id` (`report_weekday_id`);

--
-- Indexes for table `weekday`
--
ALTER TABLE `weekday`
  ADD PRIMARY KEY (`ID_weekday`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
--
-- AUTO_INCREMENT for table `report_weekday`
--
ALTER TABLE `report_weekday`
  MODIFY `report_weekday_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;
--
-- AUTO_INCREMENT for table `report_weekday_row`
--
ALTER TABLE `report_weekday_row`
  MODIFY `report_weekday_row_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=243;
--
-- AUTO_INCREMENT for table `weekday`
--
ALTER TABLE `weekday`
  MODIFY `ID_weekday` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `report_weekday`
--
ALTER TABLE `report_weekday`
  ADD CONSTRAINT `report_weekday_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `report` (`report_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `report_weekday_ibfk_2` FOREIGN KEY (`weekday_id`) REFERENCES `weekday` (`ID_weekday`) ON UPDATE CASCADE;

--
-- Constraints for table `report_weekday_row`
--
ALTER TABLE `report_weekday_row`
  ADD CONSTRAINT `report_weekday_row_ibfk_1` FOREIGN KEY (`report_weekday_id`) REFERENCES `report_weekday` (`report_weekday_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
