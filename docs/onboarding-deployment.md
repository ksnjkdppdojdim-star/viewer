# Viewer onboarding and deployment policy

## Goal

Viewer should be usable by three profiles without mixing their responsibilities:

- non-technical users configure identity, colors, assets, texts and content from the UI;
- developers override views or functions without modifying original theme/plugin files;
- operators install, provision databases and deploy with clear checks.

## Installation flow

1. Check PHP extensions, writable directories and environment.
2. Provision the system database.
3. Create or update the application database user.
4. Run migrations.
5. Create the first system admin.
6. Mark the installation complete.

The application must not run with a PostgreSQL superuser. Provisioning uses `DB_ADMIN_*`; runtime uses `DB_USERNAME` and `DB_PASSWORD`.

## Theme and plugin ownership

A theme is not a plugin. A theme is a visual package made of DTV views, assets and a manifest. A plugin is a feature package. Plugins may ship their own DTV views and assets, but their role is functional.

Original theme and plugin files are treated as vendor files. They should not be edited after installation. User changes live in data/configuration, and developer changes live in `overrides/`.

## Override policy

All overrides are centralized:

```text
overrides/
  themes/<theme-name>/views/
  plugins/<plugin-name>/views/
  plugins/<plugin-name>/functions/
```

Resolution priority is:

```text
overrides -> installed theme/plugin -> core default
```

Theme updates are never forced. A theme can announce a new version, show a preview or diff, and let the user decide whether to apply it.

## Standard DTV contract

Themes and plugins should rely on stable variables exposed by Viewer, for example:

```text
site.name
site.logo
site.primary_color
site.tagline
page.title
page.content
user.name
```

These variables come from the organization/site data layer. Switching themes should not require rewriting organization identity or content.

## Deployment checks

Before deployment:

```bash
composer validate --no-check-publish
php viewer migrate
php viewer admin:create <email> <password>
php tests/manual/test_dtv.php
php tests/manual/test_events.php
php tests/manual/test_integration.php
```

The `.env` file must not be committed. Commit `.env.example` only.
