
SET NAMES 'utf8';

INSERT   INTO `system_users` (`user_id`, `userlogin`, `userpass`, `createdon`, `active`, `email`, `username`, `acl`) VALUES(4, 'admin', 'admin', now(), 1, 'admin@admin.admin', '', '');

INSERT INTO erp_item_measures(measure_id, measure_name, measure_code) VALUES(1, 'шт.', '2009');
INSERT INTO erp_item_measures(measure_id, measure_name, measure_code) VALUES(2, 'кг', '0301');

-- Денежные  счета
INSERT INTO `erp_bank` (`bank_id`, `bank_name`, `detail`) VALUES (1, 'Приват Банк', '<detail><mfo>305299</mfo></detail>');

INSERT INTO erp_moneyfunds(id, title, bank, bankaccount, ftype) VALUES(1, 'Основна  касса', 0, '', 0);
INSERT INTO erp_moneyfunds(id, title, bank, bankaccount, ftype) VALUES(2, 'Основнbй рахунок', 1, '2600222222222222', 1);
INSERT INTO erp_moneyfunds(id, title, bank, bankaccount, ftype) VALUES(3, 'Додатковий рахунок', 1, '2600111111111', 2);

-- настройки
INSERT INTO `system_options` (`optname`, `optvalue`) VALUES('firmdetail', 'a:14:{s:4:"name";s:20:"Наша  фірма";s:6:"edrpou";s:3:"111";s:6:"koatuu";s:4:"2222";s:5:"kopfg";s:4:"3333";s:4:"kodu";s:0:"";s:4:"kved";s:5:"2.2.3";s:3:"gni";s:0:"";s:3:"inn";s:17:"23424234234234234";s:4:"city";s:0:"";s:6:"street";s:0:"";s:7:"manager";s:0:"";s:9:"accounter";s:0:"";s:5:"phone";s:0:"";s:5:"email";s:0:"";}');
INSERT INTO `system_options` (`optname`, `optvalue`) VALUES('common', 'a:6:{s:10:"closeddate";b:0;s:3:"nds";s:2:"20";s:6:"hasnds";b:1;s:9:"simpletax";b:0;s:9:"juridical";b:0;s:9:"basestore";s:2:"15";}');
INSERT INTO `system_options` (`optname`, `optvalue`) VALUES('tax', 'a:9:{s:9:"minsalary";s:4:"1378";s:3:"nsl";s:3:"689";s:6:"minnsl";s:4:"1930";s:3:"nds";s:2:"20";s:6:"onetax";s:2:"10";s:6:"ecbfot";s:2:"22";s:6:"ecbinv";s:4:"8.41";s:5:"taxfl";s:2:"18";s:8:"military";s:3:"1.5";}');

 -- Суммовой учет
INSERT INTO `erp_item` (`item_id`, `itemname`, `description`, `measure_id`,  `detail`, `item_code`, `item_type`) 
VALUES(1, 'Суммовий облік', NULL,   NULL, '1', NULL, 6);

-- план счетов
 INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(20, 'Виробничі запаси', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(22, 'МШП', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(23, 'Виробництво', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(26, 'Готова продукція', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(28, 'Товари', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(30, 'Каса', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(31, 'Рахунки в банках', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(36, 'Розрахунки з покупцями ', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(37, 'Розрахунки з рiзними дебiторами', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(40, 'Статутний капiтал', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(63, 'Розрахунки з постачальниками', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(64, 'Розрахунки за податками й платежами', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(66, 'Розрахунки зa виплатами працівникам', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(70, 'Доходи від реалізації', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(79, 'Фінансові результати', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(90, 'Собівартість реалізації', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(91, 'Загальновиробничі витрати', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(92, 'Адміністративні витрати', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(93, 'Витрати на збут', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(94, 'Інші витрати операційної діяльності', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(201, 'Сировина й матеріали', 20);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(281, 'Товари на складі', 28);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(282, 'Товари в торгівлі', 28);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(285, 'Торгова націнка', 28);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(372, 'Розрахунки з пiдзвiтними особами', 37);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(641, 'Розрахунки за податками', 64);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(642, 'Розрахунки за платежами', 64);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(643, 'Податкові зобов’язання', 64);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(644, 'Податковий кредит', 64);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(98, 'Податок на прибуток', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(701, 'Дохід від реалізації готової продукції', 70);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(702, 'Дохід від реалізації товарів', 70);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(703, 'Дохід від реалізації робіт і послуг', 70);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(901, 'Собівартість реалізованої готової продукції', 90);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(902, 'Собівартість реалізованих товарів', 90);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(903, 'Собівартість реалізованих робіт і послуг', 90);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(97, 'Iншi витриати', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(65, 'Розрахунки за страхуванням', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(15, 'Капітальні інвестиції', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(71, 'Доходи операційної діяльності', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(1001, 'МЦ. Забалансовий', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(12, 'Нематеріальні активи', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(13, 'Знос необоротних активів', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(371, 'Розрахунки за виданими авансами', 37);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(68, 'Розрахунки за iншми операцiями', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(681, 'Розрахунки за отриманими авансами', 68);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(44, 'Нерозподiлений прибуток', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(11, 'Iншi необоротнi активи', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(10, 'Основнi засоби', 0);
INSERT INTO `erp_account_plan` (`acc_code`, `acc_name`, `acc_pid`) VALUES(25, 'Напiвфабрикати', 0);

   
 
-- метаданные
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(1, 4, 'Склади', 'StoreList', 'Склад', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(3, 1, 'Прибуткова  накладна', 'GoodsReceipt', 'Закупівля', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(4, 1, 'Видаткова накладна ', 'GoodsIssue', 'Продажі', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(6, 3, 'Журнал документів', 'DocList', '', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(7, 4, 'Номенклатура', 'ItemList', 'Склад', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(8, 4, 'План рахунків', 'AccountList', 'Бухгалтерія', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(10, 3, 'Журнал проводок', 'Entrylist', '', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(11, 2, 'Оборотно-сальдова відомість', 'Obsaldo', 'Бухгалтерія', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(12, 2, 'Шахматка', 'Shahmatka', 'Бухгалтерія', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(13, 1, 'Ручна  операція', 'ManualEntry', 'Інше', '    <p> Документ предназначен  для  ввода  редкоиспользуемых операций   а  также   ввода  начальных  остатков. </p>\r\n    <p>    Проводки не  требуют  корреспонденции счетов - счета  можно  вводить  по  одному.      </p>\r\n   <p>    Для ввода  аналитики  необходимо  выбрать  один  из  счетов  из  проводок к  которому  будут привязаны  данные    аналитического  учета       </p>  ', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(14, 1, 'Банківска виписка', 'BankStatement', 'Банк', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(15, 4, 'Контрагенти', 'CustomerList', '', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(16, 2, 'Рух по  складу', 'ItemActivity', 'Склад', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(17, 1, 'Переміщення товару', 'MoveItem', 'Склад', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(20, 4, 'Банки', 'BankList', '', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(21, 5, 'Расчеты с  контрагентами', 'CustPayments', '', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(22, 4, 'Співробітники', 'EmployeeList', 'Кадри', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(23, 4, 'Відділи', 'DepartmentList', 'Кадри', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(24, 4, 'Посади', 'PositionList', 'Кадри', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(25, 4, 'Контакти (фіз. особи)', 'ContactList', '', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(26, 3, 'Проекти', 'ProjectList', 'Проекти', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(28, 3, 'Замовлення покупців', 'CustomerOrderList', '', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(29, 3, 'Замовлення постачальникам', 'SupplierOrderList', '', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(30, 3, 'Завдання', 'TaskList', 'Проекти', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(32, 1, 'Замовлення покупця (клієнта)', 'CustomerOrder', 'Продажі', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(33, 1, 'Замовлення постачальнику', 'SupplierOrder', 'Закупівля', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(34, 1, 'Вхідний рахунок', 'PurchaseInvoice', 'Закупівля', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(35, 1, 'Рахунок-фактура', 'Invoice', 'Продажі', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(36, 1, 'Угода', 'Contract', 'Інше', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(39, 1, 'Платіжне доручення', 'TransferOrder', 'Банк', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(41, 1, 'Прибутковий КО', 'CashReceiptIn', 'Каса', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(42, 1, 'Видатковий КО', 'CashReceiptOut', 'Каса', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(43, 1, 'Авансовий звіт', 'ExpenseReport', 'Інше', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(44, 1, 'Роздрібна накладна ', 'RetailIssue', 'Роздріб', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(45, 1, 'Товарний чек', 'RegisterReceipt', 'Роздріб', 'Ввод  проданного  товара  в  рознице.  Может  формироваться   ЭККА  с  помощью  API', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(46, 1, 'Зписання торгової націнки', 'TradeMargin', 'Роздріб', 'Списание  проданных  товаров  в  магазине  с  суммовым  учетом', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(47, 1, 'Гарантійний  талон', 'Warranty', 'Роздріб', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(48, 1, 'Податкова накладна', 'TaxInvoice', 'Продажі', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(49, 1, 'Вхідна НН', 'TaxInvoiceIncome', 'Закупівля', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(50, 3, 'Реєстр ПН', 'TaxInvoiceList', '', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(51, 1, 'Акт виконаних робіт', 'ServiceAct', 'Продажі', 'Акто  виконаних робіт/послуг нашою фірмою', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(52, 1, 'Надані послуги', 'ServiceIncome', 'Закупівля', 'Акт про надання послуг сторонньою организацією', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(88, 5, 'Статистика', 'DashBoard', '', 'Статистика на начало дня.', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(89, 1, 'Накладна інет магазину', 'OnlineIssue', 'Продажі', '', 1);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(54, 2, 'Финансовий звіт малого підприємствая', 'FinancialReportSmall', 'Регламентовані', 'Додаток 1\r\nдо Положення (стандарту) бухгалтерського обліку 25 «Фінансовий звіт суб’єкта малого підприємництва»\r\n', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(55, 1, 'Фінансові  результати', 'FinResult', 'Інше', 'Списание  фин. результатов  на  79  счет', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(56, 5, 'Розрахунки з підзвітними особами', 'AccountablePayments', '', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(57, 1, 'Повернення постачальнику', 'ReturnGoodsReceipt', 'Закупівля', '           Повернення товарів постачальникуу.   Може  бути створений  на  підставі прибуткової  накладної', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(58, 1, 'Накладна на  повернення', 'ReturnGoodsIssue', 'Продажі', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(59, 1, 'Повернення в  роздрібі', 'ReturnRetailIssue', 'Роздріб', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(60, 1, 'Додаток2 до пН', 'TaxInvoice2', '', 'Приложение2 к  НН (корректировка)', 1);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(61, 1, 'Переоцінка (сумовий облік)', 'RevaluationRetSum', 'Склад', '  Документ служит для  переоценки товара  в магазине  с  суммовым  учетом.  также   может  быть использован  для  списания  товаров.', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(62, 1, 'Повернення на  склад', 'MoveBackItem', 'Склад', '  Повернення на склад  з роздрібу ', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(63, 1, 'ІнвентаризацІя', 'Inventory', 'Склад', 'Акт інвентаризації\r\n', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(64, 1, 'Зписання ТМЦ (втрати)', 'InventoryLost', 'Склад', 'Списание  ТМЦ в  результате  недостачи, порчи и  т.д.', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(65, 1, 'Оприбуткування злишків', 'InventoryGain', 'Склад', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(66, 1, 'Переоцінка (роздріб)', 'RevaluationRet', 'Склад', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(67, 2, 'Рух  по рахунку', 'AccountActivity', 'Бухгалтерія', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(68, 4, 'ОЗ та НМА', 'CapitalAssets', '', 'Основні засоби та  нематеріальні активи', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(69, 1, 'Введення в  експлуатацію МЦ', 'MZInMaintenance', 'Малоцінка', 'Введення в  експлуатацію (списання)  МШП та малоцінних необоротних активів', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(70, 1, 'Зписання МЦ', 'MZOutMaintenance', 'Малоцінка', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(71, 1, 'Введення в експлуатацію ОЗ', 'NAInMaintenance', 'ОЗ та НМА', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(72, 1, 'Ліквідація ОЗ', 'NAOutMaintenance', 'ОЗ та НМА', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(73, 1, 'Нарахування зносу', 'NADeprecation', 'ОЗ та НМА', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(74, 1, 'Нарахування зарплаты', 'InSalary', 'Зарплата', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(75, 1, 'Виплата зарплати', 'OutSalary', 'Зарплата', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(76, 1, 'Зписання ТМЦ (видатки)', 'InventoryExpence', 'Склад', 'Списание  ТМЦ  на  производство,  непроизводственные  и административные  затраты', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(78, 5, 'Аналітичний облік', 'AnalyticsView', '', 'Перегляд данних аналітичного обліку в  різних розрізах', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(79, 2, 'АВС аналіз', 'ABC', 'Інше', 'Різні типи АВС аналізу', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(80, 2, 'Касова  книга', 'CashBook', 'Інше', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(81, 2, 'Книга прибутків та видатків', 'IEBook', 'Інше', '', 0);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(82, 2, 'Форма 1ДФ', 'F1df', 'Регламентовані', '', 1);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(83, 2, 'Декларація по  ПДВ', 'Decnds', 'Регламентовані', '', 1);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(84, 2, 'Декларація по податку  на прибуток', 'Declincometax', 'Регламентовані', '', 1);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(85, 2, 'Декларація по  єдиному податку (юр.)', 'Declonetaxj', 'Регламентовані', '', 1);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(86, 2, 'Декларація по єдиному податку (фіз.)', 'Declonetaxf', 'Регламентовані', '', 1);
INSERT INTO `erp_metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`) VALUES(87, 2, 'Звіт по єдиному соціальному внеску', 'Declecb', 'Регламентовані', '', 1);

