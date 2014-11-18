

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
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(22, 'Малоценка', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(28, 'Товары', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(281, 'Товары  на  складе', 28);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(282, 'Товары в торговле', 28);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(30, 'Касса', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(63, 'Расчеты с поставщиками', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(36, 'Расчеты с покупателями', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(31, 'Счет в банке', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(20, 'Производственные  запасы', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(201, 'Сырье и материалы', 20);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(64, 'Расчеты  по налогам и платежам', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(641, 'Расчеты по налогам', 64);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(642, 'Расчеты по платежам', 64);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(643, 'Налоговые обязательства', 64);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(644, 'Налоговый кредит', 64);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(285, 'Торговая  наценка', 28);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(91, 'Общие  затраты', 0);

-- метаданные
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(1, 4, 'Места хранения', 'StoreList', 'Склад', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(3, 1, 'Приходная  накладная', 'GoodsReceipt', 'Закупки', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(4, 1, 'Расходная накладная', 'GoodsIssue', 'Продажи', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(7, 4, 'ТМЦ  и услуги', 'ItemList', 'Склад', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(6, 3, 'Журнал документов', 'DocList', '', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(8, 4, 'План счетов', 'AccountList', 'Бухгалтерия', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(10, 3, 'Журнал проводок', 'Entrylist', '', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(11, 2, 'Оборотно-сальдовая ведомость', 'Obsaldo', 'Бухгалтерия', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(12, 2, 'Шахматка', 'Shahmatka', 'Бухгалтерия', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(13, 1, 'Ручная хоз. операция', 'ManualEntry', 'Прочее', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(14, 1, 'Банковская выписка', 'BankStatement', 'Банк', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(15, 4, 'Контрагенты', 'CustomerList', '', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(16, 2, 'Движение по  складу', 'ItemActivity', 'Склад', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(17, 1, 'Перемещение товара', 'MoveItem', 'Склад', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(20, 4, 'Банки', 'BankList', '', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(21, 5, 'Расчеты с  контрагентами', 'CustPayments', '', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(22, 4, 'Сотрудники', 'EmployeeList', 'Кадры', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(23, 4, 'Отделы', 'DepartmentList', 'Кадры', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(24, 4, 'Должности', 'PositionList', 'Кадры', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(25, 4, 'Контакты (физ. лица)', 'ContactList', '', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(26, 3, 'Проекты', 'ProjectList', 'Проекты', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(28, 3, 'Заказы покупателей', 'CustomerOrderList', '', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(29, 3, 'Заказы поставщикам', 'SupplierOrderList', '', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(30, 3, 'Задачи', 'TaskList', 'Проекты', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(31, 1, 'Начальные  остатки', 'StartData', 'Прочее', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(32, 1, 'Заказ  покупателя (клиента)', 'CustomerOrder', 'Продажи', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(33, 1, 'Заказ поставщику', 'SupplierOrder', 'Закупки', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(34, 1, 'Счет входящий', 'PurchaseInvoice', 'Закупки', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(35, 1, 'Счет-фактура', 'Invoice', 'Продажи', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(36, 1, 'Договор', 'Contract', '', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(37, 4, 'Организации', 'Company', '', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(39, 1, 'Платежное  поручение ', 'TransferOrder', 'Банк', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(44, 1, 'Розничная  накладная', 'RetailIssue', 'Розница', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(41, 1, 'Приходный КО', 'CashReceiptIn', 'Касса', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(42, 1, 'Расходный КО', 'CashReceiptOut', 'Касса', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(43, 1, 'Авансовый  отчет', 'ExpenseReport', 'Касса', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(45, 1, 'Чек ЭККА', 'RegisterReceipt', 'Розница', '', 1);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(47, 1, 'Гарантийный  талон', 'Warranty', 'Продажи', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(46, 1, 'Списание  торговой наценки', 'IncomeRetail', 'Розница', 'Списание  проланных  товаров  в  магазине  с  суммовым  учетом', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(48, 1, 'Налоговая накладная', 'TaxInvoice', 'Продажи', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(49, 1, 'Входящяя НН', 'TaxInvoiceIncome', 'Закупки', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(50, 3, 'Реестр  НН', 'TaxInvoiceList', '', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(51, 1, 'Акт выполненных работ', 'ServiceAct', 'Продажи', 'акт о  выполнении работ/услуг нашей фирмой', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(52, 1, 'Оказанные  услуги', 'ServiceIncome', 'Закупки', 'Акт о  выполнении  услуг  сторонней  организацией', 0);

-- служебный  товар для  суммового  учета
INSERT INTO `erp_item` (`item_id`, `itemname`, `description`, `measure_id`, `item_type`, `group_id`, `detail`) VALUES(11, 'Суммовой учет', NULL, '1', 6, NULL, '1');

