-- Azure SQL Database Script for PalPost
-- Compatible with Azure SQL Database and SQL Server
-- Generated: Nov 18, 2025

-- Create database (uncomment if creating new database)
-- CREATE DATABASE PalPostDB;
-- GO

-- Use the database
-- USE PalPostDB;
-- GO

-- ===============================
-- Table structure for table [Users]
-- ===============================

CREATE TABLE [Users] (
    [userID] BIGINT IDENTITY(1,1) PRIMARY KEY,
    [userRank] INT NOT NULL,
    [userName] NVARCHAR(20) NOT NULL,
    [userEmail] NVARCHAR(100) NOT NULL,
    [userPassword] NVARCHAR(255) NOT NULL,
    [userJoingingDate] DATETIME2 NOT NULL DEFAULT GETDATE(),
    [userImagePath] NVARCHAR(255) NOT NULL DEFAULT 'uploads/profiles/default.png',
    [userImageType] NVARCHAR(50) DEFAULT 'image/png',
    [userBio] NVARCHAR(300) DEFAULT 'I haven''t updated my Bio yet!'
);

-- ===============================
-- Table structure for table [Posts]
-- ===============================

CREATE TABLE [Posts] (
    [postID] BIGINT IDENTITY(1,1) PRIMARY KEY,
    [userID] BIGINT NOT NULL,
    [TextContent] NVARCHAR(300) DEFAULT NULL,
    [CreatedAt] DATETIME2 NOT NULL DEFAULT GETDATE(),
    CONSTRAINT [FK_Posts_Users] FOREIGN KEY ([userID]) 
        REFERENCES [Users] ([userID]) ON DELETE CASCADE
);

-- ===============================
-- Table structure for table [Comments]
-- ===============================

CREATE TABLE [Comments] (
    [commentID] BIGINT IDENTITY(1,1) PRIMARY KEY,
    [userID] BIGINT NOT NULL,
    [Text] NVARCHAR(300) NOT NULL,
    [postID] BIGINT NOT NULL,
    [CreatedAt] DATETIME2 NOT NULL DEFAULT GETDATE(),
    CONSTRAINT [FK_Comments_Posts] FOREIGN KEY ([postID]) 
        REFERENCES [Posts] ([postID]) ON DELETE CASCADE
);

-- ===============================
-- Table structure for table [Likes]
-- ===============================

CREATE TABLE [Likes] (
    [likeID] BIGINT IDENTITY(1,1) PRIMARY KEY,
    [postID] BIGINT NOT NULL,
    [userID] BIGINT NOT NULL,
    CONSTRAINT [FK_Likes_Posts] FOREIGN KEY ([postID]) 
        REFERENCES [Posts] ([postID]) ON DELETE CASCADE,
    CONSTRAINT [UK_Likes_User_Post] UNIQUE ([postID], [userID])
);

-- ===============================
-- Table structure for table [Media]
-- ===============================

CREATE TABLE [Media] (
    [mediaID] BIGINT IDENTITY(1,1) PRIMARY KEY,
    [postID] BIGINT NOT NULL,
    [mediaPath] NVARCHAR(255) NOT NULL,
    [mediaType] NVARCHAR(50) NOT NULL,
    [mediaCaption] NVARCHAR(100) NOT NULL,
    CONSTRAINT [FK_Media_Posts] FOREIGN KEY ([postID]) 
        REFERENCES [Posts] ([postID]) ON DELETE CASCADE
);

-- ===============================
-- Create Indexes for Performance
-- ===============================

-- Index on Users table
CREATE INDEX [IX_Users_userRank] ON [Users] ([userRank]);
CREATE INDEX [IX_Users_userEmail] ON [Users] ([userEmail]);

-- Index on Posts table
CREATE INDEX [IX_Posts_userID] ON [Posts] ([userID]);
CREATE INDEX [IX_Posts_CreatedAt] ON [Posts] ([CreatedAt]);

-- Index on Comments table
CREATE INDEX [IX_Comments_postID] ON [Comments] ([postID]);
CREATE INDEX [IX_Comments_userID] ON [Comments] ([userID]);

-- Index on Likes table
CREATE INDEX [IX_Likes_postID] ON [Likes] ([postID]);
CREATE INDEX [IX_Likes_userID] ON [Likes] ([userID]);

-- Index on Media table
CREATE INDEX [IX_Media_postID] ON [Media] ([postID]);

-- ===============================
-- Insert Sample Data
-- ===============================

-- Insert Users (note: IDENTITY_INSERT allows manual ID specification)
SET IDENTITY_INSERT [Users] ON;

INSERT INTO [Users] ([userID], [userRank], [userName], [userEmail], [userPassword], [userJoingingDate], [userImagePath], [userImageType], [userBio]) VALUES
(9, 1, N'Thomas', N'thomas@hotel.com', N'$2y$10$9fcNTNEkG1APp/PEE5KmCOrJsh5RWyJALjQWg6okdilkHrm5J7sfy', '2025-06-12 15:09:06', N'uploads/profiles/user_9_1749740946.jpg', N'image/jpeg', N'I like all sorts of dogs, midfvdane especially'),
(10, 1, N'Matthew', N'matthew@hotel.com', N'$2y$10$jGUMIr2AoVgyzKIlR3gg7eiRh2NHF.kus1X1UbHmyCpVfe/UzepRa', '2025-06-07 13:03:42', N'uploads/profiles/user_10_1749301422.png', N'image/png', N'Hi I like pancakes'),
(23, 2, N'Robert', N'robert@hotel.com', N'$2y$10$6HZHaVx/wKQS2dLFkeNk/.22MZAO6K6UtsrVRsBHU19f.j8l0m9v.', '2025-06-07 11:36:50', N'uploads/profiles/default.png', N'image/png', N'I haven''t updated my Bio yet!'),
(27, 2, N'michael', N'michael@hotel.com', N'$2y$10$vjt4HSjLVDAWyN784AcSFONXxuhzPG6l/ygQaXAw0Qyiqw5a77tZ2', '2025-06-11 19:04:17', N'uploads/profiles/default.png', N'image/png', N'I haven''t updated my Bio yet!'),
(29, 2, N'thomthom', N'thom@thom.com', N'$2y$10$6gxti5ZCyL0CQkpZMoUUwO9k8nroRckybyVubSVX0bcbqP.vNBIb.', '2025-11-17 14:58:07', N'uploads/profiles/default.png', N'image/png', N'I haven''t updated my Bio yet!'),
(30, 1, N'thomaspenny', N'thomaspenny@admin.com', N'$2y$10$WIQQ0Fmrm.kYLaTlHh9dEejezSSrz2ey/QlcajjIGn.65KrSQ3IPm', '2025-11-17 16:01:34', N'uploads/profiles/user_30_1763395294.jpg', N'image/jpeg', N'Check out some of my portfolio work via posts I''ve made here!'),
(31, 1, N'backupadmin', N'backupadmin@admin.com', N'$2y$10$Is.rpe8kbjl4vVfN92LwFOB48Jv5czWpb2xKsOZk0qmw6.Bft9dJO', '2025-11-17 16:03:36', N'uploads/profiles/default.png', N'image/png', N'I haven''t updated my Bio yet!'),
(32, 2, N'generaluser', N'General@admin.com', N'$2y$10$IFLf7YQORikFWnpdJzCD3eKs67uCKSh5iaGxapjJ48hpo79fXl4vK', '2025-11-17 16:05:11', N'uploads/profiles/default.png', N'image/png', N'I haven''t updated my Bio yet!');

SET IDENTITY_INSERT [Users] OFF;

-- Insert Posts
SET IDENTITY_INSERT [Posts] ON;

INSERT INTO [Posts] ([postID], [userID], [TextContent], [CreatedAt]) VALUES
(50, 30, N'Here is my more professional portfolio website' + CHAR(13) + CHAR(10) + CHAR(13) + CHAR(10) + N'https://thomaspenny.github.io/Thomas-Penny-Portfolio/', '2025-11-17 16:08:34'),
(51, 30, N'Heres some pictures of an application I built in Python for an Orthodontic Researcher to aid her in rapid Lateral Cephalogram analysis, you can find out more below:' + CHAR(13) + CHAR(10) + CHAR(13) + CHAR(10) + N'https://github.com/thomaspenny/Lateral-Cephalogram-App', '2025-11-17 16:15:08'),
(52, 30, N'Some pictures of an XAI and ML Credit Card Fraud Detection Application I built, which can compare 6 ML models, with insights from LIME and SHAP built in.', '2025-11-17 16:21:15');

SET IDENTITY_INSERT [Posts] OFF;

-- Insert Likes
SET IDENTITY_INSERT [Likes] ON;

INSERT INTO [Likes] ([likeID], [postID], [userID]) VALUES
(152, 50, 30),
(153, 52, 30);

SET IDENTITY_INSERT [Likes] OFF;

-- Insert Media
SET IDENTITY_INSERT [Media] ON;

INSERT INTO [Media] ([mediaID], [postID], [mediaPath], [mediaType], [mediaCaption]) VALUES
(45, 50, N'uploads/post_content/post_50_1763395714_0.png', N'image/png', N'main section'),
(46, 50, N'uploads/post_content/post_50_1763395714_1.png', N'image/png', N'bottom section'),
(47, 51, N'uploads/post_content/post_51_1763396108_0.png', N'image/png', N'code snippet'),
(48, 51, N'uploads/post_content/post_51_1763396108_1.png', N'image/png', N'the app in use'),
(49, 51, N'uploads/post_content/post_51_1763396108_2.png', N'image/png', N'the finished visual analysis after all points are plotted (you also get a CSV file of the results fo'),
(50, 52, N'uploads/post_content/post_52_1763396475_0.png', N'image/png', N'Real time processing performance'),
(51, 52, N'uploads/post_content/post_52_1763396475_1.png', N'image/png', N'Cross Comparison'),
(52, 52, N'uploads/post_content/post_52_1763396475_2.png', N'image/png', N'SHAP Global Analysis'),
(53, 52, N'uploads/post_content/post_52_1763396475_3.png', N'image/png', N'LIME Local Analysis'),
(54, 52, N'uploads/post_content/post_52_1763396475_4.png', N'image/png', N'Feature Variance');

SET IDENTITY_INSERT [Media] OFF;

-- ===============================
-- Optional: Create Views for Common Queries
-- ===============================

-- View for posts with user info and counts
CREATE VIEW [vw_PostsWithDetails] AS
SELECT 
    p.[postID],
    p.[TextContent],
    p.[CreatedAt],
    u.[userName],
    u.[userImagePath],
    (SELECT COUNT(*) FROM [Likes] l WHERE l.[postID] = p.[postID]) AS LikeCount,
    (SELECT COUNT(*) FROM [Comments] c WHERE c.[postID] = p.[postID]) AS CommentCount
FROM [Posts] p
JOIN [Users] u ON p.[userID] = u.[userID];

-- ===============================
-- Success Message
-- ===============================

PRINT 'PalPost Azure SQL Database schema created successfully!';
PRINT 'Tables created: Users, Posts, Comments, Likes, Media';
PRINT 'Sample data inserted successfully.';
PRINT 'Views created: vw_PostsWithDetails';