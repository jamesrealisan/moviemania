DATABASE CODE FOR MOVIES:

CREATE TABLE movies (
    movie_id INT PRIMARY KEY,  -- This matches the TMDb movie ID
    title VARCHAR(255),
    poster_url VARCHAR(500),
    release_year VARCHAR(10),
    genre VARCHAR(100)
);


DATABASE CODE FOR USERS:

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);
