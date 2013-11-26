--
-- Структура таблицы 'erp_account_entry'
--

CREATE TABLE IF NOT EXISTS erp_account_entry (
  entry_id int(11) NOT NULL AUTO_INCREMENT,
  acc_d int(11) NOT NULL,
  acc_c int(11) NOT NULL,
  amount int(11) NOT NULL,
  document_id int(11) NOT NULL,
  `comment` varchar(255) NOT NULL,
  PRIMARY KEY (entry_id),
  KEY document_id (document_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'erp_account_plan'
--

CREATE TABLE IF NOT EXISTS erp_account_plan (
  acc_id int(11) NOT NULL AUTO_INCREMENT,
  acc_code varchar(16) NOT NULL,
  acc_name varchar(255) NOT NULL,
  acc_pid int(11) NOT NULL DEFAULT '0',
  acc_type varchar(16) DEFAULT NULL COMMENT 'тип  счета - активный, пассивный  и т.д.',
  hasqty tinyint(1) DEFAULT '0' COMMENT 'Есть  ли количественный учет',
  PRIMARY KEY (acc_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'erp_customer'
--

CREATE TABLE IF NOT EXISTS erp_customer (
  customer_id int(11) NOT NULL AUTO_INCREMENT,
  customer_name varchar(45) DEFAULT NULL,
  address text NOT NULL,
  bank_account text,
  details text NOT NULL,
  PRIMARY KEY (customer_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'erp_docfiles'
--

CREATE TABLE IF NOT EXISTS erp_docfiles (
  docfile_id int(11) NOT NULL AUTO_INCREMENT,
  document_id int(11) DEFAULT NULL,
  added datetime DEFAULT NULL,
  filename varchar(255) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  PRIMARY KEY (docfile_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'erp_docfilesdata'
--

CREATE TABLE IF NOT EXISTS erp_docfilesdata (
  docfile_id int(11) DEFAULT NULL,
  filedata blob,
  UNIQUE KEY docfile_id (docfile_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'erp_docmessage'
--

CREATE TABLE IF NOT EXISTS erp_docmessage (
  docmessage_id int(11) NOT NULL AUTO_INCREMENT,
  document_id int(11) DEFAULT NULL,
  user_id int(11) DEFAULT NULL,
  created datetime DEFAULT NULL,
  message text,
  PRIMARY KEY (docmessage_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'erp_docrel'
--

CREATE TABLE IF NOT EXISTS erp_docrel (
  doc1 int(11) DEFAULT NULL,
  doc2 int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'erp_document'
--

CREATE TABLE IF NOT EXISTS erp_document (
  document_id int(11) NOT NULL AUTO_INCREMENT,
  document_number varchar(45) NOT NULL,
  document_date date NOT NULL,
  created datetime NOT NULL,
  user_id int(11) NOT NULL,
  notes text,
  content text,
  amount int(11) DEFAULT NULL,
  type_id int(11) NOT NULL,
  tag int(11) DEFAULT NULL,
  PRIMARY KEY (document_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'erp_document_update_log'
--

CREATE TABLE IF NOT EXISTS erp_document_update_log (
  document_update_log_id int(11) NOT NULL AUTO_INCREMENT,
  document_id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  document_state tinyint(4) NOT NULL,
  updatedon datetime NOT NULL,
  PRIMARY KEY (document_update_log_id),
  KEY document_id (document_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'erp_item'
--

CREATE TABLE IF NOT EXISTS erp_item (
  item_id int(11) NOT NULL AUTO_INCREMENT,
  itemname varchar(64) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  measure_id varchar(32) DEFAULT NULL,
  item_type tinyint(4) DEFAULT NULL,
  PRIMARY KEY (item_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'erp_item_measures'
--

CREATE TABLE IF NOT EXISTS erp_item_measures (
  measure_id int(11) NOT NULL AUTO_INCREMENT,
  measure_name varchar(64) NOT NULL,
  PRIMARY KEY (measure_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'erp_metadata'
--

CREATE TABLE IF NOT EXISTS erp_metadata (
  meta_id int(11) NOT NULL AUTO_INCREMENT,
  meta_type tinyint(11) NOT NULL COMMENT 'тип  метаданных. Документ,  справочник  и т.д.',
  description text,
  meta_name varchar(255) NOT NULL COMMENT 'Наименование объекта совпадающее   с  именем  класса  страницы,  сущности  и.т.д',
  menugroup varchar(255) DEFAULT NULL COMMENT 'Группировка  для   подменю',
  PRIMARY KEY (meta_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'erp_metadata_access'
--

CREATE TABLE IF NOT EXISTS erp_metadata_access (
  metadata_access_id int(11) NOT NULL AUTO_INCREMENT,
  metadata_id int(11) NOT NULL,
  role_id int(11) NOT NULL,
  viewacc tinyint(1) NOT NULL DEFAULT '0',
  editacc tinyint(1) NOT NULL DEFAULT '0',
  deleteacc tinyint(1) NOT NULL DEFAULT '0',
  execacc tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (metadata_access_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'erp_staff_department'
--

CREATE TABLE IF NOT EXISTS erp_staff_department (
  department_id int(11) NOT NULL AUTO_INCREMENT,
  department_name varchar(100) NOT NULL,
  PRIMARY KEY (department_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'erp_staff_employee'
--

CREATE TABLE IF NOT EXISTS erp_staff_employee (
  employee_id int(11) NOT NULL AUTO_INCREMENT,
  fio varchar(255) NOT NULL,
  position_id int(11) NOT NULL,
  department_id int(11) NOT NULL,
  salary_type int(11) NOT NULL,
  salary int(11) NOT NULL,
  hireday date DEFAULT NULL,
  fireday date DEFAULT NULL,
  login varchar(64) DEFAULT NULL,
  PRIMARY KEY (employee_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'erp_staff_employee_account'
--

CREATE TABLE IF NOT EXISTS erp_staff_employee_account (
  account_id int(11) NOT NULL AUTO_INCREMENT,
  employee_id int(11) NOT NULL,
  document_id int(11) NOT NULL,
  tax_type tinyint(4) NOT NULL COMMENT 'начисление/удержание',
  amount int(11) NOT NULL,
  PRIMARY KEY (account_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'erp_staff_position'
--

CREATE TABLE IF NOT EXISTS erp_staff_position (
  position_id int(11) NOT NULL AUTO_INCREMENT,
  position_name varchar(100) NOT NULL,
  PRIMARY KEY (position_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'erp_stock_activity'
--

CREATE TABLE IF NOT EXISTS erp_stock_activity (
  stock_activity_id int(11) NOT NULL AUTO_INCREMENT,
  stock_id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  document_id int(11) NOT NULL,
  qty int(11) NOT NULL,
  price int(11) NOT NULL,
  PRIMARY KEY (stock_activity_id),
  KEY document_id (document_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'erp_store'
--

CREATE TABLE IF NOT EXISTS erp_store (
  store_id int(11) NOT NULL AUTO_INCREMENT,
  storename varchar(64) DEFAULT NULL,
  description varchar(255) DEFAULT NULL,
  store_type tinyint(4) DEFAULT NULL,
  PRIMARY KEY (store_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'erp_store_stock'
--

CREATE TABLE IF NOT EXISTS erp_store_stock (
  stock_id int(11) NOT NULL AUTO_INCREMENT,
  item_id int(11) NOT NULL,
  partion int(11) DEFAULT NULL,
  store_id int(11) NOT NULL,
  PRIMARY KEY (stock_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'erp_store_stock_serials'
--

CREATE TABLE IF NOT EXISTS erp_store_stock_serials (
  stock_serial_id int(11) NOT NULL AUTO_INCREMENT,
  stock_id int(11) NOT NULL,
  serial_number varchar(255) NOT NULL,
  PRIMARY KEY (stock_serial_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'system_options'
--

CREATE TABLE IF NOT EXISTS system_options (
  optname varchar(64) NOT NULL,
  optvalue text NOT NULL,
  module varchar(64) NOT NULL,
  UNIQUE KEY optname (optname)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'system_roles'
--

CREATE TABLE IF NOT EXISTS system_roles (
  role_id int(11) NOT NULL AUTO_INCREMENT,
  rolename varchar(64) NOT NULL,
  description varchar(255) NOT NULL,
  PRIMARY KEY (role_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'system_users'
--

CREATE TABLE IF NOT EXISTS system_users (
  user_id int(11) NOT NULL AUTO_INCREMENT,
  userlogin varchar(32) NOT NULL,
  userpass varchar(255) NOT NULL,
  createdon date NOT NULL,
  active int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (user_id),
  UNIQUE KEY userlogin (userlogin)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы 'system_user_role'
--

CREATE TABLE IF NOT EXISTS system_user_role (
  role_id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  UNIQUE KEY role_id (role_id,user_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
