# Changelog

All notable changes to this package will be documented in this file.

The format is based on Keep a Changelog, and this project adheres to Semantic Versioning.

## [v0.1.4] - 2026-03-20

### Added

- Blogavel admin login page:
  - `GET /blogavel/admin/login`
  - `POST /blogavel/admin/login`
  - `POST /blogavel/admin/logout`
- Blogavel admin middleware `blogavel.admin` (default `blogavel.admin_middleware` now `['web', 'blogavel.admin']`) to redirect unauthenticated users to the Blogavel login page.
- Admin profile page to update the current user credentials:
  - `GET /blogavel/admin/profile`
  - `PUT /blogavel/admin/profile`
- `blogavel:make-admin` Artisan command to create a user and enable/configure the `manage-blog` gate using environment variables.
- Posts enhancements:
  - `author_id` support and auto-set on admin post create (web + API) when authenticated.
  - `views_count` support and auto-increment when viewing a published post (web show + public API show).

### Changed

- Admin UI refreshed with minimal CSS-only styling.
- Delete actions in the admin UI now prompt for confirmation.
- `manage_blog_gate` and `manage_blog_allow_local` can be driven by environment variables.

## [v0.1.2] - 2026-02-08

### Changed

- Package name updated to `alucard17th/blogavel-pk`.

## [v0.1.1] - 2026-01-24

### Added

- Initial Packagist-ready tags and publishing workflow.

## [v0.1.0] - 2026-01-24

### Added

- Initial release.
