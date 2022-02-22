| articles | CREATE TABLE `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `imgURL` text NOT NULL,
  `gear` tinyint(1) NOT NULL DEFAULT 0,
  `artists_and_technicians` tinyint(1) NOT NULL DEFAULT 0,
  `shows` tinyint(1) NOT NULL DEFAULT 0,
  `staff_picks` tinyint(1) NOT NULL DEFAULT 0,
  `title` text NOT NULL,
  `link` text DEFAULT NULL,
  `articleContent` longtext DEFAULT NULL,
  `postType` enum('linked_post','regular_post') NOT NULL,
  `postDateTime` datetime NOT NULL DEFAULT current_timestamp(),
  `isEdited` tinyint(1) NOT NULL DEFAULT 0,
  `lastUpdatedDateTime` datetime DEFAULT NULL,
  `blurb` text DEFAULT NULL,
  `authorID` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `authorID` (`authorID`),
  CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`authorID`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1 |


| author_requests | CREATE TABLE `author_requests` (
  `request_datetime` datetime NOT NULL,
  `requesterID` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  KEY `requesterID` (`requesterID`),
  CONSTRAINT `author_requests_ibfk_1` FOREIGN KEY (`requesterID`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 |


| comments | CREATE TABLE `comments` (
  `commentID` int(11) NOT NULL AUTO_INCREMENT,
  `likes` int(11) NOT NULL DEFAULT 0,
  `dislikes` int(11) NOT NULL DEFAULT 0,
  `articleID` int(11) NOT NULL,
  `datetime_posted` datetime NOT NULL DEFAULT current_timestamp(),
  `posterID` int(11) NOT NULL,
  `commentContent` text NOT NULL,
  `isEdited` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`commentID`),
  KEY `posterID` (`posterID`),
  KEY `articleID` (`articleID`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`posterID`) REFERENCES `users` (`id`),
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`articleID`) REFERENCES `articles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 |


| emotes | CREATE TABLE `emotes` (
  `likerID` int(11) NOT NULL,
  `likedCommentID` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `isLike` tinyint(1) DEFAULT NULL,
  `isDislike` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `likedCommentID` (`likedCommentID`),
  KEY `likerID` (`likerID`),
  CONSTRAINT `emotes_ibfk_1` FOREIGN KEY (`likedCommentID`) REFERENCES `comments` (`commentID`),
  CONSTRAINT `emotes_ibfk_2` FOREIGN KEY (`likerID`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=latin1 |


| users | CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(150) NOT NULL,
  `first_name` text NOT NULL,
  `last_name` text NOT NULL,
  `display_name` varchar(200) NOT NULL,
  `author_status` tinyint(1) NOT NULL,
  `admin_status` tinyint(1) NOT NULL,
  `email` varchar(200) NOT NULL,
  `hashed_password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1 |
