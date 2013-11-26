CREATE ALGORITHM=UNDEFINED DEFINER=root@`localhost` SQL SECURITY DEFINER VIEW erp_account_entry_view AS
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
    doc.created AS created
  from
    (((erp_account_entry e join erp_account_plan c on((e.acc_c = c.acc_id))) join erp_account_plan d on((e.acc_d = d.acc_id))) join erp_document_view doc on((e.document_id = doc.document_id)));
    
    CREATE ALGORITHM=UNDEFINED DEFINER=root@`localhost` SQL SECURITY DEFINER VIEW erp_docmessage_view AS
  select
    m.docmessage_id AS docmessage_id,
    m.document_id AS document_id,
    m.user_id AS user_id,
    m.created AS created,
    m.message AS message,
    u.userlogin AS userlogin
  from
    (erp_docmessage m join system_users u on((u.user_id = m.user_id)));
    
    CREATE ALGORITHM=UNDEFINED DEFINER=root@`localhost` SQL SECURITY DEFINER VIEW erp_document_view AS
  select
    d.document_id AS document_id,
    d.document_number AS document_number,
    d.document_date AS document_date,
    d.created AS created,
    d.user_id AS user_id,
    d.notes AS notes,
    d.content AS content,
    d.amount AS amount,
    d.type_id AS type_id,
    d.tag AS tag,
    u.userlogin AS userlogin,
    coalesce((
  select
    l.document_state AS document_state
  from
    erp_document_update_log l
  where
    (l.document_id = d.document_id)
  order by
    l.updatedon desc limit 0,1),0) AS state,erp_metadata.meta_name AS meta_name,erp_metadata.description AS meta_desc
  from
    ((erp_document d join system_users u on((d.user_id = u.user_id))) join erp_metadata on((erp_metadata.meta_id = d.type_id)));
    
    CREATE ALGORITHM=UNDEFINED DEFINER=root@`localhost` SQL SECURITY DEFINER VIEW erp_item_view AS
  select
    t.item_id AS item_id,
    t.itemname AS itemname,
    t.description AS description,
    t.measure_id AS measure_id,
    m.measure_name AS measure_name,
    t.item_type AS item_type
  from
    (erp_item t join erp_item_measures m on((t.measure_id = m.measure_id)));
    
    CREATE ALGORITHM=UNDEFINED DEFINER=root@`localhost` SQL SECURITY DEFINER VIEW erp_staff_employee_view AS
  select
    e.employee_id AS employee_id,
    e.fio AS fio,
    e.position_id AS position_id,
    e.department_id AS department_id,
    e.salary_type AS salary_type,
    e.salary AS salary,
    e.hireday AS hireday,
    e.fireday AS fireday,
    e.login AS login,
    d.department_name AS department_name,
    p.position_name AS position_name
  from
    ((erp_staff_employee e left join erp_staff_position p on((e.position_id = p.position_id))) left join erp_staff_department d on((e.department_id = d.department_id)));
    
    CREATE ALGORITHM=UNDEFINED DEFINER=root@`localhost` SQL SECURITY DEFINER VIEW erp_stock_activity_view AS
  select
    erp_stock_activity.stock_activity_id AS stock_activity_id,
    erp_stock_activity.stock_id AS stock_id,
    erp_stock_activity.qty AS quantity,
    erp_stock_activity.user_id AS user_id,
    erp_document.document_date AS updated,
    erp_document.document_id AS document_id,
    erp_store_stock.store_id AS store_id,
    erp_store_stock.item_id AS item_id,
    erp_store_stock.partion AS partion,
    erp_stock_activity.price AS price,
    erp_document.document_number AS document_number
  from
    ((erp_stock_activity join erp_store_stock on((erp_stock_activity.stock_id = erp_store_stock.stock_id))) join erp_document on((erp_stock_activity.document_id = erp_document.document_id)));
    
    CREATE ALGORITHM=UNDEFINED DEFINER=root@`localhost` SQL SECURITY DEFINER VIEW erp_stock_view AS
  select
    erp_store_stock.stock_id AS stock_id,
    erp_store_stock.item_id AS item_id,
    erp_item_view.itemname AS itemname,
    erp_store.storename AS storename,
    erp_store.store_id AS store_id,
    erp_item_view.measure_name AS measure_name,
    coalesce((
  select
    sum(erp_stock_activity.qty) AS qty
  from
    erp_stock_activity
  where
    (erp_stock_activity.stock_id = erp_store_stock.stock_id)),0) AS quantity,coalesce((
  select
    sum(erp_stock_activity.price) AS price
  from
    erp_stock_activity
  where
    (erp_stock_activity.stock_id = erp_store_stock.stock_id)),0) AS price,erp_store_stock.partion AS partion
  from
    ((erp_store_stock join erp_item_view on((erp_store_stock.item_id = erp_item_view.item_id))) join erp_store on((erp_store_stock.store_id = erp_store.store_id)));
    
    
