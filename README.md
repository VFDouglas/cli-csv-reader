# cli-csv-reader
A simple PHP CLI tool to read `.csv` files and import data into a MySQL database using Docker Compose.  
This project demonstrates how to structure a lightweight, containerized data importer with extensibility.

## 🚀 Features
- PHP CLI container (with Composer)
- MySQL 8.0 database container (with persistence)
- Easy setup with Docker Compose
- Extensible architecture (can be extended with Redis, queues, etc.)

## 🖥️Requirements
- Docker >= 20
- Docker Compose >= 2
- WSL2 (for Windows users)

## ⚙️Installation

Clone the project
```
git clone https://github.com/VFDouglas/cli-csv-reader.git
```
Enter the project:
```
cd cli-csv-reader
```
Create a `.env` file:
```
cp .env.example .env
```
Run the commands:
```
docker compose build php --no-cache
docker compose up -d
docker compose exec php composer install
```

You can access the app containers with the command:
```
# In this case PHP is the service name specified in the compose.yaml file
docker compose exec php bash
```
