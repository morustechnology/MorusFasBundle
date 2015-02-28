# Accetic Bundle SQL
INSERT INTO `accetic_config`(`control_code`, `value`, `name`, `sort_order`, `active`, `create_date`) VALUES ('INV_PREFIX', 'INV-', 'Invoice Prefix', 0, 1, now());
INSERT INTO `accetic_config`(`control_code`, `value`, `name`, `sort_order`, `active`, `create_date`) VALUES ('INV_NEXT_NUM', '00001', 'Invoice Next Number', 0, 1, now());

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