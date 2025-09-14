# cli-file-reader
A lightweight PHP CLI tool to read `.csv` files and import data into a database.  
This project demonstrates clean architecture, extensibility, and test-driven development in a containerized environment.

## 🚀 Features
- PHP CLI container with Composer dependencies
- MySQL 8.0 database container with persistent storage
- Easy setup and teardown with Docker Compose
- Extensible architecture (can add Redis, queues, or other services)
- Unit and integration tests included

## 🖥️ Requirements
- Docker >= 20
- Docker Compose >= 2
- WSL2 (for Windows users)
- For usage without Docker, check README-no-docker.md

## ⚙️ Installation

- Clone the project
```
git clone https://github.com/VFDouglas/cli-file-reader.git
```
- Run the installer script (Check file for details):
```
# Development
./install.sh

# Production
./install.sh production
```
- Run migrations:
```
docker compose exec php vendor/bin/phinx migrate
```

## 📂 Usage:
```
# Enter the container
docker compose exec php bash

# Run the command
php bin/console import:products
```
- If you want to test custom files, just add them to the directory `storage/products`.
- There are already sample files with distinct delimiters, enclosures and escape characters.

## 🧪 Testing
```
# Inside the container
vendor/bin/phpunit --testdox

# Outside the container
docker compose exec php vendor/bin/phpunit --testdox
```

## 💡 Notes
- The project is designed to be easily extensible. You can add new file readers (JSON, XML, etc.) or new persistence layers without changing the core CLI logic.
- Singleton, factories, DTOs and repositories are used to demonstrate clean and testable architecture.
- All code is compatible with PHP 8.3+ and adheres to PSR standards.

## ❌ Common Errors
- MySQL port already being used:
  - Go to `.env` and change the value of `DB_PORT_HOST`.
- Cannot load build context (while building a new image):
  - Delete `.docker` directory (after copying plausible data) and run the command again.
