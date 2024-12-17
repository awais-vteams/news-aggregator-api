# News Aggregator API

The **News Aggregator API** fetches articles from various news providers (NewsAPI, New York Times, and The Guardian), stores them in the database, and serves them via a RESTful API. It supports advanced filtering, user preferences, and personalized articles.

---

## Setup Instructions

### Step 1: Clone the Repository
```bash
git clone https://github.com/awais-vteams/news-aggregator-api.git
cd news-aggregator-api
```

---

### Step 2: Set Up the Environment Variables
1. Copy the `.env.example` file to `.env`:
   ```bash
   cp .env.example .env
   ```

2. Update the following keys in the `.env` file:
    - `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
    - Add your API keys for the news providers:
        - `NEWSAPI_KEY`
        - `NYTIMES_KEY`
        - `GUARDIAN_KEY`

---

### Step 3: Install Dependencies
Use Laravel Sail to install dependencies:
```bash
./vendor/bin/sail composer install
```

---

### Step 4: Start the Environment
Start the Sail environment:
```bash
./vendor/bin/sail up -d
```

This will start the application along with its services (database, queue, etc.).

---

### Step 5: Run Migrations and Seeders
Run the database migrations to set up the schema:
```bash
./vendor/bin/sail artisan migrate
```

Optionally, seed the database:
```bash
./vendor/bin/sail artisan db:seed
```

---

### Step 6: Generate Application Key
Generate the Laravel application key:
```bash
./vendor/bin/sail artisan key:generate
```

---

### Step 7: Test the API
Run the test suite to ensure everything is working as expected:
```bash
./vendor/bin/sail artisan test
```

Alternatively, you can run the Pest tests:
```bash
./vendor/bin/sail vendor/bin/pest
```

---

### Step 8: Access the Application
- API Base URL: `http://localhost`

---

## Development Notes

### News Providers
Each provider (NewsAPI, New York Times, The Guardian) implements the `NewsProvider` interface. This design ensures consistency and allows for easy addition of new providers.

### Database Design
- Articles are stored in a relational database with fields for metadata such as title, description, and source.
- User preferences are linked to articles for personalization.

### Running Commands with Sail
Use the `sail` command to execute Artisan and other commands. For example:
```bash
./vendor/bin/sail artisan <command>
```

---

## Additional Notes

### Environment-Specific Configurations
- Use `.env.testing` for test environment variables.
- Update `phpunit.xml` for any test-specific configurations.

## License
This project is open-source and available under the [MIT License](LICENSE).
