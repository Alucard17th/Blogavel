# Blogavel Package Documentation

## Overview

Blogavel is a Laravel package intended to provide a reusable blogging/CMS module that can be installed in other Laravel projects.

This repository currently includes the package under:

`packages/blogavel/blogavel`

## Installing (in another Laravel project)

- Add the package to your project (via VCS, path repository, or Packagist once published).
- Ensure Composer autoload is updated:

```bash
composer install
composer dump-autoload
```

Laravel package auto-discovery will register the service provider automatically.

## Configuration

The package ships with a config file `blogavel.php`.

- Publish config:

```bash
php artisan vendor:publish --tag=blogavel-config
```

### Config keys

- `route_prefix` (default: `blogavel`)
- `public_posts_prefix` (default: `posts`)
- `admin_prefix` (default: `admin`)
- `admin_middleware` (default: `['web', 'auth']`)

## Routes

### Public

- `GET /{route_prefix}`
- `GET /{route_prefix}/{public_posts_prefix}`
- `GET /{route_prefix}/{public_posts_prefix}/{postSlug}`

### Admin

Admin routes are protected by `admin_middleware`.

- `GET /{route_prefix}/{admin_prefix}/posts`
- `GET /{route_prefix}/{admin_prefix}/posts/create`
- `POST /{route_prefix}/{admin_prefix}/posts`
- `GET /{route_prefix}/{admin_prefix}/posts/{id}/edit`
- `PUT /{route_prefix}/{admin_prefix}/posts/{id}`
- `DELETE /{route_prefix}/{admin_prefix}/posts/{id}`

## Views

The package loads views under the namespace `blogavel::`.

- Publish views:

```bash
php artisan vendor:publish --tag=blogavel-views
```

## Migrations

The package loads migrations automatically.

Current tables:

- `blogavel_posts`
- `blogavel_categories`
- `blogavel_tags`
- `blogavel_post_tag`
- `blogavel_media`
- `blogavel_comments`

## Demo content

The package provides a demo command:

```bash
php artisan blogavel:demo --reset
```

This creates 3 posts:

- draft
- scheduled
- published

It also creates demo taxonomy data:

- Categories: `Tech` (parent) -> `Laravel` (child)
- Tags: `PHP`, `Laravel`

## Media

### Config

Media uploads are stored using Laravel Filesystems.

- `media_disk` (default: `public`)
- `media_directory` (default: `blogavel`)

### Admin routes

- `GET /{route_prefix}/{admin_prefix}/media`
- `POST /{route_prefix}/{admin_prefix}/media` (upload)
- `DELETE /{route_prefix}/{admin_prefix}/media/{id}`

### Featured image

Posts support a featured image via `featured_media_id`.

In the Posts admin create/edit forms you can upload a featured image.

## Comments

### Public route

- `POST /{route_prefix}/{public_posts_prefix}/{postSlug}/comments`

New comments are created with status `pending`.

### Admin routes

- `GET /{route_prefix}/{admin_prefix}/comments`
- `POST /{route_prefix}/{admin_prefix}/comments/{id}/approve`
- `POST /{route_prefix}/{admin_prefix}/comments/{id}/spam`
- `DELETE /{route_prefix}/{admin_prefix}/comments/{id}`

## API (v1)

Blogavel provides a JSON API for headless usage.

### Base URL

All endpoints are prefixed by:

`/api/{route_prefix}/v1`

With default config, that is:

`/api/blogavel/v1`

### Authentication

Admin endpoints are protected. You can choose the auth mechanism via config.

- `sanctum` (default): Bearer token auth using Laravel Sanctum.
- `api_key`: simple header-based API key.

Configure:

- `BLOGAVEL_API_ADMIN_AUTH=sanctum` or `BLOGAVEL_API_ADMIN_AUTH=api_key`

### Sanctum setup (required for admin endpoints)

In the host Laravel app:

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider" --tag=sanctum-config
php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider" --tag=sanctum-migrations
php artisan migrate
```

Add `Laravel\Sanctum\HasApiTokens` to your `App\Models\User` model:

```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
}
```

### API key setup (alternative admin auth)

Set:

```bash
BLOGAVEL_API_ADMIN_AUTH=api_key
BLOGAVEL_API_KEY_HEADER=X-API-KEY
BLOGAVEL_API_KEYS=your-secret-key-here,another-key
```

#### Auth endpoints:

- `POST /auth/login`
- `GET /auth/me` (requires token)
- `POST /auth/logout` (requires token)

### Endpoints

- `GET /posts` (paginated)
- `GET /posts/{postSlug}`
- `GET /categories`
- `GET /categories/{categorySlug}`
- `GET /tags`
- `GET /tags/{tagSlug}`
- `GET /posts/{postSlug}/comments` (approved, threaded)
- `POST /posts/{postSlug}/comments` (creates `pending`)
- `GET /admin/comments?status=pending|approved|spam`
- `POST /admin/comments/{id}/approve`
- `POST /admin/comments/{id}/spam`
- `DELETE /admin/comments/{id}`
- `POST /admin/posts`
- `PUT /admin/posts/{id}`
- `DELETE /admin/posts/{id}`
- `POST /admin/categories`
- `PUT /admin/categories/{id}`
- `DELETE /admin/categories/{id}`
- `POST /admin/tags`
- `PUT /admin/tags/{id}`
- `DELETE /admin/tags/{id}`
- `POST /admin/media` (multipart, field name `file`)
- `DELETE /admin/media/{id}`

Example (login to get a token):

```bash
curl -X POST \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password","device_name":"my-frontend"}' \
  http://localhost:8000/api/blogavel/v1/auth/login
```

Example (approve a comment):

```bash
curl -X POST \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  http://localhost:8000/api/blogavel/v1/admin/comments/1/approve
```

Example (create a comment):

```bash
curl -X POST \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"content":"Hello!","guest_name":"Jane"}' \
  http://localhost:8000/api/blogavel/v1/posts/published-post/comments
```

## Changelog

### 2026-01-17

- Package scaffold created (service provider, routes, views, config publish).
- Posts vertical slice added (model, migrations, public views, admin CRUD).
- Demo command `blogavel:demo` added.

### 2026-01-17 (Categories & Tags)

- Hierarchical categories added (parent/child).
- Tags added with pivot table and post assignment.
- Admin CRUD added for categories and tags.

### 2026-01-17 (Media)

- Media library added (upload/list/delete).
- Posts now support `featured_media_id`.

### 2026-01-17 (Comments)

- Nested comments added (parent/child).
- Public comment submission added (creates `pending` comments).
- Admin moderation UI added (approve/spam/delete).

### 2026-01-17 (API)

- Added `/api/{route_prefix}/v1` read endpoints for posts/categories/tags.
- Added comments list + create endpoints.
