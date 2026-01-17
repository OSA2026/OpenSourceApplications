-- Seed Categories
INSERT INTO categories (name, slug) VALUES 
('Mobile Apps', 'mobile-apps'),
('Desktop Applications', 'desktop-apps'),
('Firmware & Drivers', 'firmware-drivers'),
('Game Development', 'games'),
('System Tools', 'system-tools'),
('Other', 'other')
ON DUPLICATE KEY UPDATE name=name;

-- Seed Platforms
INSERT INTO platforms (name) VALUES 
('GNU/Linux'),
('Android / Ubuntu Touch'),
('Windows'),
('macOS')
ON DUPLICATE KEY UPDATE name=name;
