

#
# Structure for the `erp_account_entry` table :
#

CREATE TABLE erp_account_entry (
  entry_id int(11) NOT NULL AUTO_INCREMENT,
  acc_d int(11) NOT NULL,
  acc_c int(11) NOT NULL,
  amount int(11) NOT NULL,
  document_id int(11) NOT NULL,
  comment varchar(255) NOT NULL,
  PRIMARY KEY (entry_id),
  KEY document_id (document_id)
) ENGINE=MyISAM AUTO_INCREMENT=62 DEFAULT CHARSET=utf8 COMMENT='Бухгалтерские  проводки';

#
# Structure for the `erp_account_plan` table :
#

CREATE TABLE erp_account_plan (
  acc_id int(11) NOT NULL AUTO_INCREMENT,
  acc_code varchar(16) NOT NULL,
  acc_name varchar(255) NOT NULL,
  acc_pid int(11) NOT NULL DEFAULT '0',
  acc_type varchar(16) DEFAULT NULL COMMENT 'тип  счета - активный, пассивный  и т.д.',
  hasqty tinyint(1) DEFAULT '0' COMMENT 'Есть  ли количественный учет',
  PRIMARY KEY (acc_id)
) ENGINE=MyISAM AUTO_INCREMENT=10010 DEFAULT CHARSET=utf8 COMMENT='План счетов';

#
# Structure for the `erp_bank` table :
#

CREATE TABLE erp_bank (
  bank_id int(11) NOT NULL AUTO_INCREMENT,
  bank_name varchar(255) NOT NULL,
  detail text NOT NULL,
  PRIMARY KEY (bank_id)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Справочник  банков';

#
# Structure for the `erp_contact` table :
#

CREATE TABLE erp_contact (
  contact_id int(11) NOT NULL AUTO_INCREMENT,
  firstname varchar(64) NOT NULL,
  middlename varchar(64) DEFAULT NULL,
  lastname varchar(64) NOT NULL,
  email varchar(64) DEFAULT NULL,
  detail text NOT NULL,
  notes varchar(255) DEFAULT NULL,
  PRIMARY KEY (contact_id)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

#
# Structure for the `erp_customer` table :
#

CREATE TABLE erp_customer (
  customer_id int(11) NOT NULL AUTO_INCREMENT,
  customer_name varchar(255) DEFAULT NULL,
  detail text NOT NULL,
  PRIMARY KEY (customer_id)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='Справочник контрагентов';

#
# Structure for the `erp_customer_activity` table :
#

CREATE TABLE erp_customer_activity (
  activity_id int(11) NOT NULL AUTO_INCREMENT,
  customer_id int(11) NOT NULL,
  document_id int(11) NOT NULL,
  amount int(11) NOT NULL,
  PRIMARY KEY (activity_id),
  KEY customer_id (customer_id,document_id)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='движение средств  по  сонтрагентам';

#
# Structure for the `erp_docrel` table :
#

CREATE TABLE erp_docrel (
  doc1 int(11) DEFAULT NULL,
  doc2 int(11) DEFAULT NULL,
  KEY doc1 (doc1),
  KEY doc2 (doc2)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Связь между  документами';

#
# Structure for the `erp_document` table :
#

CREATE TABLE erp_document (
  document_id int(11) NOT NULL AUTO_INCREMENT,
  document_number varchar(45) NOT NULL,
  document_date date NOT NULL,
  created datetime NOT NULL,
  updated datetime NOT NULL,
  user_id int(11) NOT NULL,
  notes text,
  content text,
  amount int(11) DEFAULT NULL,
  type_id int(11) NOT NULL,
  state tinyint(4) NOT NULL,
  tag int(11) DEFAULT NULL,
  PRIMARY KEY (document_id)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COMMENT='Документы';

#
# Structure for the `erp_document_update_log` table :
#

CREATE TABLE erp_document_update_log (
  document_update_log_id int(11) NOT NULL AUTO_INCREMENT,
  hostname varchar(128) DEFAULT NULL,
  document_id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  document_state tinyint(4) NOT NULL,
  updatedon datetime NOT NULL,
  PRIMARY KEY (document_update_log_id),
  KEY document_id (document_id),
  KEY user_id (user_id)
) ENGINE=MyISAM AUTO_INCREMENT=148 DEFAULT CHARSET=utf8 COMMENT='Лог  изменения   статуса  документа';

#
# Structure for the `erp_files` table :
#

CREATE TABLE erp_files (
  file_id int(11) NOT NULL AUTO_INCREMENT,
  item_id int(11) DEFAULT NULL,
  filename varchar(255) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  item_type int(11) NOT NULL COMMENT 'тип  сущности  к   которой  прикреплен  файл',
  PRIMARY KEY (file_id)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='Файлы,  прикрепленные  к  документам';

#
# Structure for the `erp_filesdata` table :
#

CREATE TABLE erp_filesdata (
  file_id int(11) DEFAULT NULL,
  filedata longblob,
  UNIQUE KEY file_id (file_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Содержимое  прикрепленных  файлов';

#
# Structure for the `erp_item` table :
#

CREATE TABLE erp_item (
  item_id int(11) NOT NULL AUTO_INCREMENT,
  itemname varchar(64) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  measure_id varchar(32) DEFAULT NULL,
  item_type tinyint(4) DEFAULT NULL,
  group_id int(11) DEFAULT NULL,
  PRIMARY KEY (item_id)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='ТМЦ';

#
# Structure for the `erp_item_group` table :
#

CREATE TABLE erp_item_group (
  group_id int(11) NOT NULL AUTO_INCREMENT,
  group_name varchar(255) NOT NULL,
  PRIMARY KEY (group_id)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='Группы  товаров';

#
# Structure for the `erp_item_measures` table :
#

CREATE TABLE erp_item_measures (
  measure_id int(11) NOT NULL AUTO_INCREMENT,
  measure_name varchar(64) NOT NULL,
  PRIMARY KEY (measure_id)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Единицы  измерения';

#
# Structure for the `erp_message` table :
#

CREATE TABLE erp_message (
  message_id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) DEFAULT NULL,
  created datetime DEFAULT NULL,
  message text,
  item_id int(11) NOT NULL COMMENT 'тип  сущности  к   которой  коментарии',
  item_type int(11) DEFAULT NULL,
  PRIMARY KEY (message_id)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COMMENT='Комментарии  к  документам';

#
# Structure for the `erp_metadata` table :
#

CREATE TABLE erp_metadata (
  meta_id int(11) NOT NULL AUTO_INCREMENT,
  meta_type tinyint(11) NOT NULL COMMENT 'тип  метаданных. Документ,  справочник  и т.д.',
  description varchar(255) DEFAULT NULL,
  meta_name varchar(255) NOT NULL COMMENT 'Наименование объекта совпадающее   с  именем  класса  страницы,  сущности  и.т.д',
  menugroup varchar(255) DEFAULT NULL COMMENT 'Группировка  для   подменю',
  notes text NOT NULL,
  PRIMARY KEY (meta_id)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COMMENT='Объекты  метаданных';

#
# Structure for the `erp_metadata_access` table :
#

CREATE TABLE erp_metadata_access (
  metadata_access_id int(11) NOT NULL AUTO_INCREMENT,
  metadata_id int(11) NOT NULL,
  role_id int(11) NOT NULL,
  viewacc tinyint(1) NOT NULL DEFAULT '0',
  editacc tinyint(1) NOT NULL DEFAULT '0',
  deleteacc tinyint(1) NOT NULL DEFAULT '0',
  execacc tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (metadata_access_id)
) ENGINE=MyISAM AUTO_INCREMENT=101 DEFAULT CHARSET=utf8 COMMENT='Права  доступа   к   объектам  метаданных';

#
# Structure for the `erp_moneyfunds` table :
#

CREATE TABLE erp_moneyfunds (
  id int(11) NOT NULL AUTO_INCREMENT,
  title varchar(64) NOT NULL,
  bank int(11) NOT NULL,
  bankaccount varchar(32) NOT NULL,
  ftype smallint(6) NOT NULL COMMENT '0 касса,  1 - основной  счет, 2 -  дополнительный  счет',

  PRIMARY KEY (id)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Денежные  счета фирмы.  Банк,  касса';

#
# Structure for the `erp_moneyfunds_activity` table :
#

CREATE TABLE erp_moneyfunds_activity (
  activity_id int(11) NOT NULL AUTO_INCREMENT,
  id_moneyfund int(11) NOT NULL,
  document_id int(11) NOT NULL,
  amount int(11) NOT NULL,
  PRIMARY KEY (activity_id),
  KEY document_id (document_id)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COMMENT='Движение  по  денежным  счетам';

#
# Structure for the `erp_staff_department` table :
#

CREATE TABLE erp_staff_department (
  department_id int(11) NOT NULL AUTO_INCREMENT,
  department_name varchar(100) NOT NULL,
  PRIMARY KEY (department_id)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Справочник  отделов';

#
# Structure for the `erp_staff_employee` table :
#

CREATE TABLE erp_staff_employee (
  employee_id int(11) NOT NULL AUTO_INCREMENT,
  position_id int(11) NOT NULL,
  department_id int(11) NOT NULL,
  salary_type int(11) NOT NULL,
  salary int(11) NOT NULL,
  hireday date DEFAULT NULL,
  fireday date DEFAULT NULL,
  login varchar(64) DEFAULT NULL,
  lastname varchar(64) NOT NULL,
  firstname varchar(64) NOT NULL,
  middlename varchar(64) NOT NULL,
  PRIMARY KEY (employee_id)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='Сотрудник';

#
# Structure for the `erp_staff_employee_activity` table :
#

CREATE TABLE erp_staff_employee_activity (
  account_id int(11) NOT NULL AUTO_INCREMENT,
  employee_id int(11) NOT NULL,
  document_id int(11) NOT NULL,
  tax_type tinyint(4) NOT NULL COMMENT 'начисление/удержание',
  amount int(11) NOT NULL,
  PRIMARY KEY (account_id)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

#
# Structure for the `erp_staff_position` table :
#

CREATE TABLE erp_staff_position (
  position_id int(11) NOT NULL AUTO_INCREMENT,
  position_name varchar(100) NOT NULL,
  PRIMARY KEY (position_id)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Справочник должностей';

#
# Structure for the `erp_stock_activity` table :
#

CREATE TABLE erp_stock_activity (
  stock_activity_id int(11) NOT NULL AUTO_INCREMENT,
  stock_id int(11) NOT NULL,
  document_id int(11) NOT NULL,
  qty int(11) NOT NULL,
  PRIMARY KEY (stock_activity_id),
  KEY document_id (document_id)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 COMMENT='Движение  по  складам';

#
# Structure for the `erp_store` table :
#

CREATE TABLE erp_store (
  store_id int(11) NOT NULL AUTO_INCREMENT,
  storename varchar(64) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  store_type tinyint(4) DEFAULT NULL,
  PRIMARY KEY (store_id)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='Места хранения';

#
# Structure for the `erp_store_stock` table :
#

CREATE TABLE erp_store_stock (
  stock_id int(11) NOT NULL AUTO_INCREMENT,
  item_id int(11) NOT NULL,
  partion int(11) DEFAULT NULL,
  store_id int(11) NOT NULL,
  price int(11) DEFAULT NULL,
  PRIMARY KEY (stock_id)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='Товар на  складе';

#
# Structure for the `erp_store_stock_serials` table :
#

CREATE TABLE erp_store_stock_serials (
  stock_serial_id int(11) NOT NULL AUTO_INCREMENT,
  stock_id int(11) NOT NULL,
  serial_number varchar(255) NOT NULL,
  PRIMARY KEY (stock_serial_id)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='Серийные номера  товаров';

#
# Structure for the `erp_task_project` table :
#

CREATE TABLE erp_task_project (
  project_id int(11) NOT NULL AUTO_INCREMENT,
  doc_id int(11) DEFAULT NULL COMMENT 'документ  основание (договор)',
  description varchar(255) DEFAULT NULL,
  start_date date DEFAULT NULL,
  end_date date DEFAULT NULL,
  projectname varchar(255) NOT NULL,
  PRIMARY KEY (project_id)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

#
# Structure for the `erp_task_task` table :
#

CREATE TABLE erp_task_task (
  task_id int(11) NOT NULL AUTO_INCREMENT,
  project_id int(11) DEFAULT NULL COMMENT 'проект',
  description varchar(255) DEFAULT NULL,
  start_date date DEFAULT NULL,
  end_date date DEFAULT NULL,
  hours int(11) DEFAULT NULL,
  status tinyint(4) unsigned NOT NULL COMMENT 'состояние  задачи',
  taskname varchar(255) DEFAULT NULL,
  createdby int(11) DEFAULT NULL,
  assignedto int(11) DEFAULT NULL COMMENT 'Исполнитель',
  priority tinyint(4) unsigned DEFAULT NULL COMMENT 'приоритет задачи',
  updated datetime DEFAULT NULL,
  PRIMARY KEY (task_id)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

#
# Structure for the `erp_task_task_emp` table :
#

CREATE TABLE erp_task_task_emp (
  task_emp_id int(11) NOT NULL AUTO_INCREMENT,
  task_id int(11) NOT NULL,
  employee_id int(11) NOT NULL,
  PRIMARY KEY (task_emp_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='  исполнители  задачи';

#
# Structure for the `system_options` table :
#

CREATE TABLE system_options (
  optname varchar(64) NOT NULL,
  optvalue text NOT NULL,
  module varchar(64) NOT NULL,
  UNIQUE KEY optname (optname)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Structure for the `system_roles` table :
#

CREATE TABLE system_roles (
  role_id int(11) NOT NULL AUTO_INCREMENT,
  rolename varchar(64) NOT NULL,
  description varchar(255) NOT NULL,
  PRIMARY KEY (role_id)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

#
# Structure for the `system_user_role` table :
#

CREATE TABLE system_user_role (
  role_id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  UNIQUE KEY role_id (role_id,user_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Structure for the `system_users` table :
#

CREATE TABLE system_users (
  user_id int(11) NOT NULL AUTO_INCREMENT,
  userlogin varchar(32) NOT NULL,
  userpass varchar(255) NOT NULL,
  email varchar(255)  NULL,
  createdon date NOT NULL,
  active int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (user_id),
  UNIQUE KEY userlogin (userlogin)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;


#
# Definition for the `erp_document_view` view :
#

CREATE  VIEW erp_document_view AS
  select
    d.document_id AS document_id,
    d.document_number AS document_number,
    d.document_date AS document_date,
    d.created AS created,
    d.updated AS updated,
    d.user_id AS user_id,
    d.notes AS notes,
    d.content AS content,
    d.amount AS amount,
    d.type_id AS type_id,
    d.tag AS tag,
    u.userlogin AS userlogin,
    d.state AS state,
    erp_metadata.meta_name AS meta_name,
    erp_metadata.description AS meta_desc
  from
    ((erp_document d join system_users u on((d.user_id = u.user_id))) join erp_metadata on((erp_metadata.meta_id = d.type_id)));



#
# Definition for the `erp_account_entry_view` view :
#

CREATE  VIEW erp_account_entry_view AS
  select
    e.entry_id AS entry_id,
    e.acc_d AS acc_d,
    e.acc_c AS acc_c,
    e.amount AS amount,
    e.document_id AS document_id,
    e.comment AS comment,
    d.acc_code AS acc_d_code,
    c.acc_code AS acc_c_code,
    doc.document_number AS document_number,
    doc.meta_desc AS meta_desc,
    doc.type_id AS type_id,
    doc.document_date AS created
  from
    (((erp_account_entry e left join erp_account_plan c on((e.acc_c = c.acc_id))) left join erp_account_plan d on((e.acc_d = d.acc_id))) join erp_document_view doc on((e.document_id = doc.document_id)));

#
# Definition for the `erp_customer_view` view :
#

CREATE  VIEW erp_customer_view AS
  select
    c.customer_id AS customer_id,
    c.customer_name AS customer_name,
    c.detail AS detail,
    (
  select
    sum(a.amount) AS amount
  from
    erp_customer_activity a
  where
    (c.customer_id = a.customer_id)) AS amount
  from
    erp_customer c;


#
# Definition for the `erp_item_view` view :
#

CREATE  VIEW erp_item_view AS
  select
    t.item_id AS item_id,
    t.itemname AS itemname,
    t.description AS description,
    t.measure_id AS measure_id,
    m.measure_name AS measure_name,
    t.item_type AS item_type,
    t.group_id AS group_id,
    g.group_name AS group_name
  from
    ((erp_item t join erp_item_measures m on((t.measure_id = m.measure_id))) left join erp_item_group g on((t.group_id = g.group_id)));

#
# Definition for the `erp_message_view` view :
#

CREATE  VIEW erp_message_view AS
  select
    erp_message.message_id AS message_id,
    erp_message.user_id AS user_id,
    erp_message.created AS created,
    erp_message.message AS message,
    erp_message.item_id AS item_id,
    erp_message.item_type AS item_type,
    system_users.userlogin AS userlogin
  from
    (erp_message join system_users on((erp_message.user_id = system_users.user_id)));

#
# Definition for the `erp_staff_employee_view` view :
#

CREATE  VIEW erp_staff_employee_view AS
  select
    e.employee_id AS employee_id,
    e.position_id AS position_id,
    e.department_id AS department_id,
    e.salary_type AS salary_type,
    e.salary AS salary,
    e.hireday AS hireday,
    e.fireday AS fireday,
    e.login AS login,
    e.firstname AS firstname,
    e.lastname AS lastname,
    e.middlename AS middlename,
    d.department_name AS department_name,
    p.position_name AS position_name,
    concat_ws(' ',
    e.lastname,
    e.firstname,
    e.middlename) AS fullname
  from
    ((erp_staff_employee e left join erp_staff_position p on((e.position_id = p.position_id))) left join erp_staff_department d on((e.department_id = d.department_id)));

#
# Definition for the `erp_stock_activity_view` view :
#

CREATE  VIEW erp_stock_activity_view AS
  select
    erp_stock_activity.stock_activity_id AS stock_activity_id,
    erp_stock_activity.stock_id AS stock_id,
    erp_stock_activity.qty AS quantity,
    erp_document.document_date AS updated,
    erp_document.document_id AS document_id,
    erp_store_stock.store_id AS store_id,
    erp_store_stock.item_id AS item_id,
    erp_store_stock.partion AS partion,
    erp_document.document_number AS document_number
  from
    ((erp_stock_activity join erp_store_stock on((erp_stock_activity.stock_id = erp_store_stock.stock_id))) join erp_document on((erp_stock_activity.document_id = erp_document.document_id)));

#
# Definition for the `erp_stock_view` view :
#

CREATE  VIEW erp_stock_view AS
  select
    erp_store_stock.stock_id AS stock_id,
    erp_store_stock.item_id AS item_id,
    erp_item_view.itemname AS itemname,
    erp_store.storename AS storename,
    erp_store.store_id AS store_id,
    erp_item_view.measure_name AS measure_name,
    erp_store_stock.price AS price,
    erp_store_stock.partion AS partion,
    coalesce((
  select
    sum(erp_stock_activity.qty) AS qty
  from
    erp_stock_activity
  where
    (erp_stock_activity.stock_id = erp_store_stock.stock_id)),0) AS quantity
  from
    ((erp_store_stock join erp_item_view on((erp_store_stock.item_id = erp_item_view.item_id))) join erp_store on((erp_store_stock.store_id = erp_store.store_id)));

#
# Definition for the `erp_task_project_view` view :
#

CREATE  VIEW erp_task_project_view AS
  select
    erp_task_project.project_id AS project_id,
    erp_task_project.doc_id AS doc_id,
    erp_task_project.description AS description,
    erp_task_project.start_date AS start_date,
    erp_task_project.end_date AS end_date,
    erp_task_project.projectname AS projectname,
    1 AS taskall,
    0 AS taskclosed
  from
    erp_task_project;

#
# Definition for the `erp_task_task_view` view :
#

CREATE  VIEW erp_task_task_view AS
  select
    t.task_id AS task_id,
    t.project_id AS project_id,
    t.description AS description,
    t.start_date AS start_date,
    t.end_date AS end_date,
    t.hours AS hours,
    t.status AS status,
    t.taskname AS taskname,
    t.createdby AS createdby,
    t.assignedto AS assignedto,
    t.priority AS priority,
    t.updated AS updated,
    u.userlogin AS creatwedbyname,
    a.userlogin AS assignedtoname,
    p.projectname AS projectname
  from
    (((erp_task_task t join erp_task_project p on((t.project_id = p.project_id))) join system_users u on((t.createdby = u.user_id))) left join system_users a on((t.assignedto = a.user_id)));

