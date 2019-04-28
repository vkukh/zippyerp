
SET NAMES 'utf8';


 
INSERT   INTO `users` (  `userlogin`, `userpass`, `createdon`, `active`, `email`, `acl`, `smartmenu`) VALUES(  'admin', '$2y$10$GsjC.thVpQAPMQMO6b4Ma.olbIFr2KMGFz12l5/wnmxI1PEqRDQf.', '2017-01-01', 1, 'admin@admin.admin', '<detail><acltype>1</acltype><onlymy></onlymy><aclview></aclview><acledit></acledit><widgets></widgets></detail>', NULL);

 
INSERT  INTO `stores` (  `storename`, `description`) VALUES(  'Основной склад', '');

 
INSERT INTO `options` (`optname`, `optvalue`) VALUES('common', 'a:11:{s:8:"defstore";s:2:"22";s:9:"qtydigits";s:1:"0";s:8:"amdigits";s:1:"2";s:6:"price1";s:18:"Розничная";s:6:"price2";s:14:"Оптовая";s:6:"price3";s:0:"";s:6:"price4";s:0:"";s:6:"price5";s:0:"";s:6:"hasnds";b:1;s:9:"simpletax";b:0;s:9:"juridical";b:0;}');
INSERT INTO `options` (`optname`, `optvalue`) VALUES('shop', 'N;');
INSERT INTO `options` (`optname`, `optvalue`) VALUES('firmdetail', 'a:13:{s:8:"firmname";s:17:"Моя фирма";s:6:"edrpou";s:13:"каа4234234";s:6:"koatuu";s:0:"";s:5:"kopfg";s:0:"";s:4:"kodu";s:0:"";s:4:"kved";s:0:"";s:3:"gni";s:0:"";s:3:"inn";s:10:"2223323233";s:7:"address";s:0:"";s:5:"phone";s:0:"";s:5:"email";s:12:"leon@eee.eee";s:4:"bank";s:1:"2";s:11:"bankaccount";s:3:"111";}');
INSERT INTO `options` (`optname`, `optvalue`) VALUES('tax', 'a:9:{s:9:"minsalary";s:4:"1378";s:3:"nsl";s:3:"689";s:6:"minnsl";s:4:"1930";s:3:"nds";s:2:"20";s:6:"onetax";s:2:"10";s:6:"ecbfot";s:2:"22";s:6:"ecbinv";s:0:"";s:5:"taxfl";s:2:"18";s:8:"military";s:3:"1.5";}');


INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(1, 4, 'Склады', 'StoreList', 'ТМЦ', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(2, 4, 'Номенклатура', 'ItemList', 'ТМЦ', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(3, 4, 'Сотрудники', 'EmployeeList', '', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(4, 4, 'Категории товаров', 'CategoryList', 'ТМЦ', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(5, 4, 'Контрагенты', 'CustomerList', '', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(6, 1, 'Приходная накладная', 'GoodsReceipt', 'Закупки', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(7, 1, 'Расходная накладная', 'GoodsIssue', 'Продажи', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(8, 3, 'Журнал документов', 'DocList', '', '', 0, 1);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(9, 3, 'Товары на складе', 'StockList', '', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(10, 1, 'Гарантийный талон', 'Warranty', '', '', 1, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(11, 1, 'Перемещение товара', 'MoveItem', 'Склад', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(12, 2, 'Движение по складу', 'ItemActivity', 'Склад', '', 0, 1);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(13, 2, 'ABC анализ', 'ABC', 'Прочие', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(14, 4, 'Услуги, работы', 'ServiceList', '', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(15, 1, 'Акт выполненных работ', 'ServiceAct', 'Продажи', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(16, 1, 'Возврат от покупателя', 'ReturnIssue', 'Продажи', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(18, 3, 'Работы, наряды', 'TaskList', '', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(19, 1, 'Наряд', 'Task', 'Прочее', 'Наряд на выполнение работы, задачи', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(20, 2, 'Оплата по нарядам', 'EmpTask', '', '', 1, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(21, 2, 'Закупки', 'Income', '', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(22, 2, 'Продажи', 'Outcome', '', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(27, 3, 'Журнал заказов', 'OrderList', '', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(28, 1, 'Заказ покупателя', 'Order', 'Продажи', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(30, 1, 'Оприходование  с  производства', 'ProdReceipt', 'Склад', 'Оприходование готовой продукции и полуфабрикатов  с  производства  на  склад.  ', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(31, 1, 'Списание ТМЦ', 'ItemExpence', 'Склад', 'Передача  на производство  материалов  и комплектующий.', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(32, 2, 'Отчет по производству', 'Prod', 'Склад', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(33, 4, 'Производственные участки', 'ProdAreaList', '', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(49, 1, 'Счет входящий', 'PurchaseInvoice', 'Закупки', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(47, 4, 'ОС и НМА', 'CAssetList', '', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(37, 4, 'Единицы измерения', 'MeasureList', 'ТМЦ', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(38, 3, 'План счетов', 'AccountList', 'Бухучет', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(39, 3, 'Журнал проводок', 'AccountEntryList', 'Бухучет', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(40, 3, 'Проводки аналитики', 'AnalyticsView', 'Бухучет', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(41, 1, 'Ручная операция', 'ManualEntry', 'Прочее', 'Документ предназначен для ввода произвольных проводок  по  счетам и аналитике.  Может использоваться  для  ввода начальных остатков ', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(42, 2, 'Оборотно-сальдовая ведомость', 'ObSaldo', 'Бухучет', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(43, 2, 'Шахматная ведомость', 'Shahmatka', 'Бухучет', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(44, 2, 'Движения по  счету', 'AccountActivity', 'Бухучет', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(45, 2, 'Финансовый отчет  малого  предприятия', 'FinancialReportSmall', 'Регламентированные', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(46, 4, 'Банки', 'BankList', '', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(48, 1, 'Фин. результаты', 'FinResult', 'Прочее', 'Списывает  счета на  79 счет', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(50, 1, 'Счет-фактура', 'Invoice', 'Продажи', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(51, 3, 'Расчеты с  контрагентами', 'CustPayments', '', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(52, 2, 'Кассовая книга', 'CashBook', '', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(53, 1, 'Банковская выписка', 'BankStatement', 'Банк и касса', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(54, 1, 'Приходный кассовый ордер', 'CashReceiptIn', 'Банк и касса', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(55, 1, 'Расходный кассовый ордер', 'CashReceiptOut', 'Банк и касса', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(56, 1, 'Ввод МЦ в эксплуатацию', 'MZInMaintenance', 'Склад', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(57, 1, 'Списание  МЦ с эксплуатации', 'MZOutMaintenance', 'Склад', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(58, 3, 'Заявки поставщику', 'OrderCustList', '', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(59, 1, 'Заявка  поставщику', 'OrderCust', 'Закупки', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(60, 3, 'Реестр НН', 'TaxInvoiceList', '', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(61, 1, 'Налоговая накладная', 'TaxInvoice', 'Продажи', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(62, 1, 'Приложение2 к НН', 'TaxInvoice2', 'Продажи', '', 1, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(63, 1, 'Входящая НН', 'TaxInvoiceIncome', 'Закупки', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(64, 1, 'Оприходование излишков', 'InventoryGain', 'Склад', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(65, 1, 'Списание ТМЦ на потери', 'InventoryLost', 'Склад', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(66, 2, 'Прайс', 'Price', 'Склад', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(67, 1, 'Ввод ОС в  эксплуатацию', 'NAInMaintenance', 'ОС и НМА', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(68, 1, 'Амортизация  ОС', 'NADeprecation', 'ОС и НМА', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(69, 1, 'Ликвидация ОС', 'NAOutMaintenance', 'ОС и НМА', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(70, 1, 'Начисление зарплаты', 'InSalary', 'Зарплата', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(71, 1, 'Выплата зарплаты', 'OutSalary', 'Зарплата', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(72, 1, 'Авансовый отчет', 'ExpenseReport', 'Прочее', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(73, 1, 'Платежное поручение', 'TransferOrder', 'Банк и касса', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(74, 1, 'Розничная накладная', 'RetailIssue', 'Розница', 'Перемещение  товаров или  готовойпродукции в торговлю с  торговой  наценкой', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(75, 1, 'Возврат поставщику', 'RetCustIssue', 'Закупки', '', 0, 0);
INSERT INTO `metadata` (`meta_id`, `meta_type`, `description`, `meta_name`, `menugroup`, `notes`, `disabled`, `smartmenu`) VALUES(76, 1, 'Возврат  в рознице', 'RetRetIssue', 'Розница', '', 0, 0);
