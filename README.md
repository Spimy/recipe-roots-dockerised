# Recipe Roots

## Steps to Run

1. Create a new folder. For this example, it'll be called `assignment`.
2. Inside `assignment`, run `git clone https://github.com/Spimy/recipe-roots.git`
   - It should create a folder called `recipe-roots`
3. Set your email and app password inside of `sendmail/msmtprc` file.
4. Open `recipe-roots/app/core/config.php`: change `DB_HOST` to `mariadb` and `DB_PASS` to `root`
5. Make sure Docker Desktop is running and/or Docker is installed
6. Inside `assignment` which contains the `docker-compose.yml` file*, run `docker compose up`

*You can check if you are in the right directory by running `ls` and see if `docker-compose.yml` shows up 

### Note

- Control + C to stop the docker containers when it is attached to your terminal
- First time setting up MariaDB might throw an error: when you see a `mariadb` folder get created, restart the containers (`Control + C` and then rerun `docker compose up`) after a few seconds
