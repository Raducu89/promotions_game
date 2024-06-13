# Symfony Project

This is a Symfony project set up to run with Docker and Apache. The project includes a game service where users can log in and win prizes.

### Installation

1. **Clone the repository**

   ```bash
   git clone https://github.com/Raducu89/promotions_game.git
   cd promotions_game

2. **Install PHP dependences**

   ```bash
   composer install

3. **Set up environment variables**

   Create .env file


4. **Set up the database**
   
   Create a new database and update your .env file with the database credentials.

   Run the database migrations and seeds:
   
   ```bash
   php bin/console doctrine:migrations:migrate
   
   php bin/console app:import-data --partners=public/csv/partners_en.csv --prizes=public/csv/prizes_en.csv
   php bin/console app:import-data --partners=public/csv/partners_de.csv --prizes=public/csv/prizes_de.csv
   php bin/console app:seed-users


### Usage

1. **Start the server**

   ```bash
   symfony server:start

2. **Import the postman collection and test the endpoints**

   ```bash
   /api/login - login endopint
   /api/status - Game Status: (GET) - Checks if the user has already played today and returns the prize if they have.
   /api/play  - Play Game: (GET) - Allows the user to play the game and win a prize.


