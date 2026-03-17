# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Vore Arts Fund is a not-for-profit arts funding website ([voreartsfund.org](https://voreartsfund.org)) built with CakePHP 4 (PHP 8.1) and React. Artists apply for funding, the community votes on projects, and the fund manages loan repayments and financial tracking.

## Common Commands

### Docker (local development)
```bash
cd docker && ./build.sh      # Build images and start containers
```
Add `127.0.0.1 vore.test` to your hosts file, then visit `http://vore.test:9000`.

### PHP
```bash
vendor/bin/phpunit                          # Run all tests
vendor/bin/phpunit tests/TestCase/Model/    # Run a subset of tests
vendor/bin/phpcs --colors -p src/ tests/    # Check code style (PSR-12)
vendor/bin/phpcbf --colors -p src/ tests/   # Auto-fix code style
vendor/bin/phpstan analyse                  # Static analysis
vendor/bin/cake migrations migrate          # Run DB migrations
vendor/bin/cake migrations rollback         # Roll back last migration
```

### React apps (each has its own `package.json`)
```bash
# vote-app (Webpack, React 17) — webroot/vote-app/
npm run dev   # Dev server on localhost:3000; append ?webpack-dev=localhost:3000 to app URL
npm run prod  # Production build

# repayment-form, transaction-form, rich-text-editor, image-uploader (Vite, React 19)
npm run dev   # Vite dev server
npm run build # Production build
npm run lint  # ESLint
```

## Architecture

### Backend: CakePHP 4 MVC

- **`src/Controller/`** — Controllers grouped into `Admin/`, `Api/`, and `My/` (user account) subdirectories
- **`src/Model/Table/`** — 15 database tables; key ones: `ProjectsTable`, `FundingCyclesTable`, `VotesTable`, `TransactionsTable`, `UsersTable`
- **`src/Model/Entity/`** — Corresponding entities
- **`templates/`** — CakePHP view templates, mirroring controller structure
- **`config/routes.php`** — All URL routing
- **`config/Migrations/`** — Phinx migration files; always use migrations for schema changes

Key non-MVC src directories:
- **`src/LoanTerms/`** — Loan repayment calculation logic
- **`src/Nudges/`** — Scheduled reminder/notification system
- **`src/Alert/`** — Alert system
- **`src/SecretHandler/`** — Secrets management (AWS, Google Drive, etc.)

### Frontend: React Islands

Each interactive component is a standalone React app built separately and embedded in CakePHP templates:
- `webroot/vote-app/` — Community voting interface (Webpack + React 17)
- `webroot/repayment-form/` — Loan repayment form (Vite + React 19)
- `webroot/transaction-form/` — Financial transaction form (Vite + React 19)
- `webroot/rich-text-editor/` — WYSIWYG editor (Vite + React 19)
- `webroot/image-uploader/` — Image upload component

### Key Integrations
- **Stripe** — Payment processing
- **Twilio** — SMS notifications
- **Mailchimp** — Email marketing
- **AWS S3** — File storage
- **Google Drive** — Document storage
- **Google reCAPTCHA** — Bot prevention

### Authentication & Authorization
Uses CakePHP Authentication and Authorization plugins. Form/Session/Cookie authenticators are configured in `src/Application.php`. Authorization policies live in `src/Policy/`.

### Email
Uses a queue-based email system (`PhantomWatson/cakephp-email-queue` plugin). Email templates are in `templates/email/`.

## Environment

Copy `config/.env.default` to `config/.env` and fill in credentials. Testing requires separate `TESTING_DATABASE_*` variables.

Docker environment is configured in `docker/.env` (MySQL on port 9906, MailHog on port 8126, PHPMyAdmin available).

## Deployment

Pushes to `development` branch auto-deploy to staging; pushes to `master` auto-deploy to production via Deploy-bot. Deployment runs `npm install` and `npm run prod` for each React app.
