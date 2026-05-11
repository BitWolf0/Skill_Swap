-- ============================================================
--  ISMO-SKILLSWAP — Base de données MySQL 8.0+
--  Version : 1.0  |  Encodage : utf8mb4_unicode_ci
-- ============================================================

CREATE DATABASE IF NOT EXISTS ismo_skillswap
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE ismo_skillswap;

-- ============================================================
--  1. TABLES PRINCIPALES
-- ============================================================

CREATE TABLE users (
    user_id             INT PRIMARY KEY AUTO_INCREMENT,
    username            VARCHAR(50)  UNIQUE NOT NULL,
    email               VARCHAR(100) UNIQUE NOT NULL,
    password_hash       VARCHAR(255) NOT NULL,
    first_name          VARCHAR(100),
    last_name           VARCHAR(100),
    profile_picture_url VARCHAR(255) NOT NULL DEFAULT '/assets/images/default_avatar.svg',
    bio                 TEXT,
    reputation_score    INT  DEFAULT 0,
    skill_points        INT  DEFAULT 0,
    role                ENUM('stagiaire','mentor','formateur','admin') DEFAULT 'stagiaire',
    status              ENUM('active','inactive','suspended','deleted')  DEFAULT 'active',
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login          TIMESTAMP NULL,

    INDEX idx_email    (email),
    INDEX idx_username (username),
    INDEX idx_role     (role),
    INDEX idx_status   (status)
);

CREATE TABLE user_profiles (
    profile_id               INT PRIMARY KEY AUTO_INCREMENT,
    user_id                  INT NOT NULL UNIQUE,
    date_of_birth            DATE,
    phone                    VARCHAR(20),
    address                  VARCHAR(255),
    city                     VARCHAR(100),
    country                  VARCHAR(100),
    company_or_school        VARCHAR(255),
    department               VARCHAR(100),
    experience_years         INT,
    availability             ENUM('full_time','part_time','weekends','flexible') DEFAULT 'flexible',
    preferred_language       VARCHAR(10) DEFAULT 'fr',
    notification_preferences JSON,
    social_links             JSON,
    verified_email           BOOLEAN DEFAULT FALSE,
    verified_phone           BOOLEAN DEFAULT FALSE,
    two_factor_enabled       BOOLEAN DEFAULT FALSE,

    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_city    (city),
    INDEX idx_country (country)
);

CREATE TABLE skills (
    skill_id       INT PRIMARY KEY AUTO_INCREMENT,
    skill_name     VARCHAR(150) NOT NULL UNIQUE,
    skill_category VARCHAR(100),
    description    TEXT,
    icon_url       VARCHAR(255),
    is_verified    BOOLEAN   DEFAULT TRUE,
    usage_count    INT       DEFAULT 0,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_category   (skill_category),
    INDEX idx_skill_name (skill_name)
);

CREATE TABLE user_skills (
    user_skill_id       INT PRIMARY KEY AUTO_INCREMENT,
    user_id             INT NOT NULL,
    skill_id            INT NOT NULL,
    proficiency_level   ENUM('beginner','intermediate','advanced','expert') DEFAULT 'beginner',
    years_of_experience INT     DEFAULT 0,
    is_willing_to_teach BOOLEAN DEFAULT FALSE,
    endorsements_count  INT     DEFAULT 0,
    verification_status ENUM('pending','verified','rejected') DEFAULT 'pending',
    added_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)  REFERENCES users(user_id)  ON DELETE CASCADE,
    FOREIGN KEY (skill_id) REFERENCES skills(skill_id) ON DELETE RESTRICT,
    UNIQUE KEY unique_user_skill (user_id, skill_id),
    INDEX idx_proficiency      (proficiency_level),
    INDEX idx_willing_to_teach (is_willing_to_teach)
);

CREATE TABLE badges (
    badge_id                INT PRIMARY KEY AUTO_INCREMENT,
    badge_name              VARCHAR(100) NOT NULL,
    badge_description       TEXT,
    icon_url                VARCHAR(255),
    badge_category          VARCHAR(50),
    points_value            INT     DEFAULT 0,
    requirement_description VARCHAR(255),
    is_active               BOOLEAN DEFAULT TRUE,
    created_at              TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_category (badge_category)
);

CREATE TABLE user_badges (
    user_badge_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id       INT NOT NULL,
    badge_id      INT NOT NULL,
    earned_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    earned_reason VARCHAR(255),

    FOREIGN KEY (user_id)  REFERENCES users(user_id)   ON DELETE CASCADE,
    FOREIGN KEY (badge_id) REFERENCES badges(badge_id) ON DELETE RESTRICT,
    UNIQUE KEY unique_user_badge (user_id, badge_id),
    INDEX idx_earned_at (earned_at)
);

-- ============================================================
--  2. MENTORAT ET AIDE
-- ============================================================

CREATE TABLE mentor_applications (
    application_id      INT PRIMARY KEY AUTO_INCREMENT,
    user_id             INT NOT NULL,
    status              ENUM('pending','approved','rejected','withdrawn') DEFAULT 'pending',
    application_date    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    motivation_text     TEXT,
    experience_summary  TEXT,
    specialties_proposed TEXT,
    maximum_mentees     INT DEFAULT 5,
    reviewed_by_admin   INT,
    review_notes        TEXT,
    reviewed_at         TIMESTAMP NULL,

    FOREIGN KEY (user_id)           REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by_admin) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_status           (status),
    INDEX idx_user_id          (user_id),
    INDEX idx_application_date (application_date)
);

CREATE TABLE help_requests (
    request_id         INT PRIMARY KEY AUTO_INCREMENT,
    requestor_id       INT NOT NULL,
    skill_id           INT NOT NULL,
    title              VARCHAR(200) NOT NULL,
    description        TEXT NOT NULL,
    urgency_level      ENUM('low','medium','high','urgent') DEFAULT 'medium',
    status             ENUM('open','in_progress','completed','closed','archived') DEFAULT 'open',
    requested_mentor_id INT,
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deadline_date      DATETIME,
    completion_date    DATETIME NULL,

    FOREIGN KEY (requestor_id)        REFERENCES users(user_id)  ON DELETE CASCADE,
    FOREIGN KEY (skill_id)            REFERENCES skills(skill_id) ON DELETE RESTRICT,
    FOREIGN KEY (requested_mentor_id) REFERENCES users(user_id)  ON DELETE SET NULL,
    INDEX idx_status      (status),
    INDEX idx_skill_id    (skill_id),
    INDEX idx_requestor_id (requestor_id),
    INDEX idx_created_at  (created_at)
);

CREATE TABLE help_responses (
    response_id                  INT PRIMARY KEY AUTO_INCREMENT,
    request_id                   INT NOT NULL,
    responder_id                 INT NOT NULL,
    response_text                TEXT,
    estimated_availability_hours INT,
    response_date                TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    accepted_by_requestor        BOOLEAN DEFAULT NULL,
    accepted_at                  TIMESTAMP NULL,

    FOREIGN KEY (request_id)   REFERENCES help_requests(request_id) ON DELETE CASCADE,
    FOREIGN KEY (responder_id) REFERENCES users(user_id)            ON DELETE CASCADE,
    INDEX idx_request_id  (request_id),
    INDEX idx_responder_id (responder_id)
);

CREATE TABLE mentor_relationships (
    relationship_id     INT PRIMARY KEY AUTO_INCREMENT,
    mentor_id           INT NOT NULL,
    stagiaire_id        INT NOT NULL,
    skill_id            INT,
    relationship_status ENUM('active','paused','completed','ended') DEFAULT 'active',
    start_date          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_date            DATETIME NULL,
    session_count       INT      DEFAULT 0,
    last_session_date   DATETIME NULL,

    FOREIGN KEY (mentor_id)    REFERENCES users(user_id)  ON DELETE CASCADE,
    FOREIGN KEY (stagiaire_id) REFERENCES users(user_id)  ON DELETE CASCADE,
    FOREIGN KEY (skill_id)     REFERENCES skills(skill_id) ON DELETE SET NULL,
    UNIQUE KEY unique_mentor_stagiaire (mentor_id, stagiaire_id, skill_id),
    INDEX idx_status (relationship_status)
);

CREATE TABLE mentoring_sessions (
    session_id              INT PRIMARY KEY AUTO_INCREMENT,
    relationship_id         INT NOT NULL,
    mentor_id               INT NOT NULL,
    stagiaire_id            INT NOT NULL,
    session_date            DATETIME NOT NULL,
    duration_minutes        INT,
    topic                   VARCHAR(200),
    notes                   TEXT,
    feedback_from_stagiaire TEXT,
    feedback_from_mentor    TEXT,
    rating_by_stagiaire     INT,
    rating_by_mentor        INT,
    session_status          ENUM('scheduled','completed','cancelled','no_show') DEFAULT 'scheduled',

    FOREIGN KEY (relationship_id) REFERENCES mentor_relationships(relationship_id) ON DELETE CASCADE,
    FOREIGN KEY (mentor_id)       REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (stagiaire_id)    REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_session_date (session_date),
    INDEX idx_status       (session_status)
);

-- ============================================================
--  3. MARKETPLACE
-- ============================================================

CREATE TABLE marketplace_listings (
    listing_id           INT PRIMARY KEY AUTO_INCREMENT,
    seller_id            INT NOT NULL,
    skill_id             INT NOT NULL,
    title                VARCHAR(200) NOT NULL,
    description          TEXT,
    offered_service_type ENUM('mentoring','help_request','project_collaboration','course') DEFAULT 'mentoring',
    price_or_points      INT     DEFAULT 0,
    is_free              BOOLEAN DEFAULT TRUE,
    availability_status  ENUM('available','unavailable','archived') DEFAULT 'available',
    views_count          INT     DEFAULT 0,
    interest_count       INT     DEFAULT 0,
    created_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (seller_id) REFERENCES users(user_id)  ON DELETE CASCADE,
    FOREIGN KEY (skill_id)  REFERENCES skills(skill_id) ON DELETE RESTRICT,
    INDEX idx_seller_id (seller_id),
    INDEX idx_skill_id  (skill_id),
    INDEX idx_status    (availability_status),
    INDEX idx_created_at (created_at)
);

CREATE TABLE marketplace_interactions (
    interaction_id   INT PRIMARY KEY AUTO_INCREMENT,
    listing_id       INT NOT NULL,
    user_id          INT NOT NULL,
    interaction_type ENUM('viewed','liked','saved','contacted','purchased') DEFAULT 'viewed',
    interaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (listing_id) REFERENCES marketplace_listings(listing_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)    REFERENCES users(user_id)                   ON DELETE CASCADE,
    INDEX idx_listing_id (listing_id),
    INDEX idx_user_id    (user_id)
);

-- ============================================================
--  4. COMMUNICATION
-- ============================================================

CREATE TABLE notifications (
    notification_id   INT PRIMARY KEY AUTO_INCREMENT,
    user_id           INT NOT NULL,
    notification_type VARCHAR(50),
    title             VARCHAR(200),
    message           TEXT,
    reference_id      INT,
    reference_type    VARCHAR(50),
    is_read           BOOLEAN   DEFAULT FALSE,
    read_at           TIMESTAMP NULL,
    created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id    (user_id),
    INDEX idx_is_read    (is_read),
    INDEX idx_created_at (created_at)
);

CREATE TABLE messages (
    message_id   INT PRIMARY KEY AUTO_INCREMENT,
    sender_id    INT NOT NULL,
    recipient_id INT NOT NULL,
    message_text TEXT NOT NULL,
    message_type ENUM('text','image','file','system') DEFAULT 'text',
    file_url     VARCHAR(255),
    is_read      BOOLEAN   DEFAULT FALSE,
    read_at      TIMESTAMP NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (sender_id)    REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_sender_id    (sender_id),
    INDEX idx_recipient_id (recipient_id),
    INDEX idx_created_at   (created_at)
);

CREATE TABLE comments (
    comment_id         INT PRIMARY KEY AUTO_INCREMENT,
    user_id            INT NOT NULL,
    target_type        VARCHAR(50),
    target_id          INT NOT NULL,
    comment_text       TEXT NOT NULL,
    is_verified_mentor BOOLEAN   DEFAULT FALSE,
    likes_count        INT       DEFAULT 0,
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_target     (target_type, target_id),
    INDEX idx_user_id    (user_id),
    INDEX idx_created_at (created_at)
);

-- ============================================================
--  5. VALIDATION ET MODÉRATION
-- ============================================================

CREATE TABLE mentor_validations (
    validation_id         INT PRIMARY KEY AUTO_INCREMENT,
    mentor_id             INT NOT NULL,
    skill_id              INT NOT NULL,
    validator_formateur_id INT NOT NULL,
    validation_status     ENUM('pending','approved','rejected') DEFAULT 'pending',
    comments              TEXT,
    validated_at          TIMESTAMP NULL,
    valid_until_date      DATE,

    FOREIGN KEY (mentor_id)              REFERENCES users(user_id)  ON DELETE CASCADE,
    FOREIGN KEY (skill_id)              REFERENCES skills(skill_id) ON DELETE CASCADE,
    FOREIGN KEY (validator_formateur_id) REFERENCES users(user_id)  ON DELETE RESTRICT,
    UNIQUE KEY unique_mentor_skill_validation (mentor_id, skill_id),
    INDEX idx_status (validation_status)
);

CREATE TABLE moderation_reports (
    report_id              INT PRIMARY KEY AUTO_INCREMENT,
    reported_by_user_id    INT NOT NULL,
    reported_user_id       INT,
    reported_content_type  VARCHAR(50),
    reported_content_id    INT,
    report_reason          VARCHAR(255) NOT NULL,
    report_description     TEXT,
    report_status          ENUM('pending','under_review','resolved','dismissed','escalated') DEFAULT 'pending',
    severity_level         ENUM('low','medium','high','critical') DEFAULT 'medium',
    moderation_notes       TEXT,
    reviewed_by_admin_id   INT,
    action_taken           VARCHAR(100),
    reviewed_at            TIMESTAMP NULL,
    created_at             TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (reported_by_user_id)  REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (reported_user_id)     REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (reviewed_by_admin_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_status     (report_status),
    INDEX idx_severity   (severity_level),
    INDEX idx_created_at (created_at)
);

CREATE TABLE blocked_users (
    block_id            INT PRIMARY KEY AUTO_INCREMENT,
    blocked_user_id     INT NOT NULL,
    blocked_by_admin_id INT NOT NULL,
    block_reason        VARCHAR(255),
    block_type          ENUM('temporary','permanent') DEFAULT 'temporary',
    blocked_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unblock_date        DATETIME NULL,

    FOREIGN KEY (blocked_user_id)     REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (blocked_by_admin_id) REFERENCES users(user_id) ON DELETE RESTRICT,
    INDEX idx_blocked_user_id (blocked_user_id),
    INDEX idx_block_type      (block_type)
);

-- ============================================================
--  6. STATISTIQUES
-- ============================================================

CREATE TABLE user_statistics (
    stat_id                       INT PRIMARY KEY AUTO_INCREMENT,
    user_id                       INT NOT NULL UNIQUE,
    total_mentoring_hours         INT          DEFAULT 0,
    total_help_requests_created   INT          DEFAULT 0,
    total_help_requests_fulfilled INT          DEFAULT 0,
    total_mentees                 INT          DEFAULT 0,
    total_sessions_participated   INT          DEFAULT 0,
    average_rating                DECIMAL(3,2) DEFAULT 0.00,
    badges_earned_count           INT          DEFAULT 0,
    skills_verified_count         INT          DEFAULT 0,
    last_activity_date            DATETIME,
    updated_at                    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE platform_statistics (
    stat_id                      INT PRIMARY KEY AUTO_INCREMENT,
    stat_date                    DATE NOT NULL,
    total_users                  INT          DEFAULT 0,
    active_users                 INT          DEFAULT 0,
    total_mentors                INT          DEFAULT 0,
    total_formateurs             INT          DEFAULT 0,
    total_admins                 INT          DEFAULT 0,
    total_skills_registered      INT          DEFAULT 0,
    total_mentor_relationships   INT          DEFAULT 0,
    total_help_requests          INT          DEFAULT 0,
    total_completed_sessions     INT          DEFAULT 0,
    total_badges_awarded         INT          DEFAULT 0,
    marketplace_listings_count   INT          DEFAULT 0,
    average_user_reputation      DECIMAL(5,2) DEFAULT 0.00,

    UNIQUE KEY unique_date (stat_date)
);

-- ============================================================
--  7. CONFIGURATION ET LOGS
-- ============================================================

CREATE TABLE system_settings (
    setting_id          INT PRIMARY KEY AUTO_INCREMENT,
    setting_key         VARCHAR(100) UNIQUE NOT NULL,
    setting_value       TEXT,
    setting_description VARCHAR(255),
    setting_type        ENUM('string','integer','boolean','json') DEFAULT 'string',
    updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE audit_logs (
    log_id             INT PRIMARY KEY AUTO_INCREMENT,
    user_id            INT,
    action_type        VARCHAR(100) NOT NULL,
    action_description TEXT,
    affected_table     VARCHAR(50),
    affected_record_id INT,
    old_value          JSON,
    new_value          JSON,
    ip_address         VARCHAR(45),
    user_agent         TEXT,
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_user_id    (user_id),
    INDEX idx_action_type (action_type),
    INDEX idx_created_at (created_at)
);

CREATE TABLE user_activity_logs (
    activity_id          INT PRIMARY KEY AUTO_INCREMENT,
    user_id              INT NOT NULL,
    activity_type        VARCHAR(100),
    activity_description VARCHAR(255),
    page_or_feature      VARCHAR(100),
    ip_address           VARCHAR(45),
    user_agent           TEXT,
    timestamp            TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id      (user_id),
    INDEX idx_activity_type (activity_type),
    INDEX idx_timestamp    (timestamp)
);

-- ============================================================
--  8. INDEX SUPPLÉMENTAIRES
-- ============================================================

CREATE INDEX idx_users_reputation         ON users(reputation_score);
CREATE INDEX idx_users_skill_points       ON users(skill_points);
CREATE INDEX idx_help_requests_deadline   ON help_requests(deadline_date);
CREATE INDEX idx_mentor_relationships_active ON mentor_relationships(relationship_status);
CREATE INDEX idx_marketplace_views        ON marketplace_listings(views_count);
CREATE INDEX idx_comments_likes           ON comments(likes_count);

-- ============================================================
--  Vérification
-- ============================================================

SHOW TABLES;
SELECT COUNT(*) AS total_tables
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'ismo_skillswap';
