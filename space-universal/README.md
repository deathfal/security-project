# Space-Universal

Space-Universal is a Docker-based Symfony project that integrates FrankenPHP, Tailwind CSS, and Webpack Encore for modern web development. This guide explains how to set up, run, and manage the project efficiently.

---

## Prerequisites

Before starting, ensure you have the following installed on your system:

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

---

## Getting Started

### First-Time Setup

1. Clone the repository:
   ```bash
   git clone <repository-url>
   cd space-universal
   ```

2. Build and start the containers (first-time setup):
   ```bash
   docker compose up -d --build
   ```

3. Install project dependencies (inside the container):
   ```bash
   docker compose exec -it php /bin/bash
   npm install
   composer install
   ```

---

## Running the Project

To start the project and compile assets in watch mode for development, use:
```bash
npm run dev
```

This single command runs both Tailwind CSS and Webpack Encore in parallel.

---

## Accessing the Symfony Container

To execute Symfony or other commands within the container:
```bash
docker compose exec -it php /bin/bash
```

Once inside the container, you can run Symfony, Composer, and Node.js commands.

---

## Common Commands

### Symfony Commands

1. Clear the cache:
   ```bash
   php bin/console cache:clear
   ```

2. List all available Symfony commands:
   ```bash
   php bin/console list
   ```

3. Check the Symfony project environment:
   ```bash
   php bin/console about
   ```

### Node.js and Webpack Commands

1. Compile assets in development mode:
   ```bash
   npm run dev
   ```

2. Compile assets in production mode:
   ```bash
   npm run build
   ```

3. If Webpack or Tailwind CSS is buggy, clean and reinstall:
   ```bash
   rm -rf node_modules package-lock.json
   npm install
   npm run dev
   ```

4. Force Webpack recompile (as a fallback):
   ```bash
   ./node_modules/.bin/webpack --config webpack.config.js --mode development
   ```

---

## Debugging

1. To check running containers:
   ```bash
   docker ps
   ```

2. To view container logs:
   ```bash
   docker compose logs -f
   ```

3. To rebuild the containers:
   ```bash
   docker compose up -d --build
   ```

---

## Stopping the Project

To stop the running containers:
```bash
docker compose down
```

This will stop and remove all the containers.

---

## Troubleshooting

If you encounter issues:

1. Ensure Docker and Docker Compose are installed and running.
2. Rebuild the containers:
   ```bash
   docker compose up -d --build
   ```
3. Clear Symfony cache:
   ```bash
   php bin/console cache:clear
   ```
4. Recompile Webpack and Tailwind CSS:
   ```bash
   npm run dev
   ```

You can also run this to recompile Webpack manually:
   ```bash
   ./node_modules/.bin/webpack --config webpack.config.js --mode development
   ```

---

## License

This project is licensed under the ISC License. See the LICENSE file for details.

---

Enjoy building with **Space-Universal**! 🚀

