FOR SEARCH MOVIES: NEED API KEY

API KEY: 7ab7b8e64f4b031bbd2f0744a187b129


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

DATABASE CODE FOR REVIEWS:

DROP TABLE IF EXISTS reviews;

CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    rating INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_movie FOREIGN KEY (movie_id) REFERENCES movies(movie_id)
) ENGINE=InnoDB;


