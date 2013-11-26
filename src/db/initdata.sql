INSERT INTO `system_users` ( `userlogin`, `userpass`, `registration_date`, `active`, `avatar`) VALUES
( 'admin', 'admin', '2012-08-23', 1, '0');

INSERT INTO `system_roles` (`role_id`, `rolename`, `description`) VALUES
(1, 'editor', 'Редактор статей'),
(2, 'moderator', 'Модератор форума');


INSERT INTO `store_measures` (`measure_id`, `measure_name`) VALUES(1, 'шт.');
INSERT INTO `store_measures` (`measure_id`, `measure_name`) VALUES(2, 'кг');

