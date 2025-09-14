# cli-file-reader

A lightweight PHP CLI tool to read .csv files and import data into a database.
This project demonstrates clean architecture, extensibility, and test-driven development.

## 🚀 Features

- PHP CLI command-line tool with Composer dependencies
- MySQL 8.0+ support (or compatible versions)
- Clean and extensible architecture (easily add new readers or persistence layers)
- Unit and integration tests included

## 🖥️ Requirements

- PHP >= 8.3
- Composer >= 2.0
- MySQL >= 8.0 (or MariaDB equivalent)
- Git

## ⚙️ Installation

- Clone the project:
```
git clone https://github.com/VFDouglas/cli-file-reader.git
cd cli-file-reader
```

- Install dependencies:
```
composer install
```

- Copy environment file and adjust database credentials:
```
cp .env.example .env
```

- Change `.env` file to your DB credentials.

- Run database migrations:
```
vendor/bin/phinx migrate
```

## 📂 Usage

- Run the command:
```
php bin/console import:products
```


- To test custom files, add them to the directory `storage/products`.
- Sample files with different delimiters, enclosures, and escape characters are already included.

## 🧪 Testing

- Run PHPUnit tests:
```
vendor/bin/phpunit --testdox
```

## 💡 Notes

- The project is designed to be easily extensible. You can add new file readers (JSON, XML, etc.) or new persistence layers without changing the core CLI logic.

- Singleton, factories, DTOs, and repositories are used to demonstrate clean and testable architecture.

- All code is compatible with PHP 8.3+ and adheres to PSR standards.
