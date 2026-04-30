# Viewer database architecture

## System database

The system database stores platform-level records:

- users;
- organizations;
- organization memberships;
- registered client databases;
- sites;
- installed themes per site;
- installed plugins per site;
- platform settings.

It is currently PostgreSQL in development. The architecture still keeps driver boundaries so another SQL driver can be supported later.

## Client databases

An organization can own one or more client databases. A site can use one database, and several sites may share the same database when the organization wants to reuse content.

This supports both models:

- one database per site for strict separation;
- one shared database for several sites when content should be reused.

## Content model direction

Viewer should avoid creating arbitrary physical tables for every content type by default. The preferred model is:

- predefined tables for common needs such as pages, articles, services, products and media;
- flexible content types backed by PostgreSQL JSONB for custom fields;
- optional plugin-managed tables when a feature genuinely needs its own schema.

This keeps user-created content flexible while avoiding uncontrolled schema changes during normal editing.
