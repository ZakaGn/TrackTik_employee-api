# TrackTik Employee API

A simple API for managing employee data for different providers and forwarding the data to TrackTik's system.

## Features

- Supports multiple providers with their own employee schemas.
- Automatically maps provider-specific schemas to TrackTik's employee schema.
- Provides endpoints for creating and updating employee data.
- Implements error handling, authentication using OAuth2, and request validation.

## Table of Contents

- [Getting Started](#getting-started)
- [Installation](#installation)
- [Usage](#usage)
- [API Endpoints](#api-endpoints)
- [Configuration](#configuration)
- [DTO Structure](#dto-structure)
- [Contributing](#contributing)
- [License](#license)

## Getting Started

These instructions will help you set up the project locally for development and testing purposes.

## Installation

1. **Clone the repository:**

    ```bash
    git clone git@github.com-ZakaGn:ZakaGn/TrackTik_employee-api.git
    ```

2. **Navigate to the project directory:**

    ```bash
    cd TrackTik_employee-api
    ```

3. **Install dependencies using Composer:**

   Make sure you have [Composer](https://getcomposer.org/) installed.

    ```bash
    composer install
    ```

    install vlucas/phpdotenv
    ```bash
    composer require vlucas/phpdotenv
    ```

    install redis-server 
    ```bash
    apt install redis-server
    systemctl start redis-server
    systemctl enable redis-server
    systemctl status redis-server
    composer require predis/predis
    ```

4. **Copy the `.env` file and configure the environment variables:**

    ```bash
    cp .env.edit .env
    ```

   Edit the `.env` file to set your environment variables, including authentication credentials, database connection, and API endpoint.

5. **Generate your SSH keys (if required) and add them to your GitHub account.**

## Usage

### Run the API Locally

1. Start the local development server using PHP's built-in server:

    ```bash
    php -S localhost:8000 -t public
    ```

2. The API should now be accessible at `http://localhost:8000`.

### Making API Requests

You can use tools like `cURL`, Postman, or any HTTP client to interact with the API endpoints.

## API Endpoints

### 1. Create Employee

- **URL**: `/api/employee`
- **Method**: `POST`
- **Request Body**:
    - Provider 1 Example:
      ```json
      {
          "firstName": "John",
          "lastName": "Doe",
          "email": "john.doe@example.com"
      }
      ```
    - Provider 2 Example:
      ```json
      {
          "firstName": "Jane",
          "lastName": "Doe",
          "username": "jane.doe"
      }
      ```
- **Response**: JSON object indicating success or failure.

### 2. Update Employee

- **URL**: `/api/employee/{id}`
- **Method**: `PUT` or `PATCH`
- **Request Body**: Same as the Create Employee endpoint.
- **Response**: JSON object indicating success or failure.

## Configuration

- **Authentication**: The API uses OAuth2 for authentication. Configure your client ID, client secret, and other relevant details in the `.env` file.
- **Environment Variables**: Use the `.env` file to set up environment variables like API endpoint, client credentials, and others.

## DTO Structure

The application uses Data Transfer Objects (DTOs) to represent employee data for different providers. Each provider has its own DTO that maps to TrackTik's schema. The `DTOMapper` class automatically maps incoming provider data to the appropriate DTO using reflection.

## Contributing

1. Fork the repository.
2. Create your feature branch: `git checkout -b my-new-feature`.
3. Commit your changes: `git commit -am 'Add some feature'`.
4. Push to the branch: `git push origin my-new-feature`.
5. Submit a pull request.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

Test:

    cd public
    php -S localhost:8000 -t public
    
    curl -X POST http://localhost:8000/api/employee -H "Content-Type: application/json" -d '{
        "firstName": "John",
        "lastName": "Doe",
        "email": "john.doe@example.com"
    }'
