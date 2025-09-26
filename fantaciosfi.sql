DROP TABLE IF EXISTS bets;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS matches;
DROP TABLE IF EXISTS teams;

CREATE TABLE IF NOT EXISTS teams(
	id BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	name VARCHAR(20) NOT NULL
);

CREATE TABLE IF NOT EXISTS matches(
	id BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	team1 BIGINT UNSIGNED NOT NULL REFERENCES teams(id),
	team2 BIGINT UNSIGNED NOT NULL REFERENCES teams(id),
	match_date DATE NOT NULL,
	GW1 float NOT NULL,
	GX float NOT NULL,
	GW2 float NOT NULL,
	GG float NOT NULL,
	GNG float NOT NULL,
	CHECK(team1<>team2)
);

CREATE TABLE IF NOT EXISTS users(
	id BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	email VARCHAR(50) NOT NULL,
	name VARCHAR(30) NOT NULL,
	surname VARCHAR(30) NOT NULL,
	role INT UNSIGNED NOT NULL,
	balance INT UNSIGNED NOT NULL
);

CREATE TABLE IF NOT EXISTS bets(
	match_id BIGINT UNSIGNED NOT NULL REFERENCES matches(id),
	user_id BIGINT UNSIGNED NOT NULL REFERENCES users(id),
	choice ENUM('W1', 'X', 'W2', 'G', 'NG') NOT NULL,
	amount INT UNSIGNED NOT NULL,
	PRIMARY KEY(match_id, user_id)
);

INSERT INTO users VALUES
(null, 'paolo.foschini@gmail.com', 'Paolo', 'Fosc', 1, 30),
(null, 'francesco.foschini@gmail.com', 'Fra', 'Fos', 0, 0);

SELECT * FROM users;

INSERT INTO teams VALUES
(null, 'Roma'),
(null, 'Lazio'),
(null, 'Inter'),
(null, 'Milan');

SELECT * FROM teams;

INSERT INTO matches VALUES
(null, 1, 2, '2025-09-20', 1.01, 1.02, 10.3, 1.04, 1.05),
(null, 3, 4, '2025-09-23', 2.01, 3.02, 4.3, 5.04, 6.05);

SELECT * FROM matches;

INSERT INTO bets VALUES
(1, 1, 'W1', 3),
(1, 2, 'X', 5),
(2, 1, 'W2', 3),
(2, 2, 'G', 5);

SELECT * FROM bets;

SELECT matches.*, home.name AS home, away.name AS away FROM matches
	JOIN teams home ON matches.team1=home.id
	JOIN teams away ON matches.team2=away.id;

DELETE FROM bets;