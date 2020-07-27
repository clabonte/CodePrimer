USE code_primer_tests_functional_test;

-- Drops the foreign key between User and 
ALTER TABLE users DROP FOREIGN KEY fk_users_user_stats_stats_id;

-- Drops the foreign key between User and 
ALTER TABLE users DROP FOREIGN KEY fk_users_subscriptions_subscription_id;

-- Drops the foreign key between Metadata and 
ALTER TABLE metadata DROP FOREIGN KEY fk_metadata_users_user_id;

-- Drops the foreign key between Post and 
ALTER TABLE posts DROP FOREIGN KEY fk_posts_users_author_id;

-- Drops the foreign key between Post and 
ALTER TABLE posts DROP FOREIGN KEY fk_posts_topics_topic_id;

-- Drops the foreign key between Subscription and 
ALTER TABLE subscriptions DROP FOREIGN KEY fk_subscriptions_users_user_id;

-- Drops the table to linking the User and Topic entities in a many to many relation
DROP TABLE `users_topics`;

-- Drops the table holding User entities
DROP TABLE `users_logs`;
-- Drops the table holding User entities
DROP TABLE `users`;
-- Drops the table holding UserStats entities
DROP TABLE `user_stats`;
-- Drops the table holding Metadata entities
DROP TABLE `metadata`;
-- Drops the table holding Post entities
DROP TABLE `posts`;
-- Drops the table holding Topic entities
DROP TABLE `topics`;
-- Drops the table holding Subscription entities
DROP TABLE `subscriptions`;
