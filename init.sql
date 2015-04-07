# Accetic Bundle SQL
INSERT INTO `accetic_config_group`(`name`, `sort_order`, `active`, `create_date`) VALUES ('INVOICE', 0, 1, now());

INSERT INTO `accetic_config`(`group_id`, `control_code`, `value`, `name`, `sort_order`, `active`, `create_date`) VALUES ((SELECT `id` FROM `accetic_config_group` WHERE `name` = 'INVOICE'), 'INV_PREFIX', 'INV-', 'Invoice Prefix', 0, 1, now());
INSERT INTO `accetic_config`(`group_id`, `control_code`, `value`, `name`, `sort_order`, `active`, `create_date`) VALUES ((SELECT `id` FROM `accetic_config_group` WHERE `name` = 'INVOICE'), 'INV_NEXT_NUM', '00001', 'Invoice Next Number', 0, 1, now());
INSERT INTO `accetic_config`(`group_id`, `control_code`, `value`, `name`, `sort_order`, `active`, `create_date`) VALUES ((SELECT `id` FROM `accetic_config_group` WHERE `name` = 'INVOICE'), 'INV_DUE_INTERVAL', '7', 'Default invoice due date interval.', 0, 1, now());

INSERT INTO `accetic_location_class`(`control_code`, `class`,`sort_order`, `active`, `create_date`) VALUES ('POSTAL', 'Postal Address', 0, 1, now());
INSERT INTO `accetic_location_class`(`control_code`, `class`,`sort_order`, `active`, `create_date`) VALUES ('PHYSICAL', 'Physical Address', 1, 1, now());
INSERT INTO `accetic_location_class`(`control_code`, `class`,`sort_order`, `active`, `create_date`) VALUES ('SHIPPING', 'Shipping Address', 2, 1, now());
INSERT INTO `accetic_location_class`(`control_code`, `class`,`sort_order`, `active`, `create_date`) VALUES ('BILLING', 'Billing Address', 3, 1, now());

INSERT INTO `accetic_contact_class`(`control_code`, `class`, `sort_order`, `active`, `create_date`) VALUES ('PRIMARY_EMAIL', 'Primary Email', 0, 1, now());
INSERT INTO `accetic_contact_class`(`control_code`, `class`, `sort_order`, `active`, `create_date`) VALUES ('PRIMARY_TELEPHONE', 'Primary Telephone', 1, 1, now());
INSERT INTO `accetic_contact_class`(`control_code`, `class`, `sort_order`, `active`, `create_date`) VALUES ('PRIMARY_FAX', 'Primary Fax', 2, 1, now());
INSERT INTO `accetic_contact_class`(`control_code`, `class`, `sort_order`, `active`, `create_date`) VALUES ('PRIMARY_MOBILE', 'Primary Mobile', 3, 1, now());
INSERT INTO `accetic_contact_class`(`control_code`, `class`, `sort_order`, `active`, `create_date`) VALUES ('PRIMARY_DIRECT', 'Primary Direct Dial', 4, 1, now());
INSERT INTO `accetic_contact_class`(`control_code`, `class`, `sort_order`, `active`, `create_date`) VALUES ('PRIMARY_WEBSITE', 'Primary Website', 5, 1, now());
INSERT INTO `accetic_contact_class`(`control_code`, `class`, `sort_order`, `active`, `create_date`) VALUES ('PRIMARY_SOCIAL', 'Primary Social', 6, 1, now());
INSERT INTO `accetic_contact_class`(`control_code`, `class`, `sort_order`, `active`, `create_date`) VALUES ('PRIMARY_OTHER', 'Primary Other', 7, 1, now());
INSERT INTO `accetic_contact_class`(`control_code`, `class`, `sort_order`, `active`, `create_date`) VALUES ('EMAIL', 'Email', 8, 1, now());
INSERT INTO `accetic_contact_class`(`control_code`, `class`, `sort_order`, `active`, `create_date`) VALUES ('TELEPHONE', 'Telephone', 9, 1, now());
INSERT INTO `accetic_contact_class`(`control_code`, `class`, `sort_order`, `active`, `create_date`) VALUES ('FAX', 'Fax', 10, 1, now());
INSERT INTO `accetic_contact_class`(`control_code`, `class`, `sort_order`, `active`, `create_date`) VALUES ('MOBILE', 'Mobile', 11, 1, now());
INSERT INTO `accetic_contact_class`(`control_code`, `class`, `sort_order`, `active`, `create_date`) VALUES ('DIRECT', 'Direct Dial', 12, 1, now());
INSERT INTO `accetic_contact_class`(`control_code`, `class`, `sort_order`, `active`, `create_date`) VALUES ('WEBSITE', 'Website', 13, 1, now());
INSERT INTO `accetic_contact_class`(`control_code`, `class`, `sort_order`, `active`, `create_date`) VALUES ('SOCIAL', 'Social',14, 1, now());
INSERT INTO `accetic_contact_class`(`control_code`, `class`, `sort_order`, `active`, `create_date`) VALUES ('OTHER', 'Other', 15, 1, now());


INSERT INTO `accetic_unit_class`(`control_code`, `class`, `description`, `sort_order`, `active`, `create_date`) VALUES ('SUPPLIER', 'Supplier', 'Supplier Class', 0, 1, now());
INSERT INTO `accetic_unit_class`(`control_code`, `class`, `description`, `sort_order`, `active`, `create_date`) VALUES ('CUSTOMER', 'Customer', 'Customer Class', 0, 1, now());
INSERT INTO `accetic_unit_class`(`control_code`, `class`, `description`, `sort_order`, `active`, `create_date`) VALUES ('EMPLOYEE', 'Employee', 'Employee Class', 0, 1, now());
INSERT INTO `accetic_unit_class`(`control_code`, `class`, `description`, `sort_order`, `active`, `create_date`) VALUES ('CONTACT', 'Contact', 'Contact Class', 0, 1, now());

# FAS Bundle SQL

INSERT INTO `fas_statement_status`(`control_code`, `name`, `sort_order`, `active`, `create_date`) VALUES ('NEW', 'New', 0, 1, NOW());
INSERT INTO `fas_statement_status`(`control_code`, `name`, `sort_order`, `active`, `create_date`) VALUES ('PROCESS', 'Processing', 1, 1, NOW());
INSERT INTO `fas_statement_status`(`control_code`, `name`, `sort_order`, `active`, `create_date`) VALUES ('ERROR', 'Contain Error', 2, 1, NOW());
INSERT INTO `fas_statement_status`(`control_code`, `name`, `sort_order`, `active`, `create_date`) VALUES ('FAIL', 'Import Fail', 3, 1, NOW());
INSERT INTO `fas_statement_status`(`control_code`, `name`, `sort_order`, `active`, `create_date`) VALUES ('COMPLETE', 'Complete', 4, 1, NOW());


INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('PFL', 'POK FU LAM ROAD', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('VTR', 'VICTORIA ROAD', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('WNC', 'WONG NAI CHUNG GAP ROAD', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('SWQ', 'SHEUNG WAN', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('PKR', 'PEAK', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('MDR', 'MACDONNELL ROAD', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('KND', 'KENNEDY ROAD', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('CRW', 'CANEL ROAD WEST', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('CWA', 'CHAI WAN', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('QBK', 'KINGS ROAD', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('WLR', 'WATERLOO ROAD WEST', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('PEW', 'PRINCE EDWARD WEST', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('KLT', 'KOWLOON TONG', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('WCS', 'KOWLOON BAY', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('KBA', 'KOWLOON BAY', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('KTO', 'KWUN TONG', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('TYI', 'TSING YI', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('KCH', 'KWAI CHUNG', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('TWN', 'TSUEN WAN', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('TYS', 'TUEN YAN STREET', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('GCO', 'GOLDEN COAST', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('SKW', 'SO KWUN WAT', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('LTE', 'LAM TEI', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('SKO', 'SHEK KONG', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('PHE', 'PAT HEUNG', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('FSW', 'FUI SHA WAI', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('TSW', 'TIN SHUI WAI', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('PSH', 'PING SHAN', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('YLO', 'YUEN LONG', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('PWA', 'POK WAI', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('AUT', 'AU TAU', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('LKM', 'LAM KAM', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('SHS', 'SHEUNG SHUI', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('FWA', 'FANLING WAI', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('FLS', 'FANLING SOUTH', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('FVB', 'FAIRVIEW PARK', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('TPK', 'TAI PO KAU', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('TTR', 'TING TAI ROAD', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('TPO', 'TAI PO', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('SLY', 'SIU LEK YUEN', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('MOS', 'MA ON SHAN', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('TCT', 'TUNG CHUNG 1', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('TC2', 'TUNG CHUNG 2', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('AA1', 'AIRSIDE NO 1', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('AA2', 'AIRSIDE NO 2', 1, now());
INSERT INTO `fas_site`(`name`, `other_name`, `active`, `create_date`) VALUES ('AA3', 'AIRSIDE NO 3', 1, now());
