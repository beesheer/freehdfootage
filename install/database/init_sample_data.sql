-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.5.20-log - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL version:             7.0.0.4053
-- Date/time:                    2014-05-02 09:58:54
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET FOREIGN_KEY_CHECKS=0 */;
-- Dumping data for table stratus.permission: ~3 rows (approximately)
/*!40000 ALTER TABLE `permission` DISABLE KEYS */;
REPLACE INTO `permission` (`id`, `name`, `label`, `description`) VALUES
	(1, '/admin/review-portal', '/admin/review-portal', 'Review portal in admin'),
	(2, '/admin/analytics', '/admin/analytics', 'Analytics in admin'),
	(3, '/client/promote', '/client/promote', 'Promote in client'),
	(4, '/client/review-portal', '/client/review-portal', 'Review portal in client'),
    (5, '/student',  '/student',  'Student area for ce quizzes');
/*!40000 ALTER TABLE `permission` ENABLE KEYS */;

-- Dumping data for table stratus.role: ~3 rows (approximately)
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
REPLACE INTO `role` (`id`, `name`, `label`, `description`) VALUES
	(1, 'SuperAdmin', 'Super Admin', 'Super Admin'),
	(2, 'Admin', 'Admin', 'Admin'),
	(3, 'User', 'User', 'User'),
    (4, 'reviewPortalUser', 'Review Portal User', 'this user only has access to the review portal'),
    (5, 'Student',  'Student',  'Student');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;

-- Dumping data for table stratus.role_permission: ~5 rows (approximately)
/*!40000 ALTER TABLE `role_permission` DISABLE KEYS */;
REPLACE INTO `role_permission` (`id`, `role_id`, `permission_id`) VALUES
	(1, 1, 1),
	(4, 2, 2),
	(5, 2, 3),
	(7, 3, 3),
	(9, 2, 1),
	(10, 2, 4),
	(11, 3, 4),
	(12, 1, 2),
	(13, 1, 3),
	(14, 1, 4),
    (15, 4, 4),
    (16, 5, 5);
/*!40000 ALTER TABLE `role_permission` ENABLE KEYS */;

-- Dumping data for table stratus.user: ~7 rows (approximately)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
REPLACE INTO `user` (`id`, `client_id`, `surname`, `firstname`, `UDID`, `user_type`, `email`, `password`, `role_id`) VALUES
	(1, 0, 'SuperAdmin', 'User', '1234', 'Manager', 'superadmin@lifelearn.com', '81dc9bdb52d04dc20036dbd8313ed055', 1),
	(2, 1, 'Admin', 'User', '1234', 'Manager', 'admin@lifelearn.com', '81dc9bdb52d04dc20036dbd8313ed055', 2),
	(3, 1, 'Xu', 'Bin', '1234', 'Manager', 'beesheer2@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 3);

REPLACE INTO `event_repeat_unit_type` (`id`, `name`) VALUES
    (1, 'month'),
    (2, 'week'),
    (3, 'day');

INSERT INTO `page_status` (`name`) VALUES ('In progress');
INSERT INTO `page_status` (`name`) VALUES ('Ready for Review');
INSERT INTO `page_status` (`name`) VALUES ('Approved');
INSERT INTO `page_status` (`name`) VALUES ('Unapproved');

INSERT INTO `survey_type` (`id`, `description`) VALUES
(1, 'exam'),(2, 'study'),(3, 'rapidreview'),('4', 'cefeedback');
INSERT INTO `survey_response_type` (`id`, `description`) VALUES
(1, 'none'),(2, 'concurrent'),(3, 'review');
INSERT INTO `question_response_scope` (`id`,`description`) VALUES
(1, 'none'),(2, 'all'),(3, 'rightwrong'),(4, 'each option'),(5, 'custom');
INSERT INTO `survey_completion_type` (`id`, `description`) VALUES
(1, 'none'),(2, 'certificate'),(3, 'certificate_email'),(4, 'email');
-- populate stratus.question_type
INSERT INTO `question_type` (`data`, `name`, `description`, `group_id`) VALUES ('', 'truefalse', 'True False', 1), ('', 'singleselect', 'Radio', 1),('', 'multiselect', 'Checkbox', 1),('', 'input', 'Input text field', 1),('', 'slider', 'Slider input', 1),('', 'dragdrop', 'Drag and drop', 1),('Yes|No', 'objective', 'Objective with comments', '2'), ('Yes|No', 'objective_nocomment', 'Objective no comments', '2'), ('Agree|Disagree',  'ad',  'Agree > Disagree', '2'),('Strongly Agree|Agree|Neutral|Disagree|Strongly Disagree',  'sasd',  'Strongly Agree > Strongly Disagree', '2'),('Strongly Agree|Agree|Neutral|Disagree|Strongly Disagree|N/A',  'saandsdna',  'Strongly Agree > N/A', '2'),('Comment', 'comment', 'Comment', '2');
INSERT INTO `question_option_type` (`id`, `description`) VALUES
(1, 'default'),(2, 'makenumber'),(3, 'multicolumn');
-- populate stratus.question_type_group
INSERT INTO `question_type_group` (`id`, `name`, `description`) VALUES ('1', 'question_type_base', 'Standard survey question types'), ('2', 'question_type_evaluation_cefeedback', 'Questions specific to CE course evaluations');

-- populate stratus.student_workposition
INSERT INTO `student_workposition` (`id`, `title`, `tally`) VALUES
(1, 'vet', NULL),
(2, 'vettech', NULL),
(3, 'receptionist', NULL),
(4, 'other', NULL);

insert into role (name,label,description) values ('Facilitator','Facilita
tor','Facilitator') on duplicate key update id=id;

insert into permission (name,label,description) values ('/client/my-presentations','/client/my-presentations','/client/my-presentations') on duplicate key update id = id;
insert into permission (name,label,description) values ('/client/meeting','/client/meeting','/client/meeting') on duplicate key update id = id;
insert into permission (name,label,description) values ('/client/contact','/client/contact','/client/contact') on duplicate key update id = id;
insert into permission (name,label,description) values ('/client/analytics','/client/analytics','/client/analytics') on duplicate key update id = id;

set @role:= (select id from role where name = 'Facilitator');

set @meeting_id:= (select id from permission where name = '/client/meeting');

insert into role_permission (role_id,permission_id) values (@role,@meeting_id);

set @meeting_id:= null;

set @contact_id:= (select id from permission where name = '/client/contact');

insert into role_permission (role_id,permission_id) values (@role,@contact_id);

set @contact_id:= null;

set @my_presentations:= (select id from permission where name = '/client/my-presentations');

insert into role_permission (role_id,permission_id) values (@role,@my_presentations);

set @my_presentations:= null;

set @role:= null;

insert into permission (name,label,description) values ('/client/index/change-password','/client/index/change-password','/client/index/change-password') on duplicate key update id = id;

set @permission_id:= (select id from permission where name = '/client/index/change-password');

insert into role_permission (role_id,permission_id) select id,@permission_id from role;

set @permission_id:= null;

INSERT INTO `role` (`name`, `label`, `description`) VALUES ('Sofie Admin', 'Sofie Admin', 'Sofie Admin');
INSERT INTO `permission` (`name`, `label`, `description`) VALUES ('service:sofie-admin', 'service:sofie-admin', 'service:sofie-admin');
SET @role:= (select id from role where name = 'Sofie Admin');
SET @perm:= (select id from permission where name = 'service:sofie-admin');
INSERT INTO role_permission (role_id,permission_id) values (@role,@perm);


INSERT INTO `user_title_status_type` (`id`, `name`, `description`) VALUES (1, 'default', 'default');
INSERT INTO `user_title_status_type` (`id`, `name`, `description`) VALUES (2, 'started', 'started');
INSERT INTO `user_title_status_type` (`id`, `name`, `description`) VALUES (3, 'in progress', 'in progress');
INSERT INTO `user_title_status_type` (`id`, `name`, `description`) VALUES (4, 'complete', 'complete');

-- Client type
INSERT INTO `client_type` (`name`) VALUES ('CS');
INSERT INTO `client_type` (`name`) VALUES ('Client ED');


/*!40000 ALTER TABLE `user` ENABLE KEYS */;
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

insert into survey_type (description) values ('watson_survey');
insert into survey_type (description) values ('watson_feedback');
insert into question_option_type (description) values ('multinumber');

insert into permission (name,label,description) values ('/admin/survey-quiz/','/admin/survey-quiz/','Surve & Quiz Results');

insert into tag (`name`,client_id) values ('Sofie',1);

INSERT INTO `role` (`name`, `label`, `description`) VALUES ('Test Result', 'Test Result', 'Test Result');
INSERT INTO permission (name,label,description) values ('/client/result','/client/result','Student Test Results');
SET @role:= (select id from role where name = 'Test Result');
SET @perm:= (select id from permission where name = '/client/result');
INSERT INTO role_permission (role_id,permission_id) values (@role,@perm);
SET @perm:= (select id from permission where name = '/client/analytics');
INSERT INTO role_permission (role_id,permission_id) values (@role,@perm);

INSERT INTO `role` (`name`, `label`, `description`) VALUES ('Client Documents', 'Client Documents', 'Client Documents');
INSERT INTO permission (name,label,description) values ('/client/document','/client/document','Client Document');
SET @role:= (select id from role where name = 'Client Documents');
SET @perm:= (select id from permission where name = '/client/document');
INSERT INTO role_permission (role_id,permission_id) values (@role,@perm);

INSERT INTO `role` (`name`, `label`, `description`) VALUES ('Resource Library', 'Resource Library', 'Resource Library');
INSERT INTO permission (name,label,description) values ('/client/resource-library','/client/resource-library','Resource Library');
SET @role:= (select id from role where name = 'Resource Library');
SET @perm:= (select id from permission where name = '/client/resource-library');
INSERT INTO role_permission (role_id,permission_id) values (@role,@perm);

INSERT INTO `role` (`name`, `label`, `description`) VALUES ('Client Usage and Results', 'Client Usage and Results', 'Client Usage and Results');
INSERT INTO permission (name,label,description) values ('/client/allresults','/client/allresults','Client Usage and Results');
SET @role:= (select id from role where name = 'Client Usage and Results');
SET @perm:= (select id from permission where name = '/client/allresults');
INSERT INTO role_permission (role_id,permission_id) values (@role,@perm);

INSERT INTO `role` (`name`, `label`, `description`) VALUES ('Page Editing', 'Page Editing', 'Page Editing');
INSERT INTO permission (name,label,description) values ('/client/page','/client/page','Page Editing');
SET @role:= (select id from role where name = 'Page Editing');
SET @perm:= (select id from permission where name = '/client/page');
INSERT INTO role_permission (role_id,permission_id) values (@role,@perm);

INSERT INTO `role` (`name`, `label`, `description`) VALUES ('Title Editing', 'Title Editing', 'Title Editing');
INSERT INTO permission (name,label,description) values ('/client/title','/client/title','Title Editing');
SET @role:= (select id from role where name = 'Title Editing');
SET @perm:= (select id from permission where name = '/client/title');
INSERT INTO role_permission (role_id,permission_id) values (@role,@perm);

INSERT INTO `role` (`name`, `label`, `description`) VALUES ('Media Asset Editing', 'Media Asset Editing', 'Media Asset Editing');
INSERT INTO permission (name,label,description) values ('/client/media-asset','/client/media-asset','Media Asset Editing');
SET @role:= (select id from role where name = 'Media Asset Editing');
SET @perm:= (select id from permission where name = '/client/media-asset');
INSERT INTO role_permission (role_id,permission_id) values (@role,@perm);

INSERT INTO `role` (`name`, `label`, `description`) VALUES ('Title Editing Page Mgmt Only', 'Title Editing Page Mgmt Only', 'Title Editing Page Mgmt Only');
SET @role:= (select id from role where name = 'Title Editing Page Mgmt Only');
SET @perm:= (select id from permission where name = '/client/title');
INSERT INTO role_permission (role_id,permission_id) values (@role,@perm);

INSERT INTO `role` (`name`, `label`, `description`) VALUES ('Package Editing', 'Package Editing', 'Package Editing');
INSERT INTO permission (name,label,description) values ('/client/package','/client/package','Package Editing');
SET @role:= (select id from role where name = 'Package Editing');
SET @perm:= (select id from permission where name = '/client/package');
INSERT INTO role_permission (role_id,permission_id) values (@role,@perm);


insert into permission (`name`,label,description) values ('service:sofie','service:sofie','Sofie Client');

set @role:= (select id from role where name = 'Sofie');



set @sofie_id:= (select id from permission where name = 'service:sofie');



insert into role_permission (role_id,permission_id) values (@role,@sofie_id);

set @role:= null;

set @sofie_id:= null;


INSERT INTO `user_data_key` (`name`) VALUES ('Service:Sofie:AgreeEULA');


INSERT INTO `public_authentication_token`
(
  `token`
 ,`expires_on`
 ,`client_id`
 ,`description`
)
VALUES
(
 '6ydgdh73jd33hdge6dhdu' -- token - VARCHAR(96) NOT NULL
 ,NOW() -- expires_on - DATETIME NOT NULL
 ,0 -- client_id - INT(11) NOT NULL
 ,'George Client' -- description - TEXT
)