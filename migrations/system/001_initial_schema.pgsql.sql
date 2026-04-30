-- 001_initial_schema.pgsql.sql
-- Fichier de migration pour la base de données Système (PostgreSQL).

CREATE TABLE IF NOT EXISTS organizations (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    settings JSONB,
    status VARCHAR(50) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS databases (
    id SERIAL PRIMARY KEY,
    organization_id INT NOT NULL,
    driver VARCHAR(50) DEFAULT 'pgsql',
    host VARCHAR(255) NOT NULL,
    db_name VARCHAR(255) NOT NULL,
    db_user VARCHAR(255) NOT NULL,
    db_password VARCHAR(255) NOT NULL,
    status VARCHAR(50) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS sites (
    id SERIAL PRIMARY KEY,
    organization_id INT NOT NULL,
    database_id INT NOT NULL,
    domain VARCHAR(255) NOT NULL UNIQUE,
    theme_name VARCHAR(255) NOT NULL,
    status VARCHAR(50) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (database_id) REFERENCES databases(id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    global_role VARCHAR(50) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS organization_users (
    user_id INT NOT NULL,
    organization_id INT NOT NULL,
    role VARCHAR(50) DEFAULT 'admin',
    PRIMARY KEY (user_id, organization_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS site_plugins (
    id SERIAL PRIMARY KEY,
    site_id INT NOT NULL,
    plugin_name VARCHAR(255) NOT NULL,
    settings JSONB,
    status VARCHAR(50) DEFAULT 'active',
    FOREIGN KEY (site_id) REFERENCES sites(id) ON DELETE CASCADE
);
