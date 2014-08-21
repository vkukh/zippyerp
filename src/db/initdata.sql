

INSERT INTO `system_users` ( `userlogin`, `userpass`, `createdon`, `active`) VALUES
( 'admin', 'admin', '2012-08-23', 1);

INSERT INTO `system_roles` (`role_id`, `rolename`, `description`) VALUES
(1, 'accounter', 'Бухгалтер');


INSERT INTO `erp_item_measures` (`measure_id`, `measure_name`) VALUES(1, 'шт.');
INSERT INTO `erp_item_measures` (`measure_id`, `measure_name`) VALUES(2, 'кг');

-- Денежные  счета
INSERT INTO `erp_moneyfunds` ( `title`, `bank`, `bankaccount`,ftype) VALUES( 'Основная  касса', 0, '',0);
INSERT INTO `erp_moneyfunds` ( `title`, `bank`, `bankaccount`,ftype) VALUES( 'Основной счет', 0, '',1);



-- план счетов
INSERT INTO `erp_account_plan` (`acc_id`, `acc_code`, `acc_name`, `acc_pid`, `acc_type`, `hasqty`) VALUES(10001, '22', 'Малоценка', 0, NULL, 0);
INSERT INTO `erp_account_plan` (`acc_id`, `acc_code`, `acc_name`, `acc_pid`, `acc_type`, `hasqty`) VALUES(10002, '28', 'Товары', 0, NULL, 0);
INSERT INTO `erp_account_plan` (`acc_id`, `acc_code`, `acc_name`, `acc_pid`, `acc_type`, `hasqty`) VALUES(10003, '281', 'Товары  на  складе', 10002, NULL, 0);
INSERT INTO `erp_account_plan` (`acc_id`, `acc_code`, `acc_name`, `acc_pid`, `acc_type`, `hasqty`) VALUES(10004, '282', 'Товары в торговле', 10002, NULL, 0);
INSERT INTO `erp_account_plan` (`acc_id`, `acc_code`, `acc_name`, `acc_pid`, `acc_type`, `hasqty`) VALUES(10005, '30', 'Касса', 0, NULL, 0);
INSERT INTO `erp_account_plan` (`acc_id`, `acc_code`, `acc_name`, `acc_pid`, `acc_type`, `hasqty`) VALUES(10006, '63', 'Расчеты с поставщиками', 0, NULL, 0);
INSERT INTO `erp_account_plan` (`acc_id`, `acc_code`, `acc_name`, `acc_pid`, `acc_type`, `hasqty`) VALUES(10007, '36', 'Расчеты с покупателями', 0, NULL, 0);
INSERT INTO `erp_account_plan` (`acc_id`, `acc_code`, `acc_name`, `acc_pid`, `acc_type`, `hasqty`) VALUES(10009, '31', 'Счет в банке', 0, NULL, 0);

-- метаданные
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(1, 4, 'Места хранения', 'StoreList', 'Склад', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(3, 1, 'Приходная  накладная', 'PurchaseInvoice', 'Склад', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(4, 1, 'Расходная накладная', 'SalesInvoice', 'Склад', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(7, 4, 'ТМЦ  и услуги', 'ItemList', 'Склад', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(6, 3, 'Журнал документов', 'DocList', '', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(8, 4, 'План счетов', 'AccountList', 'Бухгалтерия', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(10, 3, 'Журнал проводок', 'Entrylist', '', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(11, 2, 'Оборотно-сальдовая ведомость', 'Obsaldo', 'Бухгалтерия', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(12, 2, 'Шахматка', 'Shahmatka', 'Бухгалтерия', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(13, 1, 'Ручная хоз. операция', 'ManualEntry', 'Бухгалтерия', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(14, 1, 'Банковская выписка', 'BankStatement', 'Банк', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(15, 4, 'Контрагенты', 'CustomerList', '', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(16, 2, 'Движение по  складу', 'ItemActivity', 'Склад', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(17, 1, 'Перемещение товара', 'MoveItem', 'Склад', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(20, 4, 'Банки', 'BankList', 'Банк', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(21, 5, 'Расчеты с  контрагентами', 'CustPayments', '', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(22, 4, 'Сотрудники', 'EmployeeList', 'Кадры', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(23, 4, 'Отделы', 'DepartmentList', 'Кадры', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(24, 4, 'Должности', 'PositionList', 'Кадры', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(25, 4, 'Контакты', 'ContactList', '', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(26, 3, 'Проекты', 'ProjectList', 'Проекты', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(28, 3, 'Заказы покупателей', 'CustomerOrderList', '', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(29, 3, 'Заказы поставщикам', 'SupplierOrderList', '', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(30, 3, 'Задачи', 'TaskList', 'Проекты', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(31, 1, 'Начальные  остатки', 'StartData', '', 'Документа  для   ввода  начальных  остатков.');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(32, 1, 'Заказ  покупателя (клиента)', 'CustomerOrder', '', '');
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`) VALUES(33, 1, 'Заказ поставщику', 'SupplierOrder', '', '');

