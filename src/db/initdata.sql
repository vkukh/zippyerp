

INSERT INTO `system_users` ( `userlogin`, `userpass`, `createdon`, `active`) VALUES
( 'admin', 'admin', '2012-08-23', 1);

INSERT INTO `system_roles` (`role_id`, `rolename`, `description`) VALUES
(1, 'accounter', 'Бухгалтер');

INSERT INTO `system_user_role` (`role_id`, `user_id`) VALUES
(1, 2);

INSERT INTO erp_item_measures(measure_id, measure_name, measure_code) VALUES(1, 'шт.', '2009');
INSERT INTO erp_item_measures(measure_id, measure_name, measure_code) VALUES(2, 'кг', '0301');

-- Денежные  счета
INSERT INTO `erp_bank` (`bank_id`, `bank_name`, `detail`) VALUES (1, 'Приват Банк', '<detail><mfo>305299</mfo></detail>');

INSERT INTO erp_moneyfunds(id, title, bank, bankaccount, ftype) VALUES(3, 'Основная  касса', 0, '', 0);
INSERT INTO erp_moneyfunds(id, title, bank, bankaccount, ftype) VALUES(4, 'Основной счет', 1, '2600222222222222', 1);

-- настройки
INSERT INTO `system_options` (`optname`, `optvalue`) VALUES('common', 's:84:"a:4:{s:10:"closeddate";b:0;s:3:"nds";s:2:"20";s:6:"hasnds";b:0;s:9:"simpletax";b:0;}";');
INSERT INTO `system_options` (`optname`, `optvalue`) VALUES('firmdetail', 's:263:"a:9:{s:4:"name";s:19:"Наша фирма";s:4:"code";s:11:"11111111111";s:3:"inn";s:11:"22222222222";s:4:"city";s:17:"Наш город";s:6:"street";s:20:"Наша  улица";s:7:"manager";s:0:"";s:9:"accounter";s:0:"";s:6:"рhone";s:0:"";s:5:"email";s:0:"";}";');


-- план счетов
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(22, 'МШП', 0);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(28, 'Товари', 0);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(281, 'Товари на складі', 28);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(282, 'Товари в торгівлі', 28);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(30, 'Каса', 0);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(63, 'Розрахунки з постачальниками', 0);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(36, 'Розрахунки з покупцями ', 0);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(31, 'Рахунки в банках', 0);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(20, 'Виробничі запаси', 0);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(201, 'Сировина й матеріали', 20);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(64, 'Розрахунки за податками й платежами', 0);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(641, 'Розрахунки за податками', 64);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(642, 'Розрахунки за платежами', 64);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(643, 'Податкові зобов’язання', 64);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(644, 'Податковий кредит', 64);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(285, 'Торгова націнка', 28);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(91, 'Загальновиробничі витрати', 0);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(26, 'Готова продукція', 0);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(701, 'Дохід від реалізації готової продукції', 70);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(702, 'Дохід від реалізації товарів', 70);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(703, 'Дохід від реалізації робіт і послуг', 70);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(92, 'Адміністративні витрати', 0);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(93, 'Витрати на збут', 0);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(94, 'Інші витрати операційної діяльності', 0);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(947, 'Нестачі і втрати від псування цінностей', 94);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(90, 'Собівартість реалізації', 0);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(901, 'Собівартість реалізованої готової продукції', 90);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(902, 'Собівартість реалізованих товарів', 90);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(903, 'Собівартість реалізованих робіт і послуг', 90);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(79, 'Фінансові результати', 0);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(37, 'Розрахунки з рiзними дебiторами', 0);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(372, 'Розрахунки з пiдзвiтними особами', 37);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(70, 'Доходи від реалізації', 0);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(66, 'Розрахунки зa виплатами працівникам', 0);
INSERT INTO erp_account_plan(acc_code, acc_name, acc_pid) VALUES
(661, 'Розрахунки за заробітною платою', 66);


-- метаданные

INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(1, 4, 'Места хранения', 'StoreList', 'Склад', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(3, 1, 'Приходная  накладная', 'GoodsReceipt', 'Закупки', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(4, 1, 'Расходная накладная', 'GoodsIssue', 'Продажи', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(7, 4, 'Номенклатура', 'ItemList', 'Склад', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(6, 3, 'Журнал документов', 'DocList', '', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(8, 4, 'План счетов', 'AccountList', 'Бухгалтерия', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(10, 3, 'Журнал проводок', 'Entrylist', '', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(11, 2, 'Оборотно-сальдовая ведомость', 'Obsaldo', 'Бухгалтерия', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(12, 2, 'Шахматка', 'Shahmatka', 'Бухгалтерия', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(13, 1, 'Ручная хоз. операция', 'ManualEntry', 'Прочее', '    <p> Документ предназначен  для  ввода  редкоиспользуемых операций   а  также   ввода  начальных  остатков. </p>\r\n    <p>    Проводки не  требуют  корреспонденции счетов - счета  можно  вводить  по  одному.     Счета  только  синтетические - аналитика  выполняет  свои  проводки. </p>\r\n   ', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(14, 1, 'Банковская выписка', 'BankStatement', 'Банк', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(15, 4, 'Контрагенты', 'CustomerList', '', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(16, 2, 'Движение по  складу', 'ItemActivity', 'Склад', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(17, 1, 'Перемещение товара', 'MoveItem', 'Склад', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(20, 4, 'Банки', 'BankList', '', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(21, 5, 'Расчеты с  контрагентами', 'CustPayments', '', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(22, 4, 'Сотрудники', 'EmployeeList', 'Кадры', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(23, 4, 'Отделы', 'DepartmentList', 'Кадры', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(24, 4, 'Должности', 'PositionList', 'Кадры', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(25, 4, 'Контакты (физ. лица)', 'ContactList', '', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(26, 3, 'Проекты', 'ProjectList', 'Проекты', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(28, 3, 'Заказы покупателей', 'CustomerOrderList', '', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(29, 3, 'Заказы поставщикам', 'SupplierOrderList', '', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(30, 3, 'Задачи', 'TaskList', 'Проекты', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(53, 4, 'Группа  Товара', 'GroupItemList', 'Склад', '  Группировка  для  удобства  работы  со списками  и  прайсами', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(32, 1, 'Заказ  покупателя (клиента)', 'CustomerOrder', 'Продажи', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(33, 1, 'Заказ поставщику', 'SupplierOrder', 'Закупки', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(34, 1, 'Счет входящий', 'PurchaseInvoice', 'Закупки', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(35, 1, 'Счет-фактура', 'Invoice', 'Продажи', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(36, 1, 'Договор', 'Contract', '', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(39, 1, 'Платежное  поручение ', 'TransferOrder', 'Банк', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(44, 1, 'Розничная  накладная', 'RetailIssue', 'Розница', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(41, 1, 'Приходный КО', 'CashReceiptIn', 'Касса', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(42, 1, 'Расходный КО', 'CashReceiptOut', 'Касса', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(43, 1, 'Авансовый  отчет', 'ExpenseReport', 'Касса', '', 1);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(45, 1, 'Чек ЭККА', 'RegisterReceipt', 'Розница', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(47, 1, 'Гарантийный  талон', 'Warranty', 'Розница', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(46, 1, 'Списание  торговой наценки', 'TradeMargin', 'Розница', 'Списание  проданных  товаров  в  магазине  с  суммовым  учетом', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(48, 1, 'Налоговая накладная', 'TaxInvoice', 'Продажи', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(49, 1, 'Входящяя НН', 'TaxInvoiceIncome', 'Закупки', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(50, 3, 'Реестр  НН', 'TaxInvoiceList', '', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(51, 1, 'Акт выполненных работ', 'ServiceAct', 'Продажи', 'акт о  выполнении работ/услуг нашей фирмой', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(52, 1, 'Оказанные  услуги', 'ServiceIncome', 'Закупки', 'Акт о  выполнении  услуг  сторонней  организацией', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(54, 2, 'Баланс малого предприятия', 'BalanceSimple', 'Регламентированные', '', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(55, 1, 'Финансовые  результаты', 'FinResult', 'Прочее', 'Списание  фин. результатов  на  79  счет', 0);
INSERT INTO erp_metadata(meta_id, meta_type, description, meta_name, menugroup, notes, disabled) VALUES
(56, 5, 'Расчеты  с  подотчетными  лицами', 'AccountablePayments', '', '', 1);
-- служебный  товар для  суммового  учета
INSERT INTO `erp_item` (`item_id`, `itemname`, `description`, `measure_id`, `item_type`, `group_id`, `detail`) VALUES(11, 'Суммовой учет', NULL, '1', 6, NULL, '1');

INSERT INTO erp_store(store_id, storename, description, store_type) VALUES
(12, 'Основной склад', '', 1);
