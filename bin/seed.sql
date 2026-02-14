USE user_mgmt;

-- 1) Features
INSERT INTO features (name) VALUES
('user'),
('roles'),
('product')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- 2) Permissions (CRUD per feature)
-- helper: get feature ids
SET @feature_user  := (SELECT id FROM features WHERE name='user' LIMIT 1);
SET @feature_roles := (SELECT id FROM features WHERE name='roles' LIMIT 1);

INSERT INTO permissions (name, feature_id) VALUES
('create', @feature_user),
('read',   @feature_user),
('update', @feature_user),
('delete', @feature_user),
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

-- helper: permission ids for user feature
SET @p_user_create := (SELECT p.id FROM permissions p JOIN features f ON f.id=p.feature_id WHERE f.name='user'  AND p.name='create' LIMIT 1);
SET @p_user_read   := (SELECT p.id FROM permissions p JOIN features f ON f.id=p.feature_id WHERE f.name='user'  AND p.name='read'   LIMIT 1);
SET @p_user_update := (SELECT p.id FROM permissions p JOIN features f ON f.id=p.feature_id WHERE f.name='user'  AND p.name='update' LIMIT 1);
SET @p_user_delete := (SELECT p.id FROM permissions p JOIN features f ON f.id=p.feature_id WHERE f.name='user'  AND p.name='delete' LIMIT 1);

-- helper: permission ids for roles feature
SET @p_roles_create := (SELECT p.id FROM permissions p JOIN features f ON f.id=p.feature_id WHERE f.name='roles' AND p.name='create' LIMIT 1);
SET @p_roles_read   := (SELECT p.id FROM permissions p JOIN features f ON f.id=p.feature_id WHERE f.name='roles' AND p.name='read'   LIMIT 1);
SET @p_roles_update := (SELECT p.id FROM permissions p JOIN features f ON f.id=p.feature_id WHERE f.name='roles' AND p.name='update' LIMIT 1);
SET @p_roles_delete := (SELECT p.id FROM permissions p JOIN features f ON f.id=p.feature_id WHERE f.name='roles' AND p.name='delete' LIMIT 1);

-- 4) Role permissions
-- admin gets all (user + roles CRUD)
INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES
(@role_admin, @p_user_create),
(@role_admin, @p_user_read),
(@role_admin, @p_user_update),
(@role_admin, @p_user_delete),
(@role_admin, @p_roles_create),
(@role_admin, @p_roles_read),
(@role_admin, @p_roles_update),
(@role_admin, @p_roles_delete);

-- operator example (adjust to match your lab table)
INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES
(@role_operator, @p_user_create),
(@role_operator, @p_user_read),
(@role_operator, @p_user_update),
(@role_operator, @p_roles_read);

-- cashier example (adjust to match your lab table)
INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES
(@role_cashier, @p_user_read);

-- 5) Default admin user
-- NOTE: password here is plain text; once we implement PHP, we must store a hashed password.
-- For now, we insert a placeholder and later we update it to a hash.
INSERT INTO admin_users (name, username, role_id, password, is_active)
SELECT 'Admin', 'admin', @role_admin, 'admin123', 1
WHERE NOT EXISTS (SELECT 1 FROM admin_users WHERE username='admin');