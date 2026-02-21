# Laravel Community Uganda Platform

A production-ready Laravel-based community platform for Laravel developers in Uganda where software engineers can gather, ask questions, share resources, find jobs, and attend events.

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=flat&logo=vue.js)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=flat&logo=tailwind-css)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php)
![License](https://img.shields.io/badge/License-MIT-green?style=flat)

## 📑 Table of Contents

- [Features](#-features)
- [Tech Stack](#️-tech-stack)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Project Structure](#-project-structure)
- [API Documentation](#-api-documentation)
- [Admin Dashboard](#-admin-dashboard)
- [Testing](#-testing)
- [Deployment](#-deployment)
- [Contributing](#-contributing)
- [Code of Conduct](#-code-of-conduct)
- [License](#-license)
- [Community](#-community)

## 🚀 Features

### User System
- Registration with email verification
- Login/Logout with Laravel Sanctum
- Password reset functionality
- Social login (Google, GitHub)
- Comprehensive profile system with skills, bio, and social links
- Follow/Block users functionality
- Role-based access control (Admin, Moderator, Verified Developer, Recruiter, Member)
- Reputation system with badges

### Community Forum (StackOverflow Style)
- Create, edit, delete posts with markdown support
- Rich text editor with code syntax highlighting
- Nested comments/replies
- Upvote/Downvote system
- Mark best answer functionality
- Tagging system for better organization
- Bookmark posts for later reference
- Leaderboard for top contributors

### Resource Sharing
- Upload and share PDF files
- Share GitHub repositories
- Share YouTube tutorials
- Code snippets with syntax highlighting
- Ratings and reviews system
- Download counter
- Resource bookmarking

### Jobs Board
- Post jobs, freelance gigs, and internships
- Apply with profile or upload CV
- Track application status
- Save jobs for later
- Admin approval workflow
- Job type categorization (Full-time, Part-time, Contract, Remote)

### Events & Meetups
- Create and manage events
- RSVP system with attendance tracking
- Support for Online/Physical/Hybrid events
- Google Maps integration for venue location
- Event reminders via email
- Event calendar view

### Mentorship System
- Mentor profiles with expertise areas
- Request mentorship sessions
- Schedule one-on-one sessions
- Rating and feedback system
- Session history tracking

### Private Messaging
- Real-time one-to-one chat
- Group chat functionality
- File sharing capabilities
- Message read status
- Conversation management

### Notification System
- Post replies notifications
- @mentions alerts
- Job match notifications
- Event reminders
- Email digest options
- In-app notification center

### Admin Dashboard
- Comprehensive statistics overview
- User management (view, edit, ban, verify)
- Content moderation tools
- Report handling system
- Platform settings configuration
- System health monitoring
- Growth analytics with charts
- Top users leaderboard

## 🛠️ Tech Stack

| Category | Technology |
|----------|------------|
| **Backend** | Laravel 11, PHP 8.2+ |
| **Frontend** | Vue 3, Tailwind CSS |
| **Database** | MySQL 8.0+ / PostgreSQL 14+ |
| **Cache/Queue** | Redis (optional) |
| **Authentication** | Laravel Sanctum |
| **Permissions** | Spatie Laravel Permission |
| **Real-time** | Laravel Echo, Pusher (optional) |
| **File Storage** | Laravel Filesystem (local/S3) |

## 📋 Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL >= 8.0 or PostgreSQL >= 14
- Redis (optional, for caching and queues)
- npm or pnpm

## 🚀 Installation

### 1. Clone the repository
```bash
git clone https://github.com/laravelcommunityug/community-platform.git
cd community-platform
```

### 2. Install PHP dependencies
```bash
composer install
```

### 3. Install Node.js dependencies
```bash
npm install
# or
pnpm install
```

### 4. Environment setup
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure database
Edit `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_ug
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Run migrations and seeders
```bash
php artisan migrate
php artisan db:seed
```

### 7. Build frontend assets
```bash
npm run build
```

### 8. Start the development server
```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## 🔧 Configuration

### Social Login
Add your OAuth credentials in `.env`:
```env
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret

GITHUB_CLIENT_ID=your-github-client-id
GITHUB_CLIENT_SECRET=your-github-client-secret
```

### Redis (Optional)
```env
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

### Mail Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="noreply@laravelcommunity.ug"
MAIL_FROM_NAME="${APP_NAME}"
```

### File Storage
For production, configure S3 or another cloud storage:
```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
```

## 📁 Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/       # API Controllers
│   │   │   ├── Admin/             # Admin Controllers
│   │   │   ├── AuthController.php
│   │   │   ├── PostController.php
│   │   │   └── ...
│   │   ├── Middleware/            # Custom Middleware
│   │   └── Resources/             # API Resources
│   ├── Models/                    # Eloquent Models
│   ├── Policies/                  # Authorization Policies
│   └── Services/                  # Business Logic Services
├── bootstrap/
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   └── permission.php             # Spatie Permission Config
├── database/
│   ├── factories/                 # Model Factories
│   ├── migrations/                # Database Migrations
│   └── seeders/                   # Database Seeders
├── public/
│   └── build/                     # Compiled Assets
├── resources/
│   ├── js/
│   │   ├── components/            # Vue Components
│   │   ├── views/                 # Vue Views/Pages
│   │   │   ├── admin/             # Admin Views
│   │   │   ├── auth/              # Auth Views
│   │   │   └── ...
│   │   ├── stores/                # Pinia Stores
│   │   ├── router/                # Vue Router
│   │   └── api.js                 # Axios Configuration
│   └── css/                       # Tailwind CSS
├── routes/
│   ├── api.php                    # API Routes
│   └── web.php                    # Web Routes
├── tests/                         # Test Files
├── deployment/
│   ├── nginx.conf                 # Nginx Configuration
│   └── supervisor.conf            # Supervisor Configuration
├── CODE_OF_CONDUCT.md
├── README.md
└── package.json
```

## 📡 API Documentation

The API is versioned and available at `/api/v1/`. All responses follow this format:

### Success Response
```json
{
  "success": true,
  "data": { ... },
  "message": "Operation successful"
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description",
  "errors": { ... }
}
```

### Key Endpoints

#### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/auth/register` | Register new user |
| POST | `/api/v1/auth/login` | Login user |
| POST | `/api/v1/auth/logout` | Logout user (auth required) |
| GET | `/api/v1/auth/user` | Get authenticated user |
| POST | `/api/v1/auth/forgot-password` | Request password reset |
| POST | `/api/v1/auth/reset-password` | Reset password |

#### Posts
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/posts` | List posts (paginated) |
| POST | `/api/v1/posts` | Create post (auth required) |
| GET | `/api/v1/posts/{slug}` | Get single post |
| PUT | `/api/v1/posts/{post}` | Update post (auth required) |
| DELETE | `/api/v1/posts/{post}` | Delete post (auth required) |
| POST | `/api/v1/posts/{post}/vote` | Vote on post |
| POST | `/api/v1/posts/{post}/bookmark` | Bookmark post |

#### Jobs
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/jobs` | List jobs (paginated) |
| POST | `/api/v1/jobs` | Create job (auth required) |
| GET | `/api/v1/jobs/{slug}` | Get single job |
| POST | `/api/v1/jobs/{job}/apply` | Apply for job |
| POST | `/api/v1/jobs/{job}/save` | Save job |

#### Events
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/events` | List events |
| GET | `/api/v1/events/upcoming` | Get upcoming events |
| POST | `/api/v1/events` | Create event (auth required) |
| POST | `/api/v1/events/{event}/rsvp` | RSVP to event |

#### Admin Endpoints
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/admin/statistics` | Dashboard statistics |
| GET | `/api/v1/admin/users` | List all users |
| PUT | `/api/v1/admin/users/{user}` | Update user |
| POST | `/api/v1/admin/users/{user}/ban` | Ban user |
| GET | `/api/v1/admin/reports` | List reports |
| POST | `/api/v1/admin/reports/{report}/resolve` | Resolve report |
| GET | `/api/v1/admin/settings` | Get platform settings |
| PUT | `/api/v1/admin/settings` | Update settings |

##  Admin Dashboard

The admin dashboard provides comprehensive management tools:

### Dashboard Overview
- Platform statistics (users, posts, jobs, events)
- Growth charts with customizable time periods
- Recent activity feed
- Pending reports summary
- Top contributors leaderboard

### User Management
- View and search all users
- Edit user profiles and roles
- Ban/unban users with reason tracking
- Verify user accounts
- Assign roles (Admin, Moderator, etc.)

### Content Moderation
- Review and moderate posts
- Approve/reject job listings
- Manage reported content
- Feature/highlight content

### Reports Handling
- View all user reports
- Resolve or dismiss reports
- Track resolution history
- Take action on reported content

### Platform Settings
- General settings (name, tagline, contact)
- Feature flags (enable/disable features)
- Content moderation settings
- Email notification preferences
- Security settings
- Maintenance mode control

##  Default Users

After seeding, you can login with:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@laravelcommunity.ug | password |
| Moderator | moderator@laravelcommunity.ug | password |

>  **Important**: Change these passwords in production!

##Testing

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test --filter PostTest

# Run with coverage
php artisan test --coverage

# Run frontend tests
npm run test
```

## 🚀 Deployment

### Production Checklist

1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`
3. Configure proper database credentials
4. Set up Redis for caching and queues
5. Configure mail driver
6. Set up SSL certificate
7. Configure queue workers with Supervisor
8. Set up scheduled tasks cron
9. Optimize autoloader: `composer install --no-dev --optimize-autoloader`
10. Cache config: `php artisan config:cache`
11. Cache routes: `php artisan route:cache`
12. Cache views: `php artisan view:cache`

### Queue Worker (Supervisor)
```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/app/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/app/storage/logs/worker.log
stopwaitsecs=3600
```

### Cron Job
```bash
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

### Nginx Configuration
A sample Nginx configuration is provided in `deployment/nginx.conf`.

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

### Development Workflow

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Run tests to ensure everything works
5. Commit your changes (`git commit -m 'Add amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

### Coding Standards

- Follow PSR-12 coding standards for PHP
- Use ESLint and Prettier for JavaScript/Vue
- Write meaningful commit messages
- Add tests for new features
- Update documentation as needed

## 📜 Code of Conduct

Please read our [Code of Conduct](CODE_OF_CONDUCT.md) to understand the standards of behavior we expect from all participants in our community.

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👥 Community

- Website: [laravelcommunity.ug](https://laravelcommunity.ug)
- Twitter: [@LaravelCommunityUG](https://twitter.com/LaravelCommunityUG)
- Discord: [Join our community](https://discord.gg/laravelcommunityug)
- GitHub: [github.com/laravelcommunityug](https://github.com/laravelcommunityug)

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework for Web Artisans
- [Vue.js](https://vuejs.org) - The Progressive JavaScript Framework
- [Tailwind CSS](https://tailwindcss.com) - A utility-first CSS framework
- [Spatie](https://spatie.be) - For their excellent Laravel packages
- All our [contributors](https://github.com/laravelcommunityug/community-platform/graphs/contributors)

---

Built with ❤️ by the Laravel Community Uganda
