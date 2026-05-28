-- ============================================================
--  ISMO-SkillSwap - Minimal Schema v3
--  Purpose: keep the essential application workflows with the
--  smallest practical table set.
--
--  Merges from v2:
--  - user_profiles -> users
--  - mentor_validations -> user_skills
--  - help_requests + marketplace_listings -> skill_posts
--  - help_responses + mentor_relationships + mentoring_sessions
--    + marketplace_interactions -> skill_responses
--  - support_tickets + moderation_reports -> service_cases
--  - ticket_replies -> case_replies
--  - blocked_users -> users.status/suspension fields
--  - statistics/log tables -> derived views
-- ============================================================

DROP DATABASE IF EXISTS ismo_skillswap_v3;

CREATE DATABASE ismo_skillswap_v3
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE ismo_skillswap_v3;

-- ============================================================
--  1. USERS
-- ============================================================

CREATE TABLE users (
    user_id               INT PRIMARY KEY AUTO_INCREMENT,
    username              VARCHAR(50)  UNIQUE NOT NULL,
    email                 VARCHAR(100) UNIQUE NOT NULL,
    password_hash         VARCHAR(255) NOT NULL,
    first_name            VARCHAR(100),
    last_name             VARCHAR(100),
    profile_picture_url   VARCHAR(255) NOT NULL DEFAULT '/assets/images/default_avatar.svg',
    bio                   TEXT,
    phone                 VARCHAR(20),
    city                  VARCHAR(100),
    department            VARCHAR(100), --cho3ba
    availability          ENUM('full_time','part_time','weekends','flexible') DEFAULT 'flexible',
    notification_settings JSON,
    reputation_score      INT DEFAULT 0,
    skill_points          INT DEFAULT 0,
    role                  ENUM('stagiaire','mentor','formateur','admin') DEFAULT 'stagiaire',
    status                ENUM('active','suspended','deleted') DEFAULT 'active',
    suspension_reason     VARCHAR(255),
    suspended_until       DATETIME NULL,
    suspended_by_user_id  INT NULL,
    email_verified        BOOLEAN DEFAULT FALSE,
    phone_verified        BOOLEAN DEFAULT FALSE,
    two_factor_enabled    BOOLEAN DEFAULT FALSE,
    last_login            TIMESTAMP NULL,
    created_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_role (role),
    INDEX idx_status (status),
    INDEX idx_city (city),
    INDEX idx_reputation (reputation_score)
);

ALTER TABLE users
    ADD CONSTRAINT fk_users_suspended_by
    FOREIGN KEY (suspended_by_user_id) REFERENCES users(user_id) ON DELETE SET NULL;

-- ============================================================
--  2. SKILLS
-- ============================================================

CREATE TABLE skills (
    skill_id       INT PRIMARY KEY AUTO_INCREMENT,
    skill_name     VARCHAR(150) NOT NULL UNIQUE,
    skill_category VARCHAR(100),
    description    TEXT,
    icon_url       VARCHAR(255),
    is_active      BOOLEAN DEFAULT TRUE,
    usage_count    INT DEFAULT 0,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_skill_name (skill_name),
    INDEX idx_category (skill_category)
);

CREATE TABLE user_skills (
    user_skill_id        INT PRIMARY KEY AUTO_INCREMENT,
    user_id              INT NOT NULL,
    skill_id             INT NOT NULL,
    proficiency_level    ENUM('beginner','intermediate','advanced','expert') DEFAULT 'beginner',
    years_of_experience   INT DEFAULT 0,
    is_willing_to_teach   BOOLEAN DEFAULT FALSE,
    validation_status    ENUM('pending','verified','rejected') DEFAULT 'pending',
    validated_by_user_id INT NULL,
    validated_at         TIMESTAMP NULL,
    valid_until_date     DATE NULL,
    validation_notes     TEXT,
    added_at             TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(skill_id) ON DELETE RESTRICT,
    FOREIGN KEY (validated_by_user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_skill (user_id, skill_id),
    INDEX idx_skill_id (skill_id),
    INDEX idx_proficiency (proficiency_level),
    INDEX idx_validation_status (validation_status)
);

-- ============================================================
--  3. MENTOR APPLICATIONS
-- ============================================================

CREATE TABLE mentor_applications (
    application_id       INT PRIMARY KEY AUTO_INCREMENT,
    user_id              INT NOT NULL,
    status               ENUM('pending','approved','rejected','withdrawn') DEFAULT 'pending',
    application_date     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    motivation_text      TEXT,
    experience_summary   TEXT,
    specialties_proposed JSON,
    maximum_mentees      INT DEFAULT 5,
    reviewed_by_user_id  INT NULL,
    review_notes         TEXT,
    reviewed_at          TIMESTAMP NULL,

    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by_user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_user_id (user_id),
    INDEX idx_application_date (application_date)
);

-- ============================================================
--  4. SKILL POSTS
-- ============================================================

CREATE TABLE skill_posts (
    post_id              INT PRIMARY KEY AUTO_INCREMENT,
    owner_id             INT NOT NULL,
    post_type            ENUM('request','offer') NOT NULL,
    skill_id             INT NOT NULL,
    title                VARCHAR(200) NOT NULL,
    description          TEXT NOT NULL,
    urgency_level        ENUM('low','medium','high','urgent') DEFAULT 'medium',
    service_type         ENUM('mentoring','help_request','project_collaboration','course') DEFAULT 'mentoring',
    price_or_points      INT DEFAULT 0,
    is_free              BOOLEAN DEFAULT TRUE,
    status               ENUM('open','in_progress','completed','closed','archived') DEFAULT 'open',
    target_user_id       INT NULL,
    views_count          INT DEFAULT 0,
    interest_count       INT DEFAULT 0,
    deadline_date        DATETIME NULL,
    completion_date      DATETIME NULL,
    created_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (owner_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(skill_id) ON DELETE RESTRICT,
    FOREIGN KEY (target_user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_owner_id (owner_id),
    INDEX idx_skill_id (skill_id),
    INDEX idx_post_type (post_type),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_deadline_date (deadline_date)
);

CREATE TABLE skill_responses (
    response_id             INT PRIMARY KEY AUTO_INCREMENT,
    post_id                 INT NOT NULL,
    responder_id            INT NOT NULL,
    response_kind           ENUM('proposal','interest','contact','acceptance','follow_up') DEFAULT 'proposal',
    response_status         ENUM('pending','accepted','rejected','withdrawn') DEFAULT 'pending',
    response_text           TEXT,
    estimated_availability_hours INT,
    session_count           INT DEFAULT 0,
    last_session_date       DATETIME NULL,
    rating_by_owner         TINYINT NULL,
    rating_by_responder     TINYINT NULL,
    response_date           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    accepted_at             TIMESTAMP NULL,

    FOREIGN KEY (post_id) REFERENCES skill_posts(post_id) ON DELETE CASCADE,
    FOREIGN KEY (responder_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_post_id (post_id),
    INDEX idx_responder_id (responder_id),
    INDEX idx_response_status (response_status),
    INDEX idx_response_date (response_date)
);

-- ============================================================
--  5. BADGES
-- ============================================================

CREATE TABLE badges (
    badge_id                INT PRIMARY KEY AUTO_INCREMENT,
    badge_name              VARCHAR(100) NOT NULL,
    badge_description       TEXT,
    icon_url                VARCHAR(255),
    badge_category          VARCHAR(50),
    points_value            INT DEFAULT 0,
    requirement_description VARCHAR(255),
    is_active               BOOLEAN DEFAULT TRUE,
    created_at              TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_category (badge_category),
    INDEX idx_badge_name (badge_name)
);

CREATE TABLE user_badges (
    user_badge_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id       INT NOT NULL,
    badge_id      INT NOT NULL,
    earned_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    earned_reason VARCHAR(255),

    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (badge_id) REFERENCES badges(badge_id) ON DELETE RESTRICT,
    UNIQUE KEY unique_user_badge (user_id, badge_id),
    INDEX idx_earned_at (earned_at)
);

-- ============================================================
--  6. NOTIFICATIONS
-- ============================================================

CREATE TABLE notifications (
    notification_id   INT PRIMARY KEY AUTO_INCREMENT,
    user_id           INT NOT NULL,
    notification_type VARCHAR(60),
    title             VARCHAR(200),
    message           TEXT,
    reference_type    VARCHAR(50),
    reference_id      INT,
    is_read           BOOLEAN DEFAULT FALSE,
    read_at           TIMESTAMP NULL,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, is_read),
    INDEX idx_created_at (created_at)
);

-- ============================================================
--  7. CASE MANAGEMENT
-- ============================================================

CREATE TABLE service_cases (
    case_id              INT PRIMARY KEY AUTO_INCREMENT,
    case_type            ENUM('support','report') NOT NULL,
    created_by_user_id   INT NOT NULL,
    subject              VARCHAR(200) NOT NULL,
    description          TEXT NOT NULL,
    category             ENUM('account','technical','billing','report','content','other') DEFAULT 'other',
    status               ENUM('open','in_progress','waiting','resolved','closed','under_review','dismissed','escalated') DEFAULT 'open',
    priority             ENUM('low','medium','high','urgent') DEFAULT 'medium',
    severity_level       ENUM('low','medium','high','critical') DEFAULT 'medium',
    target_user_id       INT NULL,
    target_content_type  VARCHAR(50),
    target_content_id    INT NULL,
    assigned_to_user_id  INT NULL,
    reviewed_by_user_id  INT NULL,
    action_taken         VARCHAR(120),
    resolution_notes     TEXT,
    resolved_at          TIMESTAMP NULL,
    created_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (created_by_user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (target_user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_to_user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (reviewed_by_user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_case_type (case_type),
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_severity (severity_level),
    INDEX idx_created_by (created_by_user_id),
    INDEX idx_target_user (target_user_id),
    INDEX idx_created_at (created_at)
);

CREATE TABLE case_replies (
    reply_id   INT PRIMARY KEY AUTO_INCREMENT,
    case_id    INT NOT NULL,
    user_id    INT NOT NULL,
    message    TEXT NOT NULL,
    is_staff   BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (case_id) REFERENCES service_cases(case_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_case_id (case_id),
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
);

-- ============================================================
--  8. DERIVED VIEWS
-- ============================================================

CREATE VIEW vw_user_dashboard AS
SELECT
    u.user_id,
    u.username,
    u.role,
    u.status,
    u.reputation_score,
    u.skill_points,
    COUNT(DISTINCT us.user_skill_id) AS skills_count,
    COUNT(DISTINCT CASE WHEN us.validation_status = 'verified' THEN us.user_skill_id END) AS verified_skills_count,
    COUNT(DISTINCT CASE WHEN sp.post_type = 'request' THEN sp.post_id END) AS requests_count,
    COUNT(DISTINCT CASE WHEN sr.response_status = 'accepted' THEN sr.response_id END) AS accepted_responses_count,
    COUNT(DISTINCT ub.user_badge_id) AS badges_count,
    COUNT(DISTINCT CASE WHEN n.is_read = FALSE THEN n.notification_id END) AS unread_notifications_count
FROM users u
LEFT JOIN user_skills us ON us.user_id = u.user_id
LEFT JOIN skill_posts sp ON sp.owner_id = u.user_id
LEFT JOIN skill_responses sr ON sr.responder_id = u.user_id
LEFT JOIN user_badges ub ON ub.user_id = u.user_id
LEFT JOIN notifications n ON n.user_id = u.user_id
GROUP BY u.user_id, u.username, u.role, u.status, u.reputation_score, u.skill_points;

CREATE VIEW vw_platform_statistics AS
SELECT
    (SELECT COUNT(*) FROM users) AS total_users,
    (SELECT COUNT(*) FROM users WHERE status = 'active') AS active_users,
    (SELECT COUNT(*) FROM users WHERE role = 'mentor') AS total_mentors,
    (SELECT COUNT(*) FROM users WHERE role = 'formateur') AS total_formateurs,
    (SELECT COUNT(*) FROM users WHERE role = 'admin') AS total_admins,
    (SELECT COUNT(*) FROM skills) AS total_skills_registered,
    (SELECT COUNT(*) FROM skill_posts WHERE post_type = 'request') AS total_help_requests,
    (SELECT COUNT(*) FROM skill_posts WHERE post_type = 'offer') AS total_marketplace_listings,
    (SELECT COUNT(*) FROM skill_responses WHERE response_status = 'accepted') AS total_accepted_responses,
    (SELECT COUNT(*) FROM user_badges) AS total_badges_awarded,
    (SELECT ROUND(COALESCE(AVG(reputation_score), 0), 2) FROM users) AS average_user_reputation,
    (SELECT COUNT(*) FROM service_cases WHERE status IN ('open','in_progress','waiting','under_review','escalated')) AS open_cases;

CREATE VIEW vw_moderation_queue AS
SELECT
    sc.case_id,
    sc.case_type,
    sc.subject,
    sc.description,
    sc.category,
    sc.status,
    sc.priority,
    sc.severity_level,
    sc.target_user_id,
    sc.target_content_type,
    sc.target_content_id,
    sc.created_by_user_id,
    sc.assigned_to_user_id,
    sc.reviewed_by_user_id,
    sc.action_taken,
    sc.created_at,
    sc.updated_at
FROM service_cases sc
WHERE sc.case_type = 'report';

-- ============================================================
--  Verification
-- ============================================================

SHOW TABLES;
SELECT COUNT(*) AS total_tables
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'ismo_skillswap_v3';