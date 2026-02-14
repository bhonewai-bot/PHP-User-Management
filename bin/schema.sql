CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE features (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    feature_id INT NOT NULL,
    UNIQUE KEY uq_permissions (name, feature_id),
    CONSTRAINT fk_permissions_feature
        FOREIGN KEY (feature_id) REFERENCES features(id)
);

CREATE TABLE role_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    UNIQUE KEY uq_role_perm (role_id, permission_id),
    CONSTRAINT fk_rp_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    CONSTRAINT fk_rp_perm FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    role_id INT NOT NULL,
    phone VARCHAR(50) NULL,
    email VARCHAR(100) NULL UNIQUE,
    address VARCHAR(255) NULL,
    password VARCHAR(255) NOT NULL,
    gender BOOLEAN NULL,
    is_active BOOLEAN NOT NULL DEFAULT 1,
    CONSTRAINT fk_admin_user_role FOREIGN KEY (role_id) REFERENCES roles(id)
);