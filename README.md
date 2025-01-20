# Booking API

A REST API built with PHP 8.3 and Symfony components for managing booking statistics and maximizing booking profits.

## Features

- Calculate booking statistics (average, minimum, and maximum profits per night)
- Find optimal booking combinations to maximize total profit
- CQRS and DDD architectural patterns
- Built with PHP 8.3
- Docker ready
- The application runs on port `8089` to avoid collision with other common ports.

## Prerequisites

- Docker and Docker Compose
- Git

## Testing the API

You can test the endpoints using either:
* Postman - GUI tool for API testing
* cURL - Command-line tool for HTTP requests


## Installation

1. Clone the repository
```bash
  git clone [your-repository-url]
```

2. Build and start Docker containers
```bash
  docker-compose up -d
```
3. Install dependencies
```bash
  docker exec -it booking-api composer install
```
4. Run phpunit tests
```bash
  docker exec -it booking-api vendor/bin/phpunit
```
## TODO

+ Implement Value Objects for BookingRequest validation
+ Create domain-specific exceptions
+ Add Query Bus for CQRS pattern
+ Create BookingRequests Collection for type safety and encapsulation
+ Install Symfony YAML component for config
+ Set up Xdebug for debugging
+ Add OpenAPI/Swagger documentation
+ Configure custom domain for local environment
+ Add PHP-CS-Fixer for code style and PHPStan for checking errors


## API Endpoints
### Calculate Booking Statistics
```bash
  curl -X POST http://localhost:8089/stats \
-H "Content-Type: application/json" \
-d '[
   {
       "request_id": "1",
       "check_in": "2024-01-01",
       "nights": 2,
       "selling_rate": 100,
       "margin": 10.5
   }
]'
```

Response:

```
{
    "average": 5.25,
    "minimum": 5.25,
    "maximum": 5.25
}
```

### Maximize Bookings
```bash
  curl -X POST http://localhost:8089/maximize \
-H "Content-Type: application/json" \
-d '[
    {
        "request_id": "A",
        "check_in": "2024-01-01",
        "nights": 5,
        "selling_rate": 1000,
        "margin": 10
    },
    {
        "request_id": "B",
        "check_in": "2024-01-03",
        "nights": 5,
        "selling_rate": 700,
        "margin": 10
    },
    {
        "request_id": "C",
        "check_in": "2024-01-07",
        "nights": 5,
        "selling_rate": 400,
        "margin": 10
    }
]'
```

Response:

```
{
    "request_ids": ["A", "C"],
    "total_profit": 140,
    "avg_night": 14,
    "min_night": 8,
    "max_night": 20
}
```