# Recipe Roots

## Steps to Run

1. Set up email and passwords in `conf/sendmail/msmtprc.conf`
2. Run `docker compose up`

## Traefik setup

This assumes Traefik reverse proxy is already set up. The app still uses apache behind the scenes and Traefik simply proxies the right url to the apache server in the container. Ensure Traefik's network is running on `web-gateway`. Following this can ignore [the normal setup](#steps-to-run).

1. Create a `.env` based on [`.env.example`](./.env.example).
2. Run `docker compose up -f docker-compose.traefik.yml -d --build` and Traefik should automatically acquire an SSL certificate from LetsEncrypt.
3. Run `docker exec -i mariadb mysql -u root -p[password] RecipeRoots < seed/script(-prod).sql` to seed the database with dummy data.
4. Run `cp seed/uploads recipe-roots/public/uploads` to copy all seeded media to the actual server.

The set up is very scuffed because I initially did not plan for this but after moving everything into a single server, I needed a main proxy otherwise running multiple web servers (NGINX, Apache, Traefik) would cause port 80 and port 443 to conflict among the web servers. Traefik now stands as the main proxy for everything.

Also with this, the database user and password are no longer just `root` anymore as everything is set in the `.env`.
