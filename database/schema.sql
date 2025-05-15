-- Create categories table if not exists
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create event_types table if not exists
CREATE TABLE IF NOT EXISTS event_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create events table
CREATE TABLE events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category_id INT,
    type_id INT,
    event_date DATETIME NOT NULL,
    registration_deadline DATETIME NOT NULL,
    location TEXT,  -- Stores either physical location or JSON for virtual/hybrid
    price DECIMAL(10,2) DEFAULT 0.00,
    slots INT NOT NULL,
    max_participants_per_registration INT DEFAULT 1,
    visibility ENUM('public', 'private', 'invite-only') DEFAULT 'public',
    status ENUM('draft', 'pending', 'published', 'cancelled') DEFAULT 'draft',
    banner VARCHAR(255),  -- Stores path to banner image
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (type_id) REFERENCES event_types(id) ON DELETE SET NULL
);

-- Insert default categories if not exists
INSERT IGNORE INTO categories (name, description) VALUES
('Conference', 'Professional conferences, conventions, and large-scale business gatherings'),
('Workshop', 'Interactive learning sessions focused on skill development and hands-on practice'),
('Seminar', 'Educational presentations and lectures by industry experts'),
('Networking', 'Events focused on building professional connections and business relationships'),
('Entertainment', 'Cultural events, performances, shows, and recreational activities'),
('Sports', 'Athletic competitions, tournaments, and sporting events'),
('Other', 'Miscellaneous events that don\'t fit other categories');

-- Insert default event types if not exists
INSERT IGNORE INTO event_types (name, description) VALUES
('Physical', 'Traditional in-person events'),
('Virtual', 'Online-only events conducted virtually'),
('Hybrid', 'Events with both physical and virtual attendance options');

-- Update the status ENUM in events table
ALTER TABLE events 
MODIFY COLUMN status ENUM('draft', 'pending', 'published', 'cancelled') DEFAULT 'draft';

-- Create activity_logs table
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    actor_type ENUM('admin', 'client') NOT NULL,
    actor_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- First, make sure all categories have descriptions
UPDATE categories SET description = CASE name
    WHEN 'Conference' THEN 'Professional conferences, conventions, and large-scale business gatherings'
    WHEN 'Workshop' THEN 'Interactive learning sessions focused on skill development and hands-on practice'
    WHEN 'Seminar' THEN 'Educational presentations and lectures by industry experts'
    WHEN 'Networking' THEN 'Events focused on building professional connections and business relationships'
    WHEN 'Entertainment' THEN 'Cultural events, performances, shows, and recreational activities'
    WHEN 'Sports' THEN 'Athletic competitions, tournaments, and sporting events'
    WHEN 'Other' THEN 'Miscellaneous events that don\'t fit other categories'
    ELSE description
END
WHERE description IS NULL;

-- Make description NOT NULL to ensure all categories have descriptions
ALTER TABLE categories MODIFY COLUMN description TEXT NOT NULL;

-- Drop and recreate categories with proper descriptions
TRUNCATE TABLE categories;

-- Insert categories with descriptions
INSERT INTO categories (name, description) VALUES
('Conference', 'Professional conferences, conventions, and large-scale business gatherings'),
('Workshop', 'Interactive learning sessions focused on skill development and hands-on practice'),
('Seminar', 'Educational presentations and lectures by industry experts'),
('Networking', 'Events focused on building professional connections and business relationships'),
('Entertainment', 'Cultural events, performances, shows, and recreational activities'),
('Sports', 'Athletic competitions, tournaments, and sporting events'),
('Other', 'Miscellaneous events that don\'t fit other categories');

-- Create participants table if not exists
CREATE TABLE IF NOT EXISTS participants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
); 