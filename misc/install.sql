-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 23, 2012 at 09:39 PM
-- Server version: 5.1.44
-- PHP Version: 5.2.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: 'foxCMS'
--

-- --------------------------------------------------------

--
-- Table structure for table 'ads'
--

CREATE TABLE IF NOT EXISTS ads (
  aid int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  content text,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (aid)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'blocks'
--

CREATE TABLE IF NOT EXISTS blocks (
  bid int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `desc` text NOT NULL,
  PRIMARY KEY (bid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'block_content'
--

CREATE TABLE IF NOT EXISTS block_content (
  bid int(11) NOT NULL,
  content_type varchar(255) NOT NULL,
  content_id int(11) NOT NULL,
  weight int(3) NOT NULL,
  KEY bid (bid,content_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'files'
--

CREATE TABLE IF NOT EXISTS files (
  fid int(11) NOT NULL AUTO_INCREMENT,
  fd_id int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  ext_name varchar(255) NOT NULL,
  path varchar(255) DEFAULT NULL,
  last_modified timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  sys_created timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  file_size int(11) NOT NULL,
  page_meta text NOT NULL,
  page_title varchar(255) NOT NULL,
  width int(11) NOT NULL,
  height int(11) NOT NULL,
  image_type int(2) NOT NULL,
  size_string varchar(255) NOT NULL,
  bits int(2) NOT NULL,
  channels int(2) NOT NULL,
  mime varchar(255) NOT NULL,
  PRIMARY KEY (fid),
  KEY fdid (fd_id),
  KEY image_type (image_type,mime)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'folders'
--

CREATE TABLE IF NOT EXISTS folders (
  fd_id int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  parent_id int(11) NOT NULL DEFAULT '0',
  path varchar(255) DEFAULT NULL,
  last_modified timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  sys_created timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  locked int(2) NOT NULL DEFAULT '0',
  page_meta text NOT NULL,
  page_title varchar(255) NOT NULL,
  PRIMARY KEY (fd_id),
  KEY parent_id (parent_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
