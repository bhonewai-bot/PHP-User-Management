USE user_mgmt;

-- 1) Features
INSERT INTO features (name) VALUES
('users'),
('roles')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- 2) Permissions (CRUD per feature)
-- helper: get feature ids
SET @feature_users := (SELECT id FROM features WHERE name='users' LIMIT 1);
SET @feature_roles := (SELECT id FROM features WHERE name='roles' LIMIT 1);

INSERT INTO permissions (name, feature_id) VALUES
('create', @feature_users),
('read',   @feature_users),
('update', @feature_users),
('delete', @feature_users),
('create', @feature_roles),
('read',   @feature_roles),
('update', @feature_roles),
('delete', @feature_roles)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- 3) Roles
INSERT INTO roles (name) VALUES
('admin'),
('operator'),
('cashier')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- helper: role ids
SET @role_admin    := (SELECT id FROM roles WHERE name='admin' LIMIT 1);
SET @role_operator := (SELECT id FROM roles WHERE name='operator' LIMIT 1);
SET @role_cashier  := (SELECT id FROM roles WHERE name='cashier' LIMIT 1);

-- helper: permission ids for users feature
SET @p_users_create := (SELECT p.id FROM permissions p JOIN features f ON f.id = p.feature_id WHERE f.name='users' AND p.name='create' LIMIT 1);
SET @p_users_read   := (SELECT p.id FROM permissions p JOIN features f ON f.id = p.feature_id WHERE f.name='users' AND p.name='read'   LIMIT 1);
SET @p_users_update := (SELECT p.id FROM permissions p JOIN features f ON f.id = p.feature_id WHERE f.name='users' AND p.name='update' LIMIT 1);
SET @p_users_delete := (SELECT p.id FROM permissions p JOIN features f ON f.id = p.feature_id WHERE f.name='users' AND p.name='delete' LIMIT 1);

-- helper: permission ids for roles feature
SET @p_roles_create := (SELECT p.id FROM permissions p JOIN features f ON f.id = p.feature_id WHERE f.name='roles' AND p.name='create' LIMIT 1);
SET @p_roles_read   := (SELECT p.id FROM permissions p JOIN features f ON f.id = p.feature_id WHERE f.name='roles' AND p.name='read'   LIMIT 1);
SET @p_roles_update := (SELECT p.id FROM permissions p JOIN features f ON f.id = p.feature_id WHERE f.name='roles' AND p.name='update' LIMIT 1);
SET @p_roles_delete := (SELECT p.id FROM permissions p JOIN features f ON f.id = p.feature_id WHERE f.name='roles' AND p.name='delete' LIMIT 1);

-- 4) Role permissions
-- admin: full CRUD for users + roles
INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES
(@role_admin, @p_users_create),
(@role_admin, @p_users_read),
(@role_admin, @p_users_update),
(@role_admin, @p_users_delete),
(@role_admin, @p_roles_create),
(@role_admin, @p_roles_read),
(@role_admin, @p_roles_update),
(@role_admin, @p_roles_delete);

-- operator: CRU for users + roles (no delete)
INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES
(@role_operator, @p_users_create),
(@role_operator, @p_users_read),
(@role_operator, @p_users_update),
(@role_operator, @p_roles_create),
(@role_operator, @p_roles_read),
(@role_operator, @p_roles_update);

-- cashier: users read only
INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES
(@role_cashier, @p_users_read);

-- 5) Default admin user (password is already bcrypt-hashed for admin123)
INSERT INTO admin_users (name, username, role_id, password, is_active)
SELECT 'Admin', 'admin', @role_admin, '$2y$10$3C1DKBX.4KrlZ1huWD046eSahBAL38MqFtwKpWijf7tpDhWpZVvTm', 1
WHERE NOT EXISTS (SELECT 1 FROM admin_users WHERE username='admin');
